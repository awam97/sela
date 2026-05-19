<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\SchoolModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function landing()
    {
        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        $data = [];
        foreach ($settings as $row) {
            $data[$row['type']] = $row['description'];
        }

        // Defaults
        $data['system_name'] = $data['system_name'] ?? 'مدرسة السيلا الذكية';
        $data['primary_color'] = $data['primary_color'] ?? '#192A56';
        $data['secondary_color'] = $data['secondary_color'] ?? '#C5A021';

        return view('landing', $data);
    }

    public function about()
    {
        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        $data = [];
        foreach ($settings as $row) {
            $data[$row['type']] = $row['description'];
        }

        // Defaults
        $data['system_name'] = $data['system_name'] ?? 'مدرسة السيلا الذكية';
        $data['primary_color'] = $data['primary_color'] ?? '#192A56';
        $data['secondary_color'] = $data['secondary_color'] ?? '#C5A021';

        return view('about', $data);
    }

    public function contact()
    {
        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        $data = [];
        foreach ($settings as $row) {
            $data[$row['type']] = $row['description'];
        }

        // Defaults
        $data['system_name'] = $data['system_name'] ?? 'مدرسة السيلا الذكية';
        $data['primary_color'] = $data['primary_color'] ?? '#192A56';
        $data['secondary_color'] = $data['secondary_color'] ?? '#C5A021';

        return view('contact', $data);
    }

    public function schoolRegistration()
    {
        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        $data = [];
        foreach ($settings as $row) {
            $data[$row['type']] = $row['description'];
        }
        
        $data['cities'] = $db->table('cities')->get()->getResultArray();

        // Defaults
        $data['system_name'] = $data['system_name'] ?? 'مدرسة السيلا الذكية';
        $data['primary_color'] = $data['primary_color'] ?? '#192A56';
        $data['secondary_color'] = $data['secondary_color'] ?? '#C5A021';

        return view('school_registration', $data);
    }

    public function postSchoolRegistration()
    {
        $db = \Config\Database::connect();
        
        $data = [
            'name' => $this->request->getPost('school_name'),
            'city' => $this->request->getPost('city'),
            'address' => $this->request->getPost('address'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'manager' => $this->request->getPost('manager_name'),
            'year' => date('Y'),
            'status' => 'pending'
        ];

        $db->table('schools')->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'تم إرسال طلب تسجيل المدرسة بنجاح. سيقوم فريقنا بمراجعة الطلب والتواصل معكم لتفعيل الحساب.'
        ]);
    }

    public function superLogin()
    {
        if (session()->get('isLoggedIn')) {
            if (session()->get('role') === 'super_admin') {
                return redirect()->to('/superadmin/dashboard');
            }
            return redirect()->to('/admin/dashboard');
        }

        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        $data = [];
        foreach ($settings as $row) {
            $data[$row['type']] = $row['description'];
        }

        // Defaults
        $data['system_name'] = $data['system_name'] ?? 'مدرسة السيلا الذكية';
        $data['system_title'] = $data['system_title'] ?? 'مدرسة السيلا الذكية';
        $data['system_desc'] = $data['system_desc'] ?? 'نظام إدارة المدارس المتكامل والذكي لنظام تعليمي متطور ومتميز';
        $data['primary_color'] = $data['primary_color'] ?? '#192A56';
        $data['secondary_color'] = $data['secondary_color'] ?? '#C5A021';
        $data['form_action'] = base_url('superadmin/login');
        $data['is_super_login'] = true;

        return view('auth/login', $data);
    }

    public function postSuperLogin()
    {
        $session = session();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $db = \Config\Database::connect();
        
        // 1. Only Check Super Admin Table
        $super = $db->table('super')->where('username', $username)->get()->getRowArray();
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
                    $db->table('super')->where('super_id', $super['super_id'])->update(['password' => $newHash]);
                }
                if ($this->shouldBypassOtp($super, 'super_admin')) {
                    return $this->finalizeLogin($super, 'super_admin', true);
                }
                return $this->initOtpLogin($super, 'super_admin');
            }
        }

        return redirect()->back()->with('error', 'بيانات الدخول غير صحيحة أو الحساب ليس مديراً عاماً');
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }

        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        $data = [];
        foreach ($settings as $row) {
            $data[$row['type']] = $row['description'];
        }

        // Defaults
        $data['system_name'] = $data['system_name'] ?? 'مدرسة السيلا الذكية';
        $data['system_title'] = $data['system_title'] ?? 'مدرسة السيلا الذكية';
        $data['system_desc'] = $data['system_desc'] ?? 'نظام إدارة المدارس المتكامل والذكي لنظام تعليمي متطور ومتميز';
        $data['primary_color'] = $data['primary_color'] ?? '#192A56';
        $data['secondary_color'] = $data['secondary_color'] ?? '#C5A021';

        return view('auth/login', $data);
    }

    public function postLogin()
    {
        $session = session();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $db = \Config\Database::connect();
        
        // Check Standard Admin Table (School Managers & standard admins only)
        $admin = $db->table('admin')->where('username', $username)->get()->getRowArray();
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
                    $db->table('admin')->where('admin_id', $admin['admin_id'])->update(['password' => $newHash]);
                }
                if ($this->shouldBypassOtp($admin, 'admin')) {
                    return $this->finalizeLogin($admin, 'admin', true);
                }
                return $this->initOtpLogin($admin, 'admin');
            }
        }

        return redirect()->back()->with('error', 'بيانات الدخول غير صحيحة');
    }

    private function initOtpLogin($user, $role)
    {
        // Store user in session for the next steps
        session()->set([
            'temp_user' => $user,
            'temp_role' => $role
        ]);

        return redirect()->to('/auth/select-otp-method');
    }

    public function selectOtpMethod()
    {
        if (!session()->has('temp_user')) {
            return redirect()->to('/auth/login');
        }

        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        $data = [];
        foreach ($settings as $row) {
            $data[$row['type']] = $row['description'];
        }

        $user = session()->get('temp_user');
        $data['user_email'] = $user['email'] ?? null;
        $data['user_phone'] = $user['phone'] ?? null;
        
        $wa_setting = strtolower($data['whatsapp_otp_enabled'] ?? 'false');
        $data['wa_enabled'] = in_array($wa_setting, ['true', '1', 'on', 'yes']);

        return view('auth/select_otp_method', $data);
    }

    public function sendSelectedOtp()
    {
        if (!session()->has('temp_user')) {
            return redirect()->to('/auth/login');
        }

        $method = $this->request->getPost('method');
        $user = session()->get('temp_user');
        $role = session()->get('temp_role');
        $otp = rand(100000, 999999);

        session()->set([
            'login_otp' => $otp,
            'otp_expiry' => time() + 600
        ]);

        if ($method === 'email' && !empty($user['email'])) {
            if (!$this->sendOtpEmail($user['email'], $otp)) {
                return redirect()->back()->with('error', 'فشل إرسال رمز التحقق عبر البريد الإلكتروني. تأكد من إعدادات SMTP أو سجلات النظام.');
            }
        } elseif ($method === 'whatsapp' && !empty($user['phone'])) {
            $this->sendOtpWhatsApp($user['phone'], $otp);
        } else {
            return redirect()->back()->with('error', 'وسيلة التحقق المختارة غير متوفرة لهذا الحساب');
        }

        return redirect()->to('/auth/verify-otp');
    }

    private function sendOtpWhatsApp($phone, $otp)
    {
        $db = \Config\Database::connect();
        $apiKey = $db->table('settings')->where('type', 'textmebot_api_key')->get()->getRow()->description ?? '';
        $system_name = $db->table('settings')->where('type', 'system_name')->get()->getRow()->description ?? 'منصة صلة';

        if (empty($apiKey)) return;

        $text = urlencode("*{$system_name}*\n\nرمز التحقق الخاص بك للدخول هو: *{$otp}*\n\nهذا الرمز صالح لمدة 10 دقائق فقط. يرجى عدم مشاركته مع أحد.");
        $recipient = urlencode($phone);
        $url = "https://api.textmebot.com/send.php?recipient={$recipient}&apikey={$apiKey}&text={$text}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local/remote compatibility
        curl_exec($ch);
        curl_close($ch);
    }

    private function sendOtpEmail($to, $otp)
    {
        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        $config = [];
        foreach ($settings as $row) {
            $config[$row['type']] = $row['description'];
        }

        $email = \Config\Services::email();

        $smtp_port = (int)($config['smtp_port'] ?? 587);
        $smtp_crypto = $config['smtp_crypto'] ?? 'tls';

        // Auto-correct mismatch between Port and Encryption to prevent fsockopen OpenSSL wrong version number errors
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
                    body { background-color: #f4f7f9; margin: 0; padding: 0; font-family: 'Tajawal', Arial, sans-serif; }
                    .email-wrapper { width: 100%; padding: 40px 0; background-color: #f4f7f9; }
                    .email-card { max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
                    .header { background: #192A56; padding: 40px 20px; text-align: center; }
                    .header img { height: 50px; }
                    .body { padding: 40px; text-align: center; }
                    .title { font-size: 24px; font-weight: 800; color: #192A56; margin-bottom: 10px; }
                    .subtitle { font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 30px; }
                    .otp-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 25px; margin-bottom: 30px; }
                    .otp-code { font-size: 42px; font-weight: 900; color: #C5A021; letter-spacing: 10px; text-shadow: 1px 1px 0 #fff; }
                    .expiry { font-size: 13px; color: #94a3b8; font-weight: 600; }
                    .footer { padding: 30px; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; font-size: 12px; color: #94a3b8; }
                    .footer a { color: #C5A021; text-decoration: none; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='email-wrapper'>
                    <div class='email-card'>
                        <div class='header'>
                            <img src='" . base_url('public/uploads/logo.png') . "' alt='Sela Logo' style='filter: brightness(0) invert(1);'>
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
                            جميع الحقوق محفوظة &copy; " . date('Y') . " " . ($config['system_name'] ?? 'منصة صلة') . "<br>
                            تحتاج مساعدة؟ تواصل معنا عبر <a href='mailto:" . ($config['contact_email'] ?? 'support@sela.ly') . "'>" . ($config['contact_email'] ?? 'support@sela.ly') . "</a>
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

    public function verifyOtpPage()
    {
        if (!session()->has('login_otp')) {
            return redirect()->to('/auth/login');
        }

        $db = \Config\Database::connect();
        $data = [
            'system_name' => $db->table('settings')->where('type', 'system_name')->get()->getRow()->description ?? 'Sela Platform',
            'primary_color' => $db->table('settings')->where('type', 'primary_color')->get()->getRow()->description ?? '#192A56',
            'secondary_color' => $db->table('settings')->where('type', 'secondary_color')->get()->getRow()->description ?? '#C5A021',
        ];

        return view('auth/verify_otp', $data);
    }

    public function processOtpVerify()
    {
        $input_otp = $this->request->getPost('otp');
        $session_otp = session()->get('login_otp');
        $expiry = session()->get('otp_expiry');

        if (time() > $expiry) {
            return redirect()->to('/auth/login')->with('error', 'انتهت صلاحية رمز التحقق، يرجى المحاولة مرة أخرى');
        }

        if ($input_otp == $session_otp) {
            $user = session()->get('temp_user');
            $role = session()->get('temp_role');
            return $this->finalizeLogin($user, $role);
        }

        return redirect()->back()->with('error', 'رمز التحقق غير صحيح');
    }

    private function finalizeLogin($user, $role, $bypassed = false)
    {
        $session = session();
        $ses_data = [
            'user_id' => $role === 'super_admin' ? $user['super_id'] : $user['admin_id'],
            'username' => $user['username'],
            'role' => $role,
            'school_id' => $user['school'] ?? null,
            'isLoggedIn' => TRUE
        ];
        $session->set($ses_data);
        
        // Cleanup temp data
        $session->remove(['temp_user', 'temp_role', 'login_otp', 'otp_expiry']);

        // Set/refresh tracker cookie
        $userId = $role === 'super_admin' ? $user['super_id'] : $user['admin_id'];
        $timestamp = time();
        $signature = hash_hmac('sha256', "$userId:$role:$timestamp", $user['password']);
        $cookieValue = "$userId:$role:$timestamp:$signature";
        setcookie('sela_session_tracker', $cookieValue, time() + (86400 * 30), '/');

        if ($bypassed) {
            return redirect()->to($role === 'super_admin' ? '/superadmin/dashboard' : '/admin/dashboard')
                ->with('success', 'تم الدخول مباشرة لتواجدك النشط مؤخراً.');
        }

        return redirect()->to($role === 'super_admin' ? '/superadmin/dashboard' : '/admin/dashboard');
    }

    private function shouldBypassOtp($user, $role)
    {
        $db = \Config\Database::connect();
        $otpSetting = $db->table('settings')->where('type', 'whatsapp_otp_enabled')->get()->getRowArray();
        $otpEnabledVal = strtolower($otpSetting['description'] ?? 'false');
        $otpEnabled = in_array($otpEnabledVal, ['true', '1', 'on', 'yes']);
        if (!$otpEnabled) {
            return true;
        }

        $cookie = $_COOKIE['sela_session_tracker'] ?? null;
        if (!$cookie) {
            return false;
        }

        $parts = explode(':', $cookie);
        if (count($parts) !== 4) {
            return false;
        }

        list($cookieUserId, $cookieRole, $cookieTimestamp, $cookieSignature) = $parts;
        $userId = $role === 'super_admin' ? $user['super_id'] : $user['admin_id'];

        if ((string)$cookieUserId !== (string)$userId || $cookieRole !== $role) {
            return false;
        }

        // Verify cryptographic signature to prevent tampering
        $expectedSignature = hash_hmac('sha256', "$cookieUserId:$cookieRole:$cookieTimestamp", $user['password']);
        if ($cookieSignature !== $expectedSignature) {
            return false;
        }

        // Check if last activity was within 15 minutes (900 seconds)
        if (time() - (int)$cookieTimestamp > 900) {
            return false;
        }

        return true;
    }

    /**
     * Public Registration Page
     */
    public function register()
    {
        $db = \Config\Database::connect();
        $schools = $db->table('schools')->get()->getResultArray();
        
        $data = [
            'schools' => $schools,
            'system_name' => $db->table('settings')->where('type', 'system_name')->get()->getRow()->description ?? 'Sela Platform',
            'primary_color' => $db->table('settings')->where('type', 'primary_color')->get()->getRow()->description ?? '#192A56',
            'secondary_color' => $db->table('settings')->where('type', 'secondary_color')->get()->getRow()->description ?? '#C5A021',
        ];

        return view('auth/register', $data);
    }

    /**
     * AJAX: Get classes for a specific school
     */
    public function getClasses($schoolId)
    {
        $db = \Config\Database::connect();
        $classes = $db->table('class')->where('school', $schoolId)->get()->getResultArray();
        return $this->response->setJSON($classes);
    }

    /**
     * AJAX: Send OTP via WhatsApp
     */
    public function sendOtp()
    {
        $phone = $this->request->getPost('phone');
        if (!$phone) return $this->response->setJSON(['status' => 'error', 'message' => 'رقم الهاتف مطلوب']);

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        
        // Save to Session for verification (or DB)
        session()->set('reg_otp', $otp);
        session()->set('reg_phone', $phone);

        // TextMeBot Integration
        $apiKey = "RZ3eEfCTk4FS";
        $text = urlencode("رمز التحقق الخاص بك في منصة صلة هو: " . $otp);
        $url = "https://api.textmebot.com/send.php?recipient={$phone}&apikey={$apiKey}&text={$text}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $this->response->setJSON(['status' => 'success', 'message' => 'تم إرسال رمز التحقق بنجاح']);
    }

    /**
     * AJAX: Verify OTP
     */
    public function verifyOtp()
    {
        $otp = $this->request->getPost('otp');
        $session_otp = session()->get('reg_otp');

        if ($otp == $session_otp) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'رمز التحقق غير صحيح']);
    }

    /**
     * Submit Final Registration
     */
    public function submitRegistration()
    {
        $db = \Config\Database::connect();
        $data = [
            'name'      => $this->request->getPost('name'),
            'phone'     => $this->request->getPost('phone'),
            'school_id' => $this->request->getPost('school_id'),
            'class_id'  => $this->request->getPost('class_id'),
            'sex'       => $this->request->getPost('sex'),
            'status'    => 'pending'
        ];

        if ($db->table('registration_requests')->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'تم إرسال طلبك بنجاح. سيقوم مدير المدرسة بمراجعته قريباً.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'حدث خطأ أثناء إرسال الطلب']);
    }

    public function maintenance()
    {
        return view('errors/maintenance');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }

    public function viewLogs()
    {
        $date = date('Y-m-d');
        $logFile = WRITEPATH . 'logs/log-' . $date . '.log';
        if (file_exists($logFile)) {
            echo '<pre>' . htmlspecialchars(file_get_contents($logFile)) . '</pre>';
        } else {
            echo 'No log file found for today: ' . $logFile;
        }
    }
}
