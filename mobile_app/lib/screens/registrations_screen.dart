import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class RegistrationsScreen extends StatefulWidget {
  const RegistrationsScreen({Key? key}) : super(key: key);

  @override
  State<RegistrationsScreen> createState() => _RegistrationsScreenState();
}

class _RegistrationsScreenState extends State<RegistrationsScreen> {
  bool _isLoading = true;
  List<dynamic> _requests = [];
  String? _errorMessage;
  int? _actioningId; // Track ID currently being approved/rejected

  @override
  void initState() {
    super.initState();
    _fetchRequests();
  }

  Future<void> _fetchRequests() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getRegistrations();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result['status'] == 'success') {
          _requests = result['requests'];
        } else {
          _errorMessage = result['message'];
        }
      });
    }
  }

  void _handleApprove(int id, String studentName) async {
    setState(() {
      _actioningId = id;
    });

    final result = await ApiService.approveRegistration(id);

    if (mounted) {
      setState(() {
        _actioningId = null;
      });

      if (result['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'تم اعتماد الطالب "$studentName" بنجاح، وإرسال تفاصيل الدخول بالواتساب!',
              style: GoogleFonts.cairo(fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
            backgroundColor: Colors.green,
            behavior: SnackBarBehavior.floating,
          ),
        );
        _fetchRequests(); // Reload list to remove approved student
      } else {
        _showErrorDialog(result['message'] ?? 'فشلت عملية الاعتماد');
      }
    }
  }

  void _handleReject(int id, String studentName) async {
    // Show verification dialog
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('رفض طلب الالتحاق', style: GoogleFonts.cairo(fontWeight: FontWeight.bold)),
        content: Text('هل أنت متأكد من رغبتك في رفض طلب التحاق الطالب "$studentName"؟', style: GoogleFonts.cairo()),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('إلغاء', style: GoogleFonts.cairo(color: Colors.grey)),
          ),
          TextButton(
            onPressed: () async {
              Navigator.pop(context);
              setState(() {
                _actioningId = id;
              });

              final result = await ApiService.rejectRegistration(id);

              if (mounted) {
                setState(() {
                  _actioningId = null;
                });

                if (result['status'] == 'success') {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(
                        'تم رفض طلب الالتحاق بنجاح',
                        style: GoogleFonts.cairo(fontWeight: FontWeight.bold),
                        textAlign: TextAlign.center,
                      ),
                      backgroundColor: Colors.orange.shade800,
                      behavior: SnackBarBehavior.floating,
                    ),
                  );
                  _fetchRequests();
                } else {
                  _showErrorDialog(result['message'] ?? 'فشل رفض الطلب');
                }
              }
            },
            child: Text('رفض الطلب', style: GoogleFonts.cairo(color: Colors.redAccent, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  void _showErrorDialog(String message) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('تنبيه خطأ', style: GoogleFonts.cairo(fontWeight: FontWeight.bold, color: Colors.red)),
        content: Text(message, style: GoogleFonts.cairo()),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('حسناً', style: GoogleFonts.cairo()),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          'طلبات الالتحاق الجديدة',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        elevation: 0,
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
      ),
      body: RefreshIndicator(
        onRefresh: _fetchRequests,
        color: const Color(0xffc5a021),
        child: _buildMainContent(),
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
                onPressed: _fetchRequests,
                child: const Text('تحديث القائمة'),
              ),
            ],
          ),
        ),
      );
    }

    if (_requests.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.mark_email_read_rounded, size: 64, color: Colors.grey),
            const SizedBox(height: 16),
            Text(
              'لا توجد طلبات التحاق معلقة حالياً',
              style: GoogleFonts.cairo(fontSize: 14, color: Colors.grey.shade600, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            Text(
              'سيظهر أي طالب جديد يسجل هنا فوراً.',
              style: GoogleFonts.cairo(fontSize: 12, color: Colors.grey.shade400),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _requests.length,
      itemBuilder: (context, index) {
        final request = _requests[index];
        final id = int.tryParse(request['id'].toString()) ?? 0;
        final studentName = request['name'] ?? '';
        final sex = request['sex'] ?? 'male';
        final isMale = sex == 'male' || sex == 'ذكر';
        
        final isCurrentlyActioning = _actioningId == id;

        return Card(
          margin: const EdgeInsets.only(bottom: 16),
          elevation: 0,
          color: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
            side: const BorderSide(color: Color(0xffe2e8f0), width: 1),
          ),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Header (Name and Gender avatar)
                Row(
                  children: [
                    CircleAvatar(
                      radius: 22,
                      backgroundColor: isMale ? const Color(0xffeff6ff) : const Color(0xfffdf2f8),
                      child: Icon(
                        isMale ? Icons.face_rounded : Icons.face_3_rounded,
                        color: isMale ? const Color(0xff2563eb) : const Color(0xffdb2777),
                        size: 26,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            studentName,
                            style: GoogleFonts.cairo(
                              fontSize: 15,
                              fontWeight: FontWeight.bold,
                              color: const Color(0xff192a56),
                            ),
                          ),
                          Text(
                            request['school_name'] ?? 'المدرسة الافتراضية',
                            style: GoogleFonts.cairo(fontSize: 11, color: const Color(0xff94a3b8)),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 12),
                  child: Divider(height: 1, thickness: 0.5, color: Color(0xffe2e8f0)),
                ),

                // Request specifications
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'الصف الدراسي المطلوب',
                          style: GoogleFonts.cairo(fontSize: 10, color: const Color(0xff94a3b8)),
                        ),
                        Text(
                          request['class_name'] ?? 'غير محدد',
                          style: GoogleFonts.cairo(fontSize: 13, color: const Color(0xff192a56), fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(
                          'هاتف ولي الأمر',
                          style: GoogleFonts.cairo(fontSize: 10, color: const Color(0xff94a3b8)),
                        ),
                        Text(
                          request['phone'] ?? '',
                          style: const TextStyle(fontSize: 13, color: Color(0xff192a56), fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  ],
                ),
                
                const SizedBox(height: 16),

                // Control actions buttons (Approve / Reject)
                if (isCurrentlyActioning) ...[
                  const Center(
                    child: Padding(
                      padding: EdgeInsets.symmetric(vertical: 8),
                      child: CircularProgressIndicator(color: Color(0xff192a56)),
                    ),
                  ),
                ] else ...[
                  Row(
                    children: [
                      // Approve Button
                      Expanded(
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xff16a34a),
                            foregroundColor: Colors.white,
                            elevation: 0,
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                          onPressed: () => _handleApprove(id, studentName),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(Icons.check_circle_rounded, size: 18),
                              const SizedBox(width: 8),
                              Text('اعتماد وقبول', style: GoogleFonts.cairo(fontSize: 12, fontWeight: FontWeight.bold)),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      
                      // Reject Button
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => _handleReject(id, studentName),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: const Color(0xffdc2626),
                            side: const BorderSide(color: Color(0xfffca5a5)),
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(Icons.cancel_rounded, size: 18),
                              const SizedBox(width: 8),
                              Text('رفض الطلب', style: GoogleFonts.cairo(fontSize: 12, fontWeight: FontWeight.bold)),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
        );
      },
    );
  }
}
