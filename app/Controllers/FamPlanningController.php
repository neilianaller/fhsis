<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class FamPlanningController extends ResourceController
{
    public function index(): string
    {

        $BarangaysModel = new \App\Models\BarangaysModel();

        $barangays = $BarangaysModel->findAll();


        return view('pages/famplanning', [
            'barangays' => $barangays
        ]);
    }
}
