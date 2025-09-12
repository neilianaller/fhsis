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
        $IndicatorsModel = new \App\Models\IndicatorsModel();

        $barangays = $BarangaysModel->findAll();
        $fpIndicators = $IndicatorsModel->where('section_code', 'A')->findAll();

        return view('pages/famplanning', [
            'barangays' => $barangays,
            'fpIndicators' => $fpIndicators
        ]);
    }
}
