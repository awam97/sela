<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TeacherModel;

class Teachers extends BaseController
{
    protected $teacherModel;

    public function __construct()
    {
        $this->teacherModel = new TeacherModel();
    }

    public function index()
    {
        $session = session();
        $schoolId = $session->get('school_id') ?? 1;

        $data = [
            'title' => 'إدارة المعلمين',
            'page_title' => 'قائمة المعلمين',
            'active_menu' => 'teachers',
            'teachers' => $this->teacherModel->where('school', $schoolId)->findAll()
        ];

        return view('admin/teachers/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'name' => $this->request->getPost('name'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'school' => session()->get('school_id') ?? 1,
                'status' => 1
            ];

            if ($this->teacherModel->insert($data)) {
                return redirect()->to('/admin/teachers')->with('success', 'تم إضافة المعلم بنجاح');
            }
            return redirect()->back()->with('error', 'فشل الإضافة')->withInput();
        }

        $data = [
            'title' => 'إضافة معلم',
            'page_title' => 'إضافة معلم جديد',
            'active_menu' => 'teachers'
        ];
        return view('admin/teachers/create', $data);
    }

    public function edit($id)
    {
        $teacher = $this->teacherModel->find($id);
        if (!$teacher) {
            return redirect()->to('/admin/teachers')->with('error', 'المعلم غير موجود');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'name' => $this->request->getPost('name'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'status' => $this->request->getPost('status')
            ];
            
            if ($this->request->getPost('password')) {
                $data['password'] = $this->request->getPost('password');
            }

            if ($this->teacherModel->update($id, $data)) {
                return redirect()->to('/admin/teachers')->with('success', 'تم تحديث البيانات بنجاح');
            }
            return redirect()->back()->with('error', 'فشل التحديث')->withInput();
        }

        $data = [
            'title' => 'تعديل معلم',
            'page_title' => 'تعديل بيانات المعلم',
            'active_menu' => 'teachers',
            'teacher' => $teacher
        ];
        return view('admin/teachers/edit', $data);
    }

    public function delete($id)
    {
        if ($this->teacherModel->delete($id)) {
            return redirect()->to('/admin/teachers')->with('success', 'تم حذف المعلم بنجاح');
        }
        return redirect()->to('/admin/teachers')->with('error', 'فشل الحذف');
    }
}
