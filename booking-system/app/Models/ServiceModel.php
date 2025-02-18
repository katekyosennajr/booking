<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name',
        'description',
        'duration',
        'price',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'duration' => 'required|integer|greater_than[0]',
        'price' => 'required|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Service name is required',
            'min_length' => 'Service name must be at least 3 characters long',
            'max_length' => 'Service name cannot exceed 255 characters'
        ],
        'duration' => [
            'required' => 'Duration is required',
            'integer' => 'Duration must be a whole number',
            'greater_than' => 'Duration must be greater than 0'
        ],
        'price' => [
            'required' => 'Price is required',
            'numeric' => 'Price must be a number',
            'greater_than_equal_to' => 'Price cannot be negative'
        ]
    ];

    protected $skipValidation = false;

    // Get all active services
    public function getActiveServices()
    {
        return $this->where('is_active', true)->findAll();
    }
}
