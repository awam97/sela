<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDynamicGrading extends Migration
{
    public function up()
    {
        // 1. Create subject_mark_distribution table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'subject_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'max_mark' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('subject_id', 'subject', 'subject_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('subject_mark_distribution');

        // 2. Add marks_json column to mark table
        $fields = [
            'marks_json' => [
                'type' => 'JSON', // Use JSON for MySQL 5.7+ or TEXT for compatibility
                'null' => true,
                'after' => 'exam_id'
            ],
        ];
        
        // Note: CodeIgniter 4 MySQLi driver handles 'JSON' type if supported by DB.
        // Fallback to TEXT if JSON migration fails in older MySQL versions.
        try {
            $this->forge->addColumn('mark', $fields);
        } catch (\Exception $e) {
            $fields['marks_json']['type'] = 'TEXT';
            $this->forge->addColumn('mark', $fields);
        }
    }

    public function down()
    {
        $this->forge->dropTable('subject_mark_distribution');
        $this->forge->dropColumn('mark', 'marks_json');
    }
}
