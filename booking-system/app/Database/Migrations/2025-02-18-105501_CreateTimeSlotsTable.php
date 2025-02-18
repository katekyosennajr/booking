<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTimeSlotsTable extends Migration
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
            'day_of_week' => [
                'type'       => 'INT',
                'constraint' => 1,
                'comment'    => '0=Sunday, 1=Monday, ..., 6=Saturday',
            ],
            'start_time' => [
                'type' => 'TIME',
            ],
            'end_time' => [
                'type' => 'TIME',
            ],
            'capacity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'comment'    => 'Number of concurrent bookings allowed',
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['day_of_week', 'start_time', 'end_time']);
        $this->forge->createTable('time_slots');
    }

    public function down()
    {
        $this->forge->dropTable('time_slots');
    }
}
