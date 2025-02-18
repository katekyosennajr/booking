<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'title' => 'required|min_length[3]|max_length[255]',
        'start_time' => 'required|valid_date',
        'end_time' => 'required|valid_date',
        'status' => 'required|in_list[pending,approved,rejected,cancelled]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be a number'
        ],
        'title' => [
            'required' => 'Title is required',
            'min_length' => 'Title must be at least 3 characters long',
            'max_length' => 'Title cannot exceed 255 characters'
        ],
        'start_time' => [
            'required' => 'Start time is required',
            'valid_date' => 'Start time must be a valid date'
        ],
        'end_time' => [
            'required' => 'End time is required',
            'valid_date' => 'End time must be a valid date'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be one of: pending, approved, rejected, cancelled'
        ]
    ];

    protected $skipValidation = false;

    // Get bookings for a specific user
    public function getUserBookings($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

    // Get bookings by status
    public function getBookingsByStatus($status)
    {
        return $this->where('status', $status)->findAll();
    }

    // Get bookings for a specific date range
    public function getBookingsByDateRange($startDate, $endDate)
    {
        return $this->where('start_time >=', $startDate)
                    ->where('end_time <=', $endDate)
                    ->findAll();
    }
}
