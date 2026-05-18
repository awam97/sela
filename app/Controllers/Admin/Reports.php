<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Reports extends BaseController
{
    protected $session;
    protected $currentYear;
    protected $schoolId;

    public function __construct()
    {
        $this->session = session();
        $this->currentYear = $this->session->get('running_year');
        $this->schoolId = $this->session->get('school_id');
    }

    /**
     * Reports Dashboard Hub
     */
    public function index()
    {
        $data = [
            'title' => 'مركز التقارير',
            'page_title' => 'التقارير والإحصائيات الإدارية',
            'active_menu' => 'reports'
        ];

        return view('admin/reports/index', $data);
    }

    /**
     * Marksheet / Grade Report
     */
    public function marksheet($classId, $sectionId = 'all')
    {
        $db = \Config\Database::connect();
        
        $data = [
            'title' => 'كشف الدرجات المجمع',
            'page_title' => 'نتائج الطلاب لكل مادة',
            'active_menu' => 'reports',
            'class' => $db->table('class')->where('class_id', $classId)->get()->getRowArray(),
            'section' => ($sectionId !== 'all' ? $db->table('section')->where('section_id', $sectionId)->get()->getRowArray() : null),
            'subjects' => $db->table('subject')->where('class_id', $classId)->get()->getResultArray(),
            'exams' => $db->table('exams')->where(['school' => $this->schoolId, 'year' => $this->currentYear])->get()->getResultArray(),
            'year' => $this->currentYear
        ];

        // Load students
        $builder = $db->table('student s')
            ->select('s.student_id, s.name')
            ->join('enroll e', 'e.student_id = s.student_id')
            ->where('e.class_id', $classId);
        
        if ($sectionId !== 'all') {
            $builder->where('e.section_id', $sectionId);
        }

        $data['students'] = $builder->where('e.year', $this->currentYear)->get()->getResultArray();

        // Optimized mark fetching: Get all marks for students in this class/section in one query
        $studentIds = array_column($data['students'], 'student_id');
        $marksResult = [];
        
        if (!empty($studentIds)) {
            $marks = $db->table('subject_marks')
                ->whereIn('student_id', $studentIds)
                ->where('class_id', $classId)
                ->where('year', $this->currentYear)
                ->get()->getResultArray();

            foreach ($marks as $m) {
                $marksResult[$m['student_id']][$m['subject_id']] = $m;
            }
        }
        
        $data['marks_data'] = $marksResult;

        return view('admin/reports/marksheet', $data);
    }
}
