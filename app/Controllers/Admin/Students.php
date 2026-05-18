<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;

class Students extends BaseController
{
    protected $studentModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
    }

    /**
     * The Selector Hub Home
     */
    public function index()
    {
        $data = [
            'title' => 'إدارة الطلاب',
            'page_title' => 'مركز إدارة شؤون الطلاب',
            'active_menu' => 'students',
            'classes' => $this->db->table('class')->get()->getResultArray(),
            'services' => $this->db->table('student_services')->where('is_active', 1)->orderBy('sort_order', 'ASC')->get()->getResultArray()
        ];

        return view('admin/students/index', $data);
    }

    /**
     * Handle the Selector Redirection
     */
    public function selector()
    {
        $serviceId = $this->request->getPost('service_id');
        $classId = $this->request->getPost('class_id');
        $sectionId = $this->request->getPost('section_id');

        $subjectId = $this->request->getPost('subject_id');

        if (!$serviceId || (!$classId && !in_array($serviceId, ['8', '11']))) {
            return redirect()->back()->with('error', 'يرجى اختيار الخدمة والمرحلة الدراسية');
        }

        switch ($serviceId) {
            case '1': // Data Management (List)
                return redirect()->to("/admin/students/list/{$classId}/" . ($sectionId ?: 'all'));
            case '2': // Student Photos
                return redirect()->to("/admin/students/pics/{$classId}/" . ($sectionId ?: 'all'));
            case '3': // Section Distribution
                return redirect()->to("/admin/students/sections/{$classId}/" . ($sectionId ?: 'all'));
            case '4': // ID Cards (Entrance Cards)
                return redirect()->to("/admin/students/entrance_cards/{$classId}/" . ($sectionId ?: 'all'));
            case '5': // Access Cards
                return redirect()->to("/admin/students/access_cards/{$classId}/" . ($sectionId ?: 'all'));
            case '6': // Homework
                return redirect()->to("/admin/homework/index/{$classId}");
            case '7': // Timetable (Routines)
                return redirect()->to("/admin/routines/view/{$classId}/" . ($sectionId ?: '1'));
            case '8': // Exam Timetable - Corrected mapping
                return redirect()->to("/admin/academic/exams");
            case '9': // Subjects
                return redirect()->to("/admin/subjects/index/{$classId}");
            case '10': // Monthly Marks (Academic Marks Entry)
                return redirect()->to("/admin/academic/marks?class_id={$classId}&section_id={$sectionId}&subject_id={$subjectId}");
            case '11': // Classes & Sections Management
                return redirect()->to("/admin/classes");
            case '12': // Global Marksheet Report (The one just optimized)
                return redirect()->to("/admin/reports/marksheet/{$classId}/" . ($sectionId ?: 'all'));
            default:
                return redirect()->back()->with('error', 'هذه الخدمة قيد التطوير حالياً');
        }
    }

    /**
     * Internal: Fetch students with enroll info (Filtered by Global Year)
     */
    private function getEnrolledStudents($classId, $sectionId = 'all')
    {
        $builder = $this->db->table('enroll e')
            ->select('s.*, c.name as class_name, e.class_id, sec.name as section_name, e.section_id as current_section_id, e.year')
            ->join('student s', 's.student_id = e.student_id')
            ->join('class c', 'c.class_id = e.class_id')
            ->join('section sec', 'sec.section_id = e.section_id')
            ->where('e.class_id', $classId)
            ->where('e.year', $this->currentYear);

        if ($sectionId !== 'all') {
            $builder->where('e.section_id', $sectionId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Service 1: Data Management
     */
    public function list($classId, $sectionId = 'all')
    {
        $data = [
            'students' => $this->getEnrolledStudents($classId, $sectionId),
            'sections' => $this->db->table('section')->where('class_id', $classId)->get()->getResultArray(),
            'classes_all' => $this->db->table('class')->get()->getResultArray(),
            'class_id' => $classId,
            'section_id' => $sectionId,
            'title' => 'قائمة الطلاب',
            'page_title' => 'إدارة بيانات الطلاب',
            'active_menu' => 'students'
        ];
        return view('admin/students/list', $data);
    }

    /**
     * Service 2: Student Photos
     */
    public function pics($classId, $sectionId = 'all')
    {
        $data = [
            'students' => $this->getEnrolledStudents($classId, $sectionId),
            'class_id' => $classId,
            'section_id' => $sectionId,
            'title' => 'صور الطلاب',
            'page_title' => 'إدارة الصور الشخصية للطلاب',
            'active_menu' => 'students'
        ];
        return view('admin/students/pics', $data);
    }

    public function postPics($classId, $sectionId = 'all')
    {
        $files = $this->request->getFiles();
        foreach ($files as $key => $file) {
            if (strpos($key, 'file_') === 0 && $file->isValid() && !$file->hasMoved()) {
                $studentId = str_replace(['file_', '_a'], '', $key);
                $newName = $studentId . '.jpg';
                $file->move(FCPATH . 'upload/student_images/', $newName, true); // true to overwrite
            }
        }
        return redirect()->back()->with('success', 'تم تحديث الصور بنجاح');
    }

    /**
     * Service 3: Section Distribution
     */
    public function sections($classId, $sectionId = 'all')
    {
        $data = [
            'students' => $this->getEnrolledStudents($classId, $sectionId),
            'sections' => $this->db->table('section')->where('class_id', $classId)->get()->getResultArray(),
            'classes' => $this->db->table('class')->get()->getResultArray(),
            'class_id' => $classId,
            'section_id' => $sectionId,
            'title' => 'توزيع الفصول',
            'page_title' => 'توزيع الطلاب على الفصول والمراحل',
            'active_menu' => 'students'
        ];
        return view('admin/students/sections', $data);
    }

    public function postSections($classId)
    {
        $postData = $this->request->getPost();
        foreach ($postData as $key => $value) {
            if (strpos($key, 'section_') === 0) {
                $studentId = str_replace('section_', '', $key);
                $newSectionId = $value;
                $newClassId = $this->request->getPost('class_' . $studentId);
                if ($newClassId && $newSectionId) {
                    $this->db->table('enroll')->where('student_id', $studentId)->update([
                        'class_id' => $newClassId,
                        'section_id' => $newSectionId
                    ]);
                }
            }
        }
        return redirect()->back()->with('success', 'تم تحديث التوزيع بنجاح');
    }

    /**
     * Service 4: Entrance Cards (ID Cards)
     */
    public function entranceCards($classId, $sectionId = 'all')
    {
        $school = $this->db->table('schools')->where('ID', $this->schoolId)->get()->getRowArray();
        $data = [
            'students' => $this->getEnrolledStudents($classId, $sectionId),
            'class_id' => $classId,
            'section_id' => $sectionId,
            'school_name' => $school['name'] ?? 'مؤسسة تعليمية',
            'system_name' => $this->db->table('settings')->where('type', 'system_name')->get()->getRow()->description ?? 'Sela'
        ];
        return view('admin/students/entrance_cards', $data);
    }

    /**
     * Service 5: Portal Access Cards
     */
    public function accessCards($classId, $sectionId = 'all')
    {
        $school = $this->db->table('schools')->where('ID', $this->schoolId)->get()->getRowArray();
        $data = [
            'students' => $this->getEnrolledStudents($classId, $sectionId),
            'class_id' => $classId,
            'section_id' => $sectionId,
            'school_name' => $school['name'] ?? 'مؤسسة تعليمية',
            'system_name' => $this->db->table('settings')->where('type', 'system_name')->get()->getRow()->description ?? 'Sela'
        ];
        return view('admin/students/access_cards', $data);
    }

    public function create()
    {
        return redirect()->to('/admin/students');
    }

    /**
     * AJAX: Fetch All Classes for Modal
     */
    public function get_classes_all()
    {
        $classes = $this->db->table('class')->get()->getResultArray();
        return $this->response->setJSON($classes);
    }

    public function postCreate()
    {
        $studentData = [
            'name' => $this->request->getPost('name'),
            'mother' => $this->request->getPost('mother'),
            'nationalid' => $this->request->getPost('roll'),
            'sex' => $this->request->getPost('sex'),
            'phone' => $this->request->getPost('phone'),
            'password' => $this->request->getPost('password'),
            'activate' => 0,
            'school' => $this->schoolId
        ];

        if ($this->studentModel->insert($studentData)) {
            $student_id = $this->db->insertID();
            $enrollData = [
                'student_id' => $student_id,
                'class_id' => $this->request->getPost('class_id'),
                'section_id' => $this->request->getPost('section_id'),
                'year' => $this->currentYear,
                'school' => $this->schoolId
            ];
            $this->db->table('enroll')->insert($enrollData);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'تم إضافة الطالب بنجاح']);
            }
            return redirect()->to('/admin/students')->with('success', 'تم إضافة الطالب بنجاح');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'فشل الإضافة']);
        }
        return redirect()->back()->with('error', 'فشل الإضافة');
    }

    public function edit($id)
    {
        $student = $this->studentModel->find($id);
        if (!$student) {
            return redirect()->to('/admin/students')->with('error', 'الطالب غير موجود');
        }
        $enroll = $this->db->table('enroll')
            ->where('student_id', $id)
            ->where('year', $this->currentYear)
            ->get()->getRowArray();

        $data = [
            'title' => 'تعديل طالب',
            'page_title' => 'تعديل بيانات الطالب',
            'active_menu' => 'students',
            'student' => $student,
            'enroll' => $enroll,
            'classes' => $this->db->table('class')->get()->getResultArray()
        ];
        return view('admin/students/edit', $data);
    }

    public function postEdit($id)
    {
        $studentData = [
            'name' => $this->request->getPost('name'),
            'mother' => $this->request->getPost('mother'),
            'nationalid' => $this->request->getPost('roll'),
            'sex' => $this->request->getPost('sex'),
            'phone' => $this->request->getPost('phone'),
            'password' => $this->request->getPost('password'),
        ];

        if ($this->studentModel->update($id, $studentData)) {
            $enrollData = [
                'class_id' => $this->request->getPost('class_id'),
                'section_id' => $this->request->getPost('section_id'),
            ];
            $this->db->table('enroll')
                ->where('student_id', $id)
                ->where('year', $this->currentYear)
                ->update($enrollData);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'تم تحديث بيانات الطالب بنجاح']);
            }
            return redirect()->to('/admin/students')->with('success', 'تم تحديث بيانات الطالب بنجاح');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'فشل التحديث']);
        }
        return redirect()->back()->with('error', 'فشل التحديث');
    }

    public function profile($id)
    {
        $student = $this->db->table('student s')
            ->select('s.*, c.name as class_name, sec.name as section_name, e.year')
            ->join('enroll e', 'e.student_id = s.student_id')
            ->join('class c', 'c.class_id = e.class_id')
            ->join('section sec', 'sec.section_id = e.section_id')
            ->where('s.student_id', $id)
            ->where('e.year', $this->currentYear)
            ->get()->getRowArray();

        if (!$student) {
            return redirect()->to('/admin/students')->with('error', 'الطالب غير موجود في العام الدراسي الحالي');
        }

        $data = [
            'title' => 'ملف الطالب',
            'page_title' => 'الملف الشخصي للطالب',
            'active_menu' => 'students',
            'student' => $student
        ];
        return view('admin/students/profile', $data);
    }

    public function getSections($classId)
    {
        $sections = $this->db->table('section')->where(['class_id' => $classId, 'school' => $this->schoolId])->get()->getResultArray();
        $options = '<option value="">اختر الفصل</option>';
        foreach ($sections as $section) {
            $options .= '<option value="' . $section['section_id'] . '">' . $section['name'] . '</option>';
        }
        return $options;
    }

    public function getSubjects($classId)
    {
        $subjects = $this->db->table('subject')->where(['class_id' => $classId, 'school' => $this->schoolId])->get()->getResultArray();
        $options = '<option value="">اختر المادة</option>';
        foreach ($subjects as $subject) {
            $options .= '<option value="' . $subject['subject_id'] . '">' . $subject['name'] . '</option>';
        }
        return $options;
    }

    public function getSectionsGrid($classId)
    {
        $sections = $this->db->table('section')->where(['class_id' => $classId, 'school' => $this->schoolId])->get()->getResultArray();
        $html = '';
        if (empty($sections)) {
            return '<div class="col-12 text-center text-muted py-4">لا توجد فصول لهذا الصف</div>';
        }
        foreach ($sections as $section) {
            $html .= '<div class="selection-item-card" onclick="selectSection(\'' . $section['section_id'] . '\', this)">
                        <i class=\'bx bxs-door-open\'></i>
                        <div class="item-name">' . $section['name'] . '</div>
                    </div>';
        }
        return $html;
    }

    public function getSubjectsGrid($classId)
    {
        $subjects = $this->db->table('subject')->where(['class_id' => $classId, 'school' => $this->schoolId])->get()->getResultArray();
        $html = '';
        if (empty($subjects)) {
            return '<div class="col-12 text-center text-muted py-4">لا توجد مواد دراسية لهذا الصف</div>';
        }
        foreach ($subjects as $subject) {
            $html .= '<div class="selection-item-card" onclick="selectSubject(\'' . $subject['subject_id'] . '\', this)">
                        <i class=\'bx bxs-book-bookmark\'></i>
                        <div class="item-name">' . $subject['name'] . '</div>
                    </div>';
        }
        return $html;
    }

    public function delete($id)
    {
        if ($this->studentModel->delete($id)) {
            $this->db->table('enroll')->where('student_id', $id)->delete();

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'تم حذف الطالب بنجاح']);
            }
            return redirect()->to('/admin/students')->with('success', 'تم حذف الطالب بنجاح');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'فشل في حذف الطالب']);
        }
        return redirect()->to('/admin/students')->with('error', 'فشل في حذف الطالب');
    }

    /**
     * AJAX: Fetch Student List Table Partial
     */
    public function fetch_list($classId, $sectionId = 'all')
    {
        $viewPref = $this->request->getGet('view') ?: 'list';
        $data = [
            'students' => $this->getEnrolledStudents($classId, $sectionId),
            'view_pref' => $viewPref
        ];
        return view('admin/students/partials/student_list_table', $data);
    }
}
