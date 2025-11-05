<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class ReportsController extends ResourceController
{
    public function index(): string
    {

        $SectionsModel = new \App\Models\SectionsModel();
        $BarangaysModel = new \App\Models\BarangaysModel();

        $sections = $SectionsModel->findall();
        $barangays = $BarangaysModel->findAll();

        return view('pages/reports', [
            'barangays' => $barangays,
            'sections' => $sections
        ]);
    }
}
