<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\ClassModel;
use App\Models\SectionModel;
use App\Models\StudentModel;

class Attendance extends BaseController
{
    protected $attendanceModel;
    protected $classModel;
    protected $sectionModel;
    protected $studentModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->classModel = new ClassModel();
        $this->sectionModel = new SectionModel();
        $this->studentModel = new StudentModel();
    }

    /**
     * Display Attendance Selector
     */
    public function index()
    {
        $schoolId = $this->schoolId;
        $year     = $this->currentYear;

        // Fetch only classes that have enrollments in the current year and school
        $db = \Config\Database::connect();
        $classIds = $db->table('enroll')
            ->select('class_id')
            ->where(['school' => $schoolId, 'year' => $year])
            ->distinct()
            ->get()->getResultArray();
            
        $classIds = array_column($classIds, 'class_id');

        if (!empty($classIds)) {
            $data['classes'] = $this->classModel->whereIn('class_id', $classIds)->findAll();
        } else {
            $data['classes'] = [];
        }

        $data['active_menu'] = 'attendance';
        $data['page_title']  = 'رصد الحضور والغياب';
        $data['current_year'] = $year;

        return view('admin/attendance/index', $data);
    }

    /**
     * Load Student List for Attendance
     */
    public function take()
    {
        $schoolId = $this->schoolId;
        $year = $this->currentYear;

        $classId = $this->request->getGet('class_id');
        $sectionId = $this->request->getGet('section_id');
        $dateStr = $this->request->getGet('date') ?? date('Y-m-d');
        $timestamp = strtotime($dateStr);

        if (!$classId || !$sectionId) {
            return redirect()->to(site_url('admin/attendance'))->with('error', 'يرجى اختيار الصف والقسم أولاً');
        }

        // Get students in this section using a JOIN with the enroll table
        $db = \Config\Database::connect();
        $students = $db->table('enroll e')
            ->select('s.student_id, s.name')
            ->join('student s', 's.student_id = e.student_id')
            ->where([
                'e.class_id' => $classId,
                'e.section_id' => $sectionId,
                'e.school' => $schoolId,
                'e.year' => $year
            ])
            ->orderBy('s.name', 'ASC')
            ->get()->getResultArray();

        $studentIds = array_column($students, 'student_id');

        // Get existing attendance records for this day robustly using student IDs
        $existing = $this->attendanceModel->getAttendanceForStudents($studentIds, $timestamp, $schoolId);

        // Map attendance status to student IDs
        $attendanceMap = [];
        $notesMap = [];
        foreach ($existing as $record) {
            $attendanceMap[$record['student_id']] = $record['status'];
            $notesMap[$record['student_id']] = $record['notes'];
        }

        $data = [
            'students' => $students,
            'attendanceMap' => $attendanceMap,
            'notesMap' => $notesMap,
            'selected' => [
                'class_id' => $classId,
                'section_id' => $sectionId,
                'date' => $dateStr
            ],
            'current_class' => $this->classModel->find($classId),
            'current_section' => $this->sectionModel->find($sectionId),
            'page_title' => 'رصد حضور الطلاب - ' . $dateStr,
            'active_menu' => 'attendance'
        ];

        return view('admin/attendance/take', $data);
    }

    /**
     * Save Attendance Records
     */
    public function save()
    {
        $schoolId = $this->schoolId;
        $year = $this->currentYear;

        $classId = $this->request->getPost('class_id');
        $sectionId = $this->request->getPost('section_id');
        $dateStr = $this->request->getPost('date');
        $timestamp = strtotime($dateStr);

        $studentIds = $this->request->getPost('student_id');
        $statuses = $this->request->getPost('status'); // array [student_id => status]
        $notes = $this->request->getPost('notes');  // array [student_id => note]

        if (empty($studentIds)) {
            return redirect()->back()->with('error', 'لا يوجد طلاب تم اختيارهم');
        }

        foreach ($studentIds as $studentId) {
            $status = $statuses[$studentId] ?? 1; // Default to Present (1)
            $note = $notes[$studentId] ?? '';

            // Check if record already exists
            $existing = $this->attendanceModel->where([
                'student_id' => $studentId,
                'timestamp' => $timestamp,
                'school' => $schoolId
            ])->first();

            $saveData = [
                'school' => $schoolId,
                'timestamp' => $timestamp,
                'year' => $year,
                'student_id' => $studentId,
                'status' => $status,
                'notes' => $note
            ];

            if ($existing) {
                $this->attendanceModel->update($existing['attendance_id'], $saveData);
            } else {
                $this->attendanceModel->insert($saveData);
            }
        }

        return redirect()->to(site_url("admin/attendance/take?class_id=$classId&section_id=$sectionId&date=$dateStr"))
            ->with('success', 'تم حفظ سجل الحضور بنجاح');
    }
}
