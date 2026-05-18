<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class RemoteCleanup extends Controller
{
    public function index()
    {
        echo "<h1>جارٍ بدء عملية التنظيف العميق للسيرفر...</h1>";
        
        $filesToDelete = [
            'check_schema_ci4.php',
            'check_schema_final.php',
            'debug_years.php',
            'dump_attendance.php',
            'dump_attendance_v2.php',
            'dump_enroll_years.php',
            'fix_attendance_schema_direct.php',
            'research_clue.txt',
            APPPATH . 'Controllers/DbCheck.php',
            APPPATH . 'Controllers/SchemaCheck.php'
        ];

        $deletedCount = 0;
        foreach ($filesToDelete as $file) {
            $path = str_contains($file, DIRECTORY_SEPARATOR) ? $file : FCPATH . '../' . $file;
            if (file_exists($path)) {
                if (@unlink($path)) {
                    echo "<p style='color:green'>✔ تم حذف: " . basename($path) . "</p>";
                    $deletedCount++;
                } else {
                    echo "<p style='color:red'>✘ فشل حذف: " . basename($path) . " (تحقق من الصلاحيات)</p>";
                }
            }
        }

        // Clear Cache
        $cache = \Config\Services::cache();
        $cache->clean();
        echo "<p style='color:blue'>✔ تم تصفير الذاكرة المؤقتة (Cache).</p>";

        // Clear Logs
        $logPath = WRITEPATH . 'logs/';
        $logs = glob($logPath . '*.log');
        foreach ($logs as $log) {
            @unlink($log);
        }
        echo "<p style='color:blue'>✔ تم مسح كافة السجلات (Logs).</p>";

        echo "<h2>اكتملت العملية بنجاح! تم حذف $deletedCount ملفاً.</h2>";
        echo "<p style='color:orange'>تحذير: هذا الملف سيحاول الآن حذف نفسه للأمان...</p>";

        // Self-destruction
        $self = __FILE__;
        @unlink($self);
        
        return "<script>alert('اكتمل التنظيف وتم حذف أداة التنظيف تلقائياً.');</script>";
    }
}
