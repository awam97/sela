<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\SchoolModel;

class Admins extends BaseController
{
    protected $schoolModel;

    public function __construct()
    {
        $this->schoolModel = new SchoolModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $admins = $db->table('admin')
                    ->select('admin.*, schools.name as school_name')
                    ->join('schools', 'schools.ID = admin.school', 'left')
                    ->get()
                    ->getResultArray();

        $data = [
            'admins' => $admins,
            'active_menu' => 'admins'
        ];

        return view('superadmin/admins/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $db = \Config\Database::connect();
            $data = [
                'name'         => $this->request->getPost('name'),
                'school'       => $this->request->getPost('school'),
                'username'     => $this->request->getPost('username'),
                'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'phone'        => $this->request->getPost('phone'),
                'email'        => $this->request->getPost('email'),
                'status'       => 1,
                'owner_status' => 1
            ];

            $db->table('admin')->insert($data);
            return redirect()->to('/superadmin/admins')->with('success', 'تم إنشاء حساب المسؤول بنجاح');
        }

        $data = [
            'schools' => $this->schoolModel->findAll(),
            'active_menu' => 'admins'
        ];
        return view('superadmin/admins/create', $data);
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name'     => $this->request->getPost('name'),
                'school'   => $this->request->getPost('school'),
                'username' => $this->request->getPost('username'),
                'phone'    => $this->request->getPost('phone'),
                'email'    => $this->request->getPost('email'),
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $db->table('admin')->where('admin_id', $id)->update($data);
            return redirect()->to('/superadmin/admins')->with('success', 'تم تحديث بيانات الحساب بنجاح');
        }

        $data = [
            'admin' => $db->table('admin')->where('admin_id', $id)->get()->getRowArray(),
            'schools' => $this->schoolModel->findAll(),
            'active_menu' => 'admins'
        ];
        return view('superadmin/admins/edit', $data);
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $db->table('admin')->where('admin_id', $id)->delete();
        return redirect()->to('/superadmin/admins')->with('success', 'تم حذف الحساب بنجاح');
    }
}
