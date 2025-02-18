<?php

namespace App\Controllers;

use App\Models\TimeSlotModel;
use CodeIgniter\API\ResponseTrait;

class TimeSlotController extends BaseController
{
    use ResponseTrait;

    protected $timeSlotModel;

    public function __construct()
    {
        $this->timeSlotModel = new TimeSlotModel();
    }

    public function index()
    {
        $timeSlots = $this->timeSlotModel->findAll();
        
        // Format the response
        $formattedSlots = array_map(function($slot) {
            $slot['day_name'] = $this->timeSlotModel->getDayName($slot['day_of_week']);
            return $slot;
        }, $timeSlots);

        return $this->respond($formattedSlots);
    }

    public function show($id = null)
    {
        $timeSlot = $this->timeSlotModel->find($id);
        if (!$timeSlot) {
            return $this->failNotFound('Time slot not found');
        }

        $timeSlot['day_name'] = $this->timeSlotModel->getDayName($timeSlot['day_of_week']);
        return $this->respond($timeSlot);
    }

    public function create()
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only administrators can create time slots');
        }

        $data = [
            'day_of_week' => $this->request->getVar('day_of_week'),
            'start_time' => $this->request->getVar('start_time'),
            'end_time' => $this->request->getVar('end_time'),
            'capacity' => $this->request->getVar('capacity'),
            'is_active' => $this->request->getVar('is_active') ?? true
        ];

        if (!$this->timeSlotModel->insert($data)) {
            return $this->fail($this->timeSlotModel->errors());
        }

        return $this->respondCreated(['message' => 'Time slot created successfully']);
    }

    public function update($id = null)
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only administrators can update time slots');
        }

        $timeSlot = $this->timeSlotModel->find($id);
        if (!$timeSlot) {
            return $this->failNotFound('Time slot not found');
        }

        $data = [
            'day_of_week' => $this->request->getVar('day_of_week'),
            'start_time' => $this->request->getVar('start_time'),
            'end_time' => $this->request->getVar('end_time'),
            'capacity' => $this->request->getVar('capacity'),
            'is_active' => $this->request->getVar('is_active')
        ];

        if (!$this->timeSlotModel->update($id, $data)) {
            return $this->fail($this->timeSlotModel->errors());
        }

        return $this->respond(['message' => 'Time slot updated successfully']);
    }

    public function delete($id = null)
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only administrators can delete time slots');
        }

        $timeSlot = $this->timeSlotModel->find($id);
        if (!$timeSlot) {
            return $this->failNotFound('Time slot not found');
        }

        if (!$this->timeSlotModel->delete($id)) {
            return $this->fail('Failed to delete time slot');
        }

        return $this->respondDeleted(['message' => 'Time slot deleted successfully']);
    }

    public function getAvailable($dayOfWeek)
    {
        $timeSlots = $this->timeSlotModel->getAvailableTimeSlots($dayOfWeek);
        
        // Format the response
        $formattedSlots = array_map(function($slot) {
            $slot['day_name'] = $this->timeSlotModel->getDayName($slot['day_of_week']);
            return $slot;
        }, $timeSlots);

        return $this->respond($formattedSlots);
    }

    public function checkAvailability()
    {
        $rules = [
            'time_slot_id' => 'required|integer',
            'date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $timeSlotId = $this->request->getVar('time_slot_id');
        $date = $this->request->getVar('date');

        $isAvailable = $this->timeSlotModel->isSlotAvailable($timeSlotId, $date);

        return $this->respond([
            'is_available' => $isAvailable
        ]);
    }
}
