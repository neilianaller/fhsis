<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class ChildController extends ResourceController
{
    public function index(): string
    {

        $BarangaysModel = new \App\Models\BarangaysModel();
        $IndicatorsModel = new \App\Models\IndicatorsModel();

        $barangays = $BarangaysModel->findAll();

        $ca1Indicators = $IndicatorsModel->where('section_code', 'C')
            ->where('subsection', 'ca1')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ca2Indicators = $IndicatorsModel->where('section_code', 'C')
            ->where('subsection', 'ca2')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ca3Indicators = $IndicatorsModel->where('section_code', 'C')
            ->where('subsection', 'ca3')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ca4Indicators = $IndicatorsModel->where('section_code', 'C')
            ->where('subsection', 'ca4')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $cbIndicators = $IndicatorsModel
            ->where('section_code', 'C')
            ->where('subsection', 'cb')
            ->orderBy('subsection', 'ASC')
            ->orderBy('order_number', 'ASC')
            ->findAll();


        $ccIndicators = $IndicatorsModel->where('section_code', 'C')
            ->where('subsection', 'cc')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        return view('pages/child', [
            'barangays' => $barangays,
            'ca1Indicators' => $ca1Indicators,
            'ca2Indicators' => $ca2Indicators,
            'ca3Indicators' => $ca3Indicators,
            'ca4Indicators' => $ca4Indicators,
            'cbIndicators' => $cbIndicators,
            'ccIndicators' => $ccIndicators,
        ]);
    }

}
