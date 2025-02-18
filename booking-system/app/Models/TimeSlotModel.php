<?php

namespace App\Models;

use CodeIgniter\Model;

class TimeSlotModel extends Model
{
    protected $table = 'time_slots';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'day_of_week',
        'start_time',
        'end_time',
        'capacity',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'day_of_week' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[6]',
        'start_time' => 'required|valid_time',
        'end_time' => 'required|valid_time|check_time_range[start_time]',
        'capacity' => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'day_of_week' => [
            'required' => 'Day of week is required',
            'integer' => 'Day of week must be a number',
            'greater_than_equal_to' => 'Day of week must be between 0 and 6',
            'less_than_equal_to' => 'Day of week must be between 0 and 6'
        ],
        'start_time' => [
            'required' => 'Start time is required',
            'valid_time' => 'Start time must be a valid time'
        ],
        'end_time' => [
            'required' => 'End time is required',
            'valid_time' => 'End time must be a valid time',
            'check_time_range' => 'End time must be after start time'
        ],
        'capacity' => [
            'required' => 'Capacity is required',
            'integer' => 'Capacity must be a whole number',
            'greater_than' => 'Capacity must be greater than 0'
        ]
    ];

    protected $skipValidation = false;

    // Get available time slots for a specific day
    public function getAvailableTimeSlots($dayOfWeek)
    {
        return $this->where('day_of_week', $dayOfWeek)
                    ->where('is_active', true)
                    ->findAll();
    }

    // Check if a time slot is available for booking
    public function isSlotAvailable($timeSlotId, $date)
    {
        $timeSlot = $this->find($timeSlotId);
        if (!$timeSlot || !$timeSlot['is_active']) {
            return false;
        }

        // Get count of existing bookings for this time slot
        $bookingModel = new \App\Models\BookingModel();
        $bookingCount = $bookingModel->where('time_slot_id', $timeSlotId)
                                   ->where('DATE(start_time)', $date)
                                   ->countAllResults();

        return $bookingCount < $timeSlot['capacity'];
    }

    // Get day name from day_of_week
    public function getDayName($dayOfWeek)
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday'
        ];
        return $days[$dayOfWeek] ?? '';
    }
}
