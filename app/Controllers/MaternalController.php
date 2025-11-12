<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class MaternalController extends ResourceController
{
    public function index(): string
    {

        $BarangaysModel = new \App\Models\BarangaysModel();
        $IndicatorsModel = new \App\Models\IndicatorsModel();

        $barangays = $BarangaysModel->findAll();

        $b1Indicators = $IndicatorsModel->where('section_code', 'B')
            ->where('subsection', 'b1')
            ->where('code!=', '1b')
            ->where('code!=', '1c')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $b2Indicators = $IndicatorsModel->where('section_code', 'B')
            ->where('subsection', 'b2')
            ->where('code !=', '1')
            ->where('code !=', '2')
            ->where('code !=', '3')
            ->where('code !=', '4')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $b3Indicators = $IndicatorsModel->where('section_code', 'B')
            ->where('subsection', 'b3')
            ->where('code!=', '1b')
            ->where('code!=', '1c')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        return view('pages/maternal', [
            'barangays' => $barangays,
            'b1Indicators' => $b1Indicators,
            'b2Indicators' => $b2Indicators,
            'b3Indicators' => $b3Indicators,
        ]);
    }

}
