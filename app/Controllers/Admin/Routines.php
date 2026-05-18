<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoutineModel;
use App\Models\SubjectModel;
use App\Models\ClassModel;

class Routines extends BaseController
{
    protected $routineModel;
    protected $subjectModel;
    protected $classModel;

    public function __construct()
    {
        $this->routineModel = new RoutineModel();
        $this->subjectModel = new SubjectModel();
        $this->classModel = new ClassModel();
    }

    /**
     * View the weekly routine for a specific class/section
     */
    public function view($classId, $sectionId)
    {
        $session = session();
        $schoolId = $session->get('school_id') ?? 1;
        $currentYear = $session->get('running_year') ?? '2026';

        // Fetch School-Specific Off-Days (Weekends)
        $db = \Config\Database::connect();
        $school_info = $db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        $off_days_setting = $school_info['weekends'] ?? 'friday';
        $off_days_array = explode(',', $off_days_setting);

        // Fetch Routine Data
        $routines = $this->routineModel->getRoutineWithDetails($classId, $sectionId, $schoolId, $currentYear);
        
        // Group by Day
        $weekly_routine = [
            'saturday' => [], 'sunday' => [], 'monday' => [], 
            'tuesday' => [], 'wednesday' => [], 'thursday' => []
        ];

        foreach ($routines as $row) {
            $day = strtolower($row['day']);
            if (isset($weekly_routine[$day])) {
                $weekly_routine[$day][] = $row;
            }
        }

        $db = \Config\Database::connect();
        $class_name = $this->classModel->find($classId)['name'] ?? '...';
        $section_name = $db->table('section')->where('section_id', $sectionId)->get()->getRowArray()['name'] ?? '...';

        $data = [
            'weekly_routine' => $weekly_routine,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'class_name' => $class_name,
            'section_name' => $section_name,
            'subjects' => $this->subjectModel->where('class_id', $classId)->findAll(),
            'off_days' => $off_days_array,
            'page_title' => 'جدول الحصص الدراسية',
            'active_menu' => 'academic'
        ];

        return view('admin/routines/view', $data);
    }

    /**
     * Save a new routine entry
     */
    public function save()
    {
        $classId = $this->request->getPost('class_id');
        $sectionId = $this->request->getPost('section_id');

        $data = [
            'class_id'       => $classId,
            'section_id'     => $sectionId,
            'subject_id'     => $this->request->getPost('subject_id'),
            'time_start'     => $this->request->getPost('time_start'),
            'time_end'       => $this->request->getPost('time_end'),
            'time_start_min' => $this->request->getPost('time_start_min') ?? 0,
            'time_end_min'   => $this->request->getPost('time_end_min') ?? 0,
            'day'            => $this->request->getPost('day'),
            'am_pm'          => $this->request->getPost('am_pm') ?? 'am',
            'school'         => session()->get('school_id') ?? 1,
            'year'           => session()->get('running_year') ?? '2026'
        ];

        if ($this->routineModel->insert($data)) {
            return redirect()->to(site_url("admin/routines/view/{$classId}/{$sectionId}"))->with('success', 'تم إضافة الحصة بنجاح');
        }
        return redirect()->back()->with('error', 'فشل في حفظ الحصة');
    }

    /**
     * Delete a routine entry
     */
    public function delete($id, $classId, $sectionId)
    {
        if ($this->routineModel->delete($id)) {
            return redirect()->to(site_url("admin/routines/view/{$classId}/{$sectionId}"))->with('success', 'تم حذف الحصة بنجاح');
        }
        return redirect()->back()->with('error', 'فشل في حذف الحصة');
    }
}
