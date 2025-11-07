<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class SectionsController extends ResourceController
{
    public function index(): string
    {

        $SectionsModel = new \App\Models\SectionsModel();

        $sections = $SectionsModel->findAll();


        return view('pages/sections', [
            'sections' => $sections,
        ]);
    }

    public function show($id = null)
    {
        $sectionsModel = new \App\Models\SectionsModel();
        $subsectionsModel = new \App\Models\SubSectionsModel();
        $categoriesModel = new \App\Models\CategoriesModel();
        $indicatorsModel = new \App\Models\IndicatorsModel();

        $section = $sectionsModel->find($id);

        if (!$section) {
            return $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        // Fetch subsections
        $subsections = $subsectionsModel->where('section_code', $section['code'])->findAll();

        foreach ($subsections as &$sub) {
            // Categories under this subsection
            $categories = $categoriesModel->where('subsection', $sub['id'])->findAll();

            foreach ($categories as &$cat) {
                $cat['indicators'] = $indicatorsModel->where('category_id', $cat['id'])->findAll();
            }

            // Indicators directly under subsection (no category)
            $sub['indicators'] = $indicatorsModel
                ->where('subsection', $sub['id'])
                ->where('category_id', '')
                ->findAll();

            $sub['categories'] = $categories;
        }

        $section['subsections'] = $subsections;

        return $this->response->setStatusCode(Response::HTTP_OK)->setJSON($section);
    }


    /**
     * @var IncomingRequest
     */
    protected $request;

    public function sectionsList()
    {
        $SectionsModel = new \App\Models\SectionsModel();
        $postData = $this->request->getPost();

        $draw        = $postData['draw'];
        $start       = $postData['start'];
        $rowperpage  = $postData['length'];
        $searchValue = $postData['search']['value'];
        $sortby      = $postData['order'][0]['column'];
        $sortdir     = $postData['order'][0]['dir'];
        $sortcolumn  = $postData['columns'][$sortby]['data'];

        //records
        $records = $SectionsModel->select('*')
            ->like('id', $searchValue)
            ->orLike('name', $searchValue)
            ->orLike('code', $searchValue)
            ->orderBy($sortcolumn, $sortdir)
            ->findAll($rowperpage, $start);

        $data = array();

        foreach ($records as $record) {
            $data[] = array(
                'id' => $record['id'],
                'name' => $record['name'],
                'code' => $record['code'],
            );
        }

        // total records 
        $totalRecords = $SectionsModel->select('id')->countAllResults();

        // total records with filter

        $totalRecordswithFilter = $SectionsModel->select('id')
            ->like('id', $searchValue)
            ->orLike('name', $searchValue)
            ->orLike('code', $searchValue)
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

    public function indicators($code = null)
    {
        $IndicatorsModel = new \App\Models\IndicatorsModel();

        $indicators = $IndicatorsModel
            ->where('section_code', $code)
            ->findAll();

        return $this->response->setJSON($indicators);
    }

    public function create()
    {
        $IndicatorsModel = new \App\Models\IndicatorsModel();
        $data = $this->request->getJSON(true); // decode JSON into array

        if (!$IndicatorsModel->validate($data)) {
            $response = [
                'status' => 'error',
                'message' => $IndicatorsModel->errors()
            ];
            return $this->response->setStatusCode(Response::HTTP_BAD_REQUEST)->setJSON($response);
        }

        // Insert and get the inserted ID
        $id = $IndicatorsModel->insert($data, true); // "true" returns the inserted ID

        // Fetch the full inserted record
        $newIndicator = $IndicatorsModel->find($id);

        $response = [
            'status' => 'success',
            'message' => 'Indicator added successfully',
            'data'    => $newIndicator
        ];

        return $this->response->setStatusCode(Response::HTTP_CREATED)->setJSON($response);
    }


    public function update($id = null)
    {
        $IndicatorsModel = new \App\Models\IndicatorsModel();
        $data = $this->request->getJSON();

        if (!$IndicatorsModel->validate($data)) {
            $response = array(
                'status' => 'error',
                'message' => $IndicatorsModel->errors()
            );
            return $this->response->setStatusCode(Response::HTTP_BAD_REQUEST)->setJSON($response);
        }

        $IndicatorsModel->update($id, $data);
        $response = array(
            'status' => 'success',
            'message' => 'Indicator updated successfully'
        );

        return $this->response->setStatusCode(Response::HTTP_OK)->setJSON($response);
    }
}
