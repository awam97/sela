import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart' as intl;
import '../services/api_service.dart';

class AttendanceScreen extends StatefulWidget {
  const AttendanceScreen({Key? key}) : super(key: key);

  @override
  State<AttendanceScreen> createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends State<AttendanceScreen> {
  bool _isLoading = true;
  bool _isSaving = false;
  List<dynamic> _students = [];
  List<dynamic> _classes = [];
  
  int? _selectedClassId;
  DateTime _selectedDate = DateTime.now();
  
  // Maps student_id to attendance status (1 = present, 2 = absent, 3 = late)
  final Map<int, int> _attendanceRecords = {};
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchRosterData();
  }

  Future<void> _fetchRosterData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getStudents();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result['status'] == 'success') {
          _classes = result['classes'];
          _students = result['students'];
          
          if (_classes.isNotEmpty) {
            _selectedClassId = int.tryParse(_classes.first['class_id'].toString());
            _initializeRoster();
          }
        } else {
          _errorMessage = result['message'];
        }
      });
    }
  }

  void _initializeRoster() {
    _attendanceRecords.clear();
    if (_selectedClassId == null) return;
    
    // Filter active students belonging to the selected class
    final classStudents = _students.where((s) =>
        s['class_id'] != null && int.tryParse(s['class_id'].toString()) == _selectedClassId
    );

    for (var student in classStudents) {
      final studentId = int.tryParse(student['student_id'].toString());
      if (studentId != null) {
        // Defaulting status to 1 (Present) for faster workflow
        _attendanceRecords[studentId] = 1;
      }
    }
  }

  void _handleSaveAttendance() async {
    if (_selectedClassId == null || _attendanceRecords.isEmpty) return;

    setState(() {
      _isSaving = true;
    });

    final dateStr = intl.DateFormat('yyyy-MM-dd').format(_selectedDate);
    final result = await ApiService.saveAttendance(
      _selectedClassId!,
      dateStr,
      _attendanceRecords,
    );

    if (mounted) {
      setState(() {
        _isSaving = false;
      });

      if (result['status'] == 'success') {
        // Show success sheet in Arabic
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'تم حفظ كشف الحضور والغياب اليومي بنجاح!',
              style: GoogleFonts.cairo(fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
            backgroundColor: Colors.green,
            behavior: SnackBarBehavior.floating,
          ),
        );
        Navigator.pop(context);
      } else {
        showDialog(
          context: context,
          builder: (context) => AlertDialog(
            title: Text('تنبيه خطأ', style: GoogleFonts.cairo(fontWeight: FontWeight.bold, color: Colors.red)),
            content: Text(result['message'] ?? 'فشلت عملية حفظ التحضير', style: GoogleFonts.cairo()),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: Text('حسناً', style: GoogleFonts.cairo()),
              ),
            ],
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    // Filter students roster based on active selected class
    final classStudents = _students.where((s) =>
        _selectedClassId != null && s['class_id'] != null && int.tryParse(s['class_id'].toString()) == _selectedClassId
    ).toList();

    return Scaffold(
      appBar: AppBar(
        title: const Text('تسجيل الحضور والغياب'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xff192a56)))
          : Column(
              children: [
                // 1. Selector bar (Class & Date)
                Container(
                  padding: const EdgeInsets.all(16),
                  color: Colors.white,
                  child: Row(
                    children: [
                      // Class dropdown
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'الفصل الدراسي',
                              style: GoogleFonts.cairo(fontSize: 12, color: const Color(0xff64748b), fontWeight: FontWeight.bold),
                            ),
                            const SizedBox(height: 4),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12),
                              decoration: BoxDecoration(
                                color: const Color(0xfff8fafc),
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: const Color(0xffe2e8f0)),
                              ),
                              child: DropdownButtonHideUnderline(
                                child: DropdownButton<int>(
                                  value: _selectedClassId,
                                  isExpanded: true,
                                  dropdownColor: Colors.white,
                                  items: _classes.map((c) {
                                    return DropdownMenuItem<int>(
                                      value: int.tryParse(c['class_id'].toString()),
                                      child: Text(c['name'] ?? '', style: GoogleFonts.cairo(fontSize: 14, color: const Color(0xff192A56), fontWeight: FontWeight.bold)),
                                    );
                                  }).toList(),
                                  onChanged: (value) {
                                    setState(() {
                                      _selectedClassId = value;
                                      _initializeRoster();
                                    });
                                  },
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 16),
                      
                      // Date Selector
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'تاريخ التحضير',
                              style: GoogleFonts.cairo(fontSize: 12, color: const Color(0xff64748b), fontWeight: FontWeight.bold),
                            ),
                            const SizedBox(height: 4),
                            InkWell(
                              onTap: () async {
                                final DateTime? picked = await showDatePicker(
                                  context: context,
                                  initialDate: _selectedDate,
                                  firstDate: DateTime(2025),
                                  lastDate: DateTime.now(),
                                  locale: const Locale('ar', 'AE'),
                                );
                                if (picked != null && picked != _selectedDate) {
                                  setState(() {
                                    _selectedDate = picked;
                                  });
                                }
                              },
                              child: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                                decoration: BoxDecoration(
                                  color: const Color(0xfff8fafc),
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(color: const Color(0xffe2e8f0)),
                                ),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      intl.DateFormat('yyyy/MM/dd').format(_selectedDate),
                                      style: GoogleFonts.cairo(fontSize: 14, color: const Color(0xff192A56), fontWeight: FontWeight.bold),
                                    ),
                                    const Icon(Icons.calendar_month_rounded, color: Color(0xffc5a021), size: 20),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                const Divider(height: 1, thickness: 1, color: Color(0xffe2e8f0)),

                // 2. Roster List View
                Expanded(
                  child: _errorMessage != null
                      ? Center(child: Text(_errorMessage!, style: GoogleFonts.cairo()))
                      : classStudents.isEmpty
                          ? Center(
                              child: Text(
                                'لا يوجد طلاب مسجلين في هذا الصف الدراسي',
                                style: GoogleFonts.cairo(fontSize: 14, color: const Color(0xff94a3b8)),
                              ),
                            )
                          : ListView.builder(
                              padding: const EdgeInsets.all(16),
                              itemCount: classStudents.length,
                              itemBuilder: (context, index) {
                                final student = classStudents[index];
                                final studentId = int.tryParse(student['student_id'].toString()) ?? 0;
                                final status = _attendanceRecords[studentId] ?? 1;

                                return Card(
                                  margin: const EdgeInsets.only(bottom: 12),
                                  elevation: 0,
                                  color: Colors.white,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16),
                                    side: const BorderSide(color: Color(0xffe2e8f0), width: 1),
                                  ),
                                  child: Padding(
                                    padding: const EdgeInsets.all(12),
                                    child: Row(
                                      children: [
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                student['name'] ?? '',
                                                style: GoogleFonts.cairo(
                                                  fontSize: 14,
                                                  fontWeight: FontWeight.bold,
                                                  color: const Color(0xff192a56),
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        
                                        // Three-State Selector row
                                        Row(
                                          children: [
                                            _buildStateButton(studentId, 1, 'حاضر', const Color(0xff16a34a), status == 1),
                                            const SizedBox(width: 6),
                                            _buildStateButton(studentId, 2, 'غائب', const Color(0xffdc2626), status == 2),
                                            const SizedBox(width: 6),
                                            _buildStateButton(studentId, 3, 'متأخر', const Color(0xffd97706), status == 3),
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                );
                              },
                            ),
                ),
              ],
            ),
      bottomNavigationBar: _selectedClassId == null || classStudents.isEmpty
          ? null
          : Container(
              padding: const EdgeInsets.all(16),
              decoration: const BoxDecoration(
                color: Colors.white,
                border: Border(
                  top: BorderSide(color: Color(0xffe2e8f0), width: 1),
                ),
              ),
              child: ElevatedButton(
                onPressed: _isSaving ? null : _handleSaveAttendance,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xff192a56),
                  foregroundColor: Colors.white,
                  elevation: 0,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                ),
                child: _isSaving
                    ? const SizedBox(
                        height: 24,
                        width: 24,
                        child: CircularProgressIndicator(color: Colors.white, strokeWidth: 3),
                      )
                    : Text(
                        'حفظ سجل الحضور والغياب',
                        style: GoogleFonts.cairo(fontWeight: FontWeight.bold),
                      ),
              ),
            ),
    );
  }

  Widget _buildStateButton(int studentId, int targetStatus, String label, Color color, bool isSelected) {
    Color bgSelectionColor = Colors.transparent;
    if (isSelected) {
      if (targetStatus == 1) {
        bgSelectionColor = const Color(0xfff0fdf4);
      } else if (targetStatus == 2) {
        bgSelectionColor = const Color(0xfffef2f2);
      } else if (targetStatus == 3) {
        bgSelectionColor = const Color(0xfffffbeb);
      }
    }

    return InkWell(
      onTap: () {
        setState(() {
          _attendanceRecords[studentId] = targetStatus;
        });
      },
      borderRadius: BorderRadius.circular(30),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
        decoration: BoxDecoration(
          color: bgSelectionColor,
          borderRadius: BorderRadius.circular(30),
          border: Border.all(
            color: isSelected ? color : const Color(0xffe2e8f0),
            width: isSelected ? 1.5 : 1.0,
          ),
        ),
        child: Row(
          children: [
            Container(
              width: 8,
              height: 8,
              decoration: BoxDecoration(
                color: isSelected ? color : const Color(0xffcbd5e1),
                shape: BoxShape.circle,
              ),
            ),
            const SizedBox(width: 4),
            Text(
              label,
              style: GoogleFonts.cairo(
                fontSize: 10,
                color: isSelected ? color : const Color(0xff64748b),
                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
