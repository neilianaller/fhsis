<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class NCDiseaseController extends ResourceController
{
    public function index(): string
    {

        $BarangaysModel = new \App\Models\BarangaysModel();
        $IndicatorsModel = new \App\Models\IndicatorsModel();

        $barangays = $BarangaysModel->findAll();

        $ncd1Indicators = $IndicatorsModel->where('section_code', 'E')
            ->where('subsection', 'ncd1')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ncd2Indicators = $IndicatorsModel->where('section_code', 'E')
            ->where('subsection', 'ncd2')
            ->where('code !=', '2')
            ->where('code !=', '4')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ncd3Indicators = $IndicatorsModel->where('section_code', 'E')
            ->where('subsection', 'ncd3')
            ->where('code !=', '2')
            ->where('code !=', '4')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ncd4Indicators = $IndicatorsModel->where('section_code', 'E')
            ->where('subsection', 'ncd4')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ncd5Indicators = $IndicatorsModel->where('section_code', 'E')
            ->where('subsection', 'ncd5')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ncd6Indicators = $IndicatorsModel->where('section_code', 'E')
            ->where('subsection', 'ncd6')
            ->where('code !=', '1')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ncd7Indicators = $IndicatorsModel->where('section_code', 'E')
            ->where('subsection', 'ncd7')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        $ncd8Indicators = $IndicatorsModel->where('section_code', 'E')
            ->where('subsection', 'ncd8')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        return view('pages/ncdisease', [
            'barangays' => $barangays,
            'ncd1Indicators' => $ncd1Indicators,
            'ncd2Indicators' => $ncd2Indicators,
            'ncd3Indicators' => $ncd3Indicators,
            'ncd4Indicators' => $ncd4Indicators,
            'ncd5Indicators' => $ncd5Indicators,
            'ncd6Indicators' => $ncd6Indicators,
            'ncd7Indicators' => $ncd7Indicators,
            'ncd8Indicators' => $ncd8Indicators,
        ]);
    }
}
