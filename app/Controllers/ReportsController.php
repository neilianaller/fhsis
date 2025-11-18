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



    public function index()
    {

        if (auth()->loggedIn()) {


            $SectionsModel = new \App\Models\SectionsModel();
            $BarangaysModel = new \App\Models\BarangaysModel();

            $sections = $SectionsModel->findall();
            $barangays = $BarangaysModel->findAll();

            return view('pages/reports', [
                'barangays' => $barangays,
                'sections' => $sections
            ]);
        }

        return redirect()->to('/login');
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

    /**
     * @var IncomingRequest
     */
    protected $request;


    private function prepareReportData(string $sectionCode)
    {
        $year     = $this->request->getPost('report_year');
        $quarter  = $this->request->getPost('report_quarter');
        $barangay = $this->request->getPost('barangay_code');

        // 🔹 Convert quarter → label
        $quarterLabel = match ((int)$quarter) {
            1 => '1ST',
            2 => '2ND',
            3 => '3RD',
            4 => '4TH',
            5 => '2025',
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

        $entriesModel = new \App\Models\EntriesModel();

        $builder = $entriesModel
            ->where('report_year', $year)
            ->whereIn('report_month', $months);


        if ($barangay !== 'allbgy') {
            $builder->where('barangay_code', $barangay);
        }

        if ($sectionCode !== 'all') {
            $builder->where('section_code', $sectionCode);
        }

        $records = $builder->findAll();

        // Fetch all indicators once
        $indicatorsModel = new \App\Models\IndicatorsModel();
        $indicators = $indicatorsModel
            ->whereIn('section_code', ['A', 'B', 'C', 'D', 'E', 'F', 'G'])
            ->orderBy('order_number', 'ASC')
            ->findAll();

        // Group entries by section and indicator
        $allSectionsData = [];
        foreach ($records as $record) {
            $section = $record['section_code'];
            $indicatorId = $record['indicator_id'];
            $allSectionsData[$section]['entriesByIndicator'][$indicatorId][] = $record;
        }

        // Add indicators per section
        foreach ($indicators as $ind) {
            $section = $ind['section_code'];
            $allSectionsData[$section]['indicators'][$ind['id']] = $ind;
        }

        // Add meta info
        foreach ($allSectionsData as $section => &$data) {
            $data['year'] = $year;
            $data['quarter'] = $quarter;
            $data['quarterLabel'] = $quarterLabel;
            $data['barangayCode'] = $barangay;
            $data['barangayName'] = $barangayName;
            $data['entries'] = $records;
        }

        return $allSectionsData[$sectionCode] ?? [];
    }

    public function generateFPReport()
    {
        try {
            $data = $this->prepareReportData('A');


            $barangayName  = $data['barangayName'];
            $year          = $data['year'];
            $quarterLabel  = $data['quarterLabel'];
            $quarter       = $data['quarter'];
            $indicators    = $data['indicators'];
            $entries       = $data['entries'];

            // 🔹 Log count for debugging
            log_message('info', sprintf(
                'Generating FP Report | Barangay: %s | Year: %s | Quarter: %s | Entries: %d',
                $barangayName,
                $year,
                $quarterLabel,
                count($entries)
            ));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/section_a.xlsx';
            if (!is_file($templateFile)) {
                throw new \Exception('Template not found: ' . basename($templateFile));
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill template header cells
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2', $quarterLabel);

            // 🔹 Define Excel rows, skipping none (you can modify skip list if needed)
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
            $mapRules = [
                'current_user_beginning' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D'],
                'new_acceptor_previous'   => ['10-14' => 'F', '15-19' => 'G', '20-49' => 'H'],
                'other_acceptor_present'  => ['10-14' => 'J', '15-19' => 'K', '20-49' => 'L'],
                'drop_outs'               => ['10-14' => 'N', '15-19' => 'O', '20-49' => 'P'],
                'current_user_end'        => ['10-14' => 'R', '15-19' => 'S', '20-49' => 'T'],
                'new_acceptor_present'    => ['10-14' => 'V', '15-19' => 'W', '20-49' => 'X'],
            ];

            // 🔹 Group entries by indicator for summation
            $entriesByIndicator = [];
            foreach ($entries as $entry) {
                $entriesByIndicator[$entry['indicator_id']][] = $entry;
            }

            // 🔹 Fill indicator rows
            foreach ($indicators as $indicator) {
                $rowNum = $excelRows[$indicator['id']] ?? null;
                if (!$rowNum) continue;

                $indicatorEntries = $entriesByIndicator[$indicator['id']] ?? [];
                $sums = []; // column => total

                foreach ($indicatorEntries as $entry) {
                    $userType = trim($entry['user_type']);
                    $ageGroup = trim($entry['agegroup']);
                    $value    = $entry['value'] ?? 0;

                    if (isset($mapRules[$userType][$ageGroup])) {
                        $col = $mapRules[$userType][$ageGroup];
                        $sums[$col] = ($sums[$col] ?? 0) + $value;
                    }
                }

                // Write totals to Excel
                foreach ($sums as $col => $total) {
                    $sheet->setCellValue($col . $rowNum, $total);
                }
            }

            // 🔹 Save the generated report
            $fileName = sprintf(
                'SectionA_%s_Q%s_%s_%s.xlsx',
                $barangayName,
                $quarter,
                $year,
                date('Ymd_His')
            );

            $dirPath = WRITEPATH . 'reports/section_a/';
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $tempPath = $dirPath . $fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log to database
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'A',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Return success
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Report generated successfully!',
            ]);
        } catch (\Throwable $e) {
            // 🔹 Log error and show properly in the view
            log_message('error', 'Error generating FP report: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }


    public function generateMaternalReport()
    {
        try {
            // 🔹 Load Section B (Maternal)
            $data = $this->prepareReportData('B');

            $barangayName  = $data['barangayName'];
            $year          = $data['year'];
            $quarterLabel  = $data['quarterLabel'];
            $quarter       = $data['quarter'];
            $indicators    = $data['indicators'];
            $entries       = $data['entries'];

            // 🔹 Log count for quick debugging
            log_message('info', sprintf(
                'Generating Maternal Report | Barangay: %s | Year: %s | Quarter: %s | Entries: %d',
                $barangayName,
                $year,
                $quarterLabel,
                count($entries)
            ));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/section_b.xlsx';
            if (!is_file($templateFile)) {
                throw new \Exception('Template not found: ' . basename($templateFile));
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header info
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2', $quarterLabel);

            // 🔹 Define subsection mapping
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

            // 🔹 Iterate through entries directly (no grouping)
            foreach ($entries as $entry) {
                $indicatorId = $entry['indicator_id'];
                $subsection  = $entry['subsection']; // already lowercase
                $ageGroup    = $entry['agegroup'];
                $value       = (float)($entry['value'] ?? 0);

                // Skip unknown subsections
                if (!isset($mapRules[$subsection])) {
                    continue;
                }

                // Determine set (1 or 2)
                $set = null;
                foreach ($mapRules[$subsection] as $s => $rule) {
                    $nextSet   = $mapRules[$subsection][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicatorId >= $rule['start_id'] && $indicatorId < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$subsection][$set];
                $rowNum = $rule['start_row'] + ($indicatorId - $rule['start_id']);

                // Check if column for this age group exists
                if (!isset($rule['cols'][$ageGroup])) {
                    continue;
                }

                $col = $rule['cols'][$ageGroup];

                // Get current cell value (if any)
                $currentValue = (float)($sheet->getCell($col . $rowNum)->getValue() ?? 0);
                $sheet->setCellValue($col . $rowNum, $currentValue + $value);
            }

            // 🔹 Save the generated report
            $fileName = sprintf(
                'SectionB_%s_Q%s_%s_%s.xlsx',
                preg_replace('/\s+/', '_', $barangayName),
                $quarter,
                $year,
                date('Ymd_His')
            );

            $dirPath = WRITEPATH . 'reports/section_b/';
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $tempPath = $dirPath . $fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log to database
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'B',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Success response
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Maternal report generated successfully!',
                'file'    => $tempPath,
            ]);
        } catch (\Throwable $e) {
            // 🔹 Error logging
            log_message('error', 'Error generating Maternal report: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating Maternal report: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }


    public function generateChildReport()
    {
        try {
            $data = $this->prepareReportData('C');

            $barangayName  = $data['barangayName'];
            $year          = $data['year'];
            $quarterLabel  = $data['quarterLabel'];
            $quarter       = $data['quarter'];
            $indicators    = $data['indicators'];
            $entries       = $data['entries'];

            // 🔹 Log count for quick debugging
            log_message('info', sprintf(
                'Generating CHILD Report | Barangay: %s | Year: %s | Quarter: %s | Entries: %d',
                $barangayName,
                $year,
                $quarterLabel,
                count($entries)
            ));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/section_c.xlsx';
            if (!is_file($templateFile)) {
                throw new \Exception('Template not found: ' . basename($templateFile));
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header info
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2', $quarterLabel);

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

            // 🔹 Iterate through entries directly (no grouping)
            foreach ($entries as $entry) {
                $indicatorId = $entry['indicator_id'];
                $subsection  = $entry['subsection']; // already lowercase
                $sex    = $entry['sex'];
                $value       = (float)($entry['value'] ?? 0);

                // Skip unknown subsections
                if (!isset($mapRules[$subsection])) {
                    continue;
                }

                // Determine set (1 or 2)
                $set = null;
                foreach ($mapRules[$subsection] as $s => $rule) {
                    $nextSet   = $mapRules[$subsection][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicatorId >= $rule['start_id'] && $indicatorId < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$subsection][$set];
                $rowNum = $rule['start_row'] + ($indicatorId - $rule['start_id']);

                // Check if column for this age group exists
                if (!isset($rule['cols'][$sex])) {
                    continue;
                }

                $col = $rule['cols'][$sex];

                // Get current cell value (if any)
                $currentValue = (float)($sheet->getCell($col . $rowNum)->getValue() ?? 0);
                $sheet->setCellValue($col . $rowNum, $currentValue + $value);
            }

            // 🔹 Save the generated report
            $fileName = sprintf(
                'SectionC_%s_Q%s_%s_%s.xlsx',
                preg_replace('/\s+/', '_', $barangayName),
                $quarter,
                $year,
                date('Ymd_His')
            );

            $dirPath = WRITEPATH . 'reports/section_c/';
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $tempPath = $dirPath . $fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log to database
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'C',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Success response
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Report generated successfully!',
                'file'    => $tempPath,
            ]);
        } catch (\Throwable $e) {
            // 🔹 Error logging
            log_message('error', 'Error generating Maternal report: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating Maternal report: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function generateOralReport()
    {
        try {
            $data = $this->prepareReportData('D');

            $barangayName  = $data['barangayName'];
            $year          = $data['year'];
            $quarterLabel  = $data['quarterLabel'];
            $quarter       = $data['quarter'];
            $indicators    = $data['indicators'];
            $entries       = $data['entries'];

            // 🔹 Log entries count
            log_message('info', sprintf(
                'Generating ORAL Report | Barangay: %s | Year: %s | Quarter: %s | Entries: %d',
                $barangayName,
                $year,
                $quarterLabel,
                count($entries)
            ));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/section_d.xlsx';
            if (!is_file($templateFile)) {
                throw new \Exception('Template not found: ' . basename($templateFile));
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2', $quarterLabel);

            // 🔹 Mapping rules (subsection => sets => columns)
            $mapRules = [
                'o1' => [
                    1 => ['start_id' => 139, 'start_row' => 12, 'cols' => ['male' => 'B', 'female' => 'C']],
                    2 => ['start_id' => 155, 'start_row' => 12, 'cols' => ['male' => 'P', 'female' => 'Q']],
                ],
                'o2' => [
                    1 => ['start_id' => 170, 'start_row' => 30, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D']],
                    2 => ['start_id' => 173, 'start_row' => 30, 'cols' => ['10-14' => 'P', '15-19' => 'Q', '20-49' => 'R']],
                ],
            ];

            // 🔹 Iterate entries
            foreach ($entries as $entry) {
                $indicatorId = $entry['indicator_id'];
                $subsection  = $entry['subsection']; // lowercase
                $ageGroup    = $entry['agegroup'];
                $sex         = $entry['sex'] ?? null;
                $value       = (float)($entry['value'] ?? 0);

                // Skip unknown subsections
                if (!isset($mapRules[$subsection])) continue;

                // Determine set (1 or 2)
                $set = null;
                foreach ($mapRules[$subsection] as $s => $rule) {
                    $nextSet   = $mapRules[$subsection][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicatorId >= $rule['start_id'] && $indicatorId < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$subsection][$set];
                $rowNum = $rule['start_row'] + ($indicatorId - $rule['start_id']);

                // Determine the column:
                // 1️⃣ Prefer sex if present
                // 2️⃣ Otherwise fallback to agegroup
                if ($sex && isset($rule['cols'][$sex])) {
                    $col = $rule['cols'][$sex];
                } elseif (isset($rule['cols'][$ageGroup])) {
                    $col = $rule['cols'][$ageGroup];
                } else {
                    continue; // skip if no column found
                }

                // Sum value with existing cell
                $currentValue = (float)($sheet->getCell($col . $rowNum)->getValue() ?? 0);
                $sheet->setCellValue($col . $rowNum, $currentValue + $value);
            }



            // 🔹 Save report
            $fileName = sprintf(
                'SectionD_%s_Q%s_%s_%s.xlsx',
                preg_replace('/\s+/', '_', $barangayName),
                $quarter,
                $year,
                date('Ymd_His')
            );

            $dirPath = WRITEPATH . 'reports/section_d/';
            if (!is_dir($dirPath)) mkdir($dirPath, 0777, true);

            $tempPath = $dirPath . $fileName;
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log to database
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'D',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Success
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'ORAL report generated successfully!',
                'file'    => $tempPath,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating ORAL report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating ORAL report: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }


    public function generateNCDiseaseReport()
    {
        try {
            $data = $this->prepareReportData('E');

            $barangayName  = $data['barangayName'];
            $year          = $data['year'];
            $quarterLabel  = $data['quarterLabel'];
            $quarter       = $data['quarter'];
            $indicators    = $data['indicators'];
            $entries       = $data['entries'];

            // 🔹 Log entries count
            log_message('info', sprintf(
                'Generating NCDISEASE Report | Barangay: %s | Year: %s | Quarter: %s | Entries: %d',
                $barangayName,
                $year,
                $quarterLabel,
                count($entries)
            ));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/section_e.xlsx';
            if (!is_file($templateFile)) {
                throw new \Exception('Template not found: ' . basename($templateFile));
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2', $quarterLabel);

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

            foreach ($entries as $entry) {
                $indicatorId = $entry['indicator_id'];
                $subsection  = strtolower($entry['subsection']); // ensure lowercase
                $ageGroup    = $entry['agegroup'] ?? null;
                $sex         = $entry['sex'] ?? null;
                $value       = (float)($entry['value'] ?? 0);

                // Skip if subsection not mapped
                if (!isset($mapRules[$subsection])) {
                    continue;
                }

                // Determine set (1 or 2)
                $set = null;
                foreach ($mapRules[$subsection] as $s => $rule) {
                    $nextSet   = $mapRules[$subsection][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicatorId >= $rule['start_id'] && $indicatorId < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$subsection][$set];
                $rowNum = $rule['start_row'] + ($indicatorId - $rule['start_id']);

                // 🧩 Determine the correct column key
                $col = null;

                if ($subsection === 'ncd8') {
                    // 🔹 Combine sex + agegroup key (ex: "male_10-19")
                    if ($sex && $ageGroup) {
                        $key = "{$sex}_{$ageGroup}";
                        if (isset($rule['cols'][$key])) {
                            $col = $rule['cols'][$key];
                        }
                    }
                } else {
                    // 🔹 Normal sections: use sex first, fallback to agegroup
                    if ($sex && isset($rule['cols'][$sex])) {
                        $col = $rule['cols'][$sex];
                    } elseif ($ageGroup && isset($rule['cols'][$ageGroup])) {
                        $col = $rule['cols'][$ageGroup];
                    }
                }

                // Skip if column not found
                if (!$col) continue;

                // Add to existing cell value
                $currentValue = (float)($sheet->getCell($col . $rowNum)->getValue() ?? 0);
                $sheet->setCellValue($col . $rowNum, $currentValue + $value);
            }

            // 🔹 Save report
            $fileName = sprintf(
                'SectionE_%s_Q%s_%s_%s.xlsx',
                preg_replace('/\s+/', '_', $barangayName),
                $quarter,
                $year,
                date('Ymd_His')
            );

            $dirPath = WRITEPATH . 'reports/section_e/';
            if (!is_dir($dirPath)) mkdir($dirPath, 0777, true);

            $tempPath = $dirPath . $fileName;
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log to database
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'E',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Success
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'NCDISEASE report generated successfully!',
                'file'    => $tempPath,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating ORAL report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating ORAL report: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function generateEnviReport()
    {
        try {
            $data = $this->prepareReportData('F');

            $barangayName  = $data['barangayName'];
            $year          = $data['year'];
            $quarterLabel  = $data['quarterLabel'];
            $quarter       = $data['quarter'];
            $indicators    = $data['indicators'];
            $entries       = $data['entries'];

            // 🔹 Log entries count
            log_message('info', sprintf(
                'Generating ENVI Report | Barangay: %s | Year: %s | Quarter: %s | Entries: %d',
                $barangayName,
                $year,
                $quarterLabel,
                count($entries)
            ));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/section_f.xlsx';
            if (!is_file($templateFile)) {
                throw new \Exception('Template not found: ' . basename($templateFile));
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header
            $sheet->setCellValue('E6', strtoupper($barangayName));
            $sheet->setCellValue('H2', $year);
            $sheet->setCellValue('E2', $quarterLabel);
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


            foreach ($entries as $entry) {
                $indicatorId = $entry['indicator_id'];
                $subsection  = strtolower(trim($entry['subsection'])); // ensure lowercase
                $value       = (float)($entry['value'] ?? 0);

                // Skip if subsection not mapped
                if (!isset($mapRules[$subsection])) {
                    continue;
                }

                // 🔹 Determine which set applies (1, 2, etc.)
                $set = null;
                foreach ($mapRules[$subsection] as $s => $rule) {
                    $nextSet   = $mapRules[$subsection][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicatorId >= $rule['start_id'] && $indicatorId < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$subsection][$set];
                $rowNum = $rule['start_row'] + ($indicatorId - $rule['start_id']);

                // 🔹 All subsection-only values go to same “value” column
                $col = $rule['cols']['value'] ?? null;
                if (!$col) continue;

                // Add to existing cell value (in case multiple barangays or months)
                $currentValue = (float)($sheet->getCell($col . $rowNum)->getValue() ?? 0);
                $sheet->setCellValue($col . $rowNum, $currentValue + $value);
            }

            // 🔹 Save report
            $fileName = sprintf(
                'SectionF_%s_Q%s_%s_%s.xlsx',
                preg_replace('/\s+/', '_', $barangayName),
                $quarter,
                $year,
                date('Ymd_His')
            );

            $dirPath = WRITEPATH . 'reports/section_f/';
            if (!is_dir($dirPath)) mkdir($dirPath, 0777, true);

            $tempPath = $dirPath . $fileName;
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log to database
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'F',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Success
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'ENVI report generated successfully!',
                'file'    => $tempPath,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating ORAL report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating ORAL report: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function generateIDiseaseReport()
    {
        try {
            $data = $this->prepareReportData('G');

            $barangayName  = $data['barangayName'];
            $year          = $data['year'];
            $quarterLabel  = $data['quarterLabel'];
            $quarter       = $data['quarter'];
            $indicators    = $data['indicators'];
            $entries       = $data['entries'];

            // 🔹 Log entries count
            log_message('info', sprintf(
                'Generating IDISEASE Report | Barangay: %s | Year: %s | Quarter: %s | Entries: %d',
                $barangayName,
                $year,
                $quarterLabel,
                count($entries)
            ));

            // 🔹 Load Excel template
            $templateFile = APPPATH . 'Views/pages/reports/section_g.xlsx';
            if (!is_file($templateFile)) {
                throw new \Exception('Template not found: ' . basename($templateFile));
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2', $quarterLabel);

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

            // 🔹 Iterate through entries directly (no grouping)
            foreach ($entries as $entry) {
                $indicatorId = $entry['indicator_id'];
                $subsection  = $entry['subsection']; // already lowercase
                $sex    = $entry['sex'];
                $value       = (float)($entry['value'] ?? 0);

                // Skip unknown subsections
                if (!isset($mapRules[$subsection])) {
                    continue;
                }

                // Determine set (1 or 2)
                $set = null;
                foreach ($mapRules[$subsection] as $s => $rule) {
                    $nextSet   = $mapRules[$subsection][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicatorId >= $rule['start_id'] && $indicatorId < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule = $mapRules[$subsection][$set];
                $rowNum = $rule['start_row'] + ($indicatorId - $rule['start_id']);

                // Check if column for this age group exists
                if (!isset($rule['cols'][$sex])) {
                    continue;
                }

                $col = $rule['cols'][$sex];

                // Get current cell value (if any)
                $currentValue = (float)($sheet->getCell($col . $rowNum)->getValue() ?? 0);
                $sheet->setCellValue($col . $rowNum, $currentValue + $value);
            }

            // 🔹 Save report
            $fileName = sprintf(
                'SectionG_%s_Q%s_%s_%s.xlsx',
                preg_replace('/\s+/', '_', $barangayName),
                $quarter,
                $year,
                date('Ymd_His')
            );

            $dirPath = WRITEPATH . 'reports/section_g/';
            if (!is_dir($dirPath)) mkdir($dirPath, 0777, true);

            $tempPath = $dirPath . $fileName;
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log to database
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'G',
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Success
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'IDISEASE report generated successfully!',
                'file'    => $tempPath,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function generateAllReport()
    {
        try {
            // 🔹 Prepare Section A and B data
            $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
            $allData = [];
            foreach ($sections as $sectionCode) {
                $allData[$sectionCode] = $this->prepareReportData($sectionCode);
            }

            // 🔹 Common header info (from Section A)
            $barangayName  = $allData['A']['barangayName'];
            $year          = $allData['A']['year'];
            $quarterLabel  = $allData['A']['quarterLabel'];
            $quarter       = $allData['A']['quarter'];
            // $entries       = $allData['entries'];
            // $indicators       = $allData['indicators'];

            // 🔹 Log for debugging
            // log_message('info', sprintf(
            //     'Generating ALL Report | Barangay: %s | Year: %s | Quarter: %s | Entries: %d',
            //     $barangayName,
            //     $year,
            //     $quarterLabel,
            //     count($entries)
            // ));

            // 🔹 Load combined "all sections" template
            $templateFile = APPPATH . 'Views/pages/reports/section_all.xlsx';
            if (!is_file($templateFile)) {
                throw new \Exception('Template not found: ' . basename($templateFile));
            }

            $spreadsheet = IOFactory::load($templateFile);
            $sheet = $spreadsheet->getActiveSheet();

            // 🔹 Fill header
            $sheet->setCellValue('G6', strtoupper($barangayName));
            $sheet->setCellValue('J2', $year);
            $sheet->setCellValue('G2', $quarterLabel);

            // 🔹 Get column mapping from private function
            $mapRules = $this->getMapRules();
            // =========================
            // 🔹 SECTION A (FP-style)
            // =========================
            $sectionCode = 'A';
            $data        = $allData[$sectionCode];
            $indicators  = $data['indicators'];
            $entries     = $data['entries'];

            $columnMap   = $this->getMapRules();

            $entriesByIndicator = [];
            foreach ($entries as $entry) {
                $entriesByIndicator[$entry['indicator_id']][] = $entry;
            }

            // Map indicators to Excel rows
            $startRow = 16;
            $excelRows = [];
            $row = $startRow;
            foreach ($indicators as $indicator) {
                $excelRows[$indicator['id']] = $row;
                $row++;
            }

            // Fill Section A data
            foreach ($indicators as $indicator) {
                $rowNum = $excelRows[$indicator['id']] ?? null;
                if (!$rowNum) continue;

                $indicatorEntries = $entriesByIndicator[$indicator['id']] ?? [];
                $sums = [];

                foreach ($indicatorEntries as $entry) {
                    $userType = trim($entry['user_type']);
                    $ageGroup = trim($entry['agegroup']);
                    $value    = $entry['value'] ?? 0;

                    if (isset($columnMap[$userType][$ageGroup])) {
                        $col = $columnMap[$userType][$ageGroup];
                        $sums[$col] = ($sums[$col] ?? 0) + $value;
                    }
                }

                foreach ($sums as $col => $total) {
                    $sheet->setCellValue($col . $rowNum, $total);
                }
            }

            // =========================
            // 🔹 SECTION B-G (Maternal-style, agegroup + sex + subsection)
            // =========================
            $sectionCodes = ['B', 'C', 'D', 'F', 'G'];

            foreach ($sectionCodes as $sectionCode) {
                $data       = $allData[$sectionCode];
                $indicators = $data['indicators'];
                $entries    = $data['entries'];

                $mapRules = $this->getMapRules();

                foreach ($entries as $entry) {
                    $indicatorId = $entry['indicator_id'];
                    $subsection  = $entry['subsection'];
                    $ageGroup    = $entry['agegroup'] ?? null;
                    $sex         = $entry['sex'] ?? null;
                    $value       = (float)($entry['value'] ?? 0);

                    if (!isset($mapRules[$subsection])) continue;

                    // Determine set (1 or 2)
                    $set = null;
                    foreach ($mapRules[$subsection] as $s => $rule) {
                        $nextSet   = $mapRules[$subsection][$s + 1] ?? null;
                        $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                        if ($indicatorId >= $rule['start_id'] && $indicatorId < $nextStart) {
                            $set = $s;
                            break;
                        }
                    }
                    if (!$set) continue;

                    $rule   = $mapRules[$subsection][$set];
                    $rowNum = $rule['start_row'] + ($indicatorId - $rule['start_id']);

                    // Determine column
                    $col = null;

                    if ($ageGroup && $sex && isset($rule['cols'][$ageGroup][$sex])) {
                        // agegroup + sex mapping
                        $col = $rule['cols'][$ageGroup][$sex];
                    } elseif ($ageGroup && isset($rule['cols'][$ageGroup])) {
                        // agegroup only
                        $col = $rule['cols'][$ageGroup];
                    } elseif ($sex && isset($rule['cols'][$sex])) {
                        // sex only
                        $col = $rule['cols'][$sex];
                    } elseif (isset($rule['cols']['value'])) {
                        // subsection-only entry
                        $col = $rule['cols']['value'];
                    } else {
                        continue; // no valid column found
                    }

                    $currentValue = (float)($sheet->getCell($col . $rowNum)->getValue() ?? 0);
                    $sheet->setCellValue($col . $rowNum, $currentValue + $value);
                }
            }

            // =========================
            // 🔹 Section E (NCDisease-style)
            // =========================
            $sectionCode = 'E';
            $data        = $allData[$sectionCode];
            $indicators  = $data['indicators'];
            $entries     = $data['entries'];

            $mapRules = $this->getMapRules();

            foreach ($entries as $entry) {
                $indicatorId = $entry['indicator_id'];
                $subsection  = strtolower($entry['subsection'] ?? '');
                $ageGroup    = $entry['agegroup'] ?? null;
                $sex         = $entry['sex'] ?? null;
                $value       = (float)($entry['value'] ?? 0);

                if (!isset($mapRules[$subsection])) continue;

                // Determine set (1 or 2)
                $set = null;
                foreach ($mapRules[$subsection] as $s => $rule) {
                    $nextSet   = $mapRules[$subsection][$s + 1] ?? null;
                    $nextStart = $nextSet['start_id'] ?? PHP_INT_MAX;
                    if ($indicatorId >= $rule['start_id'] && $indicatorId < $nextStart) {
                        $set = $s;
                        break;
                    }
                }
                if (!$set) continue;

                $rule   = $mapRules[$subsection][$set];
                $rowNum = $rule['start_row'] + ($indicatorId - $rule['start_id']);

                // Column logic specific to NCDisease-style
                $col = null;
                if ($subsection === 'ncd8') {
                    if ($sex && $ageGroup) {
                        $key = "{$sex}_{$ageGroup}";
                        if (isset($rule['cols'][$key])) {
                            $col = $rule['cols'][$key];
                        }
                    }
                } else {
                    if ($sex && isset($rule['cols'][$sex])) {
                        $col = $rule['cols'][$sex];
                    } elseif ($ageGroup && isset($rule['cols'][$ageGroup])) {
                        $col = $rule['cols'][$ageGroup];
                    }
                }

                if (!$col) continue;

                $currentValue = (float)($sheet->getCell($col . $rowNum)->getValue() ?? 0);
                $sheet->setCellValue($col . $rowNum, $currentValue + $value);
            }



            // 🔹 Save report
            $fileName = sprintf(
                'SectionALL_%s_Q%s_%s_%s.xlsx',
                preg_replace('/\s+/', '_', $barangayName),
                $quarter,
                $year,
                date('Ymd_His')
            );

            $dirPath = WRITEPATH . 'reports/section_all/';
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $tempPath = $dirPath . $fileName;
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // 🔹 Log to database
            $reportLogsModel = new \App\Models\ReportsModel();
            $reportLogsModel->insert([
                'report_year'    => $year,
                'report_quarter' => $quarter,
                'barangay'       => $barangayName,
                'section'        => 'ALL', // combined
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Success response
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'All Sections report generated.',
                'file'    => $tempPath,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error generating All Sections report: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error generating report: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }



    private function getMapRules(): array
    {
        return [

            // SECTION A: FAMILY PLANNING
            'current_user_beginning' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D'],
            'new_acceptor_previous'   => ['10-14' => 'F', '15-19' => 'G', '20-49' => 'H'],
            'other_acceptor_present'  => ['10-14' => 'J', '15-19' => 'K', '20-49' => 'L'],
            'drop_outs'               => ['10-14' => 'N', '15-19' => 'O', '20-49' => 'P'],
            'current_user_end'        => ['10-14' => 'R', '15-19' => 'S', '20-49' => 'T'],
            'new_acceptor_present'    => ['10-14' => 'V', '15-19' => 'W', '20-49' => 'X'],

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
            'o1' => [
                1 => ['start_id' => 139, 'start_row' => 135, 'cols' => ['male' => 'B', 'female' => 'C']],
                2 => ['start_id' => 155, 'start_row' => 135, 'cols' => ['male' => 'P', 'female' => 'Q']],
            ],
            'o2' => [
                1 => ['start_id' => 170, 'start_row' => 153, 'cols' => ['10-14' => 'B', '15-19' => 'C', '20-49' => 'D']],
                2 => ['start_id' => 173, 'start_row' => 153, 'cols' => ['10-14' => 'P', '15-19' => 'Q', '20-49' => 'R']],
            ],
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
        ];
    }
}
