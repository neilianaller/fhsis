<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class EnviController extends ResourceController
{
    public function index(): string
    {

        $BarangaysModel = new \App\Models\BarangaysModel();
        $IndicatorsModel = new \App\Models\IndicatorsModel();

        $barangays = $BarangaysModel->findAll();

        $e1Indicators = $IndicatorsModel->where('section_code', 'F')
            ->where('subsection', 'e1')
            ->where('code !=', '1')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $e2Indicators = $IndicatorsModel->where('section_code', 'F')
            ->where('subsection', 'e2')
            ->where('code !=', '1')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $e3Indicators = $IndicatorsModel->where('section_code', 'F')
            ->where('subsection', 'e3')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        return view('pages/envi', [
            'barangays' => $barangays,
            'e1Indicators' => $e1Indicators,
            'e2Indicators' => $e2Indicators,
            'e3Indicators' => $e3Indicators,
        ]);
    }
}
