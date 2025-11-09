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


    /**
     * @var IncomingRequest
     */
    protected $request;

    public function save()
    {
        $entriesModel = new \App\Models\EntriesNCDiseaseModel();

        $barangay_code = $this->request->getPost('barangay_code');
        $report_month  = $this->request->getPost('report_month');
        $report_year   = $this->request->getPost('report_year');
        $subsection   = $this->request->getPost('subsection');
        $indicatorId   = $this->request->getPost('indicatorId');
        $entries       = $this->request->getPost('entries'); // array of { sex, subsection, value }
        
        log_message('info', 'SUBSECTION: ' . $subsection);

        if (!$barangay_code || !$report_month || !$report_year || !$entries) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing required data.'
            ]);
        }

        foreach ($entries as $entry) {
            $existing = $entriesModel->where([
                'barangay_code' => $barangay_code,
                'report_month'  => $report_month,
                'report_year'   => $report_year,
                'sex'      => $entry['sex'],
                'agegroup'      => $entry['agegroup'],
                'subsection'     => $subsection,
                'indicator_id'     => $indicatorId
            ])->first();

            if ($existing) {
                // Update existing record
                $entriesModel->update($existing['id'], [
                    'value'      => $entry['value'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Insert new record
                $entriesModel->insert([
                    'indicator_id' => $indicatorId,
                    'barangay_code' => $barangay_code,
                    'report_month'  => $report_month,
                    'report_year'   => $report_year,
                    'sex'      => $entry['sex'],
                    'agegroup'      => $entry['agegroup'],
                    'subsection'     => $subsection,
                    'value'         => $entry['value'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Entries saved successfully.'
        ]);
    }


    public function get()
    {
        $entriesModel = new \App\Models\EntriesNCDiseaseModel();

        $barangayCode = $this->request->getGet('barangay_code');
        $reportMonth  = $this->request->getGet('report_month');
        $reportYear   = $this->request->getGet('report_year');
        $indicator_id   = $this->request->getGet('indicator_id');
        $subsection   = $this->request->getGet('subsection');

        // Simple validation
        if (!$subsection || !$reportMonth || !$reportYear) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Missing parameters.'
            ]);
        }

        $entries = $entriesModel
            ->where('barangay_code', $barangayCode)
            ->where('report_month', $reportMonth)
            ->where('report_year', $reportYear)
            ->where('indicator_id', $indicator_id)
            ->where('subsection', $subsection)
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $entries
        ]);
    }
}
