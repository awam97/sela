<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;

class Finance extends BaseController
{
    protected $invoiceModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
    }

    public function index()
    {
        $session = session();
        $schoolId = $session->get('school_id') ?? 1;
        $currentYear = $session->get('running_year') ?? '2026';

        $data = [
            'title' => 'الشؤون المالية',
            'page_title' => 'إدارة الفواتير',
            'active_menu' => 'finance',
            'invoices' => $this->invoiceModel
                ->where('school', $schoolId)
                ->where('year', $currentYear)
                ->limit(100)
                ->findAll()
        ];

        return view('admin/finance/index', $data);
    }
}
