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

    public function addEntry()
    {
        $entriesModel = new \App\Models\EntriesModel();
        $data = $this->request->getJSON(true);

        if (!$data || !isset($data['entries'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Invalid request payload.'
            ]);
        }

        foreach ($data['entries'] as $entry) {
            // Expected fields from frontend
            $indicatorId = $entry['indicator_id'];
            $barangayCode = $entry['barangay_code'];
            $reportMonth = $entry['report_month'];
            $reportYear = $entry['report_year'];
            // $columnIndex = $entry['column_index'];
            $value = $entry['value'];

            // Upsert: check if entry exists
            $existing = $entriesModel->where([
                'indicator_id' => $indicatorId,
                'barangay_code' => $barangayCode,
                'report_month' => $reportMonth,
                'report_year' => $reportYear,
                // 'column_index' => $columnIndex,
            ])->first();

            if ($existing) {
                // Update existing
                $entriesModel->update($existing['id'], [
                    'value' => $value
                ]);
            } else {
                // Insert new
                $entriesModel->insert([
                    'indicator_id' => $indicatorId,
                    'barangay_code' => $barangayCode,
                    'report_month' => $reportMonth,
                    'report_year' => $reportYear,
                    // 'column_index' => $columnIndex,
                    'value' => $value
                ]);
            }
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'message' => 'Entries saved successfully.'
        ]);
    }
}
