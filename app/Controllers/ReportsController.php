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
    public function generateReport()
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
            $barangayName = $barangayData['name'] ?? $barangay; // fallback to code

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

            // 🔹 Fetch data
            $records = $model
                ->where('report_year', $year)
                ->where('barangay_code', $barangay)
                ->whereIn('report_month', $months)
                ->findAll();

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

            // 🔹 Save the generated report
            $fileName = 'Report_' . $barangay . '_Q' . $quarter . '_' . $year . '.xlsx';
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
                'section' => match ((int)$sectionId) {
                    1 => 'A',
                    2 => 'B',
                    3 => 'C',
                    4 => 'D',
                    5 => 'E',
                    6 => 'F',
                    7 => 'G',
                },
                'filepath'       => $tempPath,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // ✅ Return success with file URL
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Report generated successfully!',
                'download_url' => base_url('writable/reports/' . $fileName)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error generating report: ' . $e->getMessage()
            ]);
        }
    }


    public function list()
    {
        $ReportsModel = new \App\Models\ReportsModel();
        $postData = $this->request->getPost();

        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length'];
        $seachvalue = $postData['search']['value'];
        $sortby = $postData['order'][0]['column'];
        $sortdir = $postData['order'][0]['dir'];
        $sortcolumn = $postData['columns'][$sortby]['data'];

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
        $reportLogsModel = new \App\Models\ReportsModel();
        $log = $reportLogsModel->find($id);

        if (!$log || !is_file($log['filepath'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found.');
        }

        return $this->response->download($log['filepath'], null)
            ->setFileName(basename($log['filepath']));
    }
}
