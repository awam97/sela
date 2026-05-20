import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class ManageSchoolsScreen extends StatefulWidget {
  const ManageSchoolsScreen({Key? key}) : super(key: key);

  @override
  State<ManageSchoolsScreen> createState() => _ManageSchoolsScreenState();
}

class _ManageSchoolsScreenState extends State<ManageSchoolsScreen> {
  bool _isLoading = true;
  List<dynamic> _allSchools = [];
  List<dynamic> _filteredSchools = [];
  List<dynamic> _cities = [];
  String? _errorMessage;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _fetchData();
    _searchController.addListener(_filterSchools);
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _fetchData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final schoolsRes = await ApiService.getSchools();
    final citiesRes = await ApiService.getCities();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (schoolsRes['status'] == 'success' && citiesRes['status'] == 'success') {
          _allSchools = schoolsRes['schools'] ?? [];
          _cities = citiesRes['cities'] ?? [];
          _filteredSchools = List.from(_allSchools);
        } else {
          _errorMessage = schoolsRes['message'] ?? citiesRes['message'] ?? 'فشل تحميل البيانات';
        }
      });
    }
  }

  void _filterSchools() {
    final query = _searchController.text.trim().toLowerCase();
    setState(() {
      if (query.isEmpty) {
        _filteredSchools = List.from(_allSchools);
      } else {
        _filteredSchools = _allSchools.where((school) {
          final name = (school['name'] ?? '').toString().toLowerCase();
          final city = (school['city_name'] ?? '').toString().toLowerCase();
          final manager = (school['manager'] ?? '').toString().toLowerCase();
          return name.contains(query) || city.contains(query) || manager.contains(query);
        }).toList();
      }
    });
  }

  void _showSchoolDialog({Map<String, dynamic>? school}) {
    final isEdit = school != null;
    final nameController = TextEditingController(text: isEdit ? school['name'] : '');
    final addressController = TextEditingController(text: isEdit ? school['address'] : '');
    final emailController = TextEditingController(text: isEdit ? school['email'] : '');
    final yearController = TextEditingController(text: isEdit ? school['year'] : '2025-2026');
    final managerController = TextEditingController(text: isEdit ? school['manager'] : '');
    final examsManagerController = TextEditingController(text: isEdit ? school['exams_manager'] : '');
    
    // Parse selected city ID
    int? selectedCityId;
    if (isEdit && school['city'] != null) {
      selectedCityId = int.tryParse(school['city'].toString());
    } else if (_cities.isNotEmpty) {
      selectedCityId = int.tryParse(_cities.first['ID'].toString());
    }

    bool isSaving = false;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setDialogState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
              title: Text(
                isEdit ? 'تعديل بيانات المدرسة' : 'إضافة مدرسة جديدة',
                style: GoogleFonts.cairo(fontWeight: FontWeight.bold, color: const Color(0xff192A56)),
                textAlign: TextAlign.center,
              ),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    TextField(
                      controller: nameController,
                      decoration: _buildInputDecoration('اسم المدرسة', 'مثال: مدرسة الأمل النموذجية'),
                      style: GoogleFonts.cairo(),
                    ),
                    const SizedBox(height: 12),
                    
                    // City Dropdown
                    DropdownButtonFormField<int>(
                      value: selectedCityId,
                      decoration: _buildInputDecoration('المدينة / المنطقة', ''),
                      items: _cities.map<DropdownMenuItem<int>>((city) {
                        return DropdownMenuItem<int>(
                          value: int.parse(city['ID'].toString()),
                          child: Text(city['name'] ?? '', style: GoogleFonts.cairo()),
                        );
                      }).toList(),
                      onChanged: (val) {
                        setDialogState(() {
                          selectedCityId = val;
                        });
                      },
                    ),
                    const SizedBox(height: 12),

                    TextField(
                      controller: addressController,
                      decoration: _buildInputDecoration('العنوان والتفاصيل', 'مثال: حي الأندلس، بجانب المسجد الكبير'),
                      style: GoogleFonts.cairo(),
                    ),
                    const SizedBox(height: 12),

                    TextField(
                      controller: emailController,
                      keyboardType: TextInputType.emailAddress,
                      decoration: _buildInputDecoration('البريد الإلكتروني', 'مثال: school@sela.ly'),
                      style: GoogleFonts.cairo(),
                    ),
                    const SizedBox(height: 12),

                    TextField(
                      controller: yearController,
                      decoration: _buildInputDecoration('العام الدراسي الحالي', 'مثال: 2025-2026'),
                      style: GoogleFonts.cairo(),
                    ),
                    const SizedBox(height: 12),

                    TextField(
                      controller: managerController,
                      decoration: _buildInputDecoration('اسم مدير المدرسة', 'مثال: أ. محمد عبد الله'),
                      style: GoogleFonts.cairo(),
                    ),
                    const SizedBox(height: 12),

                    TextField(
                      controller: examsManagerController,
                      decoration: _buildInputDecoration('اسم مسؤول الامتحانات', 'مثال: أ. علي سالم'),
                      style: GoogleFonts.cairo(),
                    ),
                  ],
                ),
              ),
              actionsPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              actions: [
                TextButton(
                  onPressed: isSaving ? null : () => Navigator.pop(context),
                  child: Text('إلغاء', style: GoogleFonts.cairo(color: Colors.grey)),
                ),
                ElevatedButton(
                  onPressed: isSaving
                      ? null
                      : () async {
                          final name = nameController.text.trim();
                          final address = addressController.text.trim();
                          final email = emailController.text.trim();
                          final year = yearController.text.trim();
                          final manager = managerController.text.trim();
                          final examsManager = examsManagerController.text.trim();

                          if (name.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('يرجى إدخال اسم المدرسة', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (name.length < 3) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('اسم المدرسة قصير جداً (3 حروف على الأقل)', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (selectedCityId == null) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('يرجى اختيار المدينة أو المنطقة', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (email.isNotEmpty) {
                            final emailRegex = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$');
                            if (!emailRegex.hasMatch(email)) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(content: Text('يرجى إدخال بريد إلكتروني صحيح', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                              );
                              return;
                            }
                          }
                          if (year.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('يرجى تحديد العام الدراسي (مثال: 2025-2026)', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (manager.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('يرجى إدخال اسم مدير المدرسة', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (examsManager.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('يرجى إدخال اسم مسؤول الامتحانات', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }

                          setDialogState(() {
                            isSaving = true;
                          });

                          final result = isEdit
                              ? await ApiService.updateSchool(
                                  id: int.parse(school['ID'].toString()),
                                  name: name,
                                  city: selectedCityId,
                                  address: addressController.text.trim(),
                                  email: emailController.text.trim(),
                                  year: yearController.text.trim(),
                                  manager: managerController.text.trim(),
                                  examsManager: examsManagerController.text.trim(),
                                )
                              : await ApiService.createSchool(
                                  name: name,
                                  city: selectedCityId,
                                  address: addressController.text.trim(),
                                  email: emailController.text.trim(),
                                  year: yearController.text.trim(),
                                  manager: managerController.text.trim(),
                                  examsManager: examsManagerController.text.trim(),
                                );

                          if (mounted) {
                            Navigator.pop(context);
                            if (result['status'] == 'success') {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                  content: Text(result['message'] ?? 'تم حفظ التغييرات بنجاح', style: GoogleFonts.cairo()),
                                  backgroundColor: Colors.green,
                                ),
                              );
                              _fetchData();
                            } else {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                  content: Text(result['message'] ?? 'حدث خطأ ما', style: GoogleFonts.cairo()),
                                  backgroundColor: Colors.redAccent,
                                ),
                              );
                            }
                          }
                        },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xff192A56),
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  ),
                  child: isSaving
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                      : Text('حفظ', style: GoogleFonts.cairo(fontWeight: FontWeight.bold)),
                ),
              ],
            );
          },
        );
      },
    );
  }

  void _handleDeleteSchool(Map<String, dynamic> school) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('حذف المدرسة', style: GoogleFonts.cairo(fontWeight: FontWeight.bold, color: Colors.redAccent)),
        content: Text(
          'هل أنت متأكد من رغبتك في حذف مدرسة "${school['name']}"؟ سيؤدي هذا إلى مسح كل السجلات المرتبطة بها في حال عدم وجود قيود.',
          style: GoogleFonts.cairo(),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: Text('إلغاء', style: GoogleFonts.cairo(color: Colors.grey)),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: Text('حذف', style: GoogleFonts.cairo(color: Colors.redAccent, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );

    if (confirm == true) {
      setState(() {
        _isLoading = true;
      });

      final result = await ApiService.deleteSchool(int.parse(school['ID'].toString()));

      if (mounted) {
        if (result['status'] == 'success') {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(result['message'] ?? 'تم حذف المدرسة بنجاح', style: GoogleFonts.cairo()), backgroundColor: Colors.green),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'فشل حذف المدرسة لوجود معلمين أو طلاب مسجلين بها', style: GoogleFonts.cairo()),
              backgroundColor: Colors.redAccent,
            ),
          );
        }
        _fetchData();
      }
    }
  }

  InputDecoration _buildInputDecoration(String label, String hint) {
    return InputDecoration(
      labelText: label,
      labelStyle: GoogleFonts.cairo(fontSize: 13, color: Colors.grey.shade600, fontWeight: FontWeight.bold),
      hintText: hint.isNotEmpty ? hint : null,
      hintStyle: GoogleFonts.cairo(fontSize: 12, color: Colors.grey.shade400),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16),
        borderSide: BorderSide(color: Colors.grey.shade300),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16),
        borderSide: const BorderSide(color: Color(0xffC5A021), width: 1.5),
      ),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          'إدارة المدارس والمنشآت',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.white),
        elevation: 0,
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _cities.isEmpty
            ? () {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text('يرجى إضافة مدينة أولاً قبل إضافة المدارس', style: GoogleFonts.cairo())),
                );
              }
            : () => _showSchoolDialog(),
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
        elevation: 4,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        icon: const Icon(Icons.add_home_work_rounded, size: 20),
        label: Text('إضافة مدرسة', style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 13)),
      ),
      body: Column(
        children: [
          // Premium Search Bar
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.grey.shade100,
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: TextField(
                controller: _searchController,
                decoration: InputDecoration(
                  hintText: 'ابحث عن مدرسة، مدينة أو مدير...',
                  hintStyle: GoogleFonts.cairo(fontSize: 13, color: Colors.grey.shade400),
                  prefixIcon: const Icon(Icons.search_rounded, color: Color(0xff192A56)),
                  suffixIcon: _searchController.text.isNotEmpty
                      ? IconButton(
                          icon: const Icon(Icons.clear_rounded, color: Colors.grey),
                          onPressed: () => _searchController.clear(),
                        )
                      : null,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(20),
                    borderSide: BorderSide(color: Colors.grey.shade200, width: 1.2),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(20),
                    borderSide: BorderSide(color: Colors.grey.shade200, width: 1.2),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(20),
                    borderSide: const BorderSide(color: Color(0xffC5A021), width: 1.5),
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
                style: GoogleFonts.cairo(fontSize: 14),
              ),
            ),
          ),

          // Main body with dynamic GridView
          Expanded(
            child: RefreshIndicator(
              onRefresh: _fetchData,
              color: const Color(0xff192A56),
              child: _isLoading
                  ? const Center(child: CircularProgressIndicator(color: Color(0xff192A56)))
                  : _errorMessage != null
                      ? Center(
                          child: Padding(
                            padding: const EdgeInsets.all(24),
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(Icons.error_outline_rounded, size: 64, color: Colors.amber.shade700),
                                const SizedBox(height: 16),
                                Text(
                                  _errorMessage!,
                                  style: GoogleFonts.cairo(fontSize: 14, color: Colors.grey.shade700),
                                  textAlign: TextAlign.center,
                                ),
                                const SizedBox(height: 20),
                                ElevatedButton.icon(
                                  onPressed: _fetchData,
                                  icon: const Icon(Icons.refresh_rounded),
                                  label: Text('إعادة المحاولة', style: GoogleFonts.cairo(fontWeight: FontWeight.bold)),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xff192A56),
                                    foregroundColor: Colors.white,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        )
                      : _filteredSchools.isEmpty
                          ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    _searchController.text.isNotEmpty
                                        ? Icons.search_off_rounded
                                        : Icons.domain_disabled_rounded,
                                    size: 80,
                                    color: Colors.grey.shade300,
                                  ),
                                  const SizedBox(height: 16),
                                  Text(
                                    _searchController.text.isNotEmpty
                                        ? 'لم نجد أي مدرسة تطابق بحثك.'
                                        : 'لا توجد مدارس مسجلة بالنظام حالياً.',
                                    style: GoogleFonts.cairo(fontSize: 15, color: Colors.grey.shade600),
                                  ),
                                ],
                              ),
                            )
                          : GridView.builder(
                              padding: const EdgeInsets.fromLTRB(16, 8, 16, 80),
                              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                                crossAxisCount: 2,
                                mainAxisSpacing: 14,
                                crossAxisSpacing: 14,
                                childAspectRatio: 0.72,
                              ),
                              itemCount: _filteredSchools.length,
                              itemBuilder: (context, index) {
                                final school = _filteredSchools[index];
                                final cityName = school['city_name'] ?? 'غير محدد';
                                final manager = school['manager'] ?? 'غير محدد';
                                final year = school['year'] ?? '2025-2026';

                                return Card(
                                  color: Colors.white,
                                  elevation: 0,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(24),
                                    side: BorderSide(color: Colors.grey.shade200, width: 1.2),
                                  ),
                                  child: Padding(
                                    padding: const EdgeInsets.all(14),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.stretch,
                                      children: [
                                        // Header row with apartment icon
                                        Align(
                                          alignment: Alignment.topRight,
                                          child: Container(
                                            padding: const EdgeInsets.all(8),
                                            decoration: BoxDecoration(
                                              color: const Color(0xff192A56).withOpacity(0.08),
                                              shape: BoxShape.circle,
                                            ),
                                            child: const Icon(Icons.apartment_rounded, color: Color(0xff192A56), size: 18),
                                          ),
                                        ),
                                        const Spacer(),
                                        // School Name
                                        Text(
                                          school['name'] ?? '',
                                          style: GoogleFonts.cairo(
                                            fontSize: 14,
                                            fontWeight: FontWeight.bold,
                                            color: const Color(0xff192A56),
                                          ),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        const SizedBox(height: 4),
                                        // City Location Pill
                                        Row(
                                          children: [
                                            const Icon(Icons.location_on_rounded, size: 12, color: Color(0xffC5A021)),
                                            const SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                cityName,
                                                style: GoogleFonts.cairo(fontSize: 11, color: Colors.grey.shade600, fontWeight: FontWeight.bold),
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 2),
                                        // Manager Name
                                        Row(
                                          children: [
                                            const Icon(Icons.person_rounded, size: 12, color: Colors.grey),
                                            const SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                manager,
                                                style: GoogleFonts.cairo(fontSize: 11, color: Colors.grey.shade600),
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ),
                                          ],
                                        ),
                                        const Spacer(),
                                        const Divider(height: 10, thickness: 0.8),
                                        // Card Actions
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.end,
                                          children: [
                                            Expanded(
                                              child: TextButton.icon(
                                                style: TextButton.styleFrom(
                                                  padding: EdgeInsets.zero,
                                                  foregroundColor: Colors.blueAccent,
                                                ),
                                                onPressed: () => _showSchoolDialog(school: school),
                                                icon: const Icon(Icons.edit_outlined, size: 14),
                                                label: Text('تعديل', style: GoogleFonts.cairo(fontSize: 10.5, fontWeight: FontWeight.bold)),
                                              ),
                                            ),
                                            const SizedBox(width: 4),
                                            IconButton(
                                              icon: const Icon(Icons.delete_outline_rounded, color: Colors.redAccent, size: 18),
                                              onPressed: () => _handleDeleteSchool(school),
                                              constraints: const BoxConstraints(),
                                              padding: EdgeInsets.zero,
                                              tooltip: 'حذف',
                                            ),
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                );
                              },
                            ),
            ),
          ),
        ],
      ),
    );
  }
}
