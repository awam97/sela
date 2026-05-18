import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';
import 'dashboard_screen.dart';

enum LoginStep { credentials, selectMethod, enterOtp }

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _usernameController = TextEditingController();
  final _passwordController = TextEditingController();
  final _otpController = TextEditingController();

  LoginStep _currentStep = LoginStep.credentials;
  bool _isLoading = false;
  bool _obscurePassword = true;
  String? _errorMessage;

  // Stateless OTP validation variables
  String? _tempToken;
  String? _otpToken;
  String _maskedPhone = '';
  String _maskedEmail = '';
  bool _waEnabled = false;
  String _selectedMethod = 'whatsapp'; // 'whatsapp' or 'email'

  @override
  void dispose() {
    _usernameController.dispose();
    _passwordController.dispose();
    _otpController.dispose();
    super.dispose();
  }

  void _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.login(
      _usernameController.text.trim(),
      _passwordController.text,
    );

    if (mounted) {
      setState(() {
        _isLoading = false;
      });

      if (result['status'] == 'success') {
        // Direct successful login (OTP disabled/bypassed)
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (context) => const DashboardScreen()),
        );
      } else if (result['status'] == 'otp_required') {
        // OTP required sequence
        setState(() {
          _tempToken = result['temp_token'];
          _maskedPhone = result['phone'] ?? '';
          _maskedEmail = result['email'] ?? '';
          _waEnabled = result['wa_enabled'] ?? false;
          _currentStep = LoginStep.selectMethod;
        });
      } else {
        setState(() {
          _errorMessage = result['message'];
        });
      }
    }
  }

  void _handleSendOtp() async {
    if (_tempToken == null) return;

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.sendOtp(_tempToken!, _selectedMethod);

    if (mounted) {
      setState(() {
        _isLoading = false;
      });

      if (result['status'] == 'success') {
        setState(() {
          _otpToken = result['otp_token'];
          _otpController.clear();
          _currentStep = LoginStep.enterOtp;
        });
      } else {
        setState(() {
          _errorMessage = result['message'];
        });
      }
    }
  }

  void _handleVerifyOtp() async {
    if (_otpToken == null) return;
    final code = _otpController.text.trim();

    if (code.length != 6) {
      setState(() {
        _errorMessage = 'يرجى إدخال رمز مكون من 6 أرقام';
      });
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.verifyOtp(_otpToken!, code);

    if (mounted) {
      setState(() {
        _isLoading = false;
      });

      if (result['status'] == 'success') {
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (context) => const DashboardScreen()),
        );
      } else {
        setState(() {
          _errorMessage = result['message'];
        });
      }
    }
  }

  Widget _buildErrorBanner() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
      decoration: BoxDecoration(
        color: Colors.redAccent.withOpacity(0.15),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.redAccent.withOpacity(0.5)),
      ),
      child: Row(
        children: [
          const Icon(Icons.error_outline_rounded, color: Colors.redAccent),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              _errorMessage!,
              style: GoogleFonts.cairo(
                color: Colors.redAccent,
                fontSize: 13,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCredentialsStep(Size size) {
    return Form(
      key: _formKey,
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          const Spacer(flex: 2),
          
          // Sela Gold Geometric Logo Header
          Center(
            child: Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: const Color(0xffc5a021), width: 3),
                color: const Color(0xff192a56).withOpacity(0.4),
              ),
              child: const Icon(
                Icons.school_rounded,
                size: 72,
                color: Color(0xffc5a021), // Sela Gold
              ),
            ),
          ),
          const SizedBox(height: 24),
          
          // App title
          Text(
            'مَـنْـصَـة صِـلَـة',
            style: GoogleFonts.cairo(
              fontSize: 28,
              fontWeight: FontWeight.w900,
              color: const Color(0xffc5a021),
              letterSpacing: 1.5,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 8),
          Text(
            'نظام إدارة المدارس والجمعيات الذكي',
            style: GoogleFonts.cairo(
              fontSize: 14,
              color: Colors.white70,
              fontWeight: FontWeight.w500,
            ),
            textAlign: TextAlign.center,
          ),
          
          const Spacer(flex: 2),
          
          // Error Message Banner
          if (_errorMessage != null) ...[
            _buildErrorBanner(),
            const SizedBox(height: 20),
          ],

          // Input Form Fields (RTL)
          Card(
            elevation: 8,
            color: Colors.white.withOpacity(0.06),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(24),
              side: BorderSide(
                color: Colors.white.withOpacity(0.1),
                width: 1,
              ),
            ),
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  // Username field
                  TextFormField(
                    controller: _usernameController,
                    style: const TextStyle(color: Colors.white),
                    keyboardType: TextInputType.text,
                    textDirection: TextDirection.rtl,
                    decoration: InputDecoration(
                      labelText: 'اسم المستخدم',
                      labelStyle: GoogleFonts.cairo(color: Colors.white70, fontSize: 13),
                      prefixIcon: const Icon(Icons.person_outline_rounded, color: Color(0xffc5a021)),
                      enabledBorder: UnderlineInputBorder(
                        borderSide: BorderSide(color: Colors.white.withOpacity(0.3)),
                      ),
                      focusedBorder: const UnderlineInputBorder(
                        borderSide: BorderSide(color: Color(0xffc5a021), width: 2),
                      ),
                    ),
                    validator: (value) {
                      if (value == null || value.trim().isEmpty) {
                        return 'يرجى إدخال اسم المستخدم';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 20),
                  
                  // Password field
                  TextFormField(
                    controller: _passwordController,
                    obscureText: _obscurePassword,
                    style: const TextStyle(color: Colors.white),
                    textDirection: TextDirection.rtl,
                    decoration: InputDecoration(
                      labelText: 'كلمة المرور',
                      labelStyle: GoogleFonts.cairo(color: Colors.white70, fontSize: 13),
                      prefixIcon: const Icon(Icons.lock_outline_rounded, color: Color(0xffc5a021)),
                      suffixIcon: IconButton(
                        icon: Icon(
                          _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                          color: Colors.white60,
                        ),
                        onPressed: () {
                          setState(() {
                            _obscurePassword = !_obscurePassword;
                          });
                        },
                      ),
                      enabledBorder: UnderlineInputBorder(
                        borderSide: BorderSide(color: Colors.white.withOpacity(0.3)),
                      ),
                      focusedBorder: const UnderlineInputBorder(
                        borderSide: BorderSide(color: Color(0xffc5a021), width: 2),
                      ),
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'يرجى إدخال كلمة المرور';
                      }
                      return null;
                    },
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 32),
          
          // Native login trigger button
          ElevatedButton(
            onPressed: _isLoading ? null : _handleLogin,
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xffc5a021), // Sela Gold
              foregroundColor: const Color(0xff192a56), // Navy
              elevation: 4,
              shadowColor: const Color(0xffc5a021).withOpacity(0.5),
              padding: const EdgeInsets.symmetric(vertical: 16),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
              ),
            ),
            child: _isLoading
                ? const SizedBox(
                    height: 24,
                    width: 24,
                    child: CircularProgressIndicator(
                      strokeWidth: 3,
                      color: Color(0xff192a56),
                    ),
                  )
                : Text(
                    'تسجيل الدخول',
                    style: GoogleFonts.cairo(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
          ),
          
          const Spacer(flex: 3),
          
          // Copywrite metadata
          Text(
            'جميع الحقوق محفوظة © ${DateTime.now().year} منصة صلة',
            style: GoogleFonts.cairo(
              color: Colors.white38,
              fontSize: 11,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 12),
        ],
      ),
    );
  }

  Widget _buildSelectMethodStep() {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        const SizedBox(height: 40),
        // Security shield header
        Center(
          child: Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: const Color(0xffc5a021), width: 3),
              color: const Color(0xff192a56).withOpacity(0.4),
            ),
            child: const Icon(
              Icons.shield_outlined,
              size: 72,
              color: Color(0xffc5a021),
            ),
          ),
        ),
        const SizedBox(height: 24),
        
        Text(
          'التحقق من الهوية',
          style: GoogleFonts.cairo(
            fontSize: 24,
            fontWeight: FontWeight.w900,
            color: const Color(0xffc5a021),
          ),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: 8),
        Text(
          'لحماية حسابك، يرجى اختيار وسيلة استلام رمز التحقق (OTP):',
          style: GoogleFonts.cairo(
            fontSize: 13,
            color: Colors.white70,
          ),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: 24),

        // Error message banner
        if (_errorMessage != null) ...[
          _buildErrorBanner(),
          const SizedBox(height: 20),
        ],

        // Channel Select Cards
        Card(
          color: Colors.white.withOpacity(0.06),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(24),
            side: BorderSide(color: Colors.white.withOpacity(0.1)),
          ),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                // WhatsApp Option Card
                GestureDetector(
                  onTap: () {
                    setState(() {
                      _selectedMethod = 'whatsapp';
                    });
                  },
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 250),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: _selectedMethod == 'whatsapp' 
                          ? const Color(0xff192a56).withOpacity(0.8)
                          : Colors.transparent,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: _selectedMethod == 'whatsapp' 
                            ? const Color(0xffc5a021) 
                            : Colors.white12,
                        width: 2,
                      ),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                color: Colors.green.shade900.withOpacity(0.2),
                              ),
                              child: const Icon(
                                Icons.chat_rounded,
                                color: Colors.greenAccent,
                                size: 24,
                              ),
                            ),
                            const SizedBox(width: 12),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'واتساب (WhatsApp)',
                                  style: GoogleFonts.cairo(
                                    color: Colors.white,
                                    fontSize: 13,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  _maskedPhone,
                                  style: const TextStyle(
                                    color: Colors.white70,
                                    fontSize: 12,
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                        if (_selectedMethod == 'whatsapp')
                          const Icon(
                            Icons.check_circle_rounded,
                            color: Color(0xffc5a021),
                            size: 20,
                          ),
                      ],
                    ),
                  ),
                ),
                
                if (_maskedEmail.isNotEmpty) ...[
                  const SizedBox(height: 16),
                  // Email Option Card
                  GestureDetector(
                    onTap: () {
                      setState(() {
                        _selectedMethod = 'email';
                      });
                    },
                    child: AnimatedContainer(
                      duration: const Duration(milliseconds: 250),
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: _selectedMethod == 'email' 
                            ? const Color(0xff192a56).withOpacity(0.8)
                            : Colors.transparent,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(
                          color: _selectedMethod == 'email' 
                              ? const Color(0xffc5a021) 
                              : Colors.white12,
                          width: 2,
                        ),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: Colors.blue.shade900.withOpacity(0.2),
                                ),
                                child: const Icon(
                                  Icons.alternate_email_rounded,
                                  color: Colors.blueAccent,
                                  size: 24,
                                ),
                              ),
                              const SizedBox(width: 12),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'البريد الإلكتروني',
                                    style: GoogleFonts.cairo(
                                      color: Colors.white,
                                      fontSize: 13,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    _maskedEmail,
                                    style: const TextStyle(
                                      color: Colors.white70,
                                      fontSize: 12,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                          if (_selectedMethod == 'email')
                            const Icon(
                              Icons.check_circle_rounded,
                              color: Color(0xffc5a021),
                              size: 20,
                            ),
                        ],
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ),
        ),
        const SizedBox(height: 32),

        // Action Trigger Button
        ElevatedButton(
          onPressed: _isLoading ? null : _handleSendOtp,
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xffc5a021),
            foregroundColor: const Color(0xff192a56),
            elevation: 4,
            shadowColor: const Color(0xffc5a021).withOpacity(0.5),
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(20),
            ),
          ),
          child: _isLoading
              ? const SizedBox(
                  height: 24,
                  width: 24,
                  child: CircularProgressIndicator(
                    strokeWidth: 3,
                    color: Color(0xff192a56),
                  ),
                )
              : Text(
                  'إرسال رمز التحقق',
                  style: GoogleFonts.cairo(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
        ),
        const SizedBox(height: 16),

        // Cancel and Return
        TextButton(
          onPressed: () {
            setState(() {
              _currentStep = LoginStep.credentials;
              _errorMessage = null;
            });
          },
          child: Text(
            'تراجع لتسجيل الدخول',
            style: GoogleFonts.cairo(
              color: Colors.white60,
              fontSize: 13,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildEnterOtpStep() {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        const SizedBox(height: 40),
        // Lock timer icon header
        Center(
          child: Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: const Color(0xffc5a021), width: 3),
              color: const Color(0xff192a56).withOpacity(0.4),
            ),
            child: const Icon(
              Icons.lock_clock_rounded,
              size: 72,
              color: Color(0xffc5a021),
            ),
          ),
        ),
        const SizedBox(height: 24),
        
        Text(
          'أدخل رمز الأمان',
          style: GoogleFonts.cairo(
            fontSize: 24,
            fontWeight: FontWeight.w900,
            color: const Color(0xffc5a021),
          ),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: 8),
        Text(
          'يرجى كتابة رمز التحقق المكون من 6 أرقام المرسل إلى ${_selectedMethod == 'whatsapp' ? 'الواتساب' : 'البريد الإلكتروني'}:',
          style: GoogleFonts.cairo(
            fontSize: 13,
            color: Colors.white70,
          ),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: 24),

        // Error message banner
        if (_errorMessage != null) ...[
          _buildErrorBanner(),
          const SizedBox(height: 20),
        ],

        // Input Box
        Card(
          color: Colors.white.withOpacity(0.06),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(24),
            side: BorderSide(color: Colors.white.withOpacity(0.1)),
          ),
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 20),
            child: Column(
              children: [
                TextField(
                  controller: _otpController,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 8,
                  ),
                  keyboardType: TextInputType.number,
                  textAlign: TextAlign.center,
                  maxLength: 6,
                  decoration: InputDecoration(
                    counterText: '',
                    hintText: '000000',
                    hintStyle: const TextStyle(color: Colors.white24, letterSpacing: 8),
                    enabledBorder: UnderlineInputBorder(
                      borderSide: BorderSide(color: Colors.white.withOpacity(0.3)),
                    ),
                    focusedBorder: const UnderlineInputBorder(
                      borderSide: BorderSide(color: Color(0xffc5a021), width: 2.5),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 32),

        // Action Button
        ElevatedButton(
          onPressed: _isLoading ? null : _handleVerifyOtp,
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xffc5a021),
            foregroundColor: const Color(0xff192a56),
            elevation: 4,
            shadowColor: const Color(0xffc5a021).withOpacity(0.5),
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(20),
            ),
          ),
          child: _isLoading
              ? const SizedBox(
                  height: 24,
                  width: 24,
                  child: CircularProgressIndicator(
                    strokeWidth: 3,
                    color: Color(0xff192a56),
                  ),
                )
              : Text(
                  'تحقق ودخول',
                  style: GoogleFonts.cairo(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
        ),
        const SizedBox(height: 24),

        // Action controls
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            TextButton(
              onPressed: _isLoading ? null : _handleSendOtp,
              child: Text(
                'إعادة إرسال الرمز',
                style: GoogleFonts.cairo(
                  color: const Color(0xffc5a021),
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
            TextButton(
              onPressed: () {
                setState(() {
                  _currentStep = LoginStep.selectMethod;
                  _errorMessage = null;
                });
              },
              child: Text(
                'تغيير وسيلة الاستلام',
                style: GoogleFonts.cairo(
                  color: Colors.white60,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ],
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;

    return Scaffold(
      body: Container(
        width: double.infinity,
        height: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Color(0xff0f172a), // Slate Dark
              Color(0xff192a56), // Sela Navy
            ],
          ),
        ),
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 28),
            child: SizedBox(
              height: size.height - MediaQuery.of(context).padding.top - MediaQuery.of(context).padding.bottom,
              child: AnimatedSwitcher(
                duration: const Duration(milliseconds: 350),
                transitionBuilder: (Widget child, Animation<double> animation) {
                  return FadeTransition(
                    opacity: animation,
                    child: SlideTransition(
                      position: Tween<Offset>(
                        begin: const Offset(0.0, 0.05),
                        end: Offset.zero,
                      ).animate(animation),
                      child: child,
                    ),
                  );
                },
                child: _currentStep == LoginStep.credentials
                    ? _buildCredentialsStep(size)
                    : _currentStep == LoginStep.selectMethod
                        ? _buildSelectMethodStep()
                        : _buildEnterOtpStep(),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
