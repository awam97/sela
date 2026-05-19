import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class SubjectsScreen extends StatefulWidget {
  const SubjectsScreen({Key? key}) : super(key: key);

  @override
  State<SubjectsScreen> createState() => _SubjectsScreenState();
}

class _SubjectsScreenState extends State<SubjectsScreen> {
  bool _isLoading = true;
  List<dynamic> _subjects = [];
  List<dynamic> _classes = [];
  List<dynamic> _filteredSubjects = [];

  String _searchQuery = '';
  int? _selectedClassId; // null represents "All"
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchSubjectsData();
  }

  Future<void> _fetchSubjectsData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getSubjects();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result['status'] == 'success') {
          _subjects = result['subjects'];
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
      _filteredSubjects = _subjects.where((subject) {
        // 1. Search Query Filter (name or teacher name)
        final name = (subject['name'] ?? '').toString().toLowerCase();
        final teacher = (subject['teacher_name'] ?? '').toString().toLowerCase();
        final matchesSearch = name.contains(_searchQuery.toLowerCase()) || 
            teacher.contains(_searchQuery.toLowerCase());

        // 2. Class Filter
        final matchesClass = _selectedClassId == null || 
            (subject['class_id'] != null && int.tryParse(subject['class_id'].toString()) == _selectedClassId);

        return matchesSearch && matchesClass;
      }).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          'دليل المناهج والمواد',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        elevation: 0,
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: Column(
        children: [
          // 1. Sleek Search & Class Filter Panel
          Container(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
            decoration: const BoxDecoration(
              color: Colors.white,
              border: Border(
                bottom: BorderSide(color: Color(0xffe2e8f0), width: 1),
              ),
            ),
            child: Column(
              children: [
                // Modern Rounded Search Bar
                TextField(
                  onChanged: (value) {
                    _searchQuery = value;
                    _applyFilters();
                  },
                  textAlign: TextAlign.right,
                  textDirection: TextDirection.rtl,
                  decoration: InputDecoration(
                    hintText: 'ابحث عن مادة أو معلم...',
                    hintStyle: GoogleFonts.cairo(color: const Color(0xff94a3b8), fontSize: 13),
                    prefixIcon: const Icon(Icons.search_rounded, color: Color(0xff192A56)),
                    filled: true,
                    fillColor: const Color(0xfff8fafc),
                    contentPadding: const EdgeInsets.symmetric(vertical: 12),
                    enabledBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                      borderSide: const BorderSide(color: Color(0xffe2e8f0), width: 1),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                      borderSide: const BorderSide(color: Color(0xffc5a021), width: 1.5),
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // Horizontal Sliding Class Filter Chips
                if (_classes.isNotEmpty)
                  SizedBox(
                    height: 38,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      itemCount: _classes.length + 1,
                      itemBuilder: (context, index) {
                        final isAll = index == 0;
                        final classItem = isAll ? null : _classes[index - 1];
                        final classId = isAll ? null : int.tryParse(classItem['class_id'].toString());
                        final label = isAll ? 'الكل' : classItem['name'] ?? '';
                        final isSelected = _selectedClassId == classId;

                        return Container(
                          margin: const EdgeInsets.only(left: 8),
                          child: ChoiceChip(
                            label: Text(
                              label,
                              style: GoogleFonts.cairo(
                                fontSize: 12,
                                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                color: isSelected ? Colors.white : const Color(0xff192A56),
                              ),
                            ),
                            selected: isSelected,
                            onSelected: (selected) {
                              setState(() {
                                _selectedClassId = classId;
                                _applyFilters();
                              });
                            },
                            selectedColor: const Color(0xff192A56),
                            backgroundColor: Colors.white,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(30),
                              side: BorderSide(
                                color: isSelected ? const Color(0xff192A56) : const Color(0xffe2e8f0),
                              ),
                            ),
                            elevation: 0,
                            pressElevation: 0,
                          ),
                        );
                      },
                    ),
                  ),
              ],
            ),
          ),

          // 2. Loading State / Subjects Roster Listing
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator(color: Color(0xff192a56)))
                : _errorMessage != null
                    ? Center(
                        child: Text(
                          _errorMessage!,
                          style: GoogleFonts.cairo(color: Colors.redAccent),
                          textAlign: TextAlign.center,
                        ),
                      )
                    : RefreshIndicator(
                        onRefresh: _fetchSubjectsData,
                        color: const Color(0xffc5a021),
                        child: _filteredSubjects.isEmpty
                            ? Center(
                                child: SingleChildScrollView(
                                  physics: const AlwaysScrollableScrollPhysics(),
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      const Icon(
                                        Icons.library_books_rounded,
                                        size: 64,
                                        color: Color(0xffcbd5e1),
                                      ),
                                      const SizedBox(height: 16),
                                      Text(
                                        'لا يوجد مواد دراسية مطابقة لبحثك',
                                        style: GoogleFonts.cairo(color: const Color(0xff94a3b8), fontSize: 14),
                                      ),
                                    ],
                                  ),
                                ),
                              )
                            : ListView.builder(
                                padding: const EdgeInsets.all(20),
                                itemCount: _filteredSubjects.length,
                                itemBuilder: (context, index) {
                                  final subject = _filteredSubjects[index];
                                  final teacherName = subject['teacher_name'] ?? 'لم يتم تعيين معلم';
                                  final totalMark = subject['total_mark'] ?? '--';
                                  final passMark = subject['pass_mark'] ?? '--';

                                  return Card(
                                    margin: const EdgeInsets.only(bottom: 16),
                                    elevation: 0,
                                    color: Colors.white,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(16),
                                      side: const BorderSide(color: Color(0xffe2e8f0), width: 1),
                                    ),
                                    child: Padding(
                                      padding: const EdgeInsets.all(16),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          // Class Tag & Subject Title Line
                                          Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(
                                                subject['name'] ?? '',
                                                style: GoogleFonts.cairo(
                                                  fontSize: 16,
                                                  fontWeight: FontWeight.w900,
                                                  color: const Color(0xff192a56),
                                                ),
                                              ),
                                              Container(
                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                                decoration: BoxDecoration(
                                                  color: const Color(0xffFFF7ED),
                                                  borderRadius: BorderRadius.circular(30),
                                                  border: Border.all(color: const Color(0xfffed7aa), width: 1),
                                                ),
                                                child: Text(
                                                  subject['class_name'] ?? 'صف دراسي',
                                                  style: GoogleFonts.cairo(
                                                    color: const Color(0xffc5a021),
                                                    fontSize: 11,
                                                    fontWeight: FontWeight.bold,
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 12),

                                          // Teacher Information Avatar
                                          Row(
                                            children: [
                                              Container(
                                                padding: const EdgeInsets.all(8),
                                                decoration: const BoxDecoration(
                                                  shape: BoxShape.circle,
                                                  color: Color(0xfff1f5f9),
                                                ),
                                                child: const Icon(
                                                  Icons.person_outline_rounded,
                                                  size: 18,
                                                  color: Color(0xff475569),
                                                ),
                                              ),
                                              const SizedBox(width: 10),
                                              Text(
                                                teacherName,
                                                style: GoogleFonts.cairo(
                                                  fontSize: 13,
                                                  color: const Color(0xff475569),
                                                  fontWeight: FontWeight.w600,
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 16),
                                          const Divider(height: 1, thickness: 1, color: Color(0xfff1f5f9)),
                                          const SizedBox(height: 16),

                                          // Marks Grid Dashboard Widgets
                                          Row(
                                            children: [
                                              // Total Mark Card
                                              Expanded(
                                                child: Container(
                                                  padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 12),
                                                  decoration: BoxDecoration(
                                                    color: const Color(0xfff8fafc),
                                                    borderRadius: BorderRadius.circular(12),
                                                    border: Border.all(color: const Color(0xffe2e8f0)),
                                                  ),
                                                  child: Column(
                                                    crossAxisAlignment: CrossAxisAlignment.center,
                                                    children: [
                                                      Text(
                                                        'الدرجة الكاملة',
                                                        style: GoogleFonts.cairo(
                                                          fontSize: 10,
                                                          color: const Color(0xff64748b),
                                                          fontWeight: FontWeight.bold,
                                                        ),
                                                      ),
                                                      const SizedBox(height: 2),
                                                      Text(
                                                        '$totalMark',
                                                        style: GoogleFonts.cairo(
                                                          fontSize: 16,
                                                          fontWeight: FontWeight.w900,
                                                          color: const Color(0xff192a56),
                                                        ),
                                                      ),
                                                    ],
                                                  ),
                                                ),
                                              ),
                                              const SizedBox(width: 12),

                                              // Passing Mark Card
                                              Expanded(
                                                child: Container(
                                                  padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 12),
                                                  decoration: BoxDecoration(
                                                    color: const Color(0xffFFF7ED),
                                                    borderRadius: BorderRadius.circular(12),
                                                    border: Border.all(color: const Color(0xfffed7aa)),
                                                  ),
                                                  child: Column(
                                                    crossAxisAlignment: CrossAxisAlignment.center,
                                                    children: [
                                                      Text(
                                                        'درجة النجاح',
                                                        style: GoogleFonts.cairo(
                                                          fontSize: 10,
                                                          color: const Color(0xffc5a021),
                                                          fontWeight: FontWeight.bold,
                                                        ),
                                                      ),
                                                      const SizedBox(height: 2),
                                                      Text(
                                                        '$passMark',
                                                        style: GoogleFonts.cairo(
                                                          fontSize: 16,
                                                          fontWeight: FontWeight.w900,
                                                          color: const Color(0xffc5a021),
                                                        ),
                                                      ),
                                                    ],
                                                  ),
                                                ),
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
