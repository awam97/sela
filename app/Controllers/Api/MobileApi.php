<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class MobileApi extends Controller
{
    private $db;
    private $tokenSecret = 'sela_secure_api_key_2026';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();

        // 1. Enable standard CORS headers
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
        
        // Handle preflight OPTIONS requests immediately
        if ($this->request->getMethod() === 'options') {
            die();
        }
    }

    /**
     * Helper: Cryptographic token validation
     */
    private function authenticateToken()
    {
        $authHeader = $this->request->getServer('HTTP_AUTHORIZATION') ?? $this->request->header('Authorization');
        if (!$authHeader) {
            return false;
        }

        $authHeaderString = is_object($authHeader) ? $authHeader->getValue() : $authHeader;
        if (preg_match('/Bearer\s+(.*)$/i', $authHeaderString, $matches)) {
            $token = $matches[1];
            $parts = explode(':', $token);
            if (count($parts) === 3) {
                list($userId, $role, $signature) = $parts;
                $expectedSignature = hash_hmac('sha256', $userId . ':' . $role, $this->tokenSecret);
                if ($signature === $expectedSignature) {
                    return [
                        'user_id' => $userId,
                        'role' => $role
                    ];
                }
            }
        }
        return false;
    }

    /**
     * API Status Ping
     * GET /api
     */
    public function index()
    {
        return $this->response->setJSON([
            'status' => 'online',
            'system' => 'منصة صلة (Sela Platform)',
            'version' => '1.0.0',
            'api_status' => 'Active & Secure',
            'message' => 'مرحباً بك في واجهة برمجة تطبيقات منصة صلة'
        ]);
    }

    /**
     * Authentication Endpoint
     * POST /api/login
     */
    public function login()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        if (empty($username) || empty($password)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'اسم المستخدم وكلمة المرور مطلوبان'
            ])->setStatusCode(400);
        }

        // Fetch system settings to check if OTP is enabled globally
        $settingsRows = $this->db->table('settings')->get()->getResultArray();
        $settings = [];
        $waSetting = strtolower($settings['whatsapp_otp_enabled'] ?? 'true');
        $waEnabled = true; // Secure OTP verification is always enforced on Sela Mobile App

        // 1. Check Super Admin Table
        $super = $this->db->table('super')->where('username', $username)->get()->getRowArray();
        if ($super) {
            $authenticated = false;
            $needUpgrade = false;

            if (password_verify($password, $super['password'])) {
                $authenticated = true;
            } elseif ($password === $super['password']) {
                $authenticated = true;
                $needUpgrade = true;
            }

            if ($authenticated) {
                if ($needUpgrade) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $this->db->table('super')->where('super_id', $super['super_id'])->update(['password' => $newHash]);
                }

                // If OTP is enabled globally, start dynamic verification flow
                if ($waEnabled) {
                    $phone = $super['phone'] ?? '';
                    $email = $super['email'] ?? '';
                    
                    $maskedPhone = empty($phone) ? '' : substr($phone, 0, 4) . '****' . substr($phone, -4);
                    $maskedEmail = empty($email) ? '' : substr($email, 0, 3) . '****' . substr($email, strpos($email, '@') - 2);

                    // Generate secure transient token (valid for 5 minutes)
                    $tempPayload = json_encode([
                        'user_id' => (int)$super['super_id'],
                        'role' => 'super_admin',
                        'expiry' => time() + 300
                    ]);
                    $tempSignature = hash_hmac('sha256', $tempPayload, $this->tokenSecret);
                    $tempToken = base64_encode($tempPayload) . '.' . $tempSignature;

                    return $this->response->setJSON([
                        'status' => 'otp_required',
                        'temp_token' => $tempToken,
                        'phone' => $maskedPhone,
                        'email' => $maskedEmail,
                        'wa_enabled' => true
                    ]);
                }

                $token = $super['super_id'] . ':super_admin:' . hash_hmac('sha256', $super['super_id'] . ':super_admin', $this->tokenSecret);
                return $this->response->setJSON([
                    'status' => 'success',
                    'token' => $token,
                    'user' => [
                        'id' => (int)$super['super_id'],
                        'username' => $super['username'],
                        'role' => 'super_admin',
                        'name' => 'المدير العام للمنصة',
                        'school_id' => null
                    ]
                ]);
            }
        }

        // 2. Check Standard Admin Table
        $admin = $this->db->table('admin')->where('username', $username)->get()->getRowArray();
        if ($admin) {
            $authenticated = false;
            $needUpgrade = false;

            if (password_verify($password, $admin['password'])) {
                $authenticated = true;
            } elseif ($password === $admin['password']) {
                $authenticated = true;
                $needUpgrade = true;
            }

            if ($authenticated) {
                if ($needUpgrade) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $this->db->table('admin')->where('admin_id', $admin['admin_id'])->update(['password' => $newHash]);
                }

                // If OTP is enabled globally, start dynamic verification flow
                if ($waEnabled) {
                    $phone = $admin['phone'] ?? '';
                    $email = $admin['email'] ?? '';
                    
                    $maskedPhone = empty($phone) ? '' : substr($phone, 0, 4) . '****' . substr($phone, -4);
                    $maskedEmail = empty($email) ? '' : substr($email, 0, 3) . '****' . substr($email, strpos($email, '@') - 2);

                    // Generate secure transient token (valid for 5 minutes)
                    $tempPayload = json_encode([
                        'user_id' => (int)$admin['admin_id'],
                        'role' => 'admin',
                        'expiry' => time() + 300
                    ]);
                    $tempSignature = hash_hmac('sha256', $tempPayload, $this->tokenSecret);
                    $tempToken = base64_encode($tempPayload) . '.' . $tempSignature;

                    return $this->response->setJSON([
                        'status' => 'otp_required',
                        'temp_token' => $tempToken,
                        'phone' => $maskedPhone,
                        'email' => $maskedEmail,
                        'wa_enabled' => true
                    ]);
                }

                $token = $admin['admin_id'] . ':admin:' . hash_hmac('sha256', $admin['admin_id'] . ':admin', $this->tokenSecret);
                
                // Get school details
                $school = $this->db->table('schools')->where('ID', $admin['school'])->get()->getRowArray();

                return $this->response->setJSON([
                    'status' => 'success',
                    'token' => $token,
                    'user' => [
                        'id' => (int)$admin['admin_id'],
                        'username' => $admin['username'],
                        'role' => 'admin',
                        'name' => $admin['name'] ?? 'مدير المدرسة',
                        'school_id' => (int)$admin['school'],
                        'school_name' => $school ? $school['name'] : 'مدرسة السيلا'
                    ]
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'بيانات الدخول غير صحيحة'
        ])->setStatusCode(401);
    }

    /**
     * Dispatch OTP Code
     * POST /api/send-otp
     */
    public function sendOtp()
    {
        $tempToken = $this->request->getVar('temp_token');
        $method = $this->request->getVar('method') ?? 'whatsapp'; // 'whatsapp' or 'email'

        if (empty($tempToken)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'رمز المعاملة الموقت مفقود'])->setStatusCode(400);
        }

        // Validate temporary token
        list($payloadBase64, $signature) = explode('.', $tempToken);
        $payload = base64_decode($payloadBase64);
        $expectedSignature = hash_hmac('sha256', $payload, $this->tokenSecret);
        if ($signature !== $expectedSignature) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'رمز غير صالح أو تم التلاعب به'])->setStatusCode(400);
        }

        $tempData = json_decode($payload, true);
        if (time() > $tempData['expiry']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'انتهت صلاحية جلسة تسجيل الدخول المؤقتة'])->setStatusCode(400);
        }

        $userId = $tempData['user_id'];
        $role = $tempData['role'];

        // Get user details
        if ($role === 'super_admin') {
            $user = $this->db->table('super')->where('super_id', $userId)->get()->getRowArray();
        } else {
            $user = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
        }

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'المستخدم غير موجود'])->setStatusCode(404);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Send OTP via chosen channel
        if ($method === 'whatsapp') {
            $phone = $user['phone'] ?? '';
            if (empty($phone)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'رقم الهاتف الخاص بك غير مسجل بالنظام'])->setStatusCode(400);
            }
            $this->sendOtpWhatsApp($phone, $otp);
        } else {
            $email = $user['email'] ?? '';
            if (empty($email)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'بريدك الإلكتروني غير مسجل بالنظام'])->setStatusCode(400);
            }
            if (!$this->sendOtpEmail($email, $otp)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'فشل إرسال البريد الإلكتروني. يرجى مراجعة إعدادات SMTP.'])->setStatusCode(500);
            }
        }

        // Generate secure Dynamic OTP Verification Token (valid for 10 minutes)
        $verifyPayload = json_encode([
            'user_id' => $userId,
            'role' => $role,
            'otp' => $otp,
            'expiry' => time() + 600
        ]);
        $verifySignature = hash_hmac('sha256', $verifyPayload, $this->tokenSecret);
        $verifyToken = base64_encode($verifyPayload) . '.' . $verifySignature;

        return $this->response->setJSON([
            'status' => 'success',
            'otp_token' => $verifyToken,
            'message' => 'تم إرسال رمز التحقق بنجاح!'
        ]);
    }

    /**
     * Verify OTP and Login
     * POST /api/verify-otp
     */
    public function verifyOtp()
    {
        $otpToken = $this->request->getVar('otp_token');
        $code = $this->request->getVar('code');

        if (empty($otpToken) || empty($code)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'الرمز والتوكن مطلوبان'])->setStatusCode(400);
        }

        // Validate OTP verification token
        list($payloadBase64, $signature) = explode('.', $otpToken);
        $payload = base64_decode($payloadBase64);
        $expectedSignature = hash_hmac('sha256', $payload, $this->tokenSecret);
        if ($signature !== $expectedSignature) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'رمز التحقق غير صالح أو تم تعديله'])->setStatusCode(400);
        }

        $verifyData = json_decode($payload, true);
        if (time() > $verifyData['expiry']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'انتهت صلاحية رمز التحقق (10 دقائق)'])->setStatusCode(400);
        }

        // Check if code matches
        if ((string)$code !== (string)$verifyData['otp']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'رمز التحقق غير صحيح'])->setStatusCode(401);
        }

        $userId = $verifyData['user_id'];
        $role = $verifyData['role'];

        // Get user profile details
        if ($role === 'super_admin') {
            $super = $this->db->table('super')->where('super_id', $userId)->get()->getRowArray();
            if (!$super) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'المستخدم غير موجود'])->setStatusCode(404);
            }

            $token = $super['super_id'] . ':super_admin:' . hash_hmac('sha256', $super['super_id'] . ':super_admin', $this->tokenSecret);
            return $this->response->setJSON([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => (int)$super['super_id'],
                    'username' => $super['username'],
                    'role' => 'super_admin',
                    'name' => 'المدير العام للمنصة',
                    'school_id' => null
                ]
            ]);
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            if (!$admin) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'المستخدم غير موجود'])->setStatusCode(404);
            }

            $token = $admin['admin_id'] . ':admin:' . hash_hmac('sha256', $admin['admin_id'] . ':admin', $this->tokenSecret);
            
            // Get school details
            $school = $this->db->table('schools')->where('ID', $admin['school'])->get()->getRowArray();

            return $this->response->setJSON([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => (int)$admin['admin_id'],
                    'username' => $admin['username'],
                    'role' => 'admin',
                    'name' => $admin['name'] ?? 'مدير المدرسة',
                    'school_id' => (int)$admin['school'],
                    'school_name' => $school ? $school['name'] : 'مدرسة السيلا'
                ]
            ]);
        }
    }

    /**
     * Send OTP via WhatsApp
     */
    private function sendOtpWhatsApp($phone, $otp)
    {
        $apiKey = $this->db->table('settings')->where('type', 'textmebot_api_key')->get()->getRow()->description ?? '';
        $system_name = $this->db->table('settings')->where('type', 'system_name')->get()->getRow()->description ?? 'منصة صلة';

        if (empty($apiKey)) return;

        $text = urlencode("*{$system_name}*\n\nرمز التحقق الخاص بك للدخول هو: *{$otp}*\n\nهذا الرمز صالح لمدة 10 دقائق فقط. يرجى عدم مشاركته مع أحد.");
        $recipient = urlencode($phone);
        $url = "https://api.textmebot.com/send.php?recipient={$recipient}&apikey={$apiKey}&text={$text}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Send OTP via Email
     */
    private function sendOtpEmail($to, $otp)
    {
        $settings = $this->db->table('settings')->get()->getResultArray();
        $config = [];
        foreach ($settings as $row) {
            $config[$row['type']] = $row['description'];
        }

        $email = \Config\Services::email();

        $smtp_port = (int)($config['smtp_port'] ?? 587);
        $smtp_crypto = $config['smtp_crypto'] ?? 'tls';

        if ($smtp_port === 587 && strtolower($smtp_crypto) === 'ssl') {
            $smtp_crypto = 'tls';
        } elseif ($smtp_port === 465 && strtolower($smtp_crypto) === 'tls') {
            $smtp_crypto = 'ssl';
        }

        $smtp_config = [
            'protocol' => 'smtp',
            'SMTPHost' => $config['smtp_host'] ?? '',
            'SMTPUser' => $config['smtp_user'] ?? '',
            'SMTPPass' => $config['smtp_pass'] ?? '',
            'SMTPPort' => $smtp_port,
            'SMTPCrypto' => $smtp_crypto,
            'mailType' => 'html',
            'charset'  => 'utf-8',
            'newline'  => "\r\n",
            'CRLF'     => "\r\n"
        ];

        $email->initialize($smtp_config);
        $email->setFrom($config['mail_from'] ?? 'no-reply@sela.ly', $config['system_name'] ?? 'Sela Platform');
        $email->setTo($to);
        $email->setSubject('رمز التحقق للدخول - ' . ($config['system_name'] ?? 'صلة'));
        
        $message = "
            <!DOCTYPE html>
            <html lang='ar' dir='rtl'>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { background-color: #f4f7f9; margin: 0; padding: 0; font-family: Arial, sans-serif; }
                    .email-wrapper { width: 100%; padding: 40px 0; background-color: #f4f7f9; }
                    .email-card { max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
                    .header { background: #192A56; padding: 40px 20px; text-align: center; }
                    .body { padding: 40px; text-align: center; }
                    .title { font-size: 24px; font-weight: 800; color: #192A56; margin-bottom: 10px; }
                    .subtitle { font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 30px; }
                    .otp-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 25px; margin-bottom: 30px; }
                    .otp-code { font-size: 42px; font-weight: 900; color: #C5A021; letter-spacing: 10px; text-shadow: 1px 1px 0 #fff; }
                    .expiry { font-size: 13px; color: #94a3b8; font-weight: 600; }
                    .footer { padding: 30px; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; font-size: 12px; color: #94a3b8; }
                </style>
            </head>
            <body>
                <div class='email-wrapper'>
                    <div class='email-card'>
                        <div class='header'>
                            <h2 style='color:#C5A021; margin:0; font-size:28px;'>" . ($config['system_name'] ?? 'صلة') . "</h2>
                        </div>
                        <div class='body'>
                            <div class='title'>تحقق من هويتك</div>
                            <div class='subtitle'>أهلاً بك مجدداً. استخدم الرمز التالي لإكمال عملية تسجيل الدخول بأمان إلى حسابك.</div>
                            
                            <div class='otp-box'>
                                <div class='otp-code'>{$otp}</div>
                            </div>
                            
                            <div class='expiry'>هذا الرمز صالح لمدة 10 دقائق فقط.</div>
                        </div>
                        <div class='footer'>
                            جميع الحقوق محفوظة &copy; " . date('Y') . " " . ($config['system_name'] ?? 'منصة صلة') . "
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ";

        $email->setMessage($message);
        
        if (!$email->send()) {
            log_message('error', 'OTP Email Error: ' . $email->printDebugger(['headers']));
            return false;
        }
        
        return true;
    }

    /**
     * Dashboard Overview Statistics
     * GET /api/dashboard
     */
    public function dashboard()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $role = $session['role'];
        $userId = $session['user_id'];

        $data = [];

        if ($role === 'super_admin') {
            // Global aggregates
            $data['total_schools'] = $this->db->table('schools')->countAllResults();
            $data['total_students'] = $this->db->table('student')->countAllResults();
            $data['total_teachers'] = $this->db->table('teacher')->countAllResults();
            $data['pending_registrations'] = $this->db->table('registration_requests')->where('status', 'pending')->countAllResults();
            
            // Custom descriptive details
            $data['title'] = 'لوحة التحكم للمدير العام';
            $data['welcome_message'] = 'مرحباً بك في لوحة تحكم منصة السيلا';
        } else {
            // Admin specific details
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'];

            $data['total_students'] = $this->db->table('student')->where('school', $schoolId)->countAllResults();
            $data['total_teachers'] = $this->db->table('teacher')->countAllResults(); // Teachers are shared or default count
            $data['total_classes'] = $this->db->table('class')->where('school', $schoolId)->countAllResults();
            $data['pending_registrations'] = $this->db->table('registration_requests')->where('school_id', $schoolId)->where('status', 'pending')->countAllResults();
            
            $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
            $data['title'] = $school ? $school['name'] : 'لوحة تحكم المدرسة';
            $data['welcome_message'] = 'أهلاً بك مجدداً في إدارة المدرسة';
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Searchable Students Catalog
     * GET /api/students
     */
    public function students()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $role = $session['role'];
        $userId = $session['user_id'];

        // Determine school ID and current year
        if ($role === 'super_admin') {
            $schoolId = $this->request->getVar('school_id'); // Optionally filter by school
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'];
        }

        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        // Fetch students via enroll table mapping
        $builder = $this->db->table('student s')
            ->select('s.student_id, s.name, s.phone, s.sex, s.activate, c.name as class_name, c.class_id')
            ->join('enroll e', 'e.student_id = s.student_id', 'inner')
            ->join('class c', 'c.class_id = e.class_id', 'left')
            ->where('e.year', $currentYear);

        if ($schoolId) {
            $builder->where('s.school', $schoolId);
        }

        $students = $builder->orderBy('s.name', 'ASC')->get()->getResultArray();

        // Fetch classes to help mobile categorical listing
        $classBuilder = $this->db->table('class');
        if ($schoolId) {
            $classBuilder->where('school', $schoolId);
        }
        $classes = $classBuilder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'classes' => $classes,
            'students' => $students
        ]);
    }

    /**
     * Take/Save Student Attendance
     * POST /api/attendance/save
     */
    public function saveAttendance()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $classId = $this->request->getVar('class_id');
        $sectionId = $this->request->getVar('section_id') ?? 1;
        $date = $this->request->getVar('date') ?? date('Y-m-d');
        $records = $this->request->getVar('records'); // Expected array of [student_id => status_code] (1 = present, 2 = absent, 3 = late)

        if (!$classId || empty($records)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'بيانات الحضور غير مكتملة'
            ])->setStatusCode(400);
        }

        // Parse records if sent as stringified JSON
        if (is_string($records)) {
            $records = json_decode($records, true);
        }

        $this->db->transStart();

        foreach ($records as $studentId => $status) {
            // Check if attendance record already exists for this date and student
            $existing = $this->db->table('attendance')
                ->where('student_id', $studentId)
                ->where('date', $date)
                ->get()
                ->getRowArray();

            $attendanceData = [
                'student_id' => $studentId,
                'class_id'   => $classId,
                'section_id' => $sectionId,
                'status'     => $status, // 1 = present, 2 = absent, 3 = late
                'date'       => $date
            ];

            if ($existing) {
                $this->db->table('attendance')
                    ->where('attendance_id', $existing['attendance_id'])
                    ->update($attendanceData);
            } else {
                $this->db->table('attendance')->insert($attendanceData);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'فشلت عملية حفظ الحضور والغياب'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'تم حفظ سجل الحضور والغياب بنجاح'
        ]);
    }

    /**
     * Pending Registration Requests
     * GET /api/registrations
     */
    public function registrations()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $role = $session['role'];
        $userId = $session['user_id'];

        $builder = $this->db->table('registration_requests r')
            ->select('r.*, c.name as class_name, s.name as school_name')
            ->join('class c', 'c.class_id = r.class_id', 'left')
            ->join('schools s', 's.ID = r.school_id', 'left')
            ->where('r.status', 'pending');

        if ($role === 'admin') {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $builder->where('r.school_id', $admin['school']);
        }

        $requests = $builder->orderBy('r.created_at', 'DESC')->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'requests' => $requests
        ]);
    }

    /**
     * Approve Registration Request
     * POST /api/registrations/approve/(:num)
     */
    public function approveRegistration($id)
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $request = $this->db->table('registration_requests')->where('id', $id)->get()->getRowArray();
        if (!$request) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'طلب الالتحاق غير موجود'])->setStatusCode(404);
        }

        // Security check for standard school administrators
        if ($session['role'] === 'admin') {
            $admin = $this->db->table('admin')->where('admin_id', $session['user_id'])->get()->getRowArray();
            if ((int)$request['school_id'] !== (int)$admin['school']) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك باعتماد هذا الطالب'])->setStatusCode(403);
            }
        }

        // Fetch school current scholastic year
        $school = $this->db->table('schools')->where('ID', $request['school_id'])->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        $this->db->transStart();

        // 1. Create Student record
        $studentData = [
            'name'      => $request['name'],
            'phone'     => $request['phone'],
            'sex'       => $request['sex'],
            'username'  => $request['phone'], // Default username is phone number
            'password'  => $request['phone'], // Default password is phone number
            'school'    => $request['school_id'],
            'class_id'  => $request['class_id'],
            'section_id'=> 1, // Section A by default
            'parent_id' => 0,
            'activate'  => 1,
            'activate_date' => date('Y-m-d')
        ];
        
        $this->db->table('student')->insert($studentData);
        $studentId = $this->db->insertID();

        // 2. Create Enrollment record
        $enrollData = [
            'student_id' => $studentId,
            'class_id'   => $request['class_id'],
            'section_id' => 1,
            'school'     => $request['school_id'],
            'year'       => $currentYear
        ];
        $this->db->table('enroll')->insert($enrollData);

        // 3. Mark request as approved
        $this->db->table('registration_requests')->where('id', $id)->update(['status' => 'approved']);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'فشلت عملية الاعتماد'])->setStatusCode(500);
        }

        // 4. Send notification via WhatsApp
        $this->notifyStudent($request['phone'], $request['name']);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'تم اعتماد الطالب بنجاح، وإرسال رسالة ترحيبية وتفاصيل الدخول عبر الواتساب'
        ]);
    }

    /**
     * Reject Registration Request
     * POST /api/registrations/reject/(:num)
     */
    public function rejectRegistration($id)
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $request = $this->db->table('registration_requests')->where('id', $id)->get()->getRowArray();
        if (!$request) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'طلب الالتحاق غير موجود'])->setStatusCode(404);
        }

        // Security check for standard administrators
        if ($session['role'] === 'admin') {
            $admin = $this->db->table('admin')->where('admin_id', $session['user_id'])->get()->getRowArray();
            if ((int)$request['school_id'] !== (int)$admin['school']) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالتحكم في هذا الطلب'])->setStatusCode(403);
            }
        }

        if ($this->db->table('registration_requests')->where('id', $id)->update(['status' => 'rejected'])) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'تم رفض طلب الالتحاق بنجاح'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'حدث خطأ أثناء رفض الطلب'
        ])->setStatusCode(500);
    }

    /**
     * Helper: WhatsApp Notification Sender
     */
    private function notifyStudent($phone, $name)
    {
        $apiKey = $this->db->table('settings')->where('type', 'textmebot_api_key')->get()->getRow()->description ?? '';
        if (empty($apiKey)) return;

        $text = urlencode("أهلاً {$name}، تم قبول طلب انضمامك لمنصتنا بنجاح. يمكنك الآن الدخول باستخدام رقم هاتفك كاسم مستخدم وكلمة مرور.");
        $url = "https://api.textmebot.com/send.php?recipient={$phone}&apikey={$apiKey}&text={$text}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Create Student
     * POST /api/students/create
     */
    public function createStudent()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $role = $session['role'];
        $userId = $session['user_id'];

        if ($role === 'super_admin') {
            $schoolId = $this->request->getVar('school_id');
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'];
        }

        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        $name = $this->request->getVar('name');
        $phone = $this->request->getVar('phone');
        $sex = $this->request->getVar('sex') ?: 'male';
        $classId = $this->request->getVar('class_id');

        if (empty($name) || empty($classId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'الاسم والصف الدراسي مطلوبان']);
        }

        $studentData = [
            'name' => $name,
            'phone' => $phone,
            'sex' => $sex,
            'username' => $phone ?: $name,
            'password' => $phone ?: '123456',
            'school' => $schoolId,
            'activate' => 1,
            'parent_id' => 0
        ];

        $this->db->transStart();
        $this->db->table('student')->insert($studentData);
        $studentId = $this->db->insertID();

        $enrollData = [
            'student_id' => $studentId,
            'class_id' => $classId,
            'section_id' => 1, // Default Section A
            'year' => $currentYear,
            'school' => $schoolId
        ];
        $this->db->table('enroll')->insert($enrollData);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'فشل إضافة الطالب في قاعدة البيانات']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'تم إضافة الطالب بنجاح']);
    }

    /**
     * Edit Student
     * POST /api/students/edit/(:num)
     */
    public function editStudent($id)
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $name = $this->request->getVar('name');
        $phone = $this->request->getVar('phone');
        $sex = $this->request->getVar('sex');
        $classId = $this->request->getVar('class_id');

        if (empty($name) || empty($classId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'الاسم والصف الدراسي مطلوبان']);
        }

        $studentData = [
            'name' => $name,
            'phone' => $phone,
            'sex' => $sex
        ];

        $this->db->transStart();
        $this->db->table('student')->where('student_id', $id)->update($studentData);
        $this->db->table('enroll')->where('student_id', $id)->update(['class_id' => $classId]);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'فشل تحديث بيانات الطالب']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'تم تحديث بيانات الطالب بنجاح']);
    }

    /**
     * Delete Student
     * POST /api/students/delete/(:num)
     */
    public function deleteStudent($id)
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $this->db->transStart();
        $this->db->table('student')->where('student_id', $id)->delete();
        $this->db->table('enroll')->where('student_id', $id)->delete();
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'فشل حذف الطالب من قاعدة البيانات']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'تم حذف الطالب بنجاح']);
    }

    /**
     * Searchable Subjects Catalog
     * GET /api/subjects
     */
    public function subjects()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $role = $session['role'];
        $userId = $session['user_id'];

        // Determine school ID
        if ($role === 'super_admin') {
            $schoolId = $this->request->getVar('school_id'); // Optionally filter by school
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'];
        }

        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        // Fetch subjects via subject table mapping
        $builder = $this->db->table('subject sub')
            ->select('sub.subject_id, sub.name, sub.total_mark, sub.pass_mark, c.name as class_name, c.class_id, t.name as teacher_name')
            ->join('class c', 'c.class_id = sub.class_id', 'left')
            ->join('teacher t', 't.teacher_id = sub.teacher_id', 'left');

        if ($schoolId) {
            $builder->where('sub.school', $schoolId);
        }

        $subjects = $builder->orderBy('sub.name', 'ASC')->get()->getResultArray();

        // Fetch classes to help mobile categorical listing
        $classBuilder = $this->db->table('class');
        if ($schoolId) {
            $classBuilder->where('school', $schoolId);
        }
        $classes = $classBuilder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'classes' => $classes,
            'subjects' => $subjects
        ]);
    }
}

