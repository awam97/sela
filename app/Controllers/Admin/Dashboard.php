<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\TeacherModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $teacherModel = new TeacherModel();
        
        // 1. Get total students filtered by academic year
        $totalStudents = $this->db->table('enroll')
            ->where('year', $this->currentYear)
            ->where('school', $this->schoolId)
            ->countAllResults();

        // 2. Get male/female breakdown for charts
        $maleStudents = $this->db->table('enroll e')
            ->join('student s', 's.student_id = e.student_id')
            ->where('e.year', $this->currentYear)
            ->where('e.school', $this->schoolId)
            ->where('s.sex', 'male')
            ->countAllResults();

        $femaleStudents = $this->db->table('enroll e')
            ->join('student s', 's.student_id = e.student_id')
            ->where('e.year', $this->currentYear)
            ->where('e.school', $this->schoolId)
            ->where('s.sex', 'female')
            ->countAllResults();

        $stats = [
            'students' => $totalStudents,
            'teachers' => $teacherModel->countAllResults(),
            'classes'  => $this->db->table('class')->countAllResults(),
            'revenue'  => '45,250 د.ل', // Placeholder for now
            'male_students'   => $maleStudents,
            'female_students' => $femaleStudents
        ];

        $data = [
            'title' => 'لوحة التحكم',
            'page_title' => 'لوحة التحكم الرئيسية',
            'active_menu' => 'dashboard',
            'stats' => $stats,
            'classes' => $this->db->table('class')->get()->getResultArray()
        ];

        return view('admin/dashboard', $data);
    }
}
