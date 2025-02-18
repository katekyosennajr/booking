<?php

namespace App\Controllers;

use App\Models\ServiceModel;
use CodeIgniter\API\ResponseTrait;

class ServiceController extends BaseController
{
    use ResponseTrait;

    protected $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new ServiceModel();
    }

    public function index()
    {
        $services = $this->serviceModel->findAll();
        return $this->respond($services);
    }

    public function show($id = null)
    {
        $service = $this->serviceModel->find($id);
        if (!$service) {
            return $this->failNotFound('Service not found');
        }
        return $this->respond($service);
    }

    public function create()
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only administrators can create services');
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
            'duration' => $this->request->getVar('duration'),
            'price' => $this->request->getVar('price'),
            'is_active' => $this->request->getVar('is_active') ?? true
        ];

        if (!$this->serviceModel->insert($data)) {
            return $this->fail($this->serviceModel->errors());
        }

        return $this->respondCreated(['message' => 'Service created successfully']);
    }

    public function update($id = null)
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only administrators can update services');
        }

        $service = $this->serviceModel->find($id);
        if (!$service) {
            return $this->failNotFound('Service not found');
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
            'duration' => $this->request->getVar('duration'),
            'price' => $this->request->getVar('price'),
            'is_active' => $this->request->getVar('is_active')
        ];

        if (!$this->serviceModel->update($id, $data)) {
            return $this->fail($this->serviceModel->errors());
        }

        return $this->respond(['message' => 'Service updated successfully']);
    }

    public function delete($id = null)
    {
        // Check if user is admin
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only administrators can delete services');
        }

        $service = $this->serviceModel->find($id);
        if (!$service) {
            return $this->failNotFound('Service not found');
        }

        if (!$this->serviceModel->delete($id)) {
            return $this->fail('Failed to delete service');
        }

        return $this->respondDeleted(['message' => 'Service deleted successfully']);
    }

    public function active()
    {
        $services = $this->serviceModel->getActiveServices();
        return $this->respond($services);
    }
}
