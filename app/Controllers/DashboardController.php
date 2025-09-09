<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class DashboardController extends ResourceController
{
    public function index(): string
    {

        return view('pages/dashboard');
    }
}
