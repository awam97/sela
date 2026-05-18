<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\CityModel;

class Cities extends BaseController
{
    protected $cityModel;

    public function __construct()
    {
        $this->cityModel = new CityModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $cities = $db->table('cities')
            ->select('cities.*, COUNT(schools.ID) as schools_count')
            ->join('schools', 'schools.city = cities.ID', 'left')
            ->groupBy('cities.ID')
            ->get()
            ->getResultArray();

        $data = [
            'cities' => $cities,
            'active_menu' => 'cities'
        ];

        return view('superadmin/cities/index', $data);
    }

    public function create()
    {
        $name = $this->request->getPost('name');
        if ($name) {
            $this->cityModel->insert(['name' => $name]);
            return redirect()->to('/superadmin/cities')->with('success', 'تم إضافة المدينة بنجاح');
        }
        return redirect()->to('/superadmin/cities')->with('error', 'حدث خطأ أثناء إضافة المدينة');
    }

    public function update($id)
    {
        $name = $this->request->getPost('name');
        if ($name) {
            $this->cityModel->update($id, ['name' => $name]);
            return redirect()->to('/superadmin/cities')->with('success', 'تم تحديث بيانات المدينة');
        }
        return redirect()->to('/superadmin/cities')->with('error', 'حدث خطأ أثناء تحديث المدينة');
    }

    public function delete($id)
    {
        $this->cityModel->delete($id);
        return redirect()->to('/superadmin/cities')->with('success', 'تم حذف المدينة بنجاح');
    }
}
