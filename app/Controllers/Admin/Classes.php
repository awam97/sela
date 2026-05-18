<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\SectionModel;

class Classes extends BaseController
{
    protected $classModel;
    protected $sectionModel;

    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->sectionModel = new SectionModel();
    }

    /**
     * List all classes and their sections
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $classes = $this->classModel->where('school', session()->get('school_id'))->findAll();
        
        // Enrich classes with their sections
        foreach ($classes as &$class) {
            $class['sections'] = $this->sectionModel->where('class_id', $class['class_id'])->findAll();
        }

        $data = [
            'title' => 'إدارة الصفوف والفصول',
            'page_title' => 'هيكلية الصفوف الدراسية',
            'active_menu' => 'students',
            'classes' => $classes
        ];

        return view('admin/classes/index', $data);
    }

    /**
     * Create Class
     */
    public function create()
    {
        return redirect()->to('/admin/classes');
    }

    public function postCreate()
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'study_system' => $this->request->getPost('study_system') ?? 'semester',
            'balance' => $this->request->getPost('balance') ?? 0,
            'school' => session()->get('school_id')
        ];

        if ($this->classModel->insert($data)) {
            return redirect()->to('/admin/classes')->with('success', 'تم إضافة الصف بنجاح');
        }
        return redirect()->back()->with('error', 'فشل إضافة الصف');
    }

    /**
     * Edit Class
     */
    public function edit($id)
    {
        return redirect()->to('/admin/classes');
    }

    public function postEdit($id)
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'study_system' => $this->request->getPost('study_system') ?? 'semester',
            'balance' => $this->request->getPost('balance') ?? 0
        ];

        if ($this->classModel->update($id, $data)) {
            return redirect()->to('/admin/classes')->with('success', 'تم تحديث الصف بنجاح');
        }
        return redirect()->back()->with('error', 'فشل التحديث');
    }

    /**
     * Delete Class
     */
    public function delete($id)
    {
        // Optional: Check if students are enrolled before deleting
        if ($this->classModel->delete($id)) {
            $this->sectionModel->where('class_id', $id)->delete();
            return redirect()->to('/admin/classes')->with('success', 'تم حذف الصف وجميع فصوله');
        }
        return redirect()->to('/admin/classes')->with('error', 'فشل الحذف');
    }

    /**
     * Create Section
     */
    public function section_create($class_id)
    {
        $class = $this->classModel->find($class_id);
        if (!$class) return redirect()->to('/admin/classes')->with('error', 'الصف غير موجود');

        $data = [
            'title' => 'إضافة فصل جديد',
            'page_title' => 'إضافة فصل إلى ' . $class['name'],
            'active_menu' => 'students',
            'class' => $class
        ];
        return view('admin/sections/create', $data);
    }

    public function section_postCreate($class_id)
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'class_id' => $class_id,
            'school' => session()->get('school_id')
        ];

        if ($this->sectionModel->insert($data)) {
            return redirect()->to('/admin/classes')->with('success', 'تم إضافة الفصل بنجاح');
        }
        return redirect()->back()->with('error', 'فشل إضافة الفصل');
    }

    /**
     * Edit Section
     */
    public function section_edit($id)
    {
        return redirect()->to('/admin/classes');
    }

    public function section_postEdit($id)
    {
        $data = [
            'name' => $this->request->getPost('name')
        ];

        if ($this->sectionModel->update($id, $data)) {
            return redirect()->to('/admin/classes')->with('success', 'تم تحديث الفصل بنجاح');
        }
        return redirect()->back()->with('error', 'فشل التحديث');
    }

    /**
     * Delete Section
     */
    public function section_delete($id)
    {
        if ($this->sectionModel->delete($id)) {
            return redirect()->to('/admin/classes')->with('success', 'تم حذف الفصل بنجاح');
        }
        return redirect()->to('/admin/classes')->with('error', 'فشل الحذف');
    }
}
