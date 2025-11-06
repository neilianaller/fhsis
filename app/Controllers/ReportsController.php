<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

use App\Models\EntriesFPModel;
use App\Models\EntriesMaternalModel;
use App\Models\EntriesChildModel;
use App\Models\EntriesOralModel;
use App\Models\EntriesNCDiseaseModel;
use App\Models\EntriesEnviModel;
use App\Models\EntriesIDiseaseModel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Writer\Html;
class ReportsController extends ResourceController
{



    public function index(): string
    {

        $SectionsModel = new \App\Models\SectionsModel();
        $BarangaysModel = new \App\Models\BarangaysModel();

        $sections = $SectionsModel->findall();
        $barangays = $BarangaysModel->findAll();

        return view('pages/reports', [
            'barangays' => $barangays,
            'sections' => $sections
        ]);
    }

    public function list()
    {
        $ReportsModel = new \App\Models\ReportsModel();
        $postData = $this->request->getPost();

        $draw = $postData['draw'] ?? 1;
        $start = $postData['start'] ?? 0;
        $rowperpage = $postData['length'] ?? 10;
        $seachvalue = $postData['search']['value'] ?? '';

        // Default sort: created_at DESC
        $sortcolumn = 'created_at';
        $sortdir = 'desc';

        // Only read order if it exists
        if (!empty($postData['order']) && isset($postData['columns'])) {
            $sortby = $postData['order'][0]['column'] ?? 0;
            $sortdir = $postData['order'][0]['dir'] ?? 'asc';
            $sortcolumn = $postData['columns'][$sortby]['data'] ?? 'created_at';
        }


        //records
        $records = $ReportsModel->select('*')
            ->like('id', $seachvalue)
            ->orLike('report_year', $seachvalue)
            ->orLike('report_quarter', $seachvalue)
            ->orLike('barangay', $seachvalue)
            ->orLike('section', $seachvalue)
            ->orderBy($sortcolumn, $sortdir)
            ->findAll($rowperpage, $start);

        $data = array();

        foreach ($records as $record) {
            $data[] = array(
                'id' => $record['id'],
                'report_year' => $record['report_year'],
                'report_quarter' => $record['report_quarter'],
                'barangay' => $record['barangay'],
                'section' => $record['section'],
                'filepath' => $record['filepath'],
                'created_at' => $record['created_at'],
            );
        }

        // total records 
        $totalRecords = $ReportsModel->select('id')->countAllResults();

        // total records with filter

        $totalRecordswithFilter = $ReportsModel->select('id')
            ->like('id', $seachvalue)
            ->orLike('report_year', $seachvalue)
            ->orLike('report_quarter', $seachvalue)
            ->orLike('barangay', $seachvalue)
            ->orLike('section', $seachvalue)
            ->orderBy($sortcolumn, $sortdir)
            ->countAllResults();

        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $data
        );

        return $this->response->setStatusCode(Response::HTTP_OK)->setJSON($response);
    }

    protected $sectionModels = [
        // Map section ID or code to model class
        '1' => EntriesFPModel::class,
        '2' => EntriesMaternalModel::class,
        '3' => EntriesChildModel::class,
        '4' => EntriesOralModel::class,
        '5' => EntriesNCDiseaseModel::class,
        '6' => EntriesEnviModel::class,
        '7' => EntriesIDiseaseModel::class,

    ];

    protected $sectionTemplates = [
        '1' => 'section_a.xlsx',
    ];

    /**
     * @var IncomingRequest
     */
    protected $request;
    public function generateFPReport()
    {
        try {
            $sectionId = $this->request->getPost('sectionSelect');
            $year      = $this->request->getPost('report_year');
            $quarter   = $this->request->getPost('report_quarter');
            $barangay  = $this->request->getPost('barangay_code');

            // 🔹 Convert quarter to label
            $quarterLabel = match ((int)$quarter) {
                1 => '1ST',
                2 => '2ND',
                3 => '3RD',
                4 => '4TH',
                default => '',
            };

            // 🔹 Fetch barangay name
            $barangaysModel = new \App\Models\BarangaysModel();
            $barangayData = $barangaysModel->where('code', $barangay)->first();
            $barangayName = $barangayData['name'] ?? $barangay;

            // 🔹 Verify valid section
            if (!isset($this->sectionModels[$sectionId])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid section selected.']);
            }

            $modelClass = $this->sectionModels[$sectionId];
            $model = new $modelClass();

            // 🔹 Get months by quarter
            $monthRanges = [
                1 => [1, 2, 3],
                2 => [4, 5, 6],
                3 => [7, 8, 9],
                4 => [10, 11, 12],
            ];
            $months = $monthRanges[$quarter] ?? [];

            // 🔹 Fetch entries from EntriesFPModel
            $records = $model
                ->where('report_year', $year)
                ->where('barangay_code', $barangay)
                ->whereIn('report_month', $months)
                ->findAll();

            // 🔹 Fetch indicators for the section
            $indicatorsModel = new \App\Models\IndicatorsModel();
            $indicators = $indicatorsModel
                ->where('section_code', 'A')
                ->orderBy('order_number', 'ASC')
                ->findAll();

            // 🔹 Map entries by indicator_id
            $entriesByIndicator = [];
            foreach ($records as $record) {
                $entriesByIndicator[$record['indicator_id']][] = $record;
            }

            // log_message('debug', 'Entries grouped by indicator: ' . print_r($entriesByIndicator, true));


            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/' . ($this->sectionTemplates[$sectionId] ?? '');
            if (!is_file($templateFile)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found: ' . basename($templateFile)]);
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill template header cells
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2',  $quarterLabel);

            // 🔹 Define Excel rows, skipping 19, 23, 26
            $startRow = 16;
            $skipRows = [];
            $excelRows = [];
            $row = $startRow;
            foreach ($indicators as $indicator) {
                while (in_array($row, $skipRows)) {
                    $row++;
                }
                $excelRows[$indicator['id']] = $row;
                $row++;
            }

            // 🔹 Define column mapping
            $columnMap = [
                'current_user_beginning' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D'],
                'new_acceptor_previous'   => ['10-14' => 'F', '15-19' => 'G', '20-49' => 'H'],
                'other_acceptor_present'  => ['10-14' => 'J', '15-19' => 'K', '20-49' => 'L'],
                'drop_outs'               => ['10-14' => 'N', '15-19' => 'O', '20-49' => 'P'],
                'current_user_end'        => ['10-14' => 'R', '15-19' => 'S', '20-49' => 'T'],
                'new_acceptor_present'    => ['10-14' => 'V', '15-19' => 'W', '20-49' => 'X'],
            ];

            // 🔹 Fill indicator rows
            foreach ($indicators as $indicator) {
                $rowNum = $excelRows[$indicator['id']] ?? null;
                if (!$rowNum) continue;

                $entries = $entriesByIndicator[$indicator['id']] ?? [];
                $sums = []; // column => total

                foreach ($entries as $entry) {
                    $userType = trim($entry['user_type']);
                    $ageGroup = trim($entry['agegroup']);
                    $value    = $entry['value'] ?? 0;

                    if (isset($columnMap[$userType][$ageGroup])) {
                        $col = $columnMap[$userType][$ageGroup];
                        $sums[$col] = ($sums[$col] ?? 0) + $value; // sum all months
                    }
                }

                // Write totals to Excel
                foreach ($sums as $col => $total) {
                    $sheet->setCellValue($col . $rowNum, $total);
                }
            }



            // 🔹 Save the generated report
            $fileName = 'Report_' . $barangay . '_Q' . $quarter . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
            $tempPath = WRITEPATH . 'reports/' . $fileName;

            if (!is_dir(WRITEPATH . 'reports')) {
                mkdir(WRITEPATH . 'reports', 0777, true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Save log to database
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section' => 'A',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Return success with file URL
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Report generated successfully!',
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error generating report: ' . $e->getMessage()
            ]);
        }
    }


    public function download($id)
    {
        $reportsModel = new \App\Models\ReportsModel();
        $log = $reportsModel->find($id);

        if (!$log || !is_file($log['filepath'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found.');
        }

        return $this->response->download($log['filepath'], null)
            ->setFileName(basename($log['filepath']));
    }

}
