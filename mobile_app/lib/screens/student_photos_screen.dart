import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import '../services/api_service.dart';
import '../services/app_titles.dart';

class StudentPhotosScreen extends StatefulWidget {
  const StudentPhotosScreen({Key? key}) : super(key: key);

  @override
  State<StudentPhotosScreen> createState() => _StudentPhotosScreenState();
}

class _StudentPhotosScreenState extends State<StudentPhotosScreen> {
  final primaryColor = const Color(0xff192a56);
  final accentColor = const Color(0xffc5a021);

  bool _isLoading = true;
  bool _isUploading = false;
  int? _uploadingStudentId;

  List<dynamic> _students = [];
  List<dynamic> _classes = [];
  List<dynamic> _sections = [];
  List<dynamic> _filteredSections = [];
  List<dynamic> _filteredStudents = [];

  String _searchQuery = '';
  int? _selectedClassId; // null = all
  int? _selectedSectionId; // null = all

  final ImagePicker _picker = ImagePicker();
  // Map to store cache-buster timestamps for each student image to force reload on upload
  final Map<int, String> _imageVersions = {};

  @override
  void initState() {
    super.initState();
    _fetchStudents();
  }

  Future<void> _fetchStudents() async {
    setState(() {
      _isLoading = true;
    });

    final res = await ApiService.getStudents();
    if (res['status'] == 'success') {
      setState(() {
        _students = res['students'] ?? [];
        _classes = res['classes'] ?? [];
        _sections = res['sections'] ?? [];
        _updateFilteredSections();
        _applyFilters();
        _isLoading = false;
      });
    } else {
      setState(() {
        _isLoading = false;
      });
      _showSnackBar(res['message'] ?? 'فشل تحميل بيانات الطلاب', isError: true);
    }
  }

  void _updateFilteredSections() {
    if (_selectedClassId == null) {
      _filteredSections = [];
      _selectedSectionId = null;
    } else {
      _filteredSections = _sections.where((sec) {
        return sec['class_id']?.toString() == _selectedClassId.toString();
      }).toList();
      // Reset selected section if it's not in the newly filtered sections
      if (_selectedSectionId != null &&
          !_filteredSections.any((sec) => int.tryParse(sec['section_id']?.toString() ?? '') == _selectedSectionId)) {
        _selectedSectionId = null;
      }
    }
  }

  void _applyFilters() {
    setState(() {
      _filteredStudents = _students.where((s) {
        final name = (s['name'] ?? '').toString().toLowerCase();
        final matchesSearch = name.contains(_searchQuery.toLowerCase());
        final matchesClass = _selectedClassId == null ||
            (s['class_id'] != null && int.tryParse(s['class_id'].toString()) == _selectedClassId);
        final matchesSection = _selectedSectionId == null ||
            (s['section_id'] != null && int.tryParse(s['section_id'].toString()) == _selectedSectionId);
        return matchesSearch && matchesClass && matchesSection;
      }).toList();
    });
  }

  Future<void> _pickAndUploadPhoto(int studentId, ImageSource source) async {
    try {
      final XFile? file = await _picker.pickImage(
        source: source,
        imageQuality: 80,
        maxWidth: 600,
        maxHeight: 600,
      );

      if (file == null) return; // User cancelled

      setState(() {
        _isUploading = true;
        _uploadingStudentId = studentId;
      });

      final res = await ApiService.uploadStudentPhoto(studentId, file.path);

      setState(() {
        _isUploading = false;
        _uploadingStudentId = null;
      });

      if (res['status'] == 'success') {
        setState(() {
          // Update version token to force Image.network cache-buster refresh
          _imageVersions[studentId] = DateTime.now().millisecondsSinceEpoch.toString();
        });
        _showSnackBar(res['message'] ?? 'تم تحديث الصورة بنجاح');
      } else {
        _showSnackBar(res['message'] ?? 'فشل تحميل وصنع صورة الطالب', isError: true);
      }
    } catch (e) {
      setState(() {
        _isUploading = false;
        _uploadingStudentId = null;
      });
      _showSnackBar('حدث خطأ أثناء التقاط أو رفع الصورة', isError: true);
    }
  }

  void _showSnackBar(String msg, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          msg,
          style: GoogleFonts.cairo(color: Colors.white, fontWeight: FontWeight.bold),
          textAlign: TextAlign.right,
        ),
        backgroundColor: isError ? Colors.redAccent : const Color(0xff16a34a),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xfff8fafc),
      appBar: AppBar(
        title: Text(
          AppTitles.studentPhotos,
          style: GoogleFonts.cairo(fontWeight: FontWeight.w900, color: Colors.white),
        ),
        centerTitle: true,
        backgroundColor: primaryColor,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Color(0xff192a56)),
              ),
            )
          : Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Collapsible Filter & Search Card
                Container(
                  color: primaryColor,
                  padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
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
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        // Search field
                        TextField(
                          onChanged: (val) {
                            setState(() {
                              _searchQuery = val;
                            });
                            _applyFilters();
                          },
                          style: GoogleFonts.cairo(fontSize: 14),
                          decoration: InputDecoration(
                            filled: true,
                            fillColor: const Color(0xfff1f5f9),
                            hintText: 'ابحث عن اسم الطالب...',
                            hintStyle: GoogleFonts.cairo(color: const Color(0xff94a3b8), fontSize: 13),
                            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                            prefixIcon: const Icon(Icons.search_rounded, color: Color(0xff64748b)),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide.none,
                            ),
                          ),
                        ),
                        const SizedBox(height: 12),

                        // Class & Section Filters in a Row
                        Row(
                          children: [
                            // Class Filter Dropdown
                            Expanded(
                              child: DropdownButtonFormField<int>(
                                isExpanded: true,
                                value: _selectedClassId,
                                style: GoogleFonts.cairo(
                                  fontSize: 14,
                                  color: primaryColor,
                                  fontWeight: FontWeight.bold,
                                ),
                                decoration: InputDecoration(
                                  filled: true,
                                  fillColor: const Color(0xfff1f5f9),
                                  contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                                  labelText: 'الصف',
                                  labelStyle: GoogleFonts.cairo(color: const Color(0xff64748b), fontSize: 12),
                                  border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(12),
                                    borderSide: BorderSide.none,
                                  ),
                                ),
                                items: [
                                  DropdownMenuItem<int>(
                                    value: null,
                                    child: Text('الكل', style: GoogleFonts.cairo(), overflow: TextOverflow.ellipsis),
                                  ),
                                  ..._classes.map((c) {
                                    final id = int.tryParse(c['class_id']?.toString() ?? '');
                                    return DropdownMenuItem<int>(
                                      value: id,
                                      child: Text(c['name'] ?? '', style: GoogleFonts.cairo(), overflow: TextOverflow.ellipsis),
                                    );
                                  }),
                                ],
                                onChanged: (val) {
                                  setState(() {
                                    _selectedClassId = val;
                                    _updateFilteredSections();
                                  });
                                  _applyFilters();
                                },
                              ),
                            ),
                            const SizedBox(width: 10),

                            // Section Filter Dropdown
                            Expanded(
                              child: DropdownButtonFormField<int>(
                                isExpanded: true,
                                value: _selectedSectionId,
                                style: GoogleFonts.cairo(
                                  fontSize: 14,
                                  color: primaryColor,
                                  fontWeight: FontWeight.bold,
                                ),
                                decoration: InputDecoration(
                                  filled: true,
                                  fillColor: const Color(0xfff1f5f9),
                                  contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                                  labelText: 'الفصل',
                                  labelStyle: GoogleFonts.cairo(color: const Color(0xff64748b), fontSize: 12),
                                  border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(12),
                                    borderSide: BorderSide.none,
                                  ),
                                ),
                                items: [
                                  DropdownMenuItem<int>(
                                    value: null,
                                    child: Text('الكل', style: GoogleFonts.cairo(), overflow: TextOverflow.ellipsis),
                                  ),
                                  if (_selectedClassId != null)
                                    ..._filteredSections.map((sec) {
                                      final id = int.tryParse(sec['section_id']?.toString() ?? '');
                                      return DropdownMenuItem<int>(
                                        value: id,
                                        child: Text(sec['name'] ?? '', style: GoogleFonts.cairo(), overflow: TextOverflow.ellipsis),
                                      );
                                    }),
                                ],
                                onChanged: _selectedClassId == null
                                    ? null
                                    : (val) {
                                        setState(() {
                                          _selectedSectionId = val;
                                        });
                                        _applyFilters();
                                      },
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),

                // Student List
                Expanded(
                  child: _filteredStudents.isEmpty
                      ? Center(
                          child: Text(
                            'لا يوجد طلاب مطابقين للبحث',
                            style: GoogleFonts.cairo(
                              fontSize: 15,
                              color: const Color(0xff64748b),
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
                          itemCount: _filteredStudents.length,
                          itemBuilder: (context, index) {
                            final student = _filteredStudents[index];
                            final sId = int.tryParse(student['student_id']?.toString() ?? '0') ?? 0;
                            final classId = student['class_id']?.toString() ?? '';
                            
                            // Find class name
                            final classFound = _classes.firstWhere(
                              (c) => c['class_id']?.toString() == classId,
                              orElse: () => null,
                            );
                            final className = classFound != null ? (classFound['name'] ?? '') : 'غير معين';

                            // Construct photo URL
                            final v = _imageVersions[sId] ?? 'default';
                            final photoUrl = 'https://graya.ly/upload/student_images/$sId.jpg?v=$v';

                            final isThisUploading = _isUploading && _uploadingStudentId == sId;

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
                              padding: const EdgeInsets.all(16),
                              child: Row(
                                children: [
                                  // Photo Avatar with Upload Overlay
                                  Stack(
                                    alignment: Alignment.center,
                                    children: [
                                      Container(
                                        width: 70,
                                        height: 70,
                                        decoration: BoxDecoration(
                                          shape: BoxShape.circle,
                                          border: Border.all(color: accentColor.withOpacity(0.4), width: 2),
                                        ),
                                        child: ClipRRect(
                                          borderRadius: BorderRadius.circular(100),
                                          child: Image.network(
                                            photoUrl,
                                            fit: BoxFit.cover,
                                            errorBuilder: (ctx, err, st) {
                                              return Container(
                                                color: primaryColor.withOpacity(0.08),
                                                child: Icon(Icons.person_rounded, size: 36, color: primaryColor),
                                              );
                                            },
                                          ),
                                        ),
                                      ),
                                      if (isThisUploading)
                                        Container(
                                          width: 70,
                                          height: 70,
                                          decoration: BoxDecoration(
                                            color: Colors.black.withOpacity(0.4),
                                            shape: BoxShape.circle,
                                          ),
                                          child: const CircularProgressIndicator(
                                            strokeWidth: 3,
                                            valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                          ),
                                        ),
                                    ],
                                  ),
                                  const SizedBox(width: 16),

                                  // Student Details
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          student['name'] ?? '',
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: GoogleFonts.cairo(
                                            fontSize: 14,
                                            fontWeight: FontWeight.bold,
                                            color: primaryColor,
                                          ),
                                        ),
                                        Text(
                                          'الصف: $className | الفصل: ${student['section_name'] ?? 'غير معين'}',
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: GoogleFonts.cairo(
                                            fontSize: 12,
                                            color: const Color(0xff64748b),
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        Text(
                                          'الرمز: #$sId',
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: GoogleFonts.cairo(
                                            fontSize: 11,
                                            color: accentColor,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),

                                  // Photo Capture Controls
                                  Column(
                                    children: [
                                      // Take photo using camera
                                      IconButton(
                                        icon: Icon(Icons.camera_alt_rounded, color: accentColor, size: 26),
                                        onPressed: _isUploading ? null : () => _pickAndUploadPhoto(sId, ImageSource.camera),
                                        tooltip: 'التقاط صورة',
                                      ),
                                      // Pick photo from gallery
                                      IconButton(
                                        icon: Icon(Icons.photo_library_rounded, color: primaryColor, size: 24),
                                        onPressed: _isUploading ? null : () => _pickAndUploadPhoto(sId, ImageSource.gallery),
                                        tooltip: 'اختر من المعرض',
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
                ),
              ],
            ),
    );
  }
}
