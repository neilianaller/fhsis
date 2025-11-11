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



    protected $sectionTemplates = [
        '1' => 'section_a.xlsx',
        '2' => 'section_b.xlsx',
        '3' => 'section_c.xlsx',
        '4' => 'section_d.xlsx',
        '5' => 'section_e.xlsx',
        '6' => 'section_f.xlsx',
        '7' => 'section_g.xlsx',
        'allsections' => 'section_all.xlsx',
    ];

    /**
     * @var IncomingRequest
     */
    protected $request;


    private function prepareReportData(string $sectionCode)
    {
        $year     = $this->request->getPost('report_year');
        $quarter  = $this->request->getPost('report_quarter');
        $barangay = $this->request->getPost('barangay_code');
        $sectionId = $this->request->getPost('sectionSelect');

        // 🔹 Convert quarter → label
        $quarterLabel = match ((int)$quarter) {
            1 => '1ST',
            2 => '2ND',
            3 => '3RD',
            4 => '4TH',
            5 => '',
            default => ''
        };

        // 🔹 Quarter → months
        $monthRanges = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
            5 => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
        ];
        $months = $monthRanges[$quarter] ?? [];

        // 🔹 Get barangay name
        $barangaysModel = new \App\Models\BarangaysModel();
        $barangayData = $barangaysModel->where('code', $barangay)->first();
        $barangayName = ($barangay === 'allbgy')
            ? 'LANTAPAN'
            : ($barangayData['name'] ?? $barangay);

        

        $model = new \App\Models\EntriesModel();

        $builder = $model
            ->where('report_year', $year)
            ->whereIn('report_month', $months);

        if ($barangay !== 'allbgy') {
            $builder->where('barangay_code', $barangay);
        }

        // 🔹 Fetch entries
        $records = $builder->findAll();

        // 🔹 Fetch indicators
        $indicatorsModel = new \App\Models\IndicatorsModel();
        $indicators = $indicatorsModel
            ->where('section_code', $sectionCode)
            ->orderBy('order_number', 'ASC')
            ->findAll();

        // 🔹 Group entries by indicator
        $entriesByIndicator = [];
        foreach ($records as $record) {
            $entriesByIndicator[$record['indicator_id']][] = $record;
        }

        // 🔹 Return all as one structured array
        return [
            'year'               => $year,
            'quarter'            => $quarter,
            'quarterLabel'       => $quarterLabel,
            'barangayCode'       => $barangay,
            'barangayName'       => $barangayName,
            'model'              => $model,
            'entries'            => $records,
            'indicators'         => $indicators,
            'entriesByIndicator' => $entriesByIndicator,
        ];
    }

    public function generateFPReport()
    {
        try {
            $data = $this->prepareReportData('A'); // Section B for maternal

            $sectionId       = $this->request->getPost('sectionSelect'); // still from POST
            $barangayName    = $data['barangayName'];
            $year            = $data['year'];
            $quarterLabel    = $data['quarterLabel'];
            $quarter         = $data['quarter'];
            $indicators      = $data['indicators'];
            $entriesByIndicator = $data['entriesByIndicator'];

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
            $fileName = 'SectionA_' . $barangayName . '_Q' . $quarter . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
            $tempPath = WRITEPATH . 'reports/section_a/' . $fileName;

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

    public function generateMaternalReport()
    {
        try {
            $data = $this->prepareReportData('B'); // Section B for maternal

            $sectionId       = $this->request->getPost('sectionSelect'); // still from POST
            $barangayName    = $data['barangayName'];
            $year            = $data['year'];
            $quarterLabel    = $data['quarterLabel'];
            $quarter         = $data['quarter'];
            $indicators      = $data['indicators'];
            $entriesByIndicator = $data['entriesByIndicator'];

            // log_message('debug', 'Entries grouped by indicator: ' . print_r($entriesByIndicator, true));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/' . ($this->sectionTemplates[$sectionId] ?? '');
            if (!is_file($templateFile)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found: ' . basename($templateFile)]);
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header info
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2',  $quarterLabel);

            // 🔹 Define subsection mapping rules
            $mapRules = [
                'b1' => [
                    1 => ['start_id' => 17, 'start_row' => 12, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D']],
                    2 => ['start_id' => 30, 'start_row' => 12, 'cols' => ['10-14' => 'Q', '15-19' => 'R', '20-49' => 'S']],
                ],
                'b2' => [
                    1 => ['start_id' => 41, 'start_row' => 27, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D']],
                    2 => ['start_id' => 50, 'start_row' => 27, 'cols' => ['10-14' => 'Q', '15-19' => 'R', '20-49' => 'S']],
                ],
                'b3' => [
                    1 => ['start_id' => 60, 'start_row' => 40, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D']],
                    2 => ['start_id' => 68, 'start_row' => 40, 'cols' => ['10-14' => 'Q', '15-19' => 'R', '20-49' => 'S']],
                ],
            ];

            // 🔹 Fill indicator values
            foreach ($indicators as $indicator) {
                $sub = strtolower(trim($indicator['subsection'])); // b1, b2, b3
                if (!isset($mapRules[$sub])) {
                    log_message('debug', "Skipping indicator {$indicator['id']} — unknown subsection {$sub}");
                    continue;
                }
                // Determine set 1 or 2 by comparing id ranges
                $set = null;
                foreach ($mapRules[$sub] as $s => $rule) {
                    $nextSet = $mapRules[$sub][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicator['id'] >= $rule['start_id'] && $indicator['id'] < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$sub][$set];
                $rowNum = $rule['start_row'] + ($indicator['id'] - $rule['start_id']);

                $entries = $entriesByIndicator[$indicator['id']] ?? [];
                $sums = [];

                foreach ($entries as $entry) {
                    $ageGroup = trim($entry['agegroup']);
                    $value    = $entry['value'] ?? 0;
                    if (isset($rule['cols'][$ageGroup])) {
                        $col = $rule['cols'][$ageGroup];
                        $sums[$col] = ($sums[$col] ?? 0) + $value;
                    }
                }

                // Write totals
                foreach ($sums as $col => $total) {
                    $sheet->setCellValue($col . $rowNum, $total);
                }
            }

            // 🔹 Save report file
            $fileName = 'SectionB_' . $barangayName . '_Q' . $quarter . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
            $tempDir = WRITEPATH . 'reports/section_b/';
            if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
            $tempPath = $tempDir . $fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log report record
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'B',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Report generated successfully!',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ]);
        }
    }

    public function generateChildReport()
    {
        try {
            $data = $this->prepareReportData('C'); // Section B for maternal

            $sectionId       = $this->request->getPost('sectionSelect'); // still from POST
            $barangayName    = $data['barangayName'];
            $year            = $data['year'];
            $quarterLabel    = $data['quarterLabel'];
            $quarter         = $data['quarter'];
            $indicators      = $data['indicators'];
            $entriesByIndicator = $data['entriesByIndicator'];

            // log_message('debug', 'Entries grouped by indicator: ' . print_r($entriesByIndicator, true));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/' . ($this->sectionTemplates[$sectionId] ?? '');
            if (!is_file($templateFile)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found: ' . basename($templateFile)]);
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header info
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2',  $quarterLabel);

            // 🔹 Define subsection mapping rules
            $mapRules = [
                'ca1' => [
                    1 => ['start_id' => 70, 'start_row' => 13, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 73, 'start_row' => 13, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'ca2' => [
                    1 => ['start_id' => 75, 'start_row' => 19, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 82, 'start_row' => 19, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'ca3' => [
                    1 => ['start_id' => 89, 'start_row' => 27, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 96, 'start_row' => 27, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'ca4' => [
                    1 => ['start_id' => 103, 'start_row' => 37, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 106, 'start_row' => 37, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'cb' => [
                    1 => ['start_id' => 109, 'start_row' => 43, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 113, 'start_row' => 43, 'cols' => ['male' => 'Q', 'female' => 'R']],
                    3 => ['start_id' => 117, 'start_row' => 49, 'cols' => ['male' => 'Q', 'female' => 'R']],
                    4 => ['start_id' => 124, 'start_row' => 49, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'cc' => [
                    1 => ['start_id' => 130, 'start_row' => 59, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 135, 'start_row' => 59, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
            ];

            // 🔹 Fill indicator values
            foreach ($indicators as $indicator) {
                $sub = strtolower(trim($indicator['subsection'])); // b1, b2, b3
                if (!isset($mapRules[$sub])) {
                    log_message('debug', "Skipping indicator {$indicator['id']} — unknown subsection {$sub}");
                    continue;
                }
                // Determine set 1 or 2 by comparing id ranges
                $set = null;
                foreach ($mapRules[$sub] as $s => $rule) {
                    $nextSet = $mapRules[$sub][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicator['id'] >= $rule['start_id'] && $indicator['id'] < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$sub][$set];
                $rowNum = $rule['start_row'] + ($indicator['id'] - $rule['start_id']);

                $entries = $entriesByIndicator[$indicator['id']] ?? [];
                $sums = [];

                foreach ($entries as $entry) {
                    $sex = trim($entry['sex']);
                    $value    = $entry['value'] ?? 0;
                    if (isset($rule['cols'][$sex])) {
                        $col = $rule['cols'][$sex];
                        $sums[$col] = ($sums[$col] ?? 0) + $value;
                    }
                }

                // Write totals
                foreach ($sums as $col => $total) {
                    $sheet->setCellValue($col . $rowNum, $total);
                }
            }

            // 🔹 Save report file
            $fileName = 'SectionC_' . $barangayName . '_Q' . $quarter . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
            $tempDir = WRITEPATH . 'reports/section_c/';
            if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
            $tempPath = $tempDir . $fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log report record
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'C',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Report generated successfully!',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ]);
        }
    }

    public function generateOralReport()
    {
        try {
            $data = $this->prepareReportData('D'); // Section B for maternal

            $sectionId       = $this->request->getPost('sectionSelect'); // still from POST
            $barangayName    = $data['barangayName'];
            $year            = $data['year'];
            $quarterLabel    = $data['quarterLabel'];
            $quarter         = $data['quarter'];
            $indicators      = $data['indicators'];
            $entriesByIndicator = $data['entriesByIndicator'];

            // log_message('debug', 'Entries grouped by indicator: ' . print_r($entriesByIndicator, true));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/' . ($this->sectionTemplates[$sectionId] ?? '');
            if (!is_file($templateFile)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found: ' . basename($templateFile)]);
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header info
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2',  $quarterLabel);

            // 🔹 Define subsection mapping rules
            $mapRules = [
                'o1' => [
                    1 => ['start_id' => 139, 'start_row' => 12, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 155, 'start_row' => 12, 'cols' => ['male' => 'P', 'female' => 'Q']],
                ],
                'o2' => [
                    1 => ['start_id' => 170, 'start_row' => 30, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-59' => 'D']],
                    2 => ['start_id' => 173, 'start_row' => 30, 'cols' => ['10-14' => 'P', '15-19' => 'Q', '20-59' => 'R']],
                ],
            ];

            // 🔹 Fill indicator values
            foreach ($indicators as $indicator) {
                $sub = strtolower(trim($indicator['subsection'])); // b1, b2, b3
                if (!isset($mapRules[$sub])) {
                    log_message('debug', "Skipping indicator {$indicator['id']} — unknown subsection {$sub}");
                    continue;
                }
                // Determine set 1 or 2 by comparing id ranges
                $set = null;
                foreach ($mapRules[$sub] as $s => $rule) {
                    $nextSet = $mapRules[$sub][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicator['id'] >= $rule['start_id'] && $indicator['id'] < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$sub][$set];
                $rowNum = $rule['start_row'] + ($indicator['id'] - $rule['start_id']);

                $entries = $entriesByIndicator[$indicator['id']] ?? [];
                $sums = [];

                foreach ($entries as $entry) {
                    $sex = trim($entry['sex'] ?? '');
                    $agegroup = trim($entry['agegroup'] ?? '');
                    $value = (float)($entry['value'] ?? 0);

                    // 🔹 Build the key to find the column
                    $key = '';

                    if (!empty($sex) && !empty($agegroup)) {
                        // Both sex and agegroup are used, e.g. "male_15_19"
                        $key = strtolower("{$sex}_{$agegroup}");
                    } elseif (!empty($sex)) {
                        // Only sex is used, e.g. "male"
                        $key = strtolower($sex);
                    } elseif (!empty($agegroup)) {
                        // Only agegroup is used, e.g. "15_19"
                        $key = strtolower($agegroup);
                    }

                    // 🔹 Add to corresponding column if defined in rule
                    if (isset($rule['cols'][$key])) {
                        $col = $rule['cols'][$key];
                        $sums[$col] = ($sums[$col] ?? 0) + $value;
                    }
                }


                // Write totals
                foreach ($sums as $col => $total) {
                    $sheet->setCellValue($col . $rowNum, $total);
                }
            }

            // 🔹 Save report file
            $fileName = 'SectionD_' . $barangayName . '_Q' . $quarter . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
            $tempDir = WRITEPATH . 'reports/section_d/';
            if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
            $tempPath = $tempDir . $fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log report record
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'D',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Report generated successfully!',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ]);
        }
    }

    public function generateNCDiseaseReport()
    {
        try {
            $data = $this->prepareReportData('E'); // Section B for maternal

            $sectionId       = $this->request->getPost('sectionSelect'); // still from POST
            $barangayName    = $data['barangayName'];
            $year            = $data['year'];
            $quarterLabel    = $data['quarterLabel'];
            $quarter         = $data['quarter'];
            $indicators      = $data['indicators'];
            $entriesByIndicator = $data['entriesByIndicator'];

            // log_message('debug', 'Entries grouped by indicator: ' . print_r($entriesByIndicator, true));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/' . ($this->sectionTemplates[$sectionId] ?? '');
            if (!is_file($templateFile)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found: ' . basename($templateFile)]);
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header info
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2',  $quarterLabel);

            // 🔹 Define subsection mapping rules
            $mapRules = [
                'ncd1' => [
                    1 => ['start_id' => 176, 'start_row' => 12, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 183, 'start_row' => 12, 'cols' => ['male' => 'P', 'female' => 'Q']],
                ],
                'ncd2' => [
                    1 => ['start_id' => 190, 'start_row' => 20, 'cols' => ['male' => 'B', 'female' => 'C']],
                ],
                'ncd3' => [
                    1 => ['start_id' => 198, 'start_row' => 20, 'cols' => ['male' => 'P', 'female' => 'Q']],
                ],
                'ncd4' => [
                    1 => ['start_id' => 206, 'start_row' => 31, 'cols' => ['male' => 'B', 'female' => 'C']],
                ],
                'ncd5' => [
                    1 => ['start_id' => 209, 'start_row' => 31, 'cols' => ['male' => 'P', 'female' => 'Q']],
                ],
                'ncd6' => [
                    1 => ['start_id' => 213, 'start_row' => 36, 'cols' => ['male' => 'B', 'female' => 'C']],
                ],
                'ncd7' => [
                    1 => ['start_id' => 226, 'start_row' => 36, 'cols' => ['male' => 'P', 'female' => 'Q']],
                ],
                'ncd8' => [
                    1 => [
                        'start_id' => 240,
                        'start_row' => 54,
                        'cols' => [
                            'male_0-9'        => 'B',
                            'female_0-9'      => 'C',
                            'male_10-19'      => 'D',
                            'female_10-19'    => 'E',
                            'male_20-59'      => 'F',
                            'female_20-59'    => 'G',
                            'male_60-above'   => 'H',
                            'female_60-above' => 'I',
                        ],
                    ],
                ],

            ];

            // 🔹 Fill indicator values
            foreach ($indicators as $indicator) {
                $sub = strtolower(trim($indicator['subsection'])); // b1, b2, b3
                if (!isset($mapRules[$sub])) {
                    log_message('debug', "Skipping indicator {$indicator['id']} — unknown subsection {$sub}");
                    continue;
                }
                // Determine set 1 or 2 by comparing id ranges
                $set = null;
                foreach ($mapRules[$sub] as $s => $rule) {
                    $nextSet = $mapRules[$sub][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicator['id'] >= $rule['start_id'] && $indicator['id'] < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$sub][$set];
                $rowNum = $rule['start_row'] + ($indicator['id'] - $rule['start_id']);

                $entries = $entriesByIndicator[$indicator['id']] ?? [];
                $sums = [];

                foreach ($entries as $entry) {
                    $sex = trim($entry['sex'] ?? '');
                    $agegroup = trim($entry['agegroup'] ?? '');
                    $value = (float)($entry['value'] ?? 0);

                    // 🔹 Build the key to find the column
                    $key = '';

                    if (!empty($sex) && !empty($agegroup)) {
                        // Both sex and agegroup are used, e.g. "male_15_19"
                        $key = strtolower("{$sex}_{$agegroup}");
                    } elseif (!empty($sex)) {
                        // Only sex is used, e.g. "male"
                        $key = strtolower($sex);
                    } elseif (!empty($agegroup)) {
                        // Only agegroup is used, e.g. "15_19"
                        $key = strtolower($agegroup);
                    }

                    // 🔹 Add to corresponding column if defined in rule
                    if (isset($rule['cols'][$key])) {
                        $col = $rule['cols'][$key];
                        $sums[$col] = ($sums[$col] ?? 0) + $value;
                    }
                }


                // Write totals
                foreach ($sums as $col => $total) {
                    $sheet->setCellValue($col . $rowNum, $total);
                }
            }

            // 🔹 Save report file
            $fileName = 'SectionE_' . $barangayName . '_Q' . $quarter . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
            $tempDir = WRITEPATH . 'reports/section_e/';
            if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
            $tempPath = $tempDir . $fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log report record
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'E',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Report generated successfully!',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ]);
        }
    }

    public function generateEnviReport()
    {
        try {
            $data = $this->prepareReportData('F'); // Section B for maternal

            $sectionId       = $this->request->getPost('sectionSelect'); // still from POST
            $barangayName    = $data['barangayName'];
            $year            = $data['year'];
            $quarterLabel    = $data['quarterLabel'];
            $quarter         = $data['quarter'];
            $indicators      = $data['indicators'];
            $entriesByIndicator = $data['entriesByIndicator'];

            // log_message('debug', 'Entries grouped by indicator: ' . print_r($entriesByIndicator, true));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/' . ($this->sectionTemplates[$sectionId] ?? '');
            if (!is_file($templateFile)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found: ' . basename($templateFile)]);
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header info
            $sheet->setCellValue('E6', strtoupper($barangayName));
            $sheet->setCellValue('H2', $year);
            $sheet->setCellValue('E2',  $quarterLabel);

            // 🔹 Define subsection mapping rules
            $mapRules = [
                'e1' => [
                    1 => [
                        'start_id' => 241,
                        'start_row' => 12,
                        'cols' => [
                            'value' => 'B'  // all indicators under this map will go to col B, C, D sequentially
                        ],
                    ],
                ],
                'e2' => [
                    1 => [
                        'start_id' => 246,
                        'start_row' => 12,
                        'cols' => [
                            'value' => 'N'
                        ],
                    ],
                ],
                'e3' => [
                    1 => [
                        'start_id' => 251,
                        'start_row' => 19,
                        'cols' => [
                            'value' => 'B'
                        ],
                    ],
                ],
            ];


            // 🔹 Fill indicator values
            foreach ($indicators as $indicator) {
                $sub = strtolower(trim($indicator['subsection'])); // b1, b2, b3
                if (!isset($mapRules[$sub])) {
                    log_message('debug', "Skipping indicator {$indicator['id']} — unknown subsection {$sub}");
                    continue;
                }
                // Determine set 1 or 2 by comparing id ranges
                $set = null;
                foreach ($mapRules[$sub] as $s => $rule) {
                    $nextSet = $mapRules[$sub][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicator['id'] >= $rule['start_id'] && $indicator['id'] < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$sub][$set];
                $rowNum = $rule['start_row'] + ($indicator['id'] - $rule['start_id']);

                $entries = $entriesByIndicator[$indicator['id']] ?? [];
                $sums = [];

                foreach ($entries as $entry) {
                    $value = (float)($entry['value'] ?? 0);
                    $sums['value'] = ($sums['value'] ?? 0) + $value;
                }


                // Write totals
                foreach ($sums as $col => $total) {
                    $sheet->setCellValue($rule['cols']['value'] . $rowNum, $sums['value']);
                }
            }

            // 🔹 Save report file
            $fileName = 'SectionF_' . $barangayName . '_Q' . $quarter . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
            $tempDir = WRITEPATH . 'reports/section_f/';
            if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
            $tempPath = $tempDir . $fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log report record
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'F',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Report generated successfully!',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ]);
        }
    }

    public function generateIDiseaseReport()
    {
        try {
            $data = $this->prepareReportData('G'); // Section B for maternal

            $sectionId       = $this->request->getPost('sectionSelect'); // still from POST
            $barangayName    = $data['barangayName'];
            $year            = $data['year'];
            $quarterLabel    = $data['quarterLabel'];
            $quarter         = $data['quarter'];
            $indicators      = $data['indicators'];
            $entriesByIndicator = $data['entriesByIndicator'];

            // log_message('debug', 'Entries grouped by indicator: ' . print_r($entriesByIndicator, true));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/' . ($this->sectionTemplates[$sectionId] ?? '');
            if (!is_file($templateFile)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found: ' . basename($templateFile)]);
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header info
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2',  $quarterLabel);

            // 🔹 Define subsection mapping rules
            $mapRules = [
                'id1' => [
                    1 => ['start_id' => 253, 'start_row' => 12, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 257, 'start_row' => 12, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'id2' => [
                    1 => ['start_id' => 261, 'start_row' => 17, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 276, 'start_row' => 17, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'id3' => [
                    1 => ['start_id' => 288, 'start_row' => 35, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 314, 'start_row' => 35, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'id4' => [
                    1 => ['start_id' => 339, 'start_row' => 64, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 357, 'start_row' => 64, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'id5' => [
                    1 => ['start_id' => 376, 'start_row' => 86, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 388, 'start_row' => 86, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
                'id6' => [
                    1 => ['start_id' => 400, 'start_row' => 99, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 402, 'start_row' => 99, 'cols' => ['male' => 'Q', 'female' => 'R']],
                ],
            ];

            // 🔹 Fill indicator values
            foreach ($indicators as $indicator) {
                $sub = strtolower(trim($indicator['subsection'])); // b1, b2, b3
                if (!isset($mapRules[$sub])) {
                    log_message('debug', "Skipping indicator {$indicator['id']} — unknown subsection {$sub}");
                    continue;
                }
                // Determine set 1 or 2 by comparing id ranges
                $set = null;
                foreach ($mapRules[$sub] as $s => $rule) {
                    $nextSet = $mapRules[$sub][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicator['id'] >= $rule['start_id'] && $indicator['id'] < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$sub][$set];
                $rowNum = $rule['start_row'] + ($indicator['id'] - $rule['start_id']);

                $entries = $entriesByIndicator[$indicator['id']] ?? [];
                $sums = [];

                foreach ($entries as $entry) {
                    $sex = trim($entry['sex']);
                    $value    = $entry['value'] ?? 0;
                    if (isset($rule['cols'][$sex])) {
                        $col = $rule['cols'][$sex];
                        $sums[$col] = ($sums[$col] ?? 0) + $value;
                    }
                }

                // Write totals
                foreach ($sums as $col => $total) {
                    $sheet->setCellValue($col . $rowNum, $total);
                }
            }

            // 🔹 Save report file
            $fileName = 'SectionG_' . $barangayName . '_Q' . $quarter . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
            $tempDir = WRITEPATH . 'reports/section_g/';
            if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
            $tempPath = $tempDir . $fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log report record
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'G',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Report generated successfully!',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ]);
        }
    }

    private function prepareAllReportData(): array
    {
        $year     = $this->request->getPost('report_year');
        $quarter  = $this->request->getPost('report_quarter');
        $barangay = $this->request->getPost('barangay_code');

        $quarterLabel = match ((int)$quarter) {
            1 => '1ST',
            2 => '2ND',
            3 => '3RD',
            4 => '4TH',
            default => ''
        };

        $monthRanges = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
            5 => range(1, 12),
        ];
        $months = $monthRanges[$quarter] ?? [];

        $barangaysModel = new \App\Models\BarangaysModel();
        $barangayData = $barangaysModel->where('code', $barangay)->first();
        $barangayName = ($barangay === 'allbgy') ? 'LANTAPAN' : ($barangayData['name'] ?? $barangay);

        $allSectionsData = [];

        foreach ($this->sectionModels as $sectionCode => $modelClass) {

            $model = new $modelClass();

            $builder = $model
                ->where('report_year', $year)
                ->whereIn('report_month', $months);

            if ($barangay !== 'allbgy') {
                $builder->where('barangay_code', $barangay);
            }

            $records = $builder->findAll();
            // log_message('info', "Section {$sectionCode} records fetched: " . count($records));

            $indicatorsModel = new \App\Models\IndicatorsModel();
            $indicators = $indicatorsModel
                // ->where('section_code', $sectionCode)  // match current section
                ->whereIn('section_code', ['A', 'B'])
                ->orderBy('order_number', 'ASC')
                ->findAll();


            // log_message('info', "Section {$sectionCode} indicators fetched: " . count($indicators));



            // log_message('debug', "Section $sectionCode indicators: " . print_r($indicators, true));


            $entriesByIndicator = [];

            foreach ($records as $record) {
                if (!isset($record['indicator_id'])) {
                    log_message(
                        'error',
                        "❌ Section {$sectionCode} record missing indicator_id:\n" .
                            json_encode($record, JSON_PRETTY_PRINT)
                    );
                } else {
                    $entriesByIndicator[$record['indicator_id']][] = $record;
                }
            }

            // 🧾 Pretty log all entries per indicator
            // if (!empty($entriesByIndicator)) {
            //     foreach ($entriesByIndicator as $indicatorId => $entries) {
            //         log_message(
            //             'info',
            //             "🧩 Indicator ID: {$indicatorId}\n" .
            //                 "📘 Section: {$sectionCode}\n" .
            //                 "📊 Entries Count: " . count($entries) . "\n" .
            //                 "🧾 Entries Data:\n" .
            //                 json_encode($entries, JSON_PRETTY_PRINT)
            //         );
            //     }
            // } else {
            //     log_message('info', "⚠️ No entries found for Section {$sectionCode}");
            // }


            // // Optional: log all entries nicely
            // foreach ($entriesByIndicator as $indicatorId => $entries) {
            //     log_message('info', "Section {$sectionCode} → Indicator {$indicatorId} entries:\n" . json_encode($entries, JSON_PRETTY_PRINT));
            // }


            // ✅ Collect section data
            $allSectionsData[$sectionCode] = [
                'year'               => $year,
                'quarter'            => $quarter,
                'quarterLabel'       => $quarterLabel,
                'barangayCode'       => $barangay,
                'barangayName'       => $barangayName,
                'model'              => $model,
                'entries'            => $records,
                'indicators'         => $indicators,
                'entriesByIndicator' => $entriesByIndicator,
            ];
        }

        // ✅ Return after the loop
        return $allSectionsData;
    }



    public function generateAllReport()
    {
        try {


            $allSectionsData = $this->prepareAllReportData();

            foreach ($allSectionsData as $sectionCode => $data) {

                if (empty($data['indicators'])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => "No indicators found for section $sectionCode"
                    ]);
                }


                $barangayName       = $data['barangayName'];      // use $data, not $allSectionsData
                $year            = $data['year'];
                $quarterLabel    = $data['quarterLabel'];
                $quarter         = $data['quarter'];
                $indicators      = $data['indicators'];
                $entries = $data['entries'];

                // // 🔹 Load Excel template
                // $templateFile = APPPATH . 'Views/pages/reports/section_all.xlsx';
                // if (!is_file($templateFile)) {
                //     return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found: ' . basename($templateFile)]);
                // }

                // $spreadsheet = IOFactory::load($templateFile);
                // $sheet = $spreadsheet->getActiveSheet();

                // // 🔹 Fill header info
                // $sheet->setCellValue('G6', strtoupper($barangayName));
                // $sheet->setCellValue('J2', $year);
                // $sheet->setCellValue('G2',  $quarterLabel);

                // 🔹 Define subsection mapping rules
                $mapRules = [
                    'A' => [
                        // SECTION A: FAMILY PLANNING
                        'current_user_beginning' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D'],
                        'new_acceptor_previous'   => ['10-14' => 'F', '15-19' => 'G', '20-49' => 'H'],
                        'other_acceptor_present'  => ['10-14' => 'J', '15-19' => 'K', '20-49' => 'L'],
                        'drop_outs'               => ['10-14' => 'N', '15-19' => 'O', '20-49' => 'P'],
                        'current_user_end'        => ['10-14' => 'R', '15-19' => 'S', '20-49' => 'T'],
                        'new_acceptor_present'    => ['10-14' => 'V', '15-19' => 'W', '20-49' => 'X'],
                    ],
                    'B' => [
                        'b1' => [
                            1 => ['start_id' => 17, 'start_row' => 39, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D']],
                            2 => ['start_id' => 30, 'start_row' => 39, 'cols' => ['10-14' => 'Q', '15-19' => 'R', '20-49' => 'S']],
                        ],
                        'b2' => [
                            1 => ['start_id' => 41, 'start_row' => 54, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D']],
                            2 => ['start_id' => 50, 'start_row' => 54, 'cols' => ['10-14' => 'Q', '15-19' => 'R', '20-49' => 'S']],
                        ],
                        'b3' => [
                            1 => ['start_id' => 60, 'start_row' => 67, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D']],
                            2 => ['start_id' => 68, 'start_row' => 67, 'cols' => ['10-14' => 'Q', '15-19' => 'R', '20-49' => 'S']],
                        ],
                    ],
                    'C' => [
                        'ca1' => [
                            1 => ['start_id' => 70, 'start_row' => 80, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 73, 'start_row' => 80, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'ca2' => [
                            1 => ['start_id' => 75, 'start_row' => 86, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 82, 'start_row' => 86, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'ca3' => [
                            1 => ['start_id' => 89, 'start_row' => 94, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 96, 'start_row' => 94, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'ca4' => [
                            1 => ['start_id' => 103, 'start_row' => 104, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 106, 'start_row' => 104, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'cb' => [
                            1 => ['start_id' => 109, 'start_row' => 110, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 113, 'start_row' => 110, 'cols' => ['male' => 'Q', 'female' => 'R']],
                            3 => ['start_id' => 117, 'start_row' => 116, 'cols' => ['male' => 'Q', 'female' => 'R']],
                            4 => ['start_id' => 124, 'start_row' => 116, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'cc' => [
                            1 => ['start_id' => 130, 'start_row' => 126, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 135, 'start_row' => 126, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                    ],
                    'D' => [
                        'o1' => [
                            1 => ['start_id' => 139, 'start_row' => 135, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 155, 'start_row' => 135, 'cols' => ['male' => 'P', 'female' => 'Q']],
                        ],
                        'o2' => [
                            1 => ['start_id' => 170, 'start_row' => 153, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-59' => 'D']],
                            2 => ['start_id' => 173, 'start_row' => 153, 'cols' => ['10-14' => 'P', '15-19' => 'Q', '20-59' => 'R']],
                        ],
                    ],
                    'E' => [
                        'ncd1' => [
                            1 => ['start_id' => 176, 'start_row' => 160, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 183, 'start_row' => 160, 'cols' => ['male' => 'P', 'female' => 'Q']],
                        ],
                        'ncd2' => [
                            1 => ['start_id' => 190, 'start_row' => 168, 'cols' => ['male' => 'B', 'female' => 'C']],
                        ],
                        'ncd3' => [
                            1 => ['start_id' => 198, 'start_row' => 168, 'cols' => ['male' => 'P', 'female' => 'Q']],
                        ],
                        'ncd4' => [
                            1 => ['start_id' => 206, 'start_row' => 179, 'cols' => ['male' => 'B', 'female' => 'C']],
                        ],
                        'ncd5' => [
                            1 => ['start_id' => 209, 'start_row' => 179, 'cols' => ['male' => 'P', 'female' => 'Q']],
                        ],
                        'ncd6' => [
                            1 => ['start_id' => 213, 'start_row' => 184, 'cols' => ['male' => 'B', 'female' => 'C']],
                        ],
                        'ncd7' => [
                            1 => ['start_id' => 226, 'start_row' => 184, 'cols' => ['male' => 'P', 'female' => 'Q']],
                        ],
                        'ncd8' => [
                            1 => [
                                'start_id' => 240,
                                'start_row' => 202,
                                'cols' => [
                                    'male_0-9'        => 'B',
                                    'female_0-9'      => 'C',
                                    'male_10-19'      => 'D',
                                    'female_10-19'    => 'E',
                                    'male_20-59'      => 'F',
                                    'female_20-59'    => 'G',
                                    'male_60-above'   => 'H',
                                    'female_60-above' => 'I',
                                ],
                            ],
                        ],
                    ],
                    'F' => [
                        'e1' => [
                            1 => [
                                'start_id' => 241,
                                'start_row' => 207,
                                'cols' => [
                                    'value' => 'B'  // all indicators under this map will go to col B, C, D sequentially
                                ],
                            ],
                        ],
                        'e2' => [
                            1 => [
                                'start_id' => 246,
                                'start_row' => 207,
                                'cols' => [
                                    'value' => 'N'
                                ],
                            ],
                        ],
                        'e3' => [
                            1 => [
                                'start_id' => 251,
                                'start_row' => 214,
                                'cols' => [
                                    'value' => 'B'
                                ],
                            ],
                        ],
                    ],
                    'G' => [
                        'id1' => [
                            1 => ['start_id' => 253, 'start_row' => 220, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 257, 'start_row' => 220, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'id2' => [
                            1 => ['start_id' => 261, 'start_row' => 225, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 276, 'start_row' => 225, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'id3' => [
                            1 => ['start_id' => 288, 'start_row' => 243, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 314, 'start_row' => 243, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'id4' => [
                            1 => ['start_id' => 339, 'start_row' => 272, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 357, 'start_row' => 272, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'id5' => [
                            1 => ['start_id' => 376, 'start_row' => 294, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 388, 'start_row' => 294, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                        'id6' => [
                            1 => ['start_id' => 400, 'start_row' => 307, 'cols' => ['male' => 'B', 'female' => 'C']],
                            2 => ['start_id' => 402, 'start_row' => 307, 'cols' => ['male' => 'Q', 'female' => 'R']],
                        ],
                    ],
                ];

                // 🔹 Fill indicator values
                // foreach ($indicators as $indicator) {

                //     $section = strtoupper($indicator['section_code']); // e.g., 'A', 'B', 'C', 'F', etc.

                //     // log_message('info', "Indicator {$indicator['id']} has section_code={$indicator['section_code']} (upper={$section})");

                //     $subKey  = strtolower(trim($indicator['subsection'] ?? '')); // for sections with subsections

                //     if (!isset($mapRules[$section])) {
                //         log_message('debug', "Skipping indicator {$indicator['id']} — unknown section {$section}");
                //         continue;
                //     }

                //     $sectionMap = $mapRules[$section];

                //     // Determine which map to use
                //     if (in_array($section, ['A'])) {
                //         // log_message('info', "Processing Section {$section}, Indicator ID: {$indicator['id']}, SubKey: {$subKey}");

                //         // Section A uses user_type directly
                //         $ruleMap = $sectionMap;
                //         $rowBase = 15; // <-- adjust this to your actual first row number in Excel for Section A
                //         $rowNum = $rowBase + ($indicator['order_number'] ?? 0);

                //         // log_message('info', "Section A - Indicator {$indicator['id']} row: {$rowNum}");

                //         if (!$rowNum) continue;

                //         $sums = [];

                //         foreach ($entriesByIndicator[$indicator['id']] ?? [] as $entry) {
                //             $userType = trim($entry['user_type']);
                //             $ageGroup = trim($entry['agegroup']);
                //             $value    = $entry['value'] ?? 0;

                //             if (isset($ruleMap[$userType][$ageGroup])) {
                //                 $col = $ruleMap[$userType][$ageGroup];
                //                 $sums[$col] = ($sums[$col] ?? 0) + $value;
                //             }
                //         }
                //     }

                //     if (in_array($section, ['B', 'C', 'D', 'E', 'G'])) {
                //         // Sections with subsections
                //         if (!isset($sectionMap[$subKey])) {
                //             log_message('debug', "Skipping indicator {$indicator['id']} — unknown subsection {$subKey}");
                //             continue;
                //         }

                //         // Determine set 1/2/3 etc. by comparing start_id ranges
                //         $set = null;
                //         foreach ($sectionMap[$subKey] as $s => $rule) {
                //             $nextSet = $sectionMap[$subKey][$s + 1] ?? null;
                //             $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;

                //             if ($indicator['id'] >= $rule['start_id'] && $indicator['id'] < $nextStart) {
                //                 $set = $s;
                //                 break;
                //             }
                //         }
                //         if (!$set) continue;

                //         $rule   = $sectionMap[$subKey][$set];
                //         $rowNum = $rule['start_row'] + ($indicator['id'] - $rule['start_id']);
                //         $sums   = [];

                //         $entries = $entriesByIndicator[$indicator['id']] ?? [];
                //         $countEntries = count($entries);
                //         log_message('info', "Section {$section} → Indicator {$indicator['id']} has {$countEntries} entries");

                //         foreach ($entries as $entry) {
                //             $sex      = trim($entry['sex'] ?? '');
                //             $ageGroup = trim($entry['agegroup'] ?? '');
                //             $value    = (float)($entry['value'] ?? 0);

                //             // Build key depending on mapping style
                //             if (!empty($sex) && !empty($ageGroup)) {
                //                 $key = strtolower("{$sex}_{$ageGroup}");
                //             } elseif (!empty($sex)) {
                //                 $key = strtolower($sex);
                //             } elseif (!empty($ageGroup)) {
                //                 $key = strtolower($ageGroup);
                //             } else {
                //                 $key = 'value'; // For Section F
                //             }

                //             if (isset($rule['cols'][$key])) {
                //                 $col = $rule['cols'][$key];
                //                 $sums[$col] = ($sums[$col] ?? 0) + $value;
                //             }
                //         }
                //     } elseif ($section === 'F') {
                //         // Section F just sequential values
                //         $subKey = strtolower($subKey ?: key($sectionMap));
                //         $rule = $sectionMap[$subKey][1];
                //         $rowNum = $rule['start_row'] + ($indicator['id'] - $rule['start_id']);
                //         $sums = [];
                //         $values = $entriesByIndicator[$indicator['id']] ?? [];
                //         $col = $rule['cols']['value'] ?? 'B';

                //         foreach ($values as $i => $entry) {
                //             // Sequentially fill B, C, D, ... depending on index
                //             $colLetter = chr(ord($col) + $i);
                //             $sums[$colLetter] = $entry['value'] ?? 0;
                //         }
                //     }

                //     // log_message('info', "B–G Totals Check → Section {$section}, Indicator {$indicator['id']}, row {$rowNum}, sums: " . json_encode($sums));

                //     // Write totals to Excel
                //     foreach ($sums as $col => $total) {
                //         $sheet->setCellValue($col . $rowNum, $total);
                //     }
                // }

                $allEntries = [];

                foreach ($indicators as $indicator) {
                    $section = strtoupper($indicator['section_code']);
                    // log_message('info', "Section variable: " . $section);

                    $entriesByIndicator = $data['entriesByIndicator'];

                    $entriesForIndicator = $entriesByIndicator[$indicator['id']] ?? [];

                    $allEntries = array_merge($allEntries, $entriesForIndicator);

                    log_message('info', "Section {$sectionCode} — All entries count: " . count($allEntries));


                    // 🧾 Pretty log all entries per indicator
                    if (!empty($entriesForIndicator)) {
                        foreach ($entriesForIndicator as $indicatorId => $entries) {
                            // log_message(
                            //     'info',
                            //     "🧩 Indicator ID: {$indicatorId}\n" .
                            //         "📘 Section: {$sectionCode}\n" .
                            //         "📊 Entries Count: " . count($entries) . "\n" .
                            //         "🧾 Entries Data:\n" .
                            //         json_encode($entries, JSON_PRETTY_PRINT)
                            // );
                        }
                    } else {
                        // log_message('info', "⚠️ No entries found for Section {$sectionCode}");
                    }

                    // log_message(
                    //     'info',
                    //     "🧩 Indicator ID: {$indicator['id']}\n" .
                    //         "📘 Section: {$indicator['section_code']}\n" .
                    //         "📊 Entries Count: " . count($entriesForIndicator) . "\n" .
                    //         "🧾 Entries Data:\n" . json_encode($entriesForIndicator, JSON_PRETTY_PRINT)
                    // );
                    // $countEntries = count($entriesForIndicator);
                    // log_message('info', "Count of entriesForIndicator: " . $countEntries);



                    if (in_array($section, ['A'])) {
                        // log_message('info', "Processing Section A");
                        // ...
                    } elseif (in_array($section, ['B', 'C', 'D', 'E', 'G'])) {
                        // log_message('info', "Processing Section B-G");
                        // ...
                    } elseif ($section === 'F') {
                        // log_message('info', "Processing Section F");
                        // ...
                    }
                }

                // // 🔹 Save report file
                // $fileName = 'SectionALL_' . $barangayName . '_Q' . $quarter . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
                // $tempDir = WRITEPATH . 'reports/section_all/';
                // if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
                // $tempPath = $tempDir . $fileName;

                // $writer = new Xlsx($spreadsheet);
                // $writer->save($tempPath);

                // // 🔹 Log report record
                // $reportLogsModel = new \App\Models\ReportsModel();
                // $reportLogsModel->insert([
                //     'report_year'    => $year,
                //     'report_quarter' => $quarter,
                //     'barangay'       => $barangayName,
                //     'section'        => 'ALL',
                //     'filepath'       => $tempPath,
                //     'created_at'     => date('Y-m-d H:i:s'),
                // ]);

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Report generated successfully!',
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ]);
        }
    }
}
