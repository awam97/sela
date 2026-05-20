import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class ManageSettingsScreen extends StatefulWidget {
  const ManageSettingsScreen({Key? key}) : super(key: key);

  @override
  State<ManageSettingsScreen> createState() => _ManageSettingsScreenState();
}

class _ManageSettingsScreenState extends State<ManageSettingsScreen> {
  bool _isLoading = true;
  bool _isSaving = false;
  List<dynamic> _rawSettings = [];
  final Map<String, TextEditingController> _controllers = {};
  final Map<String, bool> _switches = {};
  String? _errorMessage;

  // Curate human-readable names & descriptions for settings keys in Sela
  final Map<String, Map<String, String>> _keysMeta = {
    'system_name': {
      'title': 'اسم المنظومة / النظام',
      'desc': 'الاسم الرسمي الذي يظهر في أعلى صفحات المنصة والتقارير.',
      'type': 'text'
    },
    'whatsapp_otp_enabled': {
      'title': 'تفعيل التحقق الثنائي عبر الواتساب (OTP)',
      'desc': 'فرض التحقق برمز سري يُرسل تلقائياً لواتساب المستخدم قبل الدخول.',
      'type': 'switch'
    },
    'active_academic_year': {
      'title': 'العام الدراسي الافتراضي النشط',
      'desc': 'العام الدراسي الحالي المستخدم افتراضياً في المعاملات والدرجات.',
      'type': 'text'
    },
    'system_email': {
      'title': 'البريد الإلكتروني للنظام',
      'desc': 'البريد المستخدم لإرسال التنبيهات والإشعارات الرسمية للمشتركين.',
      'type': 'text'
    },
    'maintenance_mode': {
      'title': 'وضع الصيانة العام',
      'desc': 'عند التفعيل، يتم حظر دخول المدارس والطلاب مؤقتاً لأعمال الصيانة.',
      'type': 'switch'
    },
  };

  @override
  void initState() {
    super.initState();
    _fetchSettings();
  }

  Future<void> _fetchSettings() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getSettings();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result['status'] == 'success') {
          _rawSettings = result['settings'] ?? [];
          _initializeForm();
        } else {
          _errorMessage = result['message'];
        }
      });
    }
  }

  void _initializeForm() {
    _controllers.clear();
    _switches.clear();

    for (var setting in _rawSettings) {
      final key = setting['type'] ?? '';
      final val = setting['description'] ?? '';

      // Determine type based on keysMeta or default to text
      final meta = _keysMeta[key] ?? {'type': 'text'};
      if (meta['type'] == 'switch') {
        _switches[key] = (val.toLowerCase() == 'true' || val == '1' || val == 'on' || val.toLowerCase() == 'yes');
      } else {
        _controllers[key] = TextEditingController(text: val);
      }
    }

    // Add missing standard Sela settings to ensure they exist in UI
    _keysMeta.forEach((key, meta) {
      if (!_controllers.containsKey(key) && !_switches.containsKey(key)) {
        if (meta['type'] == 'switch') {
          _switches[key] = false;
        } else {
          _controllers[key] = TextEditingController(text: '');
        }
      }
    });
  }

  Future<void> _saveSettings() async {
    setState(() {
      _isSaving = true;
    });

    final Map<String, String> payload = {};

    // Collect text controllers
    _controllers.forEach((key, controller) {
      payload[key] = controller.text.trim();
    });

    // Collect switches
    _switches.forEach((key, val) {
      payload[key] = val ? 'true' : 'false';
    });

    final result = await ApiService.updateSettings(payload);

    if (mounted) {
      setState(() {
        _isSaving = false;
      });

      if (result['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'تم حفظ إعدادات النظام بنجاح', style: GoogleFonts.cairo()),
            backgroundColor: Colors.green,
          ),
        );
        _fetchSettings();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'فشل حفظ الإعدادات', style: GoogleFonts.cairo()),
            backgroundColor: Colors.redAccent,
          ),
        );
      }
    }
  }

  @override
  void dispose() {
    _controllers.forEach((key, controller) => controller.dispose());
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          'إعدادات النظام العامة',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
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
                          onPressed: _fetchSettings,
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
              : Column(
                  children: [
                    Expanded(
                      child: ListView(
                        padding: const EdgeInsets.all(16),
                        children: [
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: const Color(0xffC5A021).withOpacity(0.08),
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(color: const Color(0xffC5A021).withOpacity(0.2), width: 1),
                            ),
                            child: Row(
                              children: [
                                const Icon(Icons.security_rounded, color: Color(0xffC5A021), size: 28),
                                const SizedBox(width: 14),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'لوحة التحكم الإدارية',
                                        style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 14, color: const Color(0xff192A56)),
                                      ),
                                      Text(
                                        'تغيير هذه الإعدادات يؤثر مباشرة على عمل كافة التطبيقات ومواقع الويب التابعة للمنصة.',
                                        style: GoogleFonts.cairo(fontSize: 11, color: Colors.grey.shade700),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 20),

                          // Render categorized settings list
                          ..._keysMeta.entries.map((entry) {
                            final key = entry.key;
                            final meta = entry.value;
                            final title = meta['title'] ?? key;
                            final desc = meta['desc'] ?? '';
                            final type = meta['type'] ?? 'text';

                            return Card(
                              margin: const EdgeInsets.only(bottom: 16),
                              elevation: 0,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(20),
                                side: BorderSide(color: Colors.grey.shade200, width: 1.2),
                              ),
                              child: Padding(
                                padding: const EdgeInsets.all(20),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      title,
                                      style: GoogleFonts.cairo(
                                        fontSize: 15,
                                        fontWeight: FontWeight.bold,
                                        color: const Color(0xff192A56),
                                      ),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      desc,
                                      style: GoogleFonts.cairo(fontSize: 11.5, color: Colors.grey.shade600),
                                    ),
                                    const SizedBox(height: 14),
                                    if (type == 'switch')
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(
                                            _switches[key] == true ? 'نشط ومفعل حالياً' : 'معطل ومغلق حالياً',
                                            style: GoogleFonts.cairo(
                                              fontSize: 13,
                                              fontWeight: FontWeight.bold,
                                              color: _switches[key] == true ? Colors.green : Colors.grey,
                                            ),
                                          ),
                                          Switch.adaptive(
                                            value: _switches[key] ?? false,
                                            activeColor: const Color(0xff192A56),
                                            onChanged: (val) {
                                              setState(() {
                                                _switches[key] = val;
                                              });
                                            },
                                          ),
                                        ],
                                      )
                                    else
                                      TextField(
                                        controller: _controllers[key],
                                        decoration: InputDecoration(
                                          border: OutlineInputBorder(
                                            borderRadius: BorderRadius.circular(14),
                                            borderSide: BorderSide(color: Colors.grey.shade300),
                                          ),
                                          focusedBorder: OutlineInputBorder(
                                            borderRadius: BorderRadius.circular(14),
                                            borderSide: const BorderSide(color: Color(0xffC5A021), width: 1.5),
                                          ),
                                          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                                        ),
                                        style: GoogleFonts.cairo(),
                                      ),
                                  ],
                                ),
                              ),
                            );
                          }).toList(),
                        ],
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: SizedBox(
                        width: double.infinity,
                        height: 52,
                        child: ElevatedButton(
                          onPressed: _isSaving ? null : _saveSettings,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xff192A56),
                            foregroundColor: Colors.white,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            elevation: 2,
                          ),
                          child: _isSaving
                              ? const CircularProgressIndicator(color: Colors.white)
                              : Text(
                                  'حفظ جميع التعديلات',
                                  style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 16),
                                ),
                        ),
                      ),
                    ),
                  ],
                ),
    );
  }
}
