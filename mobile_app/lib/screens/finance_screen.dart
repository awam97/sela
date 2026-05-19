import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';
import 'package:intl/intl.dart' as intl;
import 'package:url_launcher/url_launcher.dart';

class FinanceScreen extends StatefulWidget {
  const FinanceScreen({Key? key}) : super(key: key);

  @override
  State<FinanceScreen> createState() => _FinanceScreenState();
}

class _FinanceScreenState extends State<FinanceScreen> {
  bool _isLoading = true;
  String? _errorMessage;
  List<dynamic> _invoices = [];
  List<dynamic> _filteredInvoices = [];
  
  final TextEditingController _searchController = TextEditingController();

  double _totalAmount = 0.0;
  double _totalPaid = 0.0;
  double _totalDue = 0.0;

  @override
  void initState() {
    super.initState();
    _fetchFinanceData();
    _searchController.addListener(_onSearchChanged);
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _fetchFinanceData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getInvoices();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result['status'] == 'success') {
          _invoices = result['invoices'] ?? [];
          _filteredInvoices = List.from(_invoices);
          _calculateTotals();
        } else {
          _errorMessage = result['message'];
        }
      });
    }
  }

  void _calculateTotals() {
    double amt = 0.0;
    double paid = 0.0;
    double due = 0.0;

    for (var inv in _invoices) {
      double amount = double.tryParse(inv['amount']?.toString() ?? '0') ?? 0.0;
      double amountPaid = double.tryParse(inv['amount_paid']?.toString() ?? '0') ?? 0.0;
      double amountDue = double.tryParse(inv['due']?.toString() ?? '0') ?? (amount - amountPaid);
      
      amt += amount;
      paid += amountPaid;
      due += amountDue;
    }

    _totalAmount = amt;
    _totalPaid = paid;
    _totalDue = due;
  }

  void _onSearchChanged() {
    final query = _searchController.text.trim().toLowerCase();
    if (query.isEmpty) {
      setState(() {
        _filteredInvoices = List.from(_invoices);
      });
      return;
    }

    setState(() {
      _filteredInvoices = _invoices.where((inv) {
        final studentName = (inv['student_name'] ?? '').toString().toLowerCase();
        final title = (inv['title'] ?? '').toString().toLowerCase();
        final id = (inv['student_id'] ?? '').toString();
        return studentName.contains(query) || title.contains(query) || id.contains(query);
      }).toList();
    });
  }

  String _formatDate(dynamic timestamp) {
    if (timestamp == null) return '';
    try {
      int ts = int.parse(timestamp.toString());
      if (ts.toString().length < 13) {
        ts = ts * 1000; // Convert to milliseconds
      }
      final date = DateTime.fromMillisecondsSinceEpoch(ts);
      return intl.DateFormat('yyyy/MM/dd').format(date);
    } catch (e) {
      // Try parsing as normal date string
      try {
        final date = DateTime.parse(timestamp.toString());
        return intl.DateFormat('yyyy/MM/dd').format(date);
      } catch (_) {
        return timestamp.toString();
      }
    }
  }

  void _downloadInvoicePdf(dynamic invoiceId) {
    final url = '${ApiService.baseUrl}/finance/invoice/print/$invoiceId';
    final uri = Uri.parse(url);
    try {
      launchUrl(
        uri,
        mode: LaunchMode.platformDefault,
      );
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'تعذر فتح رابط الفاتورة للطباعة',
              style: GoogleFonts.cairo(color: Colors.white),
              textAlign: TextAlign.right,
            ),
            backgroundColor: Colors.redAccent,
          ),
        );
      }
    }
  }

  void _showInvoiceDetails(dynamic inv) {
    final primaryColor = const Color(0xff192A56);

    double amount = double.tryParse(inv['amount']?.toString() ?? '0') ?? 0.0;
    double paid = double.tryParse(inv['amount_paid']?.toString() ?? '0') ?? 0.0;
    double due = double.tryParse(inv['due']?.toString() ?? '0') ?? (amount - paid);

    String statusText = 'غير مدفوع';
    Color statusColor = Colors.redAccent;
    Color statusBgColor = Colors.red.shade50;

    if (due <= 0) {
      statusText = 'مدفوع كاملاً';
      statusColor = const Color(0xff16a34a);
      statusBgColor = const Color(0xfff0fdf4);
    } else if (paid > 0) {
      statusText = 'مدفوع جزئياً';
      statusColor = const Color(0xffd97706);
      statusBgColor = const Color(0xfffffbeb);
    }

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Center(
                child: Container(
                  width: 50,
                  height: 5,
                  decoration: BoxDecoration(
                    color: const Color(0xffcbd5e1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                ),
              ),
              const SizedBox(height: 20),

              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'تفاصيل الفاتورة #${inv['invoice_id']}',
                    style: GoogleFonts.cairo(
                      fontSize: 18,
                      fontWeight: FontWeight.w900,
                      color: primaryColor,
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close_rounded, color: Color(0xff64748b)),
                    onPressed: () => Navigator.pop(context),
                  ),
                ],
              ),
              const Divider(height: 20, thickness: 1.2),

              _buildDetailRow(Icons.person_outline_rounded, 'اسم الطالب:', inv['student_name'] ?? 'غير محدد'),
              _buildDetailRow(Icons.fingerprint_rounded, 'معرف الطالب:', inv['student_id']?.toString() ?? '-'),
              _buildDetailRow(Icons.receipt_long_rounded, 'بيان الفاتورة:', inv['title'] ?? ''),
              
              if (inv['description'] != null && inv['description'].toString().trim().isNotEmpty)
                _buildDetailRow(Icons.description_outlined, 'الوصف المالي:', inv['description'].toString()),

              _buildDetailRow(Icons.calendar_today_rounded, 'تاريخ الإصدار:', _formatDate(inv['creation_timestamp'] ?? inv['payment_timestamp'])),
              _buildDetailRow(Icons.school_rounded, 'العام الدراسي:', inv['year'] ?? ''),
              
              if (inv['payment_method'] != null && inv['payment_method'].toString().isNotEmpty)
                _buildDetailRow(Icons.payment_rounded, 'طريقة الدفع:', inv['payment_method'].toString()),

              _buildDetailRow(Icons.info_outline_rounded, 'حالة السداد:', statusText, textValColor: statusColor),
              
              const SizedBox(height: 20),
              
              Container(
                decoration: BoxDecoration(
                  color: const Color(0xfff8fafc),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xffe2e8f0), width: 1.2),
                ),
                padding: const EdgeInsets.all(18),
                child: Column(
                  children: [
                    _buildAmountDetailRow('المبلغ المطلوب الإجمالي:', amount, primaryColor),
                    const SizedBox(height: 8),
                    _buildAmountDetailRow('المبلغ المسدد:', paid, const Color(0xff16a34a)),
                    const Divider(height: 20, thickness: 1),
                    _buildAmountDetailRow('المبلغ المستحق المتبقي:', due, due <= 0 ? const Color(0xff16a34a) : const Color(0xffdc2626), isBold: true),
                  ],
                ),
              ),

              const SizedBox(height: 24),

              SizedBox(
                height: 54,
                child: ElevatedButton.icon(
                  onPressed: () {
                    _downloadInvoicePdf(inv['invoice_id']);
                    Navigator.pop(context);
                  },
                  icon: const Icon(Icons.picture_as_pdf_rounded, color: Colors.white),
                  label: Text(
                    'طباعة / تحميل الفاتورة (PDF)',
                    style: GoogleFonts.cairo(fontSize: 15, fontWeight: FontWeight.bold),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: primaryColor,
                    foregroundColor: Colors.white,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 16),
            ],
          ),
        );
      },
    );
  }

  Widget _buildDetailRow(IconData icon, String label, String val, {Color? textValColor}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 20, color: const Color(0xff94a3b8)),
          const SizedBox(width: 10),
          Text(
            label,
            style: GoogleFonts.cairo(
              fontSize: 13,
              fontWeight: FontWeight.bold,
              color: const Color(0xff64748b),
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              val,
              style: GoogleFonts.cairo(
                fontSize: 13,
                fontWeight: FontWeight.bold,
                color: textValColor ?? const Color(0xff1e293b),
              ),
              textAlign: TextAlign.right,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAmountDetailRow(String label, double val, Color color, {bool isBold = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: GoogleFonts.cairo(
            fontSize: 13,
            fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
            color: const Color(0xff1e293b),
          ),
        ),
        Text(
          '${val.toStringAsFixed(2)} د.ل',
          style: GoogleFonts.cairo(
            fontSize: isBold ? 15 : 13,
            fontWeight: FontWeight.w900,
            color: color,
          ),
          textDirection: TextDirection.ltr,
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = const Color(0xff192A56);
    final goldAccentColor = const Color(0xffC5A021);

    return Scaffold(
      backgroundColor: const Color(0xfff8fafc),
      appBar: AppBar(
        title: Text(
          'الإدارة المالية والفواتير',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        elevation: 0,
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xff192A56)))
          : RefreshIndicator(
              onRefresh: _fetchFinanceData,
              color: primaryColor,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
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
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                'الخلاصة المالية للمدرسة',
                                style: GoogleFonts.cairo(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                  color: primaryColor,
                                ),
                              ),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: primaryColor.withOpacity(0.06),
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: Text(
                                  '${_invoices.length} فاتورة',
                                  style: GoogleFonts.cairo(
                                    fontSize: 11,
                                    fontWeight: FontWeight.bold,
                                    color: primaryColor,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const Divider(height: 24, thickness: 1.2),
                          Row(
                            children: [
                              Expanded(
                                child: _buildSummaryItem(
                                  'إجمالي الفواتير',
                                  _totalAmount,
                                  primaryColor,
                                ),
                              ),
                              Container(height: 40, width: 1, color: const Color(0xffe2e8f0)),
                              Expanded(
                                child: _buildSummaryItem(
                                  'إجمالي المدفوع',
                                  _totalPaid,
                                  const Color(0xff16a34a),
                                ),
                              ),
                              Container(height: 40, width: 1, color: const Color(0xffe2e8f0)),
                              Expanded(
                                child: _buildSummaryItem(
                                  'إجمالي المتبقي',
                                  _totalDue,
                                  const Color(0xffdc2626),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 24),

                    TextField(
                      controller: _searchController,
                      textAlign: TextAlign.right,
                      style: GoogleFonts.cairo(fontSize: 14, fontWeight: FontWeight.bold, color: primaryColor),
                      decoration: InputDecoration(
                        hintText: 'ابحث باسم الطالب أو عنوان الفاتورة...',
                        hintStyle: GoogleFonts.cairo(fontSize: 13, color: const Color(0xff94a3b8)),
                        prefixIcon: const Icon(Icons.search_rounded, color: Color(0xff94a3b8)),
                        suffixIcon: _searchController.text.isNotEmpty
                            ? IconButton(
                                icon: const Icon(Icons.clear_rounded, color: Color(0xff94a3b8)),
                                onPressed: () {
                                  _searchController.clear();
                                },
                              )
                            : null,
                        contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
                        filled: true,
                        fillColor: Colors.white,
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: BorderSide(color: goldAccentColor, width: 1.5),
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),

                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'سجل الفواتير والعمليات',
                          style: GoogleFonts.cairo(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: primaryColor,
                          ),
                        ),
                        Text(
                          'عرض ${_filteredInvoices.length} من ${_invoices.length}',
                          style: GoogleFonts.cairo(
                            fontSize: 12,
                            color: const Color(0xff64748b),
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),

                    if (_errorMessage != null) ...[
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.amber.shade50,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: Colors.amber.shade300),
                        ),
                        child: Text(
                          _errorMessage!,
                          style: GoogleFonts.cairo(color: Colors.amber.shade900, fontSize: 13),
                          textAlign: TextAlign.center,
                        ),
                      ),
                    ] else if (_filteredInvoices.isEmpty) ...[
                      Container(
                        padding: const EdgeInsets.symmetric(vertical: 40),
                        child: Column(
                          children: [
                            Icon(Icons.receipt_long_rounded, size: 64, color: primaryColor.withOpacity(0.15)),
                            const SizedBox(height: 12),
                            Text(
                              'لا توجد فواتير مطابقة لعملية البحث',
                              style: GoogleFonts.cairo(
                                fontSize: 14,
                                color: const Color(0xff64748b),
                                fontWeight: FontWeight.bold,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          ],
                        ),
                      ),
                    ] else ...[
                      ListView.separated(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: _filteredInvoices.length,
                        separatorBuilder: (context, index) => const SizedBox(height: 12),
                        itemBuilder: (context, index) {
                          final inv = _filteredInvoices[index];
                          return InkWell(
                            onTap: () => _showInvoiceDetails(inv),
                            borderRadius: BorderRadius.circular(20),
                            child: _buildInvoiceCard(inv, primaryColor, goldAccentColor),
                          );
                        },
                      ),
                    ],
                    const SizedBox(height: 24),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildSummaryItem(String label, double val, Color color) {
    return Column(
      children: [
        Text(
          label,
          style: GoogleFonts.cairo(
            fontSize: 11,
            fontWeight: FontWeight.bold,
            color: const Color(0xff64748b),
          ),
        ),
        const SizedBox(height: 4),
        Text(
          '${val.toStringAsFixed(1)} د.ل',
          style: GoogleFonts.cairo(
            fontSize: 15,
            fontWeight: FontWeight.w900,
            color: color,
          ),
          textDirection: TextDirection.ltr,
        ),
      ],
    );
  }

  Widget _buildInvoiceCard(dynamic inv, Color primaryColor, Color goldAccentColor) {
    double amount = double.tryParse(inv['amount']?.toString() ?? '0') ?? 0.0;
    double paid = double.tryParse(inv['amount_paid']?.toString() ?? '0') ?? 0.0;
    double due = double.tryParse(inv['due']?.toString() ?? '0') ?? (amount - paid);

    String statusText = 'غير مدفوع';
    Color statusColor = Colors.redAccent;
    Color statusBgColor = Colors.red.shade50;

    if (due <= 0) {
      statusText = 'مدفوع كاملاً';
      statusColor = const Color(0xff16a34a);
      statusBgColor = const Color(0xfff0fdf4);
    } else if (paid > 0) {
      statusText = 'مدفوع جزئياً';
      statusColor = const Color(0xffd97706);
      statusBgColor = const Color(0xfffffbeb);
    }

    return Card(
      elevation: 0,
      color: Colors.white,
      margin: EdgeInsets.zero,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
        side: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
      ),
      child: Container(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Text(
                    inv['student_name'] ?? 'طالب غير محدد',
                    style: GoogleFonts.cairo(
                      fontSize: 15,
                      fontWeight: FontWeight.w900,
                      color: primaryColor,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: statusBgColor,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: statusColor.withOpacity(0.2), width: 1),
                  ),
                  child: Text(
                    statusText,
                    style: GoogleFonts.cairo(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: statusColor,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 6),
            
            Text(
              inv['title'] ?? 'تفاصيل الفاتورة',
              style: GoogleFonts.cairo(
                fontSize: 13,
                fontWeight: FontWeight.bold,
                color: const Color(0xff334155),
              ),
            ),
            if (inv['description'] != null && inv['description'].toString().trim().isNotEmpty) ...[
              const SizedBox(height: 4),
              Text(
                inv['description'].toString(),
                style: GoogleFonts.cairo(
                  fontSize: 12,
                  color: const Color(0xff64748b),
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ],
            
            const Divider(height: 20, thickness: 1),

            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'المطالبة: ${amount.toStringAsFixed(1)} د.ل',
                      style: GoogleFonts.cairo(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xff64748b),
                      ),
                      textDirection: TextDirection.ltr,
                    ),
                    Text(
                      'المدفوع: ${paid.toStringAsFixed(1)} د.ل',
                      style: GoogleFonts.cairo(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xff16a34a),
                      ),
                      textDirection: TextDirection.ltr,
                    ),
                  ],
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(
                      'المتبقي المستحق',
                      style: GoogleFonts.cairo(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xff94a3b8),
                      ),
                    ),
                    Text(
                      '${due.toStringAsFixed(1)} د.ل',
                      style: GoogleFonts.cairo(
                        fontSize: 16,
                        fontWeight: FontWeight.w900,
                        color: due <= 0 ? const Color(0xff16a34a) : const Color(0xffdc2626),
                      ),
                      textDirection: TextDirection.ltr,
                    ),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 8),
            
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'التاريخ: ${_formatDate(inv['creation_timestamp'] ?? inv['payment_timestamp'])}',
                  style: GoogleFonts.cairo(
                    fontSize: 11,
                    color: const Color(0xff94a3b8),
                    fontWeight: FontWeight.bold,
                  ),
                ),
                if (inv['payment_method'] != null && inv['payment_method'].toString().isNotEmpty)
                  Text(
                    'طريقة الدفع: ${inv['payment_method']}',
                    style: GoogleFonts.cairo(
                      fontSize: 11,
                      color: const Color(0xff94a3b8),
                      fontWeight: FontWeight.bold,
                    ),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
