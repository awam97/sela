<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\CityModel;
use App\Models\SchoolModel;
use App\Models\StudentModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $cityModel = new CityModel();
        $schoolModel = new SchoolModel();
        $studentModel = new StudentModel();
        $db = \Config\Database::connect();

        $recent_schools = $db->table('schools')
                    ->select('schools.*, cities.name as city_name')
                    ->join('cities', 'cities.ID = schools.city', 'left')
                    ->orderBy('schools.ID', 'DESC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();

        // Chart Data (Mocking for now, could be real aggregation)
        $chart_data = [
            'months' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            'registrations' => [12, 19, 15, 25, 22, 30], // Schools added per month
            'distribution' => [40, 35, 25] // Example: Primary, Middle, High or by Region
        ];

        $data = [
            'total_cities' => $cityModel->countAll(),
            'total_schools' => $schoolModel->countAll(),
            'total_students' => $studentModel->countAll(),
            'total_admins' => $db->table('admin')->countAllResults(),
            'recent_schools' => $recent_schools,
            'chart_data' => $chart_data,
            'active_menu' => 'dashboard'
        ];

        return view('superadmin/dashboard', $data);
    }
}
