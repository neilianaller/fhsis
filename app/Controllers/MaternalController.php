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


    /**
     * @var IncomingRequest
     */
    protected $request;

    public function save()
    {
        $entriesModel = new \App\Models\EntriesMaternalModel();

        $barangay_code = $this->request->getPost('barangay_code');
        $report_month  = $this->request->getPost('report_month');
        $report_year   = $this->request->getPost('report_year');
        $subsection   = $this->request->getPost('subsection');
        $indicatorId   = $this->request->getPost('indicatorId');
        $entries       = $this->request->getPost('entries'); // array of { agegroup, subsection, value }

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
        $entriesModel = new \App\Models\EntriesMaternalModel();

        $barangayCode = $this->request->getGet('barangay_code');
        $reportMonth  = $this->request->getGet('report_month');
        $reportYear   = $this->request->getGet('report_year');
        $indicator_id   = $this->request->getGet('indicator_id');
        $subsection   = $this->request->getGet('subsection');

        // Simple validation
        if (!$barangayCode || !$reportMonth || !$reportYear) {
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
