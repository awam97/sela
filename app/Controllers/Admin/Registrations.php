<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Registrations extends BaseController
{
    public function index()
    {
        $schoolId = session()->get('school_id');
        
        $requests = $this->db->table('registration_requests r')
            ->select('r.*, c.name as class_name')
            ->join('class c', 'c.class_id = r.class_id')
            ->where('r.school_id', $schoolId)
            ->where('r.status', 'pending')
            ->orderBy('r.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'طلبات التسجيل',
            'page_title' => 'إدارة طلبات الالتحاق الجديدة',
            'active_menu' => 'registrations',
            'requests' => $requests
        ];

        return view('admin/registrations/index', $data);
    }

    public function approve($id)
    {
        $request = $this->db->table('registration_requests')->where('id', $id)->get()->getRowArray();
        if (!$request) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'الطلب غير موجود']);
        }

        $this->db->transStart();

        // 1. Create Student record
        $studentData = [
            'name'      => $request['name'],
            'phone'     => $request['phone'],
            'sex'       => $request['sex'],
            'username'  => $request['phone'], // Default username is phone
            'password'  => $request['phone'], // Default password is phone
            'school'    => $request['school_id'],
            'class_id'  => $request['class_id'],
            'section_id'=> 1, // Defaulting to first section (Section A usually has ID 1 or needs selection)
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
            'year'       => $this->currentYear
        ];
        $this->db->table('enroll')->insert($enrollData);

        // 3. Mark request as approved
        $this->db->table('registration_requests')->where('id', $id)->update(['status' => 'approved']);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'فشلت عملية الاعتماد']);
        }

        // 4. Notify via WhatsApp
        $this->notifyStudent($request['phone'], $request['name']);

        return $this->response->setJSON(['status' => 'success', 'message' => 'تم اعتماد الطالب بنجاح وإرسال رسالة ترحيب عبر الواتساب']);
    }

    public function reject($id)
    {
        if ($this->db->table('registration_requests')->where('id', $id)->update(['status' => 'rejected'])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'تم رفض الطلب بنجاح']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'حدث خطأ أثناء رفض الطلب']);
    }

    private function notifyStudent($phone, $name)
    {
        $apiKey = "RZ3eEfCTk4FS";
        $text = urlencode("أهلاً {$name}، تم قبول طلب انضمامك لمنصتنا بنجاح. يمكنك الآن الدخول باستخدام رقم هاتفك كاسم مستخدم وكلمة مرور.");
        $url = "https://api.textmebot.com/send.php?recipient={$phone}&apikey={$apiKey}&text={$text}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
