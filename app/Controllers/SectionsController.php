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
        $result = $sectionsModel->find($id);

        if (!$result) {
            return $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        return $this->response->setStatusCode(Response::HTTP_OK)->setJSON($result);
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
        $data = $this->request->getJSON();

        if (!$IndicatorsModel->validate($data)) {
            $response = array(
                'status' => 'error',
                'message' => $IndicatorsModel->errors()
            );
            return $this->response->setStatusCode(Response::HTTP_BAD_REQUEST)->setJSON($response);
        }

        $IndicatorsModel->insert($data);

        $response = array(
            'status' => 'success',
            'message' => 'Indicator added successfully'
        );

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
