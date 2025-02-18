<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BookingModel;

class BookingController extends ResourceController
{
    protected $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    public function index()
    {
        return view('booking/calendar');
    }

    public function getBookings()
    {
        $bookings = $this->bookingModel->findAll();
        return $this->response->setJSON($bookings);
    }

    public function create()
    {
        $json = $this->request->getJSON();
        
        $data = [
            'user_id' => $this->request->user->id,
            'service_id' => $json->service_id,
            'title' => $json->title,
            'description' => $json->description ?? '',
            'start_time' => $json->start,
            'end_time' => $json->end,
            'status' => 'pending'
        ];

        // Validate service availability
        if (!$this->bookingModel->isServiceAvailable($data['service_id'], $data['start_time'], $data['end_time'])) {
            return $this->response->setStatusCode(400)
                                ->setJSON([
                                    'success' => false,
                                    'message' => 'The selected time slot is not available or the service is not active'
                                ]);
        }

        try {
            $this->bookingModel->insert($data);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Booking created successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                                ->setJSON([
                                    'success' => false,
                                    'message' => 'Failed to create booking'
                                ]);
        }
    }

    public function update($id = null)
    {
        $json = $this->request->getJSON();
        
        // Check if booking exists
        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            return $this->response->setStatusCode(404)
                                ->setJSON([
                                    'success' => false,
                                    'message' => 'Booking not found'
                                ]);
        }

        // Only allow updates to user's own bookings unless admin
        if ($booking['user_id'] !== $this->request->user->id && $this->request->user->role !== 'admin') {
            return $this->response->setStatusCode(403)
                                ->setJSON([
                                    'success' => false,
                                    'message' => 'You are not authorized to update this booking'
                                ]);
        }

        $data = [
            'title' => $json->title,
            'description' => $json->description ?? $booking['description'],
            'start_time' => $json->start,
            'end_time' => $json->end,
            'status' => $json->status ?? $booking['status']
        ];

        // If changing time, validate availability
        if ($data['start_time'] !== $booking['start_time'] || $data['end_time'] !== $booking['end_time']) {
            if (!$this->bookingModel->isServiceAvailable($booking['service_id'], $data['start_time'], $data['end_time'], $id)) {
                return $this->response->setStatusCode(400)
                                    ->setJSON([
                                        'success' => false,
                                        'message' => 'The selected time slot is not available'
                                    ]);
            }
        }

        try {
            $this->bookingModel->update($id, $data);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Booking updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                                ->setJSON([
                                    'success' => false,
                                    'message' => 'Failed to update booking'
                                ]);
        }
    }

    public function delete($id = null)
    {
        // Check if booking exists
        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            return $this->response->setStatusCode(404)
                                ->setJSON([
                                    'success' => false,
                                    'message' => 'Booking not found'
                                ]);
        }

        // Only allow deletion of user's own bookings unless admin
        if ($booking['user_id'] !== $this->request->user->id && $this->request->user->role !== 'admin') {
            return $this->response->setStatusCode(403)
                                ->setJSON([
                                    'success' => false,
                                    'message' => 'You are not authorized to delete this booking'
                                ]);
        }

        try {
            $this->bookingModel->delete($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Booking deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                                ->setJSON([
                                    'success' => false,
                                    'message' => 'Failed to delete booking'
                                ]);
        }
    }

    public function getUserBookings()
    {
        $userId = $this->request->user->id;
        $bookings = $this->bookingModel->getUserBookings($userId);
        return $this->response->setJSON($bookings);
    }

    public function checkAvailability()
    {
        $json = $this->request->getJSON();
        
        if (!isset($json->service_id) || !isset($json->start) || !isset($json->end)) {
            return $this->response->setStatusCode(400)
                                ->setJSON([
                                    'success' => false,
                                    'message' => 'Missing required parameters'
                                ]);
        }

        $isAvailable = $this->bookingModel->isServiceAvailable(
            $json->service_id,
            $json->start,
            $json->end
        );

        return $this->response->setJSON([
            'success' => true,
            'is_available' => $isAvailable
        ]);
    }
}
