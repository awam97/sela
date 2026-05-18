<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegistrationRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'school_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'class_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'sex' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'otp_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
            ],
            'otp_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('registration_requests');
    }

    public function down()
    {
        $this->forge->dropTable('registration_requests');
    }
}
