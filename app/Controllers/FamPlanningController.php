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
        $fpIndicators = $IndicatorsModel->where('section_code', 'A')
            ->orderBy('order_number', 'ASC')
            ->findAll();

        return view('pages/famplanning', [
            'barangays' => $barangays,
            'fpIndicators' => $fpIndicators
        ]);
    }


    /**
     * @var IncomingRequest
     */
    protected $request;

    public function save()
    {
        $model = new \App\Models\EntriesFPModel();

        $barangay = $this->request->getPost('barangay_code');
        $month = $this->request->getPost('report_month');
        $year = $this->request->getPost('report_year');
        $userType = $this->request->getPost('user_type');
        $indicatorId = $this->request->getPost('indicatorId');
        $entries = $this->request->getPost('entries');

        if (!$entries || !is_array($entries)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid data format.']);
        }

        foreach ($entries as $entry) {
            $model->insert([
                'indicator_id'  => $indicatorId, // optional if not needed
                'barangay_code' => $barangay,
                'report_month'  => $month,
                'report_year'   => $year,
                'agegroup'      => $entry['agegroup'],
                'user_type'     => $userType,
                'value'         => $entry['value'],
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Entries successfully saved!'
        ]);
    }

    public function get()
    {
        $entriesModel = new \App\Models\EntriesFPModel();

        $barangayCode = $this->request->getGet('barangay_code');
        $reportMonth  = $this->request->getGet('report_month');
        $reportYear   = $this->request->getGet('report_year');

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
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $entries
        ]);
    }
}
