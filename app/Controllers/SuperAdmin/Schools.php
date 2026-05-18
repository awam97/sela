<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\SchoolModel;
use App\Models\CityModel;

class Schools extends BaseController
{
    protected $schoolModel;
    protected $cityModel;

    public function __construct()
    {
        $this->schoolModel = new SchoolModel();
        $this->cityModel = new CityModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $schools = $db->table('schools')
                    ->select('schools.*, cities.name as city_name')
                    ->join('cities', 'cities.ID = schools.city', 'left')
                    ->get()
                    ->getResultArray();

        $data = [
            'schools' => $schools,
            'active_menu' => 'schools'
        ];

        return view('superadmin/schools/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name'    => $this->request->getPost('name'),
                'city'    => $this->request->getPost('city'),
                'address' => $this->request->getPost('address'),
                'email'   => $this->request->getPost('email'),
                'year'    => $this->request->getPost('year'),
                'manager' => $this->request->getPost('manager'),
                'exams_manager' => $this->request->getPost('exams_manager'),
            ];

            $this->schoolModel->insert($data);
            return redirect()->to('/superadmin/schools')->with('success', 'تم إضافة المدرسة بنجاح');
        }

        $data = [
            'cities' => $this->cityModel->findAll(),
            'active_menu' => 'schools'
        ];
        return view('superadmin/schools/create', $data);
    }

    public function edit($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name'    => $this->request->getPost('name'),
                'city'    => $this->request->getPost('city'),
                'address' => $this->request->getPost('address'),
                'email'   => $this->request->getPost('email'),
                'year'    => $this->request->getPost('year'),
                'manager' => $this->request->getPost('manager'),
                'exams_manager' => $this->request->getPost('exams_manager'),
            ];

            $this->schoolModel->update($id, $data);
            return redirect()->to('/superadmin/schools')->with('success', 'تم تحديث بيانات المدرسة بنجاح');
        }

        $data = [
            'school' => $this->schoolModel->find($id),
            'cities' => $this->cityModel->findAll(),
            'active_menu' => 'schools'
        ];
        return view('superadmin/schools/edit', $data);
    }

    public function delete($id)
    {
        $this->schoolModel->delete($id);
        return redirect()->to('/superadmin/schools')->with('success', 'تم حذف المدرسة بنجاح');
    }
}
