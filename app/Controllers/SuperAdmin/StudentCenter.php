<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;

class StudentCenter extends BaseController
{
    public function index()
    {
        $services = $this->db->table('student_services')
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'تنظيم مركز الطلاب',
            'page_title' => 'ترتيب خدمات مركز الطلاب',
            'active_menu' => 'student_center',
            'services' => $services
        ];

        return view('superadmin/student_center/index', $data);
    }

    public function saveOrder()
    {
        $order = $this->request->getPost('order');
        
        if ($order) {
            foreach ($order as $index => $id) {
                $this->db->table('student_services')
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
            return $this->response->setJSON(['status' => 'success', 'message' => 'تم حفظ الترتيب بنجاح']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'فشل في حفظ الترتيب']);
    }
}
