<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class IDiseaseController extends ResourceController
{
    public function index(): string
    {

        $BarangaysModel = new \App\Models\BarangaysModel();
        $IndicatorsModel = new \App\Models\IndicatorsModel();

        $barangays = $BarangaysModel->findAll();

        $id1Indicators = $IndicatorsModel->where('section_code', 'G')
            ->where('subsection', 'id1')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $id2Indicators = $IndicatorsModel->where('section_code', 'G')
            ->where('subsection', 'id2')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $id3Indicators = $IndicatorsModel->where('section_code', 'G')
            ->where('subsection', 'id3')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $id4Indicators = $IndicatorsModel->where('section_code', 'G')
            ->where('subsection', 'id4')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $id5Indicators = $IndicatorsModel->where('section_code', 'G')
            ->where('subsection', 'id5')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $id6Indicators = $IndicatorsModel->where('section_code', 'G')
            ->where('subsection', 'id6')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        return view('pages/idisease', [
            'barangays' => $barangays,
            'id1Indicators' => $id1Indicators,
            'id2Indicators' => $id2Indicators,
            'id3Indicators' => $id3Indicators,
            'id4Indicators' => $id4Indicators,
            'id5Indicators' => $id5Indicators,
            'id6Indicators' => $id6Indicators,
        ]);
    }

}
