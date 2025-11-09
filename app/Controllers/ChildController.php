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
            ->whereIn('subsection', [6, 7])
            ->orderBy('subsection', 'ASC')
            ->orderBy('order_number', 'ASC')
            ->findAll();


        $ccIndicators = $IndicatorsModel->where('section_code', 'C')
            ->where('subsection', '8')
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


    /**
     * @var IncomingRequest
     */
    protected $request;

    public function save()
    {
        $entriesModel = new \App\Models\EntriesChildModel();

        $barangay_code = $this->request->getPost('barangay_code');
        $report_month  = $this->request->getPost('report_month');
        $report_year   = $this->request->getPost('report_year');
        $subsection   = $this->request->getPost('subsection');
        $indicatorId   = $this->request->getPost('indicatorId');
        $entries       = $this->request->getPost('entries'); // array of { sex, subsection, value }

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
        $entriesModel = new \App\Models\EntriesChildModel();

        $barangayCode = $this->request->getGet('barangay_code');
        $reportMonth  = $this->request->getGet('report_month');
        $reportYear   = $this->request->getGet('report_year');
        $indicator_id   = $this->request->getGet('indicator_id');
        $subsection   = $this->request->getGet('subsection');

        log_message('info', 'SUBSECTION: ' . $subsection);

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
