import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class ManageCitiesScreen extends StatefulWidget {
  const ManageCitiesScreen({Key? key}) : super(key: key);

  @override
  State<ManageCitiesScreen> createState() => _ManageCitiesScreenState();
}

class _ManageCitiesScreenState extends State<ManageCitiesScreen> {
  bool _isLoading = true;
  List<dynamic> _allCities = [];
  List<dynamic> _filteredCities = [];
  String? _errorMessage;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _fetchCities();
    _searchController.addListener(_filterCities);
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _fetchCities() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getCities();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result['status'] == 'success') {
          _allCities = result['cities'] ?? [];
          _filteredCities = List.from(_allCities);
        } else {
          _errorMessage = result['message'];
        }
      });
    }
  }

  void _filterCities() {
    final query = _searchController.text.trim().toLowerCase();
    setState(() {
      if (query.isEmpty) {
        _filteredCities = List.from(_allCities);
      } else {
        _filteredCities = _allCities.where((city) {
          final name = (city['name'] ?? '').toString().toLowerCase();
          return name.contains(query);
        }).toList();
      }
    });
  }

  void _showCityDialog({Map<String, dynamic>? city}) {
    final isEdit = city != null;
    final nameController = TextEditingController(text: isEdit ? city['name'] : '');
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
                isEdit ? 'تعديل اسم المدينة' : 'إضافة مدينة جديدة',
                style: GoogleFonts.cairo(fontWeight: FontWeight.bold, color: const Color(0xff192A56)),
                textAlign: TextAlign.center,
              ),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  TextField(
                    controller: nameController,
                    decoration: InputDecoration(
                      labelText: 'اسم المدينة / المنطقة',
                      labelStyle: GoogleFonts.cairo(fontSize: 13, color: Colors.grey.shade600),
                      hintText: 'مثال: طرابلس، بنغازي',
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
                    ),
                    style: GoogleFonts.cairo(),
                  ),
                ],
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
                          if (name.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text('يرجى إدخال اسم المدينة / المنطقة', style: GoogleFonts.cairo()),
                                backgroundColor: Colors.orange.shade800,
                              ),
                            );
                            return;
                          }
                          if (name.length < 2) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text('اسم المدينة قصير جداً (يجب أن يكون حرفين على الأقل)', style: GoogleFonts.cairo()),
                                backgroundColor: Colors.orange.shade800,
                              ),
                            );
                            return;
                          }
                          if (name.length > 50) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text('اسم المدينة طويل جداً (الحد الأقصى 50 حرفاً)', style: GoogleFonts.cairo()),
                                backgroundColor: Colors.orange.shade800,
                              ),
                            );
                            return;
                          }

                          setDialogState(() {
                            isSaving = true;
                          });

                          final result = isEdit
                              ? await ApiService.updateCity(int.parse(city['ID'].toString()), name)
                              : await ApiService.createCity(name);

                          if (mounted) {
                            Navigator.pop(context);
                            if (result['status'] == 'success') {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                  content: Text(result['message'] ?? 'تم حفظ التغييرات بنجاح', style: GoogleFonts.cairo()),
                                  backgroundColor: Colors.green,
                                ),
                              );
                              _fetchCities();
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

  void _handleDeleteCity(Map<String, dynamic> city) async {
    final schoolsCount = int.tryParse(city['schools_count'].toString()) ?? 0;
    if (schoolsCount > 0) {
      showDialog(
        context: context,
        builder: (context) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: Text('تنبيه هام', style: GoogleFonts.cairo(fontWeight: FontWeight.bold, color: Colors.redAccent)),
          content: Text(
            'لا يمكن حذف مدينة "${city['name']}" لوجود عدد ($schoolsCount) من المدارس المرتبطة بها حالياً. يرجى نقل أو حذف المدارس أولاً.',
            style: GoogleFonts.cairo(),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('حسناً', style: GoogleFonts.cairo(fontWeight: FontWeight.bold)),
            )
          ],
        ),
      );
      return;
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('حذف المدينة', style: GoogleFonts.cairo(fontWeight: FontWeight.bold, color: Colors.redAccent)),
        content: Text('هل أنت متأكد من رغبتك في حذف مدينة "${city['name']}" نهائياً من النظام؟', style: GoogleFonts.cairo()),
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

      final result = await ApiService.deleteCity(int.parse(city['ID'].toString()));

      if (mounted) {
        if (result['status'] == 'success') {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(result['message'] ?? 'تم حذف المدينة بنجاح', style: GoogleFonts.cairo()), backgroundColor: Colors.green),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(result['message'] ?? 'فشل حذف المدينة', style: GoogleFonts.cairo()), backgroundColor: Colors.redAccent),
          );
        }
        _fetchCities();
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          'إدارة المدن والمناطق',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.white),
        elevation: 0,
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showCityDialog(),
        backgroundColor: const Color(0xffC5A021),
        foregroundColor: Colors.white,
        elevation: 4,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        icon: const Icon(Icons.add_location_alt_rounded, size: 20),
        label: Text('إضافة مدينة', style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 13)),
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
                  hintText: 'ابحث عن مدينة أو منطقة...',
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

          // Core Body Content
          Expanded(
            child: RefreshIndicator(
              onRefresh: _fetchCities,
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
                                  onPressed: _fetchCities,
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
                      : _filteredCities.isEmpty
                          ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    _searchController.text.isNotEmpty
                                        ? Icons.search_off_rounded
                                        : Icons.location_off_rounded,
                                    size: 80,
                                    color: Colors.grey.shade300,
                                  ),
                                  const SizedBox(height: 16),
                                  Text(
                                    _searchController.text.isNotEmpty
                                        ? 'لم نجد أي مدينة تطابق بحثك.'
                                        : 'لا توجد مدن مسجلة بالنظام حالياً.',
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
                                childAspectRatio: 0.95,
                              ),
                              itemCount: _filteredCities.length,
                              itemBuilder: (context, index) {
                                final city = _filteredCities[index];
                                final schoolsCount = city['schools_count'] ?? 0;

                                return Card(
                                  color: Colors.white,
                                  elevation: 0,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(24),
                                    side: BorderSide(color: Colors.grey.shade200, width: 1.2),
                                  ),
                                  child: Padding(
                                    padding: const EdgeInsets.all(16),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.stretch,
                                      children: [
                                        // Header icon circle
                                        Align(
                                          alignment: Alignment.topRight,
                                          child: Container(
                                            padding: const EdgeInsets.all(10),
                                            decoration: BoxDecoration(
                                              color: const Color(0xffC5A021).withOpacity(0.08),
                                              shape: BoxShape.circle,
                                            ),
                                            child: const Icon(Icons.location_on_rounded, color: Color(0xffC5A021), size: 20),
                                          ),
                                        ),
                                        const Spacer(),
                                        // City Name
                                        Text(
                                          city['name'] ?? '',
                                          style: GoogleFonts.cairo(
                                            fontSize: 15,
                                            fontWeight: FontWeight.bold,
                                            color: const Color(0xff192A56),
                                          ),
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        const SizedBox(height: 2),
                                        // Subtitle info
                                        Text(
                                          'عدد المدارس: $schoolsCount',
                                          style: GoogleFonts.cairo(fontSize: 11.5, color: Colors.grey.shade600, fontWeight: FontWeight.w600),
                                        ),
                                        const Spacer(),
                                        // Actions Row
                                        const Divider(height: 12, thickness: 0.8),
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.end,
                                          children: [
                                            Expanded(
                                              child: TextButton.icon(
                                                style: TextButton.styleFrom(
                                                  padding: EdgeInsets.zero,
                                                  foregroundColor: Colors.blueAccent,
                                                ),
                                                onPressed: () => _showCityDialog(city: city),
                                                icon: const Icon(Icons.edit_outlined, size: 16),
                                                label: Text('تعديل', style: GoogleFonts.cairo(fontSize: 11, fontWeight: FontWeight.bold)),
                                              ),
                                            ),
                                            const SizedBox(width: 4),
                                            IconButton(
                                              icon: const Icon(Icons.delete_outline_rounded, color: Colors.redAccent, size: 18),
                                              onPressed: () => _handleDeleteCity(city),
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
