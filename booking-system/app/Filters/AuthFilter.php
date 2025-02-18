<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeader('Authorization');
        if (!$header) {
            return Services::response()
                ->setJSON(['error' => 'No token provided'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $token = explode(' ', $header->getValue())[1];
            $key = getenv('JWT_SECRET');
            $decoded = \Firebase\JWT\JWT::decode($token, $key, ['HS256']);

            // Add user data to request for controllers to use
            $request->user = $decoded;
            return $request;
        } catch (Exception $e) {
            return Services::response()
                ->setJSON(['error' => 'Invalid token'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the request
    }
}
