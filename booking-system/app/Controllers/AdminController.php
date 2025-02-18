<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\UserModel;
use App\Models\ServiceModel;
use App\Models\TimeSlotModel;
use App\Services\EmailService;

class AdminController extends BaseController
{
    protected $bookingModel;
    protected $userModel;
    protected $serviceModel;
    protected $timeSlotModel;
    protected $emailService;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->userModel = new UserModel();
        $this->serviceModel = new ServiceModel();
        $this->timeSlotModel = new TimeSlotModel();
        $this->emailService = new EmailService();
    }

    public function index()
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return redirect()->to('/');
        }

        return view('admin/dashboard');
    }

    public function getDashboardStats()
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->response->setStatusCode(403)
                                ->setJSON(['error' => 'Unauthorized access']);
        }

        $stats = [
            'total_bookings' => $this->bookingModel->countAll(),
            'pending_bookings' => count($this->bookingModel->getBookingsByStatus('pending')),
            'total_users' => $this->userModel->countAll(),
            'total_services' => $this->serviceModel->countAll(),
            'active_services' => count($this->serviceModel->getActiveServices()),
            'recent_bookings' => $this->bookingModel->orderBy('created_at', 'DESC')
                                                   ->limit(5)
                                                   ->find(),
        ];

        return $this->response->setJSON($stats);
    }

    public function updateBookingStatus()
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->response->setStatusCode(403)
                                ->setJSON(['error' => 'Unauthorized access']);
        }

        $json = $this->request->getJSON();
        
        if (!isset($json->booking_id) || !isset($json->status)) {
            return $this->response->setStatusCode(400)
                                ->setJSON(['error' => 'Missing required parameters']);
        }

        $booking = $this->bookingModel->find($json->booking_id);
        if (!$booking) {
            return $this->response->setStatusCode(404)
                                ->setJSON(['error' => 'Booking not found']);
        }

        try {
            // Update booking status
            $this->bookingModel->update($json->booking_id, [
                'status' => $json->status,
                'notes' => $json->notes ?? $booking['notes']
            ]);

            // Get user data for email
            $user = $this->userModel->find($booking['user_id']);

            // Send email notification
            $updatedBooking = $this->bookingModel->find($json->booking_id);
            $this->emailService->sendBookingStatusUpdate($updatedBooking, $user);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Booking status updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                                ->setJSON([
                                    'error' => 'Failed to update booking status',
                                    'message' => $e->getMessage()
                                ]);
        }
    }

    public function getBookingsByDateRange()
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->response->setStatusCode(403)
                                ->setJSON(['error' => 'Unauthorized access']);
        }

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if (!$startDate || !$endDate) {
            return $this->response->setStatusCode(400)
                                ->setJSON(['error' => 'Start date and end date are required']);
        }

        $bookings = $this->bookingModel->getBookingsByDateRange($startDate, $endDate);
        return $this->response->setJSON($bookings);
    }

    public function getUserStats()
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->response->setStatusCode(403)
                                ->setJSON(['error' => 'Unauthorized access']);
        }

        $users = $this->userModel->findAll();
        $userStats = [];

        foreach ($users as $user) {
            $bookings = $this->bookingModel->getUserBookings($user['id']);
            $userStats[] = [
                'user' => $user,
                'total_bookings' => count($bookings),
                'status_counts' => [
                    'pending' => count(array_filter($bookings, fn($b) => $b['status'] === 'pending')),
                    'approved' => count(array_filter($bookings, fn($b) => $b['status'] === 'approved')),
                    'rejected' => count(array_filter($bookings, fn($b) => $b['status'] === 'rejected')),
                    'cancelled' => count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled'))
                ]
            ];
        }

        return $this->response->setJSON($userStats);
    }

    public function getServiceStats()
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->response->setStatusCode(403)
                                ->setJSON(['error' => 'Unauthorized access']);
        }

        $services = $this->serviceModel->findAll();
        $serviceStats = [];

        foreach ($services as $service) {
            $bookings = $this->bookingModel->where('service_id', $service['id'])->findAll();
            $serviceStats[] = [
                'service' => $service,
                'total_bookings' => count($bookings),
                'status_counts' => [
                    'pending' => count(array_filter($bookings, fn($b) => $b['status'] === 'pending')),
                    'approved' => count(array_filter($bookings, fn($b) => $b['status'] === 'approved')),
                    'rejected' => count(array_filter($bookings, fn($b) => $b['status'] === 'rejected')),
                    'cancelled' => count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled'))
                ]
            ];
        }

        return $this->response->setJSON($serviceStats);
    }
}
