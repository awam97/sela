import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import 'package:flutter/foundation.dart';

class ApiService {
  static const String _baseUrl = 'https://graya.ly/api';

  /**
   * Fetch Sela's hardcoded online web API URL
   */
  static Future<String> getBaseUrl() async {
    return _baseUrl;
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
          'message': decoded['message'] ?? 'بيانات الدخول غير صحيحة'
        };
      }
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
          'classes': decoded['classes'] ?? []
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
   * Clear Session and Logout
   */
  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
  }
}

