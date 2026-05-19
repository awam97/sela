import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class StudentsScreen extends StatefulWidget {
  const StudentsScreen({Key? key}) : super(key: key);

  @override
  State<StudentsScreen> createState() => _StudentsScreenState();
}

class _StudentsScreenState extends State<StudentsScreen> {
  bool _isLoading = true;
  List<dynamic> _students = [];
  List<dynamic> _classes = [];
  List<dynamic> _filteredStudents = [];
  
  String _searchQuery = '';
  int? _selectedClassId; // null represents "All"
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchStudentsData();
  }

  Future<void> _fetchStudentsData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getStudents();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result['status'] == 'success') {
          _students = result['students'];
          _classes = result['classes'];
          _applyFilters();
        } else {
          _errorMessage = result['message'];
        }
      });
    }
  }

  void _applyFilters() {
    setState(() {
      _filteredStudents = _students.where((student) {
        // 1. Search Query Filter (name or phone)
        final name = (student['name'] ?? '').toString().toLowerCase();
        final phone = (student['phone'] ?? '').toString();
        final matchesSearch = name.contains(_searchQuery.toLowerCase()) || phone.contains(_searchQuery);

        // 2. Categorical Class Filter
        final matchesClass = _selectedClassId == null || 
            (student['class_id'] != null && int.tryParse(student['class_id'].toString()) == _selectedClassId);

        return matchesSearch && matchesClass;
      }).toList();
    });
  }

  void _showStudentForm(dynamic student) {
    final bool isEdit = student != null;
    final GlobalKey<FormState> formKey = GlobalKey<FormState>();
    
    final TextEditingController nameController = TextEditingController(
      text: isEdit ? (student['name'] ?? '') : ''
    );
    final TextEditingController phoneController = TextEditingController(
      text: isEdit ? (student['phone'] ?? '') : ''
    );
    
    String selectedSex = isEdit ? (student['sex'] ?? 'male') : 'male';
    int? selectedClassId = isEdit 
        ? int.tryParse(student['class_id'].toString()) 
        : (_classes.isNotEmpty ? int.tryParse(_classes[0]['class_id'].toString()) : null);

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return Container(
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(28),
                  topRight: Radius.circular(28),
                ),
              ),
              padding: EdgeInsets.only(
                top: 24,
                left: 24,
                right: 24,
                bottom: MediaQuery.of(context).viewInsets.bottom + 24,
              ),
              child: SingleChildScrollView(
                child: Form(
                  key: formKey,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Center(
                        child: Container(
                          width: 48,
                          height: 4,
                          decoration: BoxDecoration(
                            color: Colors.grey.shade300,
                            borderRadius: BorderRadius.circular(2),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      Text(
                        isEdit ? 'تعديل بيانات الطالب' : 'إضافة طالب جديد للمؤسسة',
                        style: GoogleFonts.cairo(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xff192a56),
                        ),
                      ),
                      const SizedBox(height: 16),

                      Text(
                        'اسم الطالب الكامل',
                        style: GoogleFonts.cairo(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade700),
                      ),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: nameController,
                        textAlign: TextAlign.right,
                        textDirection: TextDirection.rtl,
                        decoration: InputDecoration(
                          hintText: 'أدخل الاسم رباعياً...',
                          hintStyle: GoogleFonts.cairo(color: Colors.grey.shade400, fontSize: 13),
                          prefixIcon: const Icon(Icons.person_rounded, color: Color(0xff192a56)),
                          filled: true,
                          fillColor: Colors.grey.shade50,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.grey.shade200),
                          ),
                        ),
                        validator: (value) {
                          if (value == null || value.trim().isEmpty) {
                            return 'الاسم مطلوب للتحقق';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 16),

                      Text(
                        'رقم الهاتف للمتابعة',
                        style: GoogleFonts.cairo(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade700),
                      ),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: phoneController,
                        keyboardType: TextInputType.phone,
                        textAlign: TextAlign.right,
                        textDirection: TextDirection.ltr,
                        decoration: InputDecoration(
                          hintText: '09xxxxxxxx',
                          hintStyle: GoogleFonts.cairo(color: Colors.grey.shade400, fontSize: 13),
                          prefixIcon: const Icon(Icons.phone_iphone_rounded, color: Color(0xff192a56)),
                          filled: true,
                          fillColor: Colors.grey.shade50,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.grey.shade200),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      Text(
                        'المرحلة والصف الدراسي',
                        style: GoogleFonts.cairo(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade700),
                      ),
                      const SizedBox(height: 6),
                      DropdownButtonFormField<int>(
                        value: selectedClassId,
                        alignment: Alignment.centerRight,
                        decoration: InputDecoration(
                          filled: true,
                          fillColor: Colors.grey.shade50,
                          prefixIcon: const Icon(Icons.school_rounded, color: Color(0xff192a56)),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.grey.shade200),
                          ),
                        ),
                        items: _classes.map<DropdownMenuItem<int>>((item) {
                          return DropdownMenuItem<int>(
                            value: int.tryParse(item['class_id'].toString()),
                            alignment: Alignment.centerRight,
                            child: Text(
                              item['name'],
                              style: GoogleFonts.cairo(fontSize: 13, fontWeight: FontWeight.bold),
                            ),
                          );
                        }).toList(),
                        onChanged: (val) {
                          setModalState(() {
                            selectedClassId = val;
                          });
                        },
                      ),
                      const SizedBox(height: 16),

                      Text(
                        'جنس الطالب',
                        style: GoogleFonts.cairo(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade700),
                      ),
                      const SizedBox(height: 6),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.end,
                        children: [
                          ChoiceChip(
                            label: Text('أنثى', style: GoogleFonts.cairo(fontSize: 12)),
                            selected: selectedSex == 'female',
                            selectedColor: Colors.pink.shade50,
                            labelStyle: GoogleFonts.cairo(
                              color: selectedSex == 'female' ? Colors.pink.shade700 : Colors.grey,
                              fontWeight: FontWeight.bold,
                            ),
                            onSelected: (selected) {
                              if (selected) {
                                setModalState(() {
                                  selectedSex = 'female';
                                });
                              }
                            },
                          ),
                          const SizedBox(width: 12),
                          ChoiceChip(
                            label: Text('ذكر', style: GoogleFonts.cairo(fontSize: 12)),
                            selected: selectedSex == 'male',
                            selectedColor: Colors.blue.shade50,
                            labelStyle: GoogleFonts.cairo(
                              color: selectedSex == 'male' ? Colors.blue.shade700 : Colors.grey,
                              fontWeight: FontWeight.bold,
                            ),
                            onSelected: (selected) {
                              if (selected) {
                                setModalState(() {
                                  selectedSex = 'male';
                                });
                              }
                            },
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),

                      SizedBox(
                        width: double.infinity,
                        height: 48,
                        child: ElevatedButton(
                          onPressed: () async {
                            if (formKey.currentState!.validate()) {
                              if (selectedClassId == null) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text(
                                      'يرجى تحديد الصف الدراسي أولاً',
                                      style: GoogleFonts.cairo(),
                                      textAlign: TextAlign.right,
                                    ),
                                  ),
                                );
                                return;
                              }

                              Navigator.pop(context);

                              setState(() {
                                _isLoading = true;
                              });

                              Map<String, dynamic> response;
                              if (isEdit) {
                                final int studentId = int.parse(student['student_id'].toString());
                                response = await ApiService.editStudent(
                                  studentId,
                                  nameController.text.trim(),
                                  phoneController.text.trim(),
                                  selectedSex,
                                  selectedClassId!,
                                );
                              } else {
                                response = await ApiService.createStudent(
                                  nameController.text.trim(),
                                  phoneController.text.trim(),
                                  selectedSex,
                                  selectedClassId!,
                                );
                              }

                              if (response['status'] == 'success') {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    backgroundColor: Colors.green.shade700,
                                    content: Text(
                                      response['message'] ?? 'تم حفظ البيانات بنجاح!',
                                      style: GoogleFonts.cairo(fontWeight: FontWeight.bold),
                                      textAlign: TextAlign.right,
                                    ),
                                  ),
                                );
                                _fetchStudentsData();
                              } else {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    backgroundColor: Colors.red.shade700,
                                    content: Text(
                                      response['message'] ?? 'فشل حفظ التعديلات',
                                      style: GoogleFonts.cairo(fontWeight: FontWeight.bold),
                                      textAlign: TextAlign.right,
                                    ),
                                  ),
                                );
                                setState(() {
                                  _isLoading = false;
                                });
                              }
                            }
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xff192a56),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                          child: Text(
                            isEdit ? 'تحديث البيانات' : 'حفظ البيانات وإضافة',
                            style: GoogleFonts.cairo(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 14,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }

  void _confirmDeleteStudent(dynamic student) {
    final int studentId = int.parse(student['student_id'].toString());
    final String name = student['name'] ?? '';

    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: Text(
            'تأكيد الحذف!',
            style: GoogleFonts.cairo(fontWeight: FontWeight.bold, color: Colors.red.shade800),
            textAlign: TextAlign.right,
          ),
          content: Text(
            'هل أنت متأكد من حذف الطالب "$name" نهائياً؟ هذا الإجراء سيقوم بمسح كافة السجلات التابعة له ولا يمكن التراجع عنه.',
            style: GoogleFonts.cairo(fontSize: 13),
            textAlign: TextAlign.right,
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text(
                'إلغاء',
                style: GoogleFonts.cairo(color: Colors.grey, fontWeight: FontWeight.bold),
              ),
            ),
            ElevatedButton(
              onPressed: () async {
                Navigator.pop(context);
                setState(() {
                  _isLoading = true;
                });

                final response = await ApiService.deleteStudent(studentId);

                if (response['status'] == 'success') {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      backgroundColor: Colors.green.shade700,
                      content: Text(
                        'تم حذف الطالب بنجاح',
                        style: GoogleFonts.cairo(fontWeight: FontWeight.bold),
                        textAlign: TextAlign.right,
                      ),
                    ),
                  );
                  _fetchStudentsData();
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      backgroundColor: Colors.red.shade700,
                      content: Text(
                        response['message'] ?? 'فشل حذف الطالب',
                        style: GoogleFonts.cairo(fontWeight: FontWeight.bold),
                        textAlign: TextAlign.right,
                      ),
                    ),
                  );
                  setState(() {
                    _isLoading = false;
                  });
                }
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red.shade700,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
              ),
              child: Text(
                'حذف نهائي',
                style: GoogleFonts.cairo(color: Colors.white, fontWeight: FontWeight.bold),
              ),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('دليل وشؤون الطلاب'),
      ),
      body: Column(
        children: [
          // 1. Search Box
          Padding(
            padding: const EdgeInsets.all(16),
            child: TextField(
              textDirection: TextDirection.rtl,
              onChanged: (value) {
                setState(() {
                  _searchQuery = value;
                  _applyFilters();
                });
              },
              decoration: InputDecoration(
                hintText: 'ابحث عن اسم الطالب أو رقم الهاتف...',
                hintStyle: GoogleFonts.cairo(color: const Color(0xff94a3b8), fontSize: 13),
                prefixIcon: const Icon(Icons.search_rounded, color: Color(0xff192a56)),
                filled: true,
                fillColor: Colors.white,
                contentPadding: const EdgeInsets.symmetric(vertical: 12),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: const BorderSide(color: Color(0xffe2e8f0)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: const BorderSide(color: Color(0xffe2e8f0)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: const BorderSide(color: Color(0xffc5a021), width: 1.5),
                ),
              ),
            ),
          ),

          // 2. Horizontal Class Filter Row
          if (!_isLoading && _classes.isNotEmpty) ...[
            SizedBox(
              height: 44,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16),
                itemCount: _classes.length + 1,
                itemBuilder: (context, index) {
                  final isAll = index == 0;
                  final classItem = isAll ? null : _classes[index - 1];
                  final classId = isAll ? null : int.tryParse(classItem['class_id'].toString());
                  final className = isAll ? 'الكل' : classItem['name'];
                  
                  final isSelected = _selectedClassId == classId;

                  return Padding(
                    padding: const EdgeInsets.only(left: 8),
                    child: ChoiceChip(
                      label: Text(className),
                      selected: isSelected,
                      selectedColor: const Color(0xff192a56),
                      backgroundColor: Colors.white,
                      labelStyle: GoogleFonts.cairo(
                        color: isSelected ? Colors.white : const Color(0xff192a56),
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                      ),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(20),
                        side: BorderSide(
                          color: isSelected ? const Color(0xff192a56) : const Color(0xffe2e8f0),
                        ),
                      ),
                      onSelected: (_) {
                        setState(() {
                          _selectedClassId = classId;
                          _applyFilters();
                        });
                      },
                    ),
                  );
                },
              ),
            ),
            const SizedBox(height: 12),
          ],

          // 3. Students List Grid Area
          Expanded(
            child: RefreshIndicator(
              onRefresh: _fetchStudentsData,
              color: const Color(0xffc5a021),
              child: _buildMainContent(),
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showStudentForm(null),
        backgroundColor: const Color(0xff192a56),
        icon: const Icon(Icons.person_add_alt_1_rounded, color: Color(0xffc5a021)),
        label: Text(
          'إضافة طالب جديد',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.white),
        ),
      ),
    );
  }

  Widget _buildMainContent() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator(color: Color(0xff192a56)));
    }

    if (_errorMessage != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.cloud_off_rounded, size: 64, color: Colors.grey),
              const SizedBox(height: 16),
              Text(
                _errorMessage!,
                style: GoogleFonts.cairo(fontSize: 14, color: Colors.grey.shade700),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: _fetchStudentsData,
                child: const Text('إعادة المحاولة'),
              ),
            ],
          ),
        ),
      );
    }

    if (_filteredStudents.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.people_outline_rounded, size: 64, color: Colors.grey),
            const SizedBox(height: 16),
            Text(
              'لا يوجد طلاب مطابقين لمعايير البحث حالياً',
              style: GoogleFonts.cairo(fontSize: 14, color: Colors.grey.shade600),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _filteredStudents.length,
      itemBuilder: (context, index) {
        final student = _filteredStudents[index];
        final bool isActive = (student['activate'] ?? 1).toString() == '1';
        final String sex = student['sex'] ?? 'male';
        final bool isMale = sex == 'male' || sex == 'ذكر';

        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          elevation: 0,
          color: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
            side: const BorderSide(color: Color(0xffe2e8f0), width: 1),
          ),
          child: Padding(
            padding: const EdgeInsets.all(14),
            child: Row(
              children: [
                // Avatar representation based on Gender
                CircleAvatar(
                  radius: 26,
                  backgroundColor: isMale ? const Color(0xffeff6ff) : const Color(0xfffdf2f8),
                  child: Icon(
                    isMale ? Icons.face_rounded : Icons.face_3_rounded,
                    color: isMale ? const Color(0xff2563eb) : const Color(0xffdb2777),
                    size: 32,
                  ),
                ),
                const SizedBox(width: 16),
                
                // Student Information Card Text
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        student['name'] ?? '',
                        style: GoogleFonts.cairo(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xff192a56),
                        ),
                      ),
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                            decoration: BoxDecoration(
                              color: const Color(0xfff1f5f9),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              student['class_name'] ?? 'غير مصنف',
                              style: GoogleFonts.cairo(
                                fontSize: 11,
                                color: const Color(0xff475569),
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          const SizedBox(width: 8),
                          if (student['phone'] != null && student['phone'].toString().isNotEmpty)
                            Text(
                              student['phone'].toString(),
                              style: const TextStyle(fontSize: 12, color: Color(0xff64748b)),
                            ),
                        ],
                      ),
                    ],
                  ),
                ),

                // Status chip & actions menu
                Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                      decoration: BoxDecoration(
                        color: isActive ? const Color(0xfff0fdf4) : const Color(0xfffef2f2),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Text(
                        isActive ? 'نشط' : 'معطل',
                        style: GoogleFonts.cairo(
                          color: isActive ? const Color(0xff16a34a) : const Color(0xffdc2626),
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                    const SizedBox(width: 4),
                    PopupMenuButton<String>(
                      icon: const Icon(Icons.more_vert_rounded, color: Color(0xff192a56), size: 20),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      onSelected: (value) {
                        if (value == 'edit') {
                          _showStudentForm(student);
                        } else if (value == 'delete') {
                          _confirmDeleteStudent(student);
                        }
                      },
                      itemBuilder: (context) => [
                        PopupMenuItem(
                          value: 'edit',
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.end,
                            children: [
                              Text('تعديل البيانات', style: GoogleFonts.cairo(fontSize: 12, fontWeight: FontWeight.bold)),
                              const SizedBox(width: 8),
                              const Icon(Icons.edit_rounded, color: Colors.blue, size: 18),
                            ],
                          ),
                        ),
                        PopupMenuItem(
                          value: 'delete',
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.end,
                            children: [
                              Text('حذف الطالب', style: GoogleFonts.cairo(fontSize: 12, color: Colors.red, fontWeight: FontWeight.bold)),
                              const SizedBox(width: 8),
                              const Icon(Icons.delete_forever_rounded, color: Colors.red, size: 18),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
