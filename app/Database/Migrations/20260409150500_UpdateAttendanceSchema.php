<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAttendanceSchema extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Fields to add to attendance table
        $fields = [
            'class_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'year'
            ],
            'section_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'class_id'
            ],
        ];

        // check if columns exist before adding
        $existingFields = $db->getFieldNames('attendance');
        
        $toAdd = [];
        if (!in_array('class_id', $existingFields)) {
            $toAdd['class_id'] = $fields['class_id'];
        }
        if (!in_array('section_id', $existingFields)) {
            $toAdd['section_id'] = $fields['section_id'];
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('attendance', $toAdd);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('attendance', 'class_id');
        $this->forge->dropColumn('attendance', 'section_id');
    }
}
