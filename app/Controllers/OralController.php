<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class OralController extends ResourceController
{
    public function index(): string
    {

        $BarangaysModel = new \App\Models\BarangaysModel();
        $IndicatorsModel = new \App\Models\IndicatorsModel();

        $barangays = $BarangaysModel->findAll();

        $o1Indicators = $IndicatorsModel->where('section_code', 'D')
            ->where('subsection', 'o1')
            ->where('id !=', '140')
            ->where('id !=', '143')
            ->where('id !=', '146')
            ->where('id !=', '149')
            ->where('id !=', '152')
            ->where('id !=', '155')
            ->where('id !=', '158')
            ->where('id !=', '161')
            ->where('id !=', '164')
            ->where('id !=', '167')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $o2Indicators = $IndicatorsModel->where('section_code', 'D')
            ->where('subsection', 'o2')
            ->where('id !=', '170')
            ->where('id !=', '173')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        return view('pages/oral', [
            'barangays' => $barangays,
            'o1Indicators' => $o1Indicators,
            'o2Indicators' => $o2Indicators,
        ]);
    }
}
