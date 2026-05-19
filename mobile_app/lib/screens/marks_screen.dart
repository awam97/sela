import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class MarksScreen extends StatefulWidget {
  const MarksScreen({Key? key}) : super(key: key);

  @override
  State<MarksScreen> createState() => _MarksScreenState();
}

class _MarksScreenState extends State<MarksScreen> {
  final primaryColor = const Color(0xff192a56);
  final accentColor = const Color(0xffc5a021);

  bool _loadingOptions = true;
  bool _loadingStudents = false;
  bool _saving = false;
  bool _filtersExpanded = true;

  List<dynamic> _classes = [];
  List<dynamic> _sections = [];
  List<dynamic> _subjects = [];
  String _currentYear = '';

  // Filter selections
  dynamic _selectedClassId;
  dynamic _selectedSectionId;
  dynamic _selectedSubjectId;

  // Filtered lists for dropdowns
  List<dynamic> _filteredSections = [];
  List<dynamic> _filteredSubjects = [];

  // Students and distribution components
  List<dynamic> _students = [];
  List<dynamic> _distribution = [];
  String _searchQuery = '';

  // Controllers to manage state per student
  // Format: { studentId: { componentName: TextEditingController } }
  final Map<int, Map<String, TextEditingController>> _scoreControllers = {};
  // Format: { studentId: TextEditingController }
  final Map<int, TextEditingController> _commentControllers = {};

  @override
  void initState() {
    super.initState();
    _loadFilters();
  }

  @override
  void dispose() {
    _clearControllers();
    super.dispose();
  }

  void _clearControllers() {
    for (var studentControllers in _scoreControllers.values) {
      for (var controller in studentControllers.values) {
        controller.dispose();
      }
    }
    _scoreControllers.clear();
    for (var controller in _commentControllers.values) {
      controller.dispose();
    }
    _commentControllers.clear();
  }

  Future<void> _loadFilters() async {
    setState(() {
      _loadingOptions = true;
    });

    final res = await ApiService.getMarksOptions();
    if (res['status'] == 'success') {
      setState(() {
        _classes = res['classes'] ?? [];
        _sections = res['sections'] ?? [];
        _subjects = res['subjects'] ?? [];
        _currentYear = res['current_year'] ?? '';
        _loadingOptions = false;
      });
    } else {
      setState(() {
        _loadingOptions = false;
      });
      _showError(res['message'] ?? 'فشل تحميل فلاتر رصد الدرجات');
    }
  }

  void _onClassChanged(dynamic classId) {
    setState(() {
      _selectedClassId = classId;
      _selectedSectionId = null;
      _selectedSubjectId = null;
      _students = [];
      _distribution = [];
      _clearControllers();

      // Filter sections and subjects belonging to this class
      _filteredSections = _sections
          .where((s) => s['class_id']?.toString() == classId?.toString())
          .toList();
      _filteredSubjects = _subjects
          .where((s) => s['class_id']?.toString() == classId?.toString())
          .toList();
    });
  }

  Future<void> _fetchStudentsAndDistribution() async {
    if (_selectedClassId == null ||
        _selectedSectionId == null ||
        _selectedSubjectId == null) {
      return;
    }

    setState(() {
      _loadingStudents = true;
      _students = [];
      _distribution = [];
      _clearControllers();
    });

    final res = await ApiService.getMarksList(
      _selectedClassId,
      _selectedSectionId,
      _selectedSubjectId,
    );

    if (res['status'] == 'success') {
      final List<dynamic> loadedDistribution = res['distribution'] ?? [];
      final List<dynamic> loadedStudents = res['students'] ?? [];

      // Initialize TextEditingControllers for each student and component
      for (var student in loadedStudents) {
        final int sId = int.tryParse(student['student_id']?.toString() ?? '0') ?? 0;
        if (sId == 0) continue;

        // Parse existing marks json
        Map<String, dynamic> existingScores = {};
        if (student['marks_json'] != null && student['marks_json'].toString().isNotEmpty) {
          try {
            existingScores = jsonDecode(student['marks_json'].toString());
          } catch (_) {}
        }

        // Initialize score controllers
        final Map<String, TextEditingController> studentScores = {};
        for (var component in loadedDistribution) {
          final compName = component['name'] ?? '';
          final double val = double.tryParse(existingScores[compName]?.toString() ?? '') ?? -1;
          
          studentScores[compName] = TextEditingController(
            text: val >= 0 ? val.toStringAsFixed(val.truncateToDouble() == val ? 0 : 1) : '',
          );
        }
        _scoreControllers[sId] = studentScores;

        // Initialize comment controller
        _commentControllers[sId] = TextEditingController(
          text: student['comment']?.toString() ?? '',
        );
      }

      setState(() {
        _distribution = loadedDistribution;
        _students = loadedStudents;
        _loadingStudents = false;
        // Automatically collapse filters after loading students to maximize viewport area
        _filtersExpanded = false;
      });
    } else {
      setState(() {
        _loadingStudents = false;
      });
      _showError(res['message'] ?? 'فشل تحميل قائمة الطلاب والدرجات');
    }
  }

  double _calculateStudentTotal(int studentId) {
    double total = 0;
    final controllers = _scoreControllers[studentId];
    if (controllers == null) return 0;

    for (var controller in controllers.values) {
      final double score = double.tryParse(controller.text) ?? 0;
      total += score;
    }
    return total;
  }

  double _getOverallMaxPossible() {
    double total = 0;
    for (var component in _distribution) {
      final double maxVal = double.tryParse(component['max_mark']?.toString() ?? '0') ?? 0;
      total += maxVal;
    }
    return total;
  }

  Future<void> _saveAllMarks() async {
    if (_selectedClassId == null ||
        _selectedSectionId == null ||
        _selectedSubjectId == null) {
      return;
    }

    // Validate grades before saving
    bool isValid = true;
    String validationMessage = '';

    for (var student in _students) {
      final int sId = int.tryParse(student['student_id']?.toString() ?? '0') ?? 0;
      if (sId == 0) continue;

      final controllers = _scoreControllers[sId];
      if (controllers == null) continue;

      for (var component in _distribution) {
        final compName = component['name'] ?? '';
        final double maxMark = double.tryParse(component['max_mark']?.toString() ?? '0') ?? 0;
        final controller = controllers[compName];
        if (controller == null) continue;

        final double? score = double.tryParse(controller.text);
        if (score != null) {
          if (score < 0 || score > maxMark) {
            isValid = false;
            validationMessage = 'الدرجة المدخلة للطالب (${student['student_name']}) في مكوّن ($compName) تتجاوز الحد الأقصى ($maxMark)';
            break;
          }
        }
      }
      if (!isValid) break;
    }

    if (!isValid) {
      _showError(validationMessage);
      return;
    }

    setState(() {
      _saving = true;
    });

    // Assemble payload
    final List<Map<String, dynamic>> payload = [];
    final double totalPossible = _getOverallMaxPossible();

    for (var student in _students) {
      final int sId = int.tryParse(student['student_id']?.toString() ?? '0') ?? 0;
      if (sId == 0) continue;

      final controllers = _scoreControllers[sId];
      final commentController = _commentControllers[sId];
      if (controllers == null) continue;

      final Map<String, double> scores = {};
      for (var entry in controllers.entries) {
        if (entry.value.text.trim().isNotEmpty) {
          scores[entry.key] = double.tryParse(entry.value.text.trim()) ?? 0;
        }
      }

      payload.add({
        'student_id': sId,
        'scores': scores,
        'total_possible': totalPossible,
        'comment': commentController?.text ?? '',
      });
    }

    final res = await ApiService.saveMarks(
      classId: _selectedClassId,
      sectionId: _selectedSectionId,
      subjectId: _selectedSubjectId,
      marks: payload,
    );

    setState(() {
      _saving = false;
    });

    if (res['status'] == 'success') {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            res['message'] ?? 'تم رصد وحفظ الدرجات بنجاح',
            style: GoogleFonts.cairo(color: Colors.white, fontWeight: FontWeight.bold),
            textAlign: TextAlign.right,
          ),
          backgroundColor: const Color(0xff16a34a),
        ),
      );
      // Reload grades list
      _fetchStudentsAndDistribution();
    } else {
      _showError(res['message'] ?? 'فشل حفظ ورصد الدرجات');
    }
  }

  void _showError(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          msg,
          style: GoogleFonts.cairo(color: Colors.white),
          textAlign: TextAlign.right,
        ),
        backgroundColor: Colors.redAccent,
      ),
    );
  }

  String _getSelectedClassName() {
    if (_selectedClassId == null) return '';
    final found = _classes.firstWhere(
        (c) => c['class_id']?.toString() == _selectedClassId.toString(),
        orElse: () => null);
    return found != null ? (found['name'] ?? '') : '';
  }

  String _getSelectedSectionName() {
    if (_selectedSectionId == null) return '';
    final found = _sections.firstWhere(
        (s) => s['section_id']?.toString() == _selectedSectionId.toString(),
        orElse: () => null);
    return found != null ? (found['name'] ?? '') : '';
  }

  String _getSelectedSubjectName() {
    if (_selectedSubjectId == null) return '';
    final found = _subjects.firstWhere(
        (s) => s['subject_id']?.toString() == _selectedSubjectId.toString(),
        orElse: () => null);
    return found != null ? (found['name'] ?? '') : '';
  }

  @override
  Widget build(BuildContext context) {
    final filteredStudentList = _students.where((s) {
      final name = s['student_name']?.toString().toLowerCase() ?? '';
      return name.contains(_searchQuery.toLowerCase());
    }).toList();

    return Scaffold(
      backgroundColor: const Color(0xfff8fafc),
      appBar: AppBar(
        title: Text(
          'رصد درجات المواد',
          style: GoogleFonts.cairo(fontWeight: FontWeight.w900, color: Colors.white),
        ),
        centerTitle: true,
        backgroundColor: primaryColor,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _loadingOptions
          ? const Center(
              child: CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Color(0xff192a56)),
              ),
            )
          : Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Academic Selection Collapsible Card
                AnimatedContainer(
                  duration: const Duration(milliseconds: 300),
                  color: primaryColor,
                  padding: const EdgeInsets.fromLTRB(20, 0, 20, 16),
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.08),
                          blurRadius: 15,
                          offset: const Offset(0, 5),
                        ),
                      ],
                    ),
                    padding: const EdgeInsets.all(18),
                    child: _filtersExpanded
                        ? Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    'تصفية خيارات الرصد',
                                    style: GoogleFonts.cairo(
                                      fontSize: 16,
                                      fontWeight: FontWeight.w900,
                                      color: primaryColor,
                                    ),
                                  ),
                                  if (_selectedClassId != null &&
                                      _selectedSectionId != null &&
                                      _selectedSubjectId != null)
                                    IconButton(
                                      icon: const Icon(Icons.unfold_less_rounded),
                                      color: primaryColor,
                                      onPressed: () {
                                        setState(() {
                                          _filtersExpanded = false;
                                        });
                                      },
                                    ),
                                ],
                              ),
                              const SizedBox(height: 12),

                              // Class Dropdown
                              _buildDropdownField(
                                label: 'الصف الدراسي',
                                value: _selectedClassId,
                                items: _classes.map((c) {
                                  return DropdownMenuItem(
                                    value: c['class_id'],
                                    child: Text(c['name'] ?? ''),
                                  );
                                }).toList(),
                                onChanged: _onClassChanged,
                              ),
                              const SizedBox(height: 12),

                              // Row for Section & Subject Dropdowns
                              Row(
                                children: [
                                  Expanded(
                                    child: _buildDropdownField(
                                      label: 'الفصل (الشعبة)',
                                      value: _selectedSectionId,
                                      items: _filteredSections.map((s) {
                                        return DropdownMenuItem(
                                          value: s['section_id'],
                                          child: Text(s['name'] ?? ''),
                                        );
                                      }).toList(),
                                      onChanged: (val) {
                                        setState(() {
                                          _selectedSectionId = val;
                                          _students = [];
                                          _distribution = [];
                                        });
                                        _fetchStudentsAndDistribution();
                                      },
                                      disabled: _selectedClassId == null,
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: _buildDropdownField(
                                      label: 'المادة الدراسية',
                                      value: _selectedSubjectId,
                                      items: _filteredSubjects.map((s) {
                                        return DropdownMenuItem(
                                          value: s['subject_id'],
                                          child: Text(s['name'] ?? ''),
                                        );
                                      }).toList(),
                                      onChanged: (val) {
                                        setState(() {
                                          _selectedSubjectId = val;
                                          _students = [];
                                          _distribution = [];
                                        });
                                        _fetchStudentsAndDistribution();
                                      },
                                      disabled: _selectedClassId == null,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          )
                        : InkWell(
                            onTap: () {
                              setState(() {
                                _filtersExpanded = true;
                              });
                            },
                            child: Row(
                              children: [
                                CircleAvatar(
                                  radius: 20,
                                  backgroundColor: primaryColor.withOpacity(0.08),
                                  child: Icon(Icons.tune_rounded, color: primaryColor),
                                ),
                                const SizedBox(width: 14),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'خيارات الرصد المحددة',
                                        style: GoogleFonts.cairo(
                                          fontSize: 11,
                                          fontWeight: FontWeight.bold,
                                          color: const Color(0xff64748b),
                                        ),
                                      ),
                                      Text(
                                        '${_getSelectedClassName()} - ${_getSelectedSectionName()} | ${_getSelectedSubjectName()}',
                                        style: GoogleFonts.cairo(
                                          fontSize: 13,
                                          fontWeight: FontWeight.bold,
                                          color: primaryColor,
                                        ),
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ],
                                  ),
                                ),
                                Icon(
                                  Icons.unfold_more_rounded,
                                  color: primaryColor,
                                ),
                              ],
                            ),
                          ),
                  ),
                ),

                // Search Bar & Info Header
                if (_selectedClassId != null &&
                    _selectedSectionId != null &&
                    _selectedSubjectId != null) ...[
                  Padding(
                    padding: const EdgeInsets.fromLTRB(20, 12, 20, 8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'قائمة الطلاب المستهدفين',
                          style: GoogleFonts.cairo(
                            fontSize: 15,
                            fontWeight: FontWeight.w900,
                            color: primaryColor,
                          ),
                        ),
                        Text(
                          'عدد الطلاب: ${filteredStudentList.length}',
                          style: GoogleFonts.cairo(
                            fontSize: 13,
                            color: const Color(0xff64748b),
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),

                  // Search Bar Input (Borderless inside soft background, matching request)
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: TextField(
                      onChanged: (val) {
                        setState(() {
                          _searchQuery = val;
                        });
                      },
                      style: GoogleFonts.cairo(fontSize: 14),
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: Colors.white,
                        hintText: 'ابحث عن اسم الطالب...',
                        hintStyle: GoogleFonts.cairo(color: const Color(0xff94a3b8), fontSize: 13),
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        prefixIcon: const Icon(Icons.search_rounded, color: Color(0xff64748b)),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: BorderSide(color: accentColor, width: 1.2),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),
                ],

                // Student marks entry list
                Expanded(
                  child: _loadingStudents
                      ? const Center(
                          child: CircularProgressIndicator(
                            valueColor: AlwaysStoppedAnimation<Color>(Color(0xff192a56)),
                          ),
                        )
                      : (_selectedClassId == null ||
                              _selectedSectionId == null ||
                              _selectedSubjectId == null)
                          ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.info_outline_rounded, size: 64, color: primaryColor.withOpacity(0.3)),
                                  const SizedBox(height: 16),
                                  Text(
                                    'يرجى تحديد الصف، الفصل، والمادة للبدء',
                                    style: GoogleFonts.cairo(
                                      fontSize: 15,
                                      color: const Color(0xff64748b),
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ],
                              ),
                            )
                          : filteredStudentList.isEmpty
                              ? Center(
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(Icons.people_outline_rounded, size: 64, color: primaryColor.withOpacity(0.3)),
                                      const SizedBox(height: 16),
                                      Text(
                                        'لا يوجد طلاب مسجلين بالخيارات المحددة',
                                        style: GoogleFonts.cairo(
                                          fontSize: 15,
                                          color: const Color(0xff64748b),
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ],
                                  ),
                                )
                              : ListView.builder(
                                  padding: const EdgeInsets.fromLTRB(20, 4, 20, 100),
                                  itemCount: filteredStudentList.length,
                                  itemBuilder: (context, idx) {
                                    final student = filteredStudentList[idx];
                                    final int sId = int.tryParse(student['student_id']?.toString() ?? '0') ?? 0;
                                    return _buildStudentMarkCard(student, sId);
                                  },
                                ),
                ),
              ],
            ),
      bottomNavigationBar: (_selectedClassId != null &&
              _selectedSectionId != null &&
              _selectedSubjectId != null &&
              _students.isNotEmpty &&
              !_loadingStudents)
          ? Container(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
              decoration: BoxDecoration(
                color: Colors.white,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.06),
                    blurRadius: 10,
                    offset: const Offset(0, -4),
                  ),
                ],
              ),
              child: SizedBox(
                height: 54,
                child: ElevatedButton.icon(
                  onPressed: _saving ? null : _saveAllMarks,
                  icon: _saving
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                          ),
                        )
                      : const Icon(Icons.save_rounded, color: Colors.white),
                  label: Text(
                    _saving ? 'جاري الحفظ...' : 'حفظ ورصد درجات الطلاب',
                    style: GoogleFonts.cairo(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: primaryColor,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                    elevation: 0,
                  ),
                ),
              ),
            )
          : null,
    );
  }

  Widget _buildDropdownField({
    required String label,
    required dynamic value,
    required List<DropdownMenuItem<dynamic>> items,
    required ValueChanged<dynamic> onChanged,
    bool disabled = false,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.cairo(
            fontSize: 12,
            fontWeight: FontWeight.bold,
            color: const Color(0xff64748b),
          ),
        ),
        const SizedBox(height: 6),
        Container(
          decoration: BoxDecoration(
            color: disabled ? const Color(0xfff1f5f9) : Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: const Color(0xffcbd5e1), width: 1.1),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 14),
          child: DropdownButtonHideUnderline(
            child: DropdownButton<dynamic>(
              value: value,
              items: disabled ? [] : items,
              onChanged: disabled ? null : onChanged,
              isExpanded: true,
              hint: Text(
                'اختر...',
                style: GoogleFonts.cairo(fontSize: 13, color: const Color(0xff94a3b8)),
              ),
              style: GoogleFonts.cairo(
                fontSize: 14,
                color: primaryColor,
                fontWeight: FontWeight.bold,
              ),
              icon: Icon(
                Icons.keyboard_arrow_down_rounded,
                color: disabled ? const Color(0xff94a3b8) : primaryColor,
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildStudentMarkCard(dynamic student, int sId) {
    final totalObtained = _calculateStudentTotal(sId);
    final totalPossible = _getOverallMaxPossible();

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xffe2e8f0), width: 1.2),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.all(18),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Student Header Row
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  CircleAvatar(
                    radius: 18,
                    backgroundColor: primaryColor.withOpacity(0.08),
                    child: Icon(Icons.person_rounded, size: 20, color: primaryColor),
                  ),
                  const SizedBox(width: 10),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        student['student_name'] ?? '',
                        style: GoogleFonts.cairo(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: primaryColor,
                        ),
                      ),
                      Text(
                        'ID: ${student['student_id'] ?? '-'}',
                        style: GoogleFonts.cairo(
                          fontSize: 11,
                          color: const Color(0xff64748b),
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ],
              ),

              // Overall Grade Pill
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: primaryColor.withOpacity(0.05),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: primaryColor.withOpacity(0.12), width: 1),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      'المجموع: ',
                      style: GoogleFonts.cairo(fontSize: 12, color: const Color(0xff64748b), fontWeight: FontWeight.bold),
                    ),
                    Text(
                      totalObtained.toStringAsFixed(totalObtained.truncateToDouble() == totalObtained ? 0 : 1),
                      style: GoogleFonts.cairo(
                        fontSize: 13,
                        color: primaryColor,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                    Text(
                      ' / $totalPossible',
                      style: GoogleFonts.cairo(
                        fontSize: 12,
                        color: accentColor,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const Divider(height: 24, thickness: 1),

          // Grade Components Input Fields (Wrap layout for mobile)
          Wrap(
            spacing: 12,
            runSpacing: 12,
            children: _distribution.map((component) {
              final compName = component['name'] ?? '';
              final double maxMark = double.tryParse(component['max_mark']?.toString() ?? '0') ?? 0;
              final controller = _scoreControllers[sId]?[compName];

              return SizedBox(
                width: 140,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      '$compName (الأقصى $maxMark)',
                      style: GoogleFonts.cairo(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xff64748b),
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 6),
                    // Borderless input styled natively without wrapping bordered containers
                    TextField(
                      controller: controller,
                      keyboardType: const TextInputType.numberWithOptions(decimal: true),
                      style: GoogleFonts.cairo(
                        fontSize: 13,
                        fontWeight: FontWeight.bold,
                        color: primaryColor,
                      ),
                      textAlign: TextAlign.center,
                      onChanged: (val) {
                        // Trigger UI recalculation for totals dynamically
                        setState(() {});
                      },
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: const Color(0xfff1f5f9),
                        contentPadding: const EdgeInsets.symmetric(horizontal: 8, vertical: 10),
                        hintText: '-',
                        hintStyle: GoogleFonts.cairo(color: const Color(0xff94a3b8)),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(10),
                          borderSide: BorderSide.none,
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(10),
                          borderSide: BorderSide.none,
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(10),
                          borderSide: BorderSide(color: accentColor, width: 1.2),
                        ),
                      ),
                    ),
                  ],
                ),
              );
            }).toList(),
          ),
          const SizedBox(height: 16),

          // Optional comment notes input
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'ملاحظات / تعليق المعلم',
                style: GoogleFonts.cairo(
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                  color: const Color(0xff64748b),
                ),
              ),
              const SizedBox(height: 6),
              // Borderless comment input styled natively without wrapping bordered containers
              TextField(
                controller: _commentControllers[sId],
                style: GoogleFonts.cairo(fontSize: 13, color: primaryColor),
                decoration: InputDecoration(
                  filled: true,
                  fillColor: const Color(0xfff1f5f9),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                  hintText: 'أضف تعليقاً على أداء الطالب هنا (اختياري)...',
                  hintStyle: GoogleFonts.cairo(color: const Color(0xff94a3b8), fontSize: 11),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide.none,
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide.none,
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: accentColor, width: 1.2),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
