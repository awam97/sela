import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import 'package:flutter/foundation.dart';

class ApiService {
  static const String baseUrl = 'https://graya.ly/api';

  /**
   * Fetch Sela's hardcoded online web API URL
   */
  static Future<String> getBaseUrl() async {
    return baseUrl;
  }

  /**
   * Helper: Retrieve headers with cached secure authorization token
   */
  static Future<Map<String, String>> _getHeaders() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('api_token') ?? '';
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  /**
   * Authenticate and Login User
   */
  static Future<Map<String, dynamic>> login(String username, String password) async {
    try {
      final url = await getBaseUrl();
      final response = await http.post(
        Uri.parse('$url/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'username': username,
          'password': password,
        }),
      );

      final decoded = jsonDecode(response.body);

      if (response.statusCode == 200) {
        if (decoded['status'] == 'success') {
          // Cache session token & user details
          final prefs = await SharedPreferences.getInstance();
          await prefs.setString('api_token', decoded['token']);
          await prefs.setInt('user_id', decoded['user']['id']);
          await prefs.setString('username', decoded['user']['username']);
          await prefs.setString('role', decoded['user']['role']);
          await prefs.setString('name', decoded['user']['name']);
          if (decoded['user']['school_id'] != null) {
            await prefs.setInt('school_id', decoded['user']['school_id']);
            await prefs.setString('school_name', decoded['user']['school_name'] ?? '');
          }

          return {'status': 'success', 'user': decoded['user']};
        } else if (decoded['status'] == 'otp_required') {
          return {
            'status': 'otp_required',
            'temp_token': decoded['temp_token'],
            'phone': decoded['phone'] ?? '',
            'email': decoded['email'] ?? '',
            'wa_enabled': decoded['wa_enabled'] ?? false,
          };
        }
      }
      
      return {
        'status': 'error',
        'message': decoded['message'] ?? 'بيانات الدخول غير صحيحة'
      };
    } catch (e) {
      return {
        'status': 'error',
        'message': 'حدث خطأ في الاتصال بالخادم. يرجى التحقق من اتصال الإنترنت وعنوان الـ IP.'
      };
    }
  }

  /**
   * Fetch Dashboard Statistics
   */
  static Future<Map<String, dynamic>> getDashboard() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/dashboard'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        if (decoded['app_titles'] != null) {
          final prefs = await SharedPreferences.getInstance();
          final appTitles = decoded['app_titles'] as Map<String, dynamic>;
          for (var entry in appTitles.entries) {
            await prefs.setString('app_title_${entry.key}', entry.value.toString());
          }
        }
        return {'status': 'success', 'data': decoded['data']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحميل بيانات لوحة التحكم'};
    } catch (e) {
      return {'status': 'error', 'message': 'تعذر الاتصال بالشبكة لربط لوحة التحكم'};
    }
  }

  /**
   * Fetch Students Catalog and Classes
   */
  static Future<Map<String, dynamic>> getStudents() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/students'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {
          'status': 'success',
          'students': decoded['students'] ?? [],
          'classes': decoded['classes'] ?? [],
          'sections': decoded['sections'] ?? []
        };
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'تعذر تحميل قائمة الطلاب'};
    } catch (e) {
      return {'status': 'error', 'message': 'فشل الاتصال بالخادم لتحميل الطلاب'};
    }
  }

  /**
   * Fetch Subjects Catalog and Classes
   */
  static Future<Map<String, dynamic>> getSubjects() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/subjects'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {
          'status': 'success',
          'subjects': decoded['subjects'] ?? [],
          'classes': decoded['classes'] ?? []
        };
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'تعذر تحميل قائمة المواد الدراسية'};
    } catch (e) {
      return {'status': 'error', 'message': 'فشل الاتصال بالخادم لتحميل المواد'};
    }
  }

  /**
   * Submit Daily Attendance Records
   */
  static Future<Map<String, dynamic>> saveAttendance(int classId, String date, Map<int, int> records) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      
      // Transform map keys to string for JSON compliance
      final Map<String, int> stringifiedRecords = {};
      records.forEach((key, value) {
        stringifiedRecords[key.toString()] = value;
      });

      final response = await http.post(
        Uri.parse('$url/attendance/save'),
        headers: headers,
        body: jsonEncode({
          'class_id': classId,
          'date': date,
          'records': stringifiedRecords,
        }),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل حفظ سجل الغياب'};
    } catch (e) {
      return {'status': 'error', 'message': 'تعذر إرسال بيانات التحضير لخطأ بالاتصال'};
    }
  }

  /**
   * Fetch Pending Registrations List
   */
  static Future<Map<String, dynamic>> getRegistrations() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/registrations'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'requests': decoded['requests'] ?? []};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحميل طلبات التسجيل'};
    } catch (e) {
      return {'status': 'error', 'message': 'فشل الاتصال بالخادم لتحميل طلبات التسجيل'};
    }
  }

  /**
   * Approve a registration request
   */
  static Future<Map<String, dynamic>> approveRegistration(int id) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/registrations/approve/$id'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل اعتماد الطلب'};
    } catch (e) {
      return {'status': 'error', 'message': 'تعذر إرسال طلب الاعتماد للشبكة'};
    }
  }

  /**
   * Reject a registration request
   */
  static Future<Map<String, dynamic>> rejectRegistration(int id) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/registrations/reject/$id'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل رفض الطلب'};
    } catch (e) {
      return {'status': 'error', 'message': 'تعذر إرسال طلب الرفض للشبكة'};
    }
  }

  /**
   * Send OTP via WhatsApp or Email
   */
  static Future<Map<String, dynamic>> sendOtp(String tempToken, String method) async {
    try {
      final url = await getBaseUrl();
      final response = await http.post(
        Uri.parse('$url/send-otp'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'temp_token': tempToken,
          'method': method,
        }),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {
          'status': 'success',
          'otp_token': decoded['otp_token'],
          'message': decoded['message'] ?? 'تم إرسال رمز التحقق بنجاح!'
        };
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل إرسال رمز التحقق'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لإرسال الرمز'};
    }
  }

  /**
   * Verify OTP Code
   */
  static Future<Map<String, dynamic>> verifyOtp(String otpToken, String code) async {
    try {
      final url = await getBaseUrl();
      final response = await http.post(
        Uri.parse('$url/verify-otp'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'otp_token': otpToken,
          'code': code,
        }),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        // Cache session token & user details
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('api_token', decoded['token']);
        await prefs.setInt('user_id', decoded['user']['id']);
        await prefs.setString('username', decoded['user']['username']);
        await prefs.setString('role', decoded['user']['role']);
        await prefs.setString('name', decoded['user']['name']);
        if (decoded['user']['school_id'] != null) {
          await prefs.setInt('school_id', decoded['user']['school_id']);
          await prefs.setString('school_name', decoded['user']['school_name'] ?? '');
        }

        return {'status': 'success', 'user': decoded['user']};
      } else {
        return {
          'status': 'error',
          'message': decoded['message'] ?? 'رمز التحقق غير صحيح'
        };
      }
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم للتحقق من الرمز'};
    }
  }

  /**
   * Create Student
   */
  static Future<Map<String, dynamic>> createStudent(String name, String phone, String sex, int classId) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/students/create'),
        headers: headers,
        body: jsonEncode({
          'name': name,
          'phone': phone,
          'sex': sex,
          'class_id': classId,
        }),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل إضافة الطالب'};
    } catch (e) {
      return {'status': 'error', 'message': 'تعذر الاتصال بالشبكة لإضافة الطالب'};
    }
  }

  /**
   * Edit Student Details
   */
  static Future<Map<String, dynamic>> editStudent(int studentId, String name, String phone, String sex, int classId) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/students/edit/$studentId'),
        headers: headers,
        body: jsonEncode({
          'name': name,
          'phone': phone,
          'sex': sex,
          'class_id': classId,
        }),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحديث بيانات الطالب'};
    } catch (e) {
      return {'status': 'error', 'message': 'تعذر الاتصال بالشبكة لتعديل الطالب'};
    }
  }

  /**
   * Delete Student Record
   */
  static Future<Map<String, dynamic>> deleteStudent(int studentId) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/students/delete/$studentId'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل حذف الطالب'};
    } catch (e) {
      return {'status': 'error', 'message': 'تعذر الاتصال بالشبكة لحذف الطالب'};
    }
  }

  /**
   * Identify Student by QR Code / ID
   */
  static Future<Map<String, dynamic>> identifyStudent(int studentId) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/students/identify/$studentId'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'data': decoded['data']};
      }
      return {
        'status': 'error',
        'message': decoded['message'] ?? 'فشل التعرف على الطالب'
      };
    } catch (e) {
      return {
        'status': 'error',
        'message': 'حدث خطأ أثناء الاتصال بالخادم والتعرف على الطالب'
      };
    }
  }

  /**
   * Clear Session and Logout
   */
  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
  }

  /**
   * Fetch Authenticated Admin/Teacher Profile details
   */
  static Future<Map<String, dynamic>> getProfile() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/profile'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'user': decoded['user']};
      }
      return {
        'status': 'error',
        'message': decoded['message'] ?? 'فشل تحميل الملف الشخصي'
      };
    } catch (e) {
      return {
        'status': 'error',
        'message': 'حدث خطأ أثناء تحميل بيانات الملف الشخصي'
      };
    }
  }

  /**
   * Update Authenticated Admin/Teacher Profile details
   */
  static Future<Map<String, dynamic>> updateProfile(Map<String, dynamic> data) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/profile/update'),
        headers: headers,
        body: jsonEncode(data),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        // Update local cached name/username if changed
        final prefs = await SharedPreferences.getInstance();
        if (data.containsKey('name')) {
          await prefs.setString('name', data['name']);
        }
        if (data.containsKey('username')) {
          await prefs.setString('username', data['username']);
        }
        return {'status': 'success', 'message': decoded['message'] ?? 'تم تحديث الملف الشخصي بنجاح'};
      }
      return {
        'status': 'error',
        'message': decoded['message'] ?? 'فشل تحديث الملف الشخصي'
      };
    } catch (e) {
      return {
        'status': 'error',
        'message': 'حدث خطأ أثناء تحديث بيانات الملف الشخصي'
      };
    }
  }

  /**
   * Fetch Finance Invoices / Payments
   */
  static Future<Map<String, dynamic>> getInvoices() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/finance/invoices'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {
          'status': 'success',
          'invoices': decoded['invoices'] ?? []
        };
      }
      return {
        'status': 'error',
        'message': decoded['message'] ?? 'فشل تحميل قائمة الفواتير والمدفوعات'
      };
    } catch (e) {
      return {
        'status': 'error',
        'message': 'حدث خطأ أثناء الاتصال بالخادم لتحميل البيانات المالية'
      };
    }
  }

  /**
   * Fetch Marks Registration Filters (Classes, Sections, Subjects)
   */
  static Future<Map<String, dynamic>> getMarksOptions() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/academic/marks/options'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {
          'status': 'success',
          'classes': decoded['classes'] ?? [],
          'sections': decoded['sections'] ?? [],
          'subjects': decoded['subjects'] ?? [],
          'current_year': decoded['current_year'] ?? '',
        };
      }
      return {
        'status': 'error',
        'message': decoded['message'] ?? 'فشل تحميل فلاتر رصد الدرجات'
      };
    } catch (e) {
      return {
        'status': 'error',
        'message': 'حدث خطأ في الاتصال بالخادم أثناء تحميل خيارات الرصد'
      };
    }
  }

  /**
   * Fetch Students and Distribution details for a specific subject, class, and section
   */
  static Future<Map<String, dynamic>> getMarksList(
      dynamic classId, dynamic sectionId, dynamic subjectId) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/academic/marks/list?class_id=$classId&section_id=$sectionId&subject_id=$subjectId'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {
          'status': 'success',
          'distribution': decoded['distribution'] ?? [],
          'students': decoded['students'] ?? [],
        };
      }
      return {
        'status': 'error',
        'message': decoded['message'] ?? 'فشل تحميل قائمة الطلاب والدرجات'
      };
    } catch (e) {
      return {
        'status': 'error',
        'message': 'حدث خطأ في الاتصال بالخادم أثناء جلب قائمة الطلاب'
      };
    }
  }

  /**
   * Submit student marks details to the backend database
   */
  static Future<Map<String, dynamic>> saveMarks({
    required dynamic classId,
    required dynamic sectionId,
    required dynamic subjectId,
    required List<Map<String, dynamic>> marks,
  }) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final body = jsonEncode({
        'class_id': classId,
        'section_id': sectionId,
        'subject_id': subjectId,
        'marks': marks,
      });

      final response = await http.post(
        Uri.parse('$url/academic/marks/save'),
        headers: headers,
        body: body,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {
          'status': 'success',
          'message': decoded['message'] ?? 'تم رصد وحفظ الدرجات بنجاح',
        };
      }
      return {
        'status': 'error',
        'message': decoded['message'] ?? 'فشل حفظ ورصد الدرجات',
      };
    } catch (e) {
      return {
        'status': 'error',
        'message': 'حدث خطأ في الاتصال بالخادم أثناء عملية حفظ الدرجات',
      };
    }
  }

  /**
   * Upload a profile photo for a specific student using multipart request
   */
  static Future<Map<String, dynamic>> uploadStudentPhoto(int studentId, String filePath) async {
    try {
      final url = await getBaseUrl();
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('api_token') ?? '';

      final request = http.MultipartRequest(
        'POST',
        Uri.parse('$url/students/upload_photo'),
      );

      // Add authorization header
      request.headers['Authorization'] = 'Bearer $token';

      // Add fields
      request.fields['student_id'] = studentId.toString();

      // Add image file
      request.files.add(await http.MultipartFile.fromPath('photo', filePath));

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {
          'status': 'success',
          'message': decoded['message'] ?? 'تم حفظ الصورة بنجاح',
          'image_url': decoded['image_url'] ?? '',
        };
      }
      return {
        'status': 'error',
        'message': decoded['message'] ?? 'فشل تحميل الصورة',
      };
    } catch (e) {
      return {
        'status': 'error',
        'message': 'حدث خطأ في الاتصال بالخادم أثناء رفع الصورة',
      };
    }
  }

  /**
   * Super Admin: Fetch Cities Catalog
   */
  static Future<Map<String, dynamic>> getCities() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/superadmin/cities'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'cities': decoded['cities'] ?? []};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحميل قائمة المدن'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لجلب المدن'};
    }
  }

  /**
   * Super Admin: Create City
   */
  static Future<Map<String, dynamic>> createCity(String name) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/cities/create'),
        headers: headers,
        body: jsonEncode({'name': name}),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل إضافة المدينة'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لإضافة المدينة'};
    }
  }

  /**
   * Super Admin: Update City
   */
  static Future<Map<String, dynamic>> updateCity(int id, String name) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/cities/edit/$id'),
        headers: headers,
        body: jsonEncode({'name': name}),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحديث المدينة'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لتحديث المدينة'};
    }
  }

  /**
   * Super Admin: Delete City
   */
  static Future<Map<String, dynamic>> deleteCity(int id) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/cities/delete/$id'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل حذف المدينة'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لحذف المدينة'};
    }
  }

  /**
   * Super Admin: Fetch Schools Catalog
   */
  static Future<Map<String, dynamic>> getSchools() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/superadmin/schools'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'schools': decoded['schools'] ?? []};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحميل قائمة المدارس'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لجلب المدارس'};
    }
  }

  /**
   * Super Admin: Create School
   */
  static Future<Map<String, dynamic>> createSchool({
    required String name,
    required int? city,
    required String address,
    required String email,
    required String year,
    required String manager,
    required String examsManager,
  }) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/schools/create'),
        headers: headers,
        body: jsonEncode({
          'name': name,
          'city': city,
          'address': address,
          'email': email,
          'year': year,
          'manager': manager,
          'exams_manager': examsManager,
        }),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل إضافة المدرسة'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لإضافة المدرسة'};
    }
  }

  /**
   * Super Admin: Update School
   */
  static Future<Map<String, dynamic>> updateSchool({
    required int id,
    required String name,
    required int? city,
    required String address,
    required String email,
    required String year,
    required String manager,
    required String examsManager,
  }) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/schools/edit/$id'),
        headers: headers,
        body: jsonEncode({
          'name': name,
          'city': city,
          'address': address,
          'email': email,
          'year': year,
          'manager': manager,
          'exams_manager': examsManager,
        }),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحديث المدرسة'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لتحديث المدرسة'};
    }
  }

  /**
   * Super Admin: Delete School
   */
  static Future<Map<String, dynamic>> deleteSchool(int id) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/schools/delete/$id'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل حذف المدرسة'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لحذف المدرسة'};
    }
  }

  /**
   * Super Admin: Fetch School Admins
   */
  static Future<Map<String, dynamic>> getAdmins() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/superadmin/admins'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'admins': decoded['admins'] ?? []};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحميل قائمة المسؤولين'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لجلب المسؤولين'};
    }
  }

  /**
   * Super Admin: Create School Admin
   */
  static Future<Map<String, dynamic>> createAdmin({
    required String name,
    required int? school,
    required String username,
    required String password,
    required String phone,
    required String email,
  }) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/admins/create'),
        headers: headers,
        body: jsonEncode({
          'name': name,
          'school': school,
          'username': username,
          'password': password,
          'phone': phone,
          'email': email,
        }),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل إضافة المسؤول'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لإضافة المسؤول'};
    }
  }

  /**
   * Super Admin: Update School Admin
   */
  static Future<Map<String, dynamic>> updateAdmin({
    required int id,
    required String name,
    required int? school,
    required String username,
    required String password,
    required String phone,
    required String email,
  }) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/admins/edit/$id'),
        headers: headers,
        body: jsonEncode({
          'name': name,
          'school': school,
          'username': username,
          'password': password,
          'phone': phone,
          'email': email,
        }),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحديث المسؤول'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لتحديث المسؤول'};
    }
  }

  /**
   * Super Admin: Delete School Admin
   */
  static Future<Map<String, dynamic>> deleteAdmin(int id) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/admins/delete/$id'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل حذف المسؤول'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لحذف المسؤول'};
    }
  }

  /**
   * Super Admin: Fetch System Settings
   */
  static Future<Map<String, dynamic>> getSettings() async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.get(
        Uri.parse('$url/superadmin/settings'),
        headers: headers,
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'settings': decoded['settings'] ?? []};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحميل قائمة الإعدادات'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لجلب الإعدادات'};
    }
  }

  /**
   * Super Admin: Update System Settings
   */
  static Future<Map<String, dynamic>> updateSettings(Map<String, String> settings) async {
    try {
      final url = await getBaseUrl();
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('$url/superadmin/settings/update'),
        headers: headers,
        body: jsonEncode({'settings': settings}),
      );

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['status'] == 'success') {
        return {'status': 'success', 'message': decoded['message']};
      }
      return {'status': 'error', 'message': decoded['message'] ?? 'فشل تحديث الإعدادات'};
    } catch (e) {
      return {'status': 'error', 'message': 'حدث خطأ في الاتصال بالخادم لتحديث الإعدادات'};
    }
  }
}

