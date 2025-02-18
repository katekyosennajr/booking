<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
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
            'key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'value' => [
                'type' => 'TEXT',
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'string, integer, float, boolean, json',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('settings');

        // Insert default settings
        $defaultSettings = [
            [
                'key' => 'business_hours_start',
                'value' => '09:00',
                'type' => 'string',
                'description' => 'Business opening hour'
            ],
            [
                'key' => 'business_hours_end',
                'value' => '17:00',
                'type' => 'string',
                'description' => 'Business closing hour'
            ],
            [
                'key' => 'booking_interval',
                'value' => '60',
                'type' => 'integer',
                'description' => 'Default booking duration in minutes'
            ],
            [
                'key' => 'max_advance_booking_days',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Maximum days in advance a booking can be made'
            ],
            [
                'key' => 'min_advance_booking_hours',
                'value' => '24',
                'type' => 'integer',
                'description' => 'Minimum hours in advance a booking must be made'
            ],
        ];

        $builder = $this->db->table('settings');
        foreach ($defaultSettings as $setting) {
            $builder->insert($setting);
        }
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
