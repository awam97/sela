import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  
  bool _isLoading = true;
  bool _isSaving = false;
  String? _errorMessage;
  String? _successMessage;

  // Form Fields
  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _usernameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController();

  String _userRole = '';
  String _schoolName = '';
  int _userId = 0;

  @override
  void initState() {
    super.initState();
    _fetchProfileData();
  }

  @override
  void dispose() {
    _nameController.dispose();
    _usernameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _fetchProfileData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getProfile();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result['status'] == 'success') {
          final user = result['user'] ?? {};
          _nameController.text = user['name'] ?? '';
          _usernameController.text = user['username'] ?? '';
          _emailController.text = user['email'] ?? '';
          _phoneController.text = user['phone'] ?? '';
          _userRole = user['role'] ?? 'admin';
          _schoolName = user['school_name'] ?? '';
          _userId = int.tryParse(user['id']?.toString() ?? '0') ?? 0;
        } else {
          _errorMessage = result['message'];
        }
      });
    }
  }

  Future<void> _handleSaveProfile() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    // Password verification logic
    if (_passwordController.text.isNotEmpty) {
      if (_passwordController.text != _confirmPasswordController.text) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'كلمتا المرور غير متطابقتين',
              style: GoogleFonts.cairo(color: Colors.white),
              textAlign: TextAlign.right,
            ),
            backgroundColor: Colors.redAccent,
          ),
        );
        return;
      }
    }

    setState(() {
      _isSaving = true;
      _errorMessage = null;
      _successMessage = null;
    });

    final updateData = {
      'name': _nameController.text.trim(),
      'username': _usernameController.text.trim(),
      'email': _emailController.text.trim(),
      'phone': _phoneController.text.trim(),
    };

    if (_passwordController.text.isNotEmpty) {
      updateData['password'] = _passwordController.text;
    }

    final result = await ApiService.updateProfile(updateData);

    if (mounted) {
      setState(() {
        _isSaving = false;
        if (result['status'] == 'success') {
          _successMessage = result['message'];
          _passwordController.clear();
          _confirmPasswordController.clear();
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                _successMessage ?? 'تم تحديث الملف الشخصي بنجاح',
                style: GoogleFonts.cairo(color: Colors.white),
                textAlign: TextAlign.right,
              ),
              backgroundColor: const Color(0xff16a34a),
            ),
          );
        } else {
          _errorMessage = result['message'];
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                _errorMessage ?? 'حدث خطأ أثناء حفظ التغييرات',
                style: GoogleFonts.cairo(color: Colors.white),
                textAlign: TextAlign.right,
              ),
              backgroundColor: Colors.redAccent,
            ),
          );
        }
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = const Color(0xff192A56);
    final goldAccentColor = const Color(0xffC5A021);

    return Scaffold(
      backgroundColor: const Color(0xfff8fafc),
      appBar: AppBar(
        title: Text(
          'الملف الشخصي للمسؤول',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        elevation: 0,
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xff192A56)))
          : RefreshIndicator(
              onRefresh: _fetchProfileData,
              color: primaryColor,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(24),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Profile Header Card
                      Container(
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(color: goldAccentColor.withOpacity(0.2), width: 1.5),
                          boxShadow: [
                            BoxShadow(
                              color: primaryColor.withOpacity(0.04),
                              blurRadius: 15,
                              spreadRadius: 1,
                            ),
                          ],
                        ),
                        padding: const EdgeInsets.all(24),
                        child: Column(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(4),
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                border: Border.all(color: goldAccentColor.withOpacity(0.4), width: 2),
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(100),
                                child: Container(
                                  width: 80,
                                  height: 80,
                                  color: primaryColor.withOpacity(0.05),
                                  child: Image.network(
                                    'https://graya.ly/upload/${_userRole}_image/$_userId.jpg',
                                    fit: BoxFit.cover,
                                    errorBuilder: (ctx, err, st) {
                                      return Center(
                                        child: Text(
                                          _nameController.text.isNotEmpty ? _nameController.text.substring(0, 1) : 'م',
                                          style: GoogleFonts.cairo(
                                            fontSize: 32,
                                            fontWeight: FontWeight.w900,
                                            color: primaryColor,
                                          ),
                                        ),
                                      );
                                    },
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),
                            Text(
                              _nameController.text.isNotEmpty ? _nameController.text : 'مسؤول النظام',
                              style: GoogleFonts.cairo(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                                color: primaryColor,
                              ),
                              textAlign: TextAlign.center,
                            ),
                            const SizedBox(height: 6),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(Icons.school_rounded, color: goldAccentColor, size: 16),
                                const SizedBox(width: 6),
                                Text(
                                  _schoolName.isNotEmpty ? _schoolName : 'مدرسة السيلا الذكية',
                                  style: GoogleFonts.cairo(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: const Color(0xff64748b),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Personal Data Section Header
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 4),
                        child: Text(
                          'البيانات الشخصية',
                          style: GoogleFonts.cairo(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                            color: primaryColor,
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Profile Details Form Fields Container
                      Container(
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(color: const Color(0xffe2e8f0), width: 1.2),
                        ),
                        padding: const EdgeInsets.all(20),
                        child: Column(
                          children: [
                            _buildInputField(
                              controller: _nameController,
                              label: 'الاسم الكامل',
                              icon: Icons.person_outline_rounded,
                              validator: (val) => val == null || val.trim().isEmpty ? 'الرجاء إدخال الاسم الكامل' : null,
                            ),
                            const SizedBox(height: 16),
                            _buildInputField(
                              controller: _usernameController,
                              label: 'اسم المستخدم',
                              icon: Icons.alternate_email_rounded,
                              enabled: _userRole != 'teacher', // Teachers use email as username, read-only
                              validator: (val) => val == null || val.trim().isEmpty ? 'الرجاء إدخال اسم المستخدم' : null,
                            ),
                            const SizedBox(height: 16),
                            _buildInputField(
                              controller: _emailController,
                              label: 'البريد الإلكتروني',
                              icon: Icons.mail_outline_rounded,
                              keyboardType: TextInputType.emailAddress,
                            ),
                            const SizedBox(height: 16),
                            _buildInputField(
                              controller: _phoneController,
                              label: 'رقم الهاتف',
                              icon: Icons.phone_android_rounded,
                              keyboardType: TextInputType.phone,
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Security / Password Section
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 4),
                        child: Text(
                          'تغيير كلمة المرور (اختياري)',
                          style: GoogleFonts.cairo(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                            color: primaryColor,
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),

                      Container(
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(color: const Color(0xffe2e8f0), width: 1.2),
                        ),
                        padding: const EdgeInsets.all(20),
                        child: Column(
                          children: [
                            _buildInputField(
                              controller: _passwordController,
                              label: 'كلمة المرور الجديدة',
                              icon: Icons.lock_outline_rounded,
                              obscureText: true,
                            ),
                            const SizedBox(height: 16),
                            _buildInputField(
                              controller: _confirmPasswordController,
                              label: 'تأكيد كلمة المرور الجديدة',
                              icon: Icons.lock_reset_rounded,
                              obscureText: true,
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 32),

                      // Save Changes Button
                      SizedBox(
                        height: 54,
                        child: ElevatedButton(
                          onPressed: _isSaving ? null : _handleSaveProfile,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: primaryColor,
                            foregroundColor: Colors.white,
                            elevation: 0,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                            disabledBackgroundColor: primaryColor.withOpacity(0.6),
                          ),
                          child: _isSaving
                              ? const SizedBox(
                                  width: 24,
                                  height: 24,
                                  child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                                )
                              : Text(
                                  'حفظ التغييرات',
                                  style: GoogleFonts.cairo(fontSize: 16, fontWeight: FontWeight.bold),
                                ),
                        ),
                      ),
                      const SizedBox(height: 24),
                    ],
                  ),
                ),
              ),
            ),
    );
  }

  Widget _buildInputField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    bool obscureText = false,
    bool enabled = true,
    TextInputType keyboardType = TextInputType.text,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      obscureText: obscureText,
      enabled: enabled,
      keyboardType: keyboardType,
      validator: validator,
      textAlign: TextAlign.right,
      style: GoogleFonts.cairo(
        fontSize: 15,
        fontWeight: FontWeight.bold,
        color: enabled ? const Color(0xff192A56) : const Color(0xff94a3b8),
      ),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: GoogleFonts.cairo(
          fontSize: 13,
          fontWeight: FontWeight.bold,
          color: const Color(0xff94a3b8),
        ),
        floatingLabelBehavior: FloatingLabelBehavior.auto,
        prefixIcon: Icon(icon, color: const Color(0xff94a3b8), size: 20),
        contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
        filled: !enabled,
        fillColor: const Color(0xfff8fafc),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: Color(0xffC5A021), width: 1.5),
        ),
        disabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: Colors.redAccent, width: 1.2),
        ),
        focusedErrorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: Colors.redAccent, width: 1.5),
        ),
        errorStyle: GoogleFonts.cairo(fontSize: 11, color: Colors.redAccent),
      ),
    );
  }
}
