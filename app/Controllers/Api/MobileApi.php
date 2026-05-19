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
        // Bulletproof native PHP parsing: Support both raw JSON payloads and standard POST parameters without framework exception risks
        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);
        if (!is_array($json)) {
            $json = [];
        }

        $username = $json['username'] ?? $this->request->getPost('username') ?? $_REQUEST['username'] ?? '';
        $password = $json['password'] ?? $this->request->getPost('password') ?? $_REQUEST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'اسم المستخدم وكلمة المرور مطلوبان'
            ])->setStatusCode(400);
        }

        // Fetch system settings to check if OTP is enabled globally
        $settingsRows = $this->db->table('settings')->get()->getResultArray();
        $settings = [];
        foreach ($settingsRows as $row) {
            $settings[$row['type']] = $row['description'];
        }
        $waSetting = strtolower($settings['whatsapp_otp_enabled'] ?? 'true');
        $waEnabled = in_array($waSetting, ['true', '1', 'on', 'yes']);

        // 1. Check Super Admin Table (Strictly block super_admin from accessing mobile app)
        $super = $this->db->table('super')->where('username', $username)->get()->getRowArray();
        if ($super) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'غير مصرح لمدير المنصة العام باستخدام تطبيق الهاتف المحمول.'
            ])->setStatusCode(403);
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

        // 3. Check Teacher Table (dynamically schema-aware to prevent missing column exceptions)
        $teacherFields = $this->db->getFieldNames('teacher');
        $hasEmail = in_array('email', $teacherFields);
        $hasPhone = in_array('phone', $teacherFields);

        $teacher = null;
        if ($hasEmail || $hasPhone) {
            $teacherBuilder = $this->db->table('teacher')->groupStart();
            if ($hasEmail) {
                $teacherBuilder->where('email', $username);
            }
            if ($hasPhone) {
                if ($hasEmail) {
                    $teacherBuilder->orWhere('phone', $username);
                } else {
                    $teacherBuilder->where('phone', $username);
                }
            }
            $teacher = $teacherBuilder->groupEnd()->get()->getRowArray();
        }

        if ($teacher) {
            $authenticated = false;
            $needUpgrade = false;

            if (password_verify($password, $teacher['password'])) {
                $authenticated = true;
            } elseif ($password === $teacher['password']) {
                $authenticated = true;
                $needUpgrade = true;
            }

            if ($authenticated) {
                if ($needUpgrade) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $this->db->table('teacher')->where('teacher_id', $teacher['teacher_id'])->update(['password' => $newHash]);
                }

                // If OTP is enabled globally, start dynamic verification flow
                if ($waEnabled) {
                    $phone = $teacher['phone'] ?? '';
                    $email = $teacher['email'] ?? '';
                    
                    $maskedPhone = empty($phone) ? '' : substr($phone, 0, 4) . '****' . substr($phone, -4);
                    $maskedEmail = empty($email) ? '' : substr($email, 0, 3) . '****' . substr($email, strpos($email, '@') - 2);

                    // Generate secure transient token (valid for 5 minutes)
                    $tempPayload = json_encode([
                        'user_id' => (int)$teacher['teacher_id'],
                        'role' => 'teacher',
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

                $token = $teacher['teacher_id'] . ':teacher:' . hash_hmac('sha256', $teacher['teacher_id'] . ':teacher', $this->tokenSecret);
                
                // Get school details
                $school = $this->db->table('schools')->where('ID', $teacher['school'])->get()->getRowArray();

                return $this->response->setJSON([
                    'status' => 'success',
                    'token' => $token,
                    'user' => [
                        'id' => (int)$teacher['teacher_id'],
                        'username' => $teacher['email'] ?? $teacher['phone'],
                        'role' => 'teacher',
                        'name' => $teacher['name'] ?? 'معلم',
                        'school_id' => (int)$teacher['school'],
                        'school_name' => $school ? $school['name'] : 'مدرسة السيلا'
                    ]
                ]);
            }
        }

        // 4. Check Student Table
        $student = $this->db->table('student')->where('username', $username)->get()->getRowArray();
        if ($student) {
            $authenticated = false;
            $needUpgrade = false;

            if (password_verify($password, $student['password'])) {
                $authenticated = true;
            } elseif ($password === $student['password']) {
                $authenticated = true;
                $needUpgrade = true;
            }

            if ($authenticated) {
                if ($needUpgrade) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $this->db->table('student')->where('student_id', $student['student_id'])->update(['password' => $newHash]);
                }

                // Students bypass OTP for seamless access to Sela Mobile App
                $token = $student['student_id'] . ':student:' . hash_hmac('sha256', $student['student_id'] . ':student', $this->tokenSecret);
                
                // Get school details
                $school = $this->db->table('schools')->where('ID', $student['school'])->get()->getRowArray();

                return $this->response->setJSON([
                    'status' => 'success',
                    'token' => $token,
                    'user' => [
                        'id' => (int)$student['student_id'],
                        'username' => $student['username'],
                        'role' => 'student',
                        'name' => $student['name'] ?? 'طالب',
                        'school_id' => (int)$student['school'],
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
        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);
        if (!is_array($json)) {
            $json = [];
        }
        $tempToken = $json['temp_token'] ?? $this->request->getPost('temp_token') ?? $_REQUEST['temp_token'] ?? '';
        $method = $json['method'] ?? $this->request->getPost('method') ?? $_REQUEST['method'] ?? 'whatsapp'; // 'whatsapp' or 'email'

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
        } elseif ($role === 'teacher') {
            $user = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
        } elseif ($role === 'student') {
            $user = $this->db->table('student')->where('student_id', $userId)->get()->getRowArray();
        } else {
            $user = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
        }

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'المستخدم غير موجود'])->setStatusCode(404);
        }

        // Generate 6-digit random OTP
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
        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);
        if (!is_array($json)) {
            $json = [];
        }
        $otpToken = $json['otp_token'] ?? $this->request->getPost('otp_token') ?? $_REQUEST['otp_token'] ?? '';
        $code = $json['code'] ?? $this->request->getPost('code') ?? $_REQUEST['code'] ?? '';

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
        } elseif ($role === 'teacher') {
            $teacher = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            if (!$teacher) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'المستخدم غير موجود'])->setStatusCode(404);
            }

            $token = $teacher['teacher_id'] . ':teacher:' . hash_hmac('sha256', $teacher['teacher_id'] . ':teacher', $this->tokenSecret);
            
            // Get school details
            $school = $this->db->table('schools')->where('ID', $teacher['school'])->get()->getRowArray();

            return $this->response->setJSON([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => (int)$teacher['teacher_id'],
                    'username' => $teacher['email'] ?? $teacher['phone'],
                    'role' => 'teacher',
                    'name' => $teacher['name'] ?? 'معلم',
                    'school_id' => (int)$teacher['school'],
                    'school_name' => $school ? $school['name'] : 'مدرسة السيلا'
                ]
            ]);
        } elseif ($role === 'student') {
            $student = $this->db->table('student')->where('student_id', $userId)->get()->getRowArray();
            if (!$student) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'المستخدم غير موجود'])->setStatusCode(404);
            }

            $token = $student['student_id'] . ':student:' . hash_hmac('sha256', $student['student_id'] . ':student', $this->tokenSecret);
            
            // Get school details
            $school = $this->db->table('schools')->where('ID', $student['school'])->get()->getRowArray();

            return $this->response->setJSON([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => (int)$student['student_id'],
                    'username' => $student['username'],
                    'role' => 'student',
                    'name' => $student['name'] ?? 'طالب',
                    'school_id' => (int)$student['school'],
                    'school_name' => $school ? $school['name'] : 'مدرسة السيلا'
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
        } elseif ($role === 'teacher') {
            // Teacher specific details
            $teacher = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            $schoolId = $teacher['school'] ?? 0;

            $data['total_students'] = $this->db->table('student')->where('school', $schoolId)->countAllResults();
            $data['total_teachers'] = $this->db->table('teacher')->where('school', $schoolId)->countAllResults();
            $data['total_classes'] = $this->db->table('class')->where('school', $schoolId)->countAllResults();
            $data['pending_registrations'] = 0; // Teachers do not manage registrations
            
            $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
            $data['title'] = 'بوابة المعلم - ' . ($school ? $school['name'] : 'صلة');
            $data['welcome_message'] = 'أهلاً بك يا ' . ($teacher['name'] ?? 'معلمنا الفاضل');
        } else {
            // Admin specific details
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'] ?? 0;

            $data['total_students'] = $this->db->table('student')->where('school', $schoolId)->countAllResults();
            $data['total_teachers'] = $this->db->table('teacher')->where('school', $schoolId)->countAllResults();
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
        } elseif ($role === 'teacher') {
            $teacher = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            $schoolId = $teacher['school'] ?? 0;
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'] ?? 0;
        }

        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        // Fetch students via enroll table mapping
        $builder = $this->db->table('student s')
            ->select('s.student_id, s.name, s.phone, s.sex, s.activate, c.name as class_name, c.class_id, e.section_id, sec.name as section_name')
            ->join('enroll e', 'e.student_id = s.student_id', 'inner')
            ->join('class c', 'c.class_id = e.class_id', 'left')
            ->join('section sec', 'sec.section_id = e.section_id', 'left')
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

        // Fetch sections to help mobile categorical listing
        $sectionBuilder = $this->db->table('section');
        if ($schoolId) {
            $sectionBuilder->where('school', $schoolId);
        }
        $sections = $sectionBuilder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'classes' => $classes,
            'sections' => $sections,
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

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);
        if (!is_array($json)) {
            $json = [];
        }
        $classId = $json['class_id'] ?? $this->request->getPost('class_id') ?? $_REQUEST['class_id'] ?? '';
        $sectionId = $json['section_id'] ?? $this->request->getPost('section_id') ?? $_REQUEST['section_id'] ?? 1;
        $date = $json['date'] ?? $this->request->getPost('date') ?? $_REQUEST['date'] ?? date('Y-m-d');
        $records = $json['records'] ?? $this->request->getPost('records') ?? $_REQUEST['records'] ?? [];

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
            $builder->where('r.school_id', $admin['school'] ?? 0);
        } elseif ($role === 'teacher') {
            $teacher = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            $builder->where('r.school_id', $teacher['school'] ?? 0);
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

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);
        if (!is_array($json)) {
            $json = [];
        }
        if ($role === 'super_admin') {
            $schoolId = $json['school_id'] ?? $this->request->getPost('school_id') ?? $_REQUEST['school_id'] ?? '';
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'];
        }

        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        $name = $json['name'] ?? $this->request->getPost('name') ?? $_REQUEST['name'] ?? '';
        $phone = $json['phone'] ?? $this->request->getPost('phone') ?? $_REQUEST['phone'] ?? '';
        $sex = $json['sex'] ?? $this->request->getPost('sex') ?? $_REQUEST['sex'] ?? 'male';
        $classId = $json['class_id'] ?? $this->request->getPost('class_id') ?? $_REQUEST['class_id'] ?? '';

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

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);
        if (!is_array($json)) {
            $json = [];
        }
        $name = $json['name'] ?? $this->request->getPost('name') ?? $_REQUEST['name'] ?? '';
        $phone = $json['phone'] ?? $this->request->getPost('phone') ?? $_REQUEST['phone'] ?? '';
        $sex = $json['sex'] ?? $this->request->getPost('sex') ?? $_REQUEST['sex'] ?? '';
        $classId = $json['class_id'] ?? $this->request->getPost('class_id') ?? $_REQUEST['class_id'] ?? '';

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
        } elseif ($role === 'teacher') {
            $teacher = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            $schoolId = $teacher['school'] ?? 0;
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'] ?? 0;
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

    /**
     * Identify Student by QR Code (ID)
     * GET /api/students/identify/(:num)
     */
    public function identifyStudent($studentId)
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $student = $this->db->table('student s')
            ->select('s.*, c.name as class_name, e.year, sch.name as school_name')
            ->join('enroll e', 'e.student_id = s.student_id', 'left')
            ->join('class c', 'c.class_id = e.class_id', 'left')
            ->join('schools sch', 'sch.ID = s.school', 'left')
            ->where('s.student_id', $studentId)
            ->get()
            ->getRowArray();

        if (!$student) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'لم يتم العثور على طالب بهذا الرمز أو الهوية'
            ])->setStatusCode(404);
        }

        // Fetch parent details
        $parent = null;
        if (!empty($student['parent_id'])) {
            $parent = $this->db->table('users')->where('id', $student['parent_id'])->get()->getRowArray();
        }

        // Fetch recent attendance statistics
        $attendance = $this->db->table('attendance')
            ->where('student_id', $studentId)
            ->orderBy('date', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'student' => [
                    'id' => (int)$student['student_id'],
                    'name' => $student['name'],
                    'phone' => $student['phone'] ?? 'غير مسجل',
                    'sex' => $student['sex'],
                    'username' => $student['username'] ?? '',
                    'class_name' => $student['class_name'] ?? 'غير معين',
                    'school_name' => $student['school_name'] ?? 'مدرسة السيلا',
                    'mother' => $student['mother'] ?? 'غير مسجل',
                    'nationalid' => $student['nationalid'] ?? 'غير مسجل',
                    'birthday' => $student['birthday'] ?? 'غير مسجل',
                    'image' => $student['image'] ?? '',
                    'activate' => (int)$student['activate'],
                    'parent_name' => $parent ? $parent['name'] : 'غير مسجل'
                ],
                'attendance' => $attendance
            ]
        ]);
    }

    /**
     * Get Authenticated Admin/Teacher Profile Details
     * GET /api/profile
     */
    public function profile()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $userId = $session['user_id'];
        $role = $session['role'];

        if ($role === 'teacher') {
            $user = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            if (!$user) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'المستخدم غير موجود'])->setStatusCode(404);
            }
            $school = $this->db->table('schools')->where('ID', $user['school'])->get()->getRowArray();

            return $this->response->setJSON([
                'status' => 'success',
                'user' => [
                    'id' => (int)$user['teacher_id'],
                    'name' => $user['name'] ?? '',
                    'username' => $user['email'] ?? $user['phone'] ?? '',
                    'email' => $user['email'] ?? '',
                    'phone' => $user['phone'] ?? '',
                    'role' => 'teacher',
                    'school_name' => $school ? $school['name'] : ''
                ]
            ]);
        } else {
            // Default: admin
            $user = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            if (!$user) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'المستخدم غير موجود'])->setStatusCode(404);
            }
            $school = $this->db->table('schools')->where('ID', $user['school'])->get()->getRowArray();

            return $this->response->setJSON([
                'status' => 'success',
                'user' => [
                    'id' => (int)$user['admin_id'],
                    'name' => $user['name'] ?? '',
                    'username' => $user['username'] ?? '',
                    'email' => $user['email'] ?? '',
                    'phone' => $user['phone'] ?? '',
                    'role' => 'admin',
                    'school_name' => $school ? $school['name'] : ''
                ]
            ]);
        }
    }

    /**
     * Update Authenticated Admin/Teacher Profile Details
     * POST /api/profile/update
     */
    public function updateProfile()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $userId = $session['user_id'];
        $role = $session['role'];

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);
        if (!is_array($json)) {
            $json = [];
        }

        $name = $json['name'] ?? $this->request->getPost('name') ?? '';
        $username = $json['username'] ?? $this->request->getPost('username') ?? '';
        $email = $json['email'] ?? $this->request->getPost('email') ?? '';
        $phone = $json['phone'] ?? $this->request->getPost('phone') ?? '';
        $password = $json['password'] ?? $this->request->getPost('password') ?? '';

        if (empty($name)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'الاسم مطلوب'])->setStatusCode(400);
        }

        if ($role === 'teacher') {
            if (!empty($email)) {
                $exists = $this->db->table('teacher')
                    ->where('teacher_id !=', $userId)
                    ->groupStart()
                        ->where('email', $email)
                    ->groupEnd()
                    ->get()
                    ->getRowArray();
                if ($exists) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'البريد الإلكتروني مستخدم بالفعل'])->setStatusCode(400);
                }
            }

            $updateData = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            ];

            if (!empty($password)) {
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $this->db->table('teacher')->where('teacher_id', $userId)->update($updateData);

            return $this->response->setJSON(['status' => 'success', 'message' => 'تم تحديث الملف الشخصي بنجاح']);
        } else {
            // Admin
            if (empty($username)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'اسم المستخدم مطلوب'])->setStatusCode(400);
            }

            $exists = $this->db->table('admin')
                ->where('admin_id !=', $userId)
                ->where('username', $username)
                ->get()
                ->getRowArray();
            if ($exists) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'اسم المستخدم مستخدم بالفعل'])->setStatusCode(400);
            }

            $updateData = [
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'phone' => $phone
            ];

            if (!empty($password)) {
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $this->db->table('admin')->where('admin_id', $userId)->update($updateData);

            return $this->response->setJSON(['status' => 'success', 'message' => 'تم تحديث الملف الشخصي بنجاح']);
        }
    }

    /**
     * Get Invoices/Financial Management List
     * GET /api/finance/invoices
     */
    public function invoices()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $userId = $session['user_id'];
        $role = $session['role'];

        if ($role === 'teacher') {
            $teacher = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            $schoolId = $teacher['school'] ?? 0;
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'] ?? 0;
        }

        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        $invoices = $this->db->table('invoice i')
            ->select('i.*, s.name as student_name')
            ->join('student s', 's.student_id = i.student_id', 'left')
            ->where('i.school', $schoolId)
            ->where('i.year', $currentYear)
            ->orderBy('i.invoice_id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'invoices' => $invoices
        ]);
    }

    /**
     * Print / Download PDF invoice
     * GET /api/finance/invoice/print/(:num)
     */
    public function printInvoice($id)
    {
        $invoice = $this->db->table('invoice i')
            ->select('i.*, s.name as student_name, s.phone as student_phone, c.name as class_name, sch.name as school_name')
            ->join('student s', 's.student_id = i.student_id', 'left')
            ->join('enroll e', 'e.student_id = s.student_id', 'left')
            ->join('class c', 'c.class_id = e.class_id', 'left')
            ->join('schools sch', 'sch.ID = i.school', 'left')
            ->where('i.invoice_id', $id)
            ->get()
            ->getRowArray();

        if (!$invoice) {
            echo "<h2>الفاتورة غير موجودة</h2>";
            return;
        }

        $amount = (double)$invoice['amount'];
        $paid = (double)$invoice['amount_paid'];
        $due = (double)$invoice['due'];

        $statusText = 'غير مدفوعة';
        if ($due <= 0) {
            $statusText = 'مدفوعة بالكامل';
        } elseif ($paid > 0) {
            $statusText = 'مدفوعة جزئياً';
        }

        $dateStr = date('Y/m/d', $invoice['creation_timestamp'] ? (int)$invoice['creation_timestamp'] : time());

        ?>
        <!DOCTYPE html>
        <html lang="ar" dir="rtl">
        <head>
            <meta charset="UTF-8">
            <title>فاتورة رقم #<?= $invoice['invoice_id'] ?></title>
            <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: 'Cairo', sans-serif;
                    background-color: #f8fafc;
                    margin: 0;
                    padding: 40px;
                    color: #1e293b;
                }
                .invoice-card {
                    max-width: 800px;
                    margin: 0 auto;
                    background: #fff;
                    padding: 40px;
                    border-radius: 20px;
                    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
                    border: 1px solid #e2e8f0;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 2px solid #e2e8f0;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .logo-section h1 {
                    color: #192a56;
                    margin: 0;
                    font-size: 26px;
                    font-weight: 900;
                }
                .logo-section p {
                    color: #c5a021;
                    margin: 5px 0 0 0;
                    font-weight: 700;
                }
                .title-section {
                    text-align: left;
                }
                .title-section h2 {
                    margin: 0;
                    color: #192a56;
                    font-size: 22px;
                }
                .title-section p {
                    margin: 5px 0 0 0;
                    color: #64748b;
                }
                .details-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 30px;
                    margin-bottom: 40px;
                }
                .details-block h3 {
                    margin-top: 0;
                    color: #192a56;
                    border-bottom: 1px solid #e2e8f0;
                    padding-bottom: 8px;
                    font-size: 16px;
                }
                .details-block p {
                    margin: 8px 0;
                    font-size: 14px;
                }
                .details-block strong {
                    color: #64748b;
                }
                .invoice-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 40px;
                }
                .invoice-table th, .invoice-table td {
                    padding: 12px 15px;
                    text-align: right;
                    border-bottom: 1px solid #e2e8f0;
                }
                .invoice-table th {
                    background-color: #f8fafc;
                    color: #192a56;
                    font-weight: 700;
                }
                .totals-section {
                    margin-right: auto;
                    width: 300px;
                    margin-bottom: 40px;
                }
                .total-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #f1f5f9;
                    font-size: 14px;
                }
                .total-row.grand-total {
                    border-bottom: none;
                    font-size: 18px;
                    font-weight: 900;
                    color: #192a56;
                    background-color: #f8fafc;
                    padding: 10px 15px;
                    border-radius: 10px;
                }
                .footer-note {
                    text-align: center;
                    color: #94a3b8;
                    font-size: 12px;
                    border-top: 1px solid #e2e8f0;
                    padding-top: 20px;
                    margin-top: 40px;
                }
                .print-actions {
                    max-width: 800px;
                    margin: 0 auto 20px auto;
                    display: flex;
                    justify-content: flex-end;
                }
                .print-btn {
                    background-color: #192a56;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 10px;
                    cursor: pointer;
                    font-family: 'Cairo', sans-serif;
                    font-weight: bold;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .print-btn:hover {
                    background-color: #111e3d;
                }
                @media print {
                    body {
                        background-color: #fff;
                        padding: 0;
                    }
                    .invoice-card {
                        box-shadow: none;
                        border: none;
                        padding: 0;
                    }
                    .print-actions {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-actions">
                <button onclick="window.print()" class="print-btn">
                    <span>طباعة الفاتورة أو حفظها كـ PDF</span>
                </button>
            </div>
            <div class="invoice-card">
                <div class="header">
                    <div class="logo-section">
                        <h1><?= htmlspecialchars($invoice['school_name'] ?? 'مدرسة السيلا الذكية') ?></h1>
                        <p>بوابة الشؤون المالية والمدفوعات</p>
                    </div>
                    <div class="title-section">
                        <h2>فاتورة مالية رسمية</h2>
                        <p>رقم الفاتورة: #<?= $invoice['invoice_id'] ?></p>
                    </div>
                </div>

                <div class="details-grid">
                    <div class="details-block">
                        <h3>بيانات الطالب</h3>
                        <p><strong>الاسم:</strong> <?= htmlspecialchars($invoice['student_name'] ?? 'غير مسجل') ?></p>
                        <p><strong>معرف الطالب:</strong> <?= htmlspecialchars($invoice['student_id'] ?? '-') ?></p>
                        <p><strong>الصف الدراسي:</strong> <?= htmlspecialchars($invoice['class_name'] ?? 'غير معين') ?></p>
                        <p><strong>الهاتف:</strong> <?= htmlspecialchars($invoice['student_phone'] ?? 'غير مسجل') ?></p>
                    </div>
                    <div class="details-block" style="text-align: left;">
                        <h3>تفاصيل الفاتورة</h3>
                        <p><strong>تاريخ الإصدار:</strong> <?= $dateStr ?></p>
                        <p><strong>العام الدراسي:</strong> <?= htmlspecialchars($invoice['year'] ?? '-') ?></p>
                        <p><strong>حالة الدفع:</strong> 
                            <span style="font-weight: bold; color: <?= $due <= 0 ? '#16a34a' : ($paid > 0 ? '#d97706' : '#dc2626') ?>;">
                                <?= $statusText ?>
                            </span>
                        </p>
                        <?php if (!empty($invoice['payment_method'])): ?>
                            <p><strong>طريقة الدفع:</strong> <?= htmlspecialchars($invoice['payment_method']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>البيان والتفاصيل</th>
                            <th style="text-align: left;">المبلغ الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($invoice['title'] ?? '') ?></strong>
                                <?php if (!empty($invoice['description'])): ?>
                                    <br><span style="font-size: 12px; color: #64748b;"><?= htmlspecialchars($invoice['description']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: left; font-weight: bold;"><?= number_format($amount, 2) ?> د.ل</td>
                        </tr>
                    </tbody>
                </table>

                <div class="totals-section">
                    <div class="total-row">
                        <span>المبلغ الإجمالي:</span>
                        <span><?= number_format($amount, 2) ?> د.ل</span>
                    </div>
                    <div class="total-row" style="color: #16a34a;">
                        <span>المبلغ المدفوع:</span>
                        <span><?= number_format($paid, 2) ?> د.ل</span>
                    </div>
                    <div class="total-row grand-total" style="color: <?= $due <= 0 ? '#16a34a' : '#dc2626' ?>;">
                        <span>المبلغ المستحق:</span>
                        <span><?= number_format($due, 2) ?> د.ل</span>
                    </div>
                </div>

                <div class="footer-note">
                    <p>هذه الفاتورة تم إنشاؤها وتأكيدها إلكترونياً من خلال نظام إدارة مدرسة السيلا.</p>
                    <p>شكراً لتعاونكم وثقتكم بنا.</p>
                </div>
            </div>
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 500);
                }
            </script>
        </body>
        </html>
        <?php
    }

    /**
     * Get marks registration filters/options
     * GET /api/academic/marks/options
     */
    public function marksOptions()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $role = $session['role'];
        $userId = $session['user_id'];

        if ($role === 'super_admin') {
            $schoolId = $this->request->getVar('school_id') ?? 1;
        } elseif ($role === 'teacher') {
            $teacher = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            $schoolId = $teacher['school'] ?? 0;
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'] ?? 0;
        }

        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        $classes = $this->db->table('class')->where('school', $schoolId)->orderBy('name', 'ASC')->get()->getResultArray();
        $sections = $this->db->table('section')->where('school', $schoolId)->orderBy('name', 'ASC')->get()->getResultArray();
        $subjects = $this->db->table('subject')->where('school', $schoolId)->orderBy('name', 'ASC')->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'classes' => $classes,
            'sections' => $sections,
            'subjects' => $subjects,
            'current_year' => $currentYear
        ]);
    }

    /**
     * Get students and distribution list for marks registration
     * GET /api/academic/marks/list
     */
    public function marksList()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $classId = (int)$this->request->getGet('class_id');
        $sectionId = (int)$this->request->getGet('section_id');
        $subjectId = (int)$this->request->getGet('subject_id');

        if (!$classId || !$sectionId || !$subjectId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'جميع الفلاتر مطلوبة'])->setStatusCode(400);
        }

        $role = $session['role'];
        $userId = $session['user_id'];

        if ($role === 'super_admin') {
            $schoolId = $this->request->getVar('school_id') ?? 1;
        } elseif ($role === 'teacher') {
            $teacher = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            $schoolId = $teacher['school'] ?? 0;
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'] ?? 0;
        }

        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        // Load subject distribution
        $distribution = $this->db->table('subject_mark_distribution')
            ->where('subject_id', $subjectId)
            ->where('year', $currentYear)
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        // If distribution is empty, create a default component using subject max marks
        if (empty($distribution)) {
            $subject = $this->db->table('subject')->where('subject_id', $subjectId)->get()->getRowArray();
            $totalMark = $subject ? (float)$subject['total_mark'] : 100;
            $distribution = [
                [
                    'id' => 0,
                    'subject_id' => $subjectId,
                    'name' => 'الامتحان النهائي',
                    'max_mark' => $totalMark,
                    'year' => $currentYear
                ]
            ];
        }

        // Get students and their existing marks
        $students = $this->db->table('student s')
            ->select('s.student_id, s.name as student_name, m.total_obtained, m.total_possible, m.comment, m.marks_json')
            ->join('enroll e', 'e.student_id = s.student_id')
            ->join('subject_marks m', "m.student_id = s.student_id AND m.subject_id = $subjectId AND m.year = '$currentYear'", 'left')
            ->where('e.class_id', $classId)
            ->where('e.section_id', $sectionId)
            ->where('e.year', $currentYear)
            ->orderBy('s.name', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'distribution' => $distribution,
            'students' => $students
        ]);
    }

    /**
     * Save student marks
     * POST /api/academic/marks/save
     */
    public function saveMarks()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $input = $this->request->getJSON(true);
        if (!$input) {
            $input = $this->request->getPost();
        }

        $classId = isset($input['class_id']) ? (int)$input['class_id'] : null;
        $sectionId = isset($input['section_id']) ? (int)$input['section_id'] : null;
        $subjectId = isset($input['subject_id']) ? (int)$input['subject_id'] : null;
        $marksData = isset($input['marks']) ? $input['marks'] : [];

        if (!$classId || !$sectionId || !$subjectId || empty($marksData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'بيانات رصد الدرجات غير مكتملة'])->setStatusCode(400);
        }

        $role = $session['role'];
        $userId = $session['user_id'];

        if ($role === 'super_admin') {
            $schoolId = $input['school_id'] ?? 1;
        } elseif ($role === 'teacher') {
            $teacher = $this->db->table('teacher')->where('teacher_id', $userId)->get()->getRowArray();
            $schoolId = $teacher['school'] ?? 0;
        } else {
            $admin = $this->db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
            $schoolId = $admin['school'] ?? 0;
        }

        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $currentYear = $school ? $school['year'] : '2025-2026';

        // Save each student's marks
        foreach ($marksData as $studentMark) {
            $studentId = (int)$studentMark['student_id'];
            $scores = isset($studentMark['scores']) ? $studentMark['scores'] : [];
            $comment = isset($studentMark['comment']) ? $studentMark['comment'] : '';

            $totalObtained = 0.0;
            foreach ($scores as $compName => $scoreVal) {
                $totalObtained += (float)$scoreVal;
            }

            $totalPossible = isset($studentMark['total_possible']) ? (float)$studentMark['total_possible'] : 100.0;

            // Check if record exists
            $check = $this->db->table('subject_marks')
                ->where('student_id', $studentId)
                ->where('subject_id', $subjectId)
                ->where('year', $currentYear)
                ->get()->getRowArray();

            $data = [
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'class_id' => $classId,
                'section_id' => $sectionId,
                'school_id' => $schoolId,
                'marks_json' => json_encode($scores),
                'total_obtained' => $totalObtained,
                'total_possible' => $totalPossible,
                'comment' => $comment,
                'year' => $currentYear
            ];

            if ($check) {
                $this->db->table('subject_marks')
                    ->where('id', $check['id'])
                    ->update($data);
            } else {
                $this->db->table('subject_marks')
                    ->insert($data);
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'تم حفظ ورصد الدرجات بنجاح'
        ]);
    }

    /**
     * Upload Student Photo
     * POST /api/students/upload_photo
     */
    public function uploadStudentPhoto()
    {
        $session = $this->authenticateToken();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'غير مصرح لك بالوصول'])->setStatusCode(401);
        }

        $studentId = $this->request->getPost('student_id');
        if (empty($studentId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'رمز الطالب مطلوب']);
        }

        $file = $this->request->getFile('photo');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'الملف غير صالح أو لم يتم تحميل أي صورة']);
        }

        // Validate that the student exists
        $student = $this->db->table('student')->where('student_id', $studentId)->get()->getRowArray();
        if (!$student) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'الطالب غير موجود']);
        }

        // Save the photo in upload/student_images/ with name [studentId].jpg
        $newName = $studentId . '.jpg';
        $uploadDir = FCPATH . 'upload/student_images/';
        
        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($file->move($uploadDir, $newName, true)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'تم تحميل وحفظ صورة الطالب بنجاح',
                'image_url' => base_url('upload/student_images/' . $newName . '?v=' . time())
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'فشل حفظ ملف الصورة في الخادم']);
    }
}

