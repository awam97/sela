import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class ManageAdminsScreen extends StatefulWidget {
  const ManageAdminsScreen({Key? key}) : super(key: key);

  @override
  State<ManageAdminsScreen> createState() => _ManageAdminsScreenState();
}

class _ManageAdminsScreenState extends State<ManageAdminsScreen> {
  bool _isLoading = true;
  List<dynamic> _allAdmins = [];
  List<dynamic> _filteredAdmins = [];
  List<dynamic> _schools = [];
  String? _errorMessage;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _fetchData();
    _searchController.addListener(_filterAdmins);
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

    final adminsRes = await ApiService.getAdmins();
    final schoolsRes = await ApiService.getSchools();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (adminsRes['status'] == 'success' && schoolsRes['status'] == 'success') {
          _allAdmins = adminsRes['admins'] ?? [];
          _schools = schoolsRes['schools'] ?? [];
          _filteredAdmins = List.from(_allAdmins);
        } else {
          _errorMessage = adminsRes['message'] ?? schoolsRes['message'] ?? 'فشل تحميل البيانات';
        }
      });
    }
  }

  void _filterAdmins() {
    final query = _searchController.text.trim().toLowerCase();
    setState(() {
      if (query.isEmpty) {
        _filteredAdmins = List.from(_allAdmins);
      } else {
        _filteredAdmins = _allAdmins.where((admin) {
          final name = (admin['name'] ?? '').toString().toLowerCase();
          final username = (admin['username'] ?? '').toString().toLowerCase();
          final school = (admin['school_name'] ?? '').toString().toLowerCase();
          return name.contains(query) || username.contains(query) || school.contains(query);
        }).toList();
      }
    });
  }

  void _showAdminDialog({Map<String, dynamic>? admin}) {
    final isEdit = admin != null;
    final nameController = TextEditingController(text: isEdit ? admin['name'] : '');
    final usernameController = TextEditingController(text: isEdit ? admin['username'] : '');
    final passwordController = TextEditingController();
    final phoneController = TextEditingController(text: isEdit ? admin['phone'] : '');
    final emailController = TextEditingController(text: isEdit ? admin['email'] : '');

    // Parse selected school ID
    int? selectedSchoolId;
    if (isEdit && admin['school'] != null) {
      selectedSchoolId = int.tryParse(admin['school'].toString());
    } else if (_schools.isNotEmpty) {
      selectedSchoolId = int.tryParse(_schools.first['ID'].toString());
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
                isEdit ? 'تعديل حساب المسؤول' : 'إنشاء حساب مسؤول جديد',
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
                      decoration: _buildInputDecoration('الاسم الكامل للمسؤول', 'مثال: أ. محمد عبد الله'),
                      style: GoogleFonts.cairo(),
                    ),
                    const SizedBox(height: 12),

                    // School Selection Dropdown
                    DropdownButtonFormField<int>(
                      value: selectedSchoolId,
                      decoration: _buildInputDecoration('المدرسة / المنشأة التابع لها', ''),
                      items: _schools.map<DropdownMenuItem<int>>((school) {
                        return DropdownMenuItem<int>(
                          value: int.parse(school['ID'].toString()),
                          child: Text(school['name'] ?? '', style: GoogleFonts.cairo()),
                        );
                      }).toList(),
                      onChanged: (val) {
                        setDialogState(() {
                          selectedSchoolId = val;
                        });
                      },
                    ),
                    const SizedBox(height: 12),

                    TextField(
                      controller: usernameController,
                      decoration: _buildInputDecoration('اسم المستخدم (لتسجيل الدخول)', 'مثال: mohamed_admin'),
                      style: GoogleFonts.cairo(),
                    ),
                    const SizedBox(height: 12),

                    TextField(
                      controller: passwordController,
                      obscureText: true,
                      decoration: _buildInputDecoration(
                        isEdit ? 'كلمة المرور الجديدة (اختياري)' : 'كلمة المرور',
                        isEdit ? 'اتركها فارغة لعدم التعديل' : 'كلمة مرور الدخول',
                      ),
                      style: GoogleFonts.cairo(),
                    ),
                    const SizedBox(height: 12),

                    TextField(
                      controller: phoneController,
                      keyboardType: TextInputType.phone,
                      decoration: _buildInputDecoration('رقم الهاتف', 'مثال: 0912345678'),
                      style: GoogleFonts.cairo(),
                    ),
                    const SizedBox(height: 12),

                    TextField(
                      controller: emailController,
                      keyboardType: TextInputType.emailAddress,
                      decoration: _buildInputDecoration('البريد الإلكتروني', 'مثال: admin@sela.ly'),
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
                          final username = usernameController.text.trim();
                          final password = passwordController.text;
                          final phone = phoneController.text.trim();
                          final email = emailController.text.trim();

                          if (name.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('يرجى إدخال اسم المسؤول', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (name.length < 3) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('الاسم يجب أن يكون 3 حروف على الأقل', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (selectedSchoolId == null) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('يرجى تحديد المدرسة التابع لها هذا المسؤول', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (username.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('يرجى إدخال اسم المستخدم للدخول', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (username.length < 3) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('اسم المستخدم يجب أن يكون 3 رموز على الأقل', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          final usernameRegex = RegExp(r'^[a-zA-Z0-9_]+$');
                          if (!usernameRegex.hasMatch(username)) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('اسم المستخدم يجب أن يحتوي على أحرف وأرقام وشرطة سفلية فقط بدون مسافات', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }

                          if (!isEdit && password.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('يرجى إدخال كلمة المرور', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }
                          if (password.isNotEmpty && password.length < 6) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text('كلمة المرور يجب أن لا تقل عن 6 خانات', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                            );
                            return;
                          }

                          if (phone.isNotEmpty) {
                            final phoneRegex = RegExp(r'^\+?[0-9]{9,15}$');
                            if (!phoneRegex.hasMatch(phone)) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(content: Text('يرجى إدخال رقم هاتف صحيح (أرقام فقط)', style: GoogleFonts.cairo()), backgroundColor: Colors.orange.shade800),
                              );
                              return;
                            }
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

                          setDialogState(() {
                            isSaving = true;
                          });

                          final result = isEdit
                              ? await ApiService.updateAdmin(
                                  id: int.parse(admin['admin_id'].toString()),
                                  name: name,
                                  school: selectedSchoolId,
                                  username: username,
                                  password: password,
                                  phone: phoneController.text.trim(),
                                  email: emailController.text.trim(),
                                )
                              : await ApiService.createAdmin(
                                  name: name,
                                  school: selectedSchoolId,
                                  username: username,
                                  password: password,
                                  phone: phoneController.text.trim(),
                                  email: emailController.text.trim(),
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

  void _handleDeleteAdmin(Map<String, dynamic> admin) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('حذف الحساب', style: GoogleFonts.cairo(fontWeight: FontWeight.bold, color: Colors.redAccent)),
        content: Text('هل أنت متأكد من رغبتك في حذف حساب المسؤول "${admin['name']}" نهائياً من النظام؟', style: GoogleFonts.cairo()),
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

      final result = await ApiService.deleteAdmin(int.parse(admin['admin_id'].toString()));

      if (mounted) {
        if (result['status'] == 'success') {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(result['message'] ?? 'تم حذف حساب المسؤول بنجاح', style: GoogleFonts.cairo()), backgroundColor: Colors.green),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(result['message'] ?? 'فشل حذف الحساب', style: GoogleFonts.cairo()), backgroundColor: Colors.redAccent),
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
          'إدارة الحسابات والمسؤولين',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.white),
        elevation: 0,
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _schools.isEmpty
            ? () {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text('يرجى إضافة مدرسة أولاً قبل إضافة المسؤولين', style: GoogleFonts.cairo())),
                );
              }
            : () => _showAdminDialog(),
        backgroundColor: const Color(0xff16a34a),
        foregroundColor: Colors.white,
        elevation: 4,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        icon: const Icon(Icons.person_add_alt_1_rounded, size: 20),
        label: Text('إضافة مسؤول', style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 13)),
      ),
      body: Column(
        children: [
          // Sleek Premium Search Bar
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
                  hintText: 'ابحث عن مسؤول، مدرسة أو اسم مستخدم...',
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
                      : _filteredAdmins.isEmpty
                          ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    _searchController.text.isNotEmpty
                                        ? Icons.search_off_rounded
                                        : Icons.no_accounts_rounded,
                                    size: 80,
                                    color: Colors.grey.shade300,
                                  ),
                                  const SizedBox(height: 16),
                                  Text(
                                    _searchController.text.isNotEmpty
                                        ? 'لم نجد أي مسؤول يطابق بحثك.'
                                        : 'لا توجد حسابات مسؤولين مسجلة حالياً.',
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
                              itemCount: _filteredAdmins.length,
                              itemBuilder: (context, index) {
                                final admin = _filteredAdmins[index];
                                final schoolName = admin['school_name'] ?? 'غير مرتبط بمدرسة';
                                final phone = admin['phone'] ?? '';
                                final email = admin['email'] ?? '';

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
                                        // Header row with person pin icon
                                        Align(
                                          alignment: Alignment.topRight,
                                          child: Container(
                                            padding: const EdgeInsets.all(8),
                                            decoration: BoxDecoration(
                                              color: const Color(0xff16a34a).withOpacity(0.08),
                                              shape: BoxShape.circle,
                                            ),
                                            child: const Icon(Icons.person_pin_rounded, color: Color(0xff16a34a), size: 18),
                                          ),
                                        ),
                                        const Spacer(),
                                        // Admin Name
                                        Text(
                                          admin['name'] ?? '',
                                          style: GoogleFonts.cairo(
                                            fontSize: 14,
                                            fontWeight: FontWeight.bold,
                                            color: const Color(0xff192A56),
                                          ),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        const SizedBox(height: 4),
                                        // School Name Pill
                                        Row(
                                          children: [
                                            const Icon(Icons.apartment_rounded, size: 12, color: Colors.grey),
                                            const SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                schoolName,
                                                style: GoogleFonts.cairo(fontSize: 11, color: Colors.grey.shade600),
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 2),
                                        // Username
                                        Row(
                                          children: [
                                            const Icon(Icons.alternate_email_rounded, size: 12, color: Color(0xffC5A021)),
                                            const SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                admin['username'] ?? '',
                                                style: GoogleFonts.cairo(fontSize: 11, color: const Color(0xffC5A021), fontWeight: FontWeight.bold),
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
                                                onPressed: () => _showAdminDialog(admin: admin),
                                                icon: const Icon(Icons.edit_outlined, size: 14),
                                                label: Text('تعديل', style: GoogleFonts.cairo(fontSize: 10.5, fontWeight: FontWeight.bold)),
                                              ),
                                            ),
                                            const SizedBox(width: 4),
                                            IconButton(
                                              icon: const Icon(Icons.delete_outline_rounded, color: Colors.redAccent, size: 18),
                                              onPressed: () => _handleDeleteAdmin(admin),
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
