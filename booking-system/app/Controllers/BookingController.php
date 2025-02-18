<?php

namespace App\Controllers;

use App\Models\BookingModel;
use CodeIgniter\RESTful\ResourceController;

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
            'user_id' => 1, // Temporary, will be replaced with actual user ID after authentication
            'title' => $json->title,
            'start_time' => $json->start,
            'end_time' => $json->end,
            'status' => 'pending'
        ];

        try {
            $this->bookingModel->insert($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Booking created successfully']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                                ->setJSON(['success' => false, 'message' => 'Failed to create booking']);
        }
    }

    public function update($id = null)
    {
        $json = $this->request->getJSON();
        
        $data = [
            'title' => $json->title,
            'start_time' => $json->start,
            'end_time' => $json->end
        ];

        try {
            $this->bookingModel->update($id, $data);
            return $this->response->setJSON(['success' => true, 'message' => 'Booking updated successfully']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                                ->setJSON(['success' => false, 'message' => 'Failed to update booking']);
        }
    }

    public function delete($id = null)
    {
        try {
            $this->bookingModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Booking deleted successfully']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                                ->setJSON(['success' => false, 'message' => 'Failed to delete booking']);
        }
    }
}
