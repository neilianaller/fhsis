<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class SubSectionsController extends ResourceController
{
    public function index(): string
    {

        $SubSectionsModel = new \App\Models\SubSectionsModel();

        $sections = $SubSectionsModel->findAll();


        return view('pages/sections', [
            'sections' => $sections,
        ]);
    }

    public function show($id = null)
    {

        $sectionsModel = new \App\Models\SubSectionsModel();
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

    public function subsection($code = null)
    {
        $SubSectionsModel = new \App\Models\SubSectionsModel();

        $subsections = $SubSectionsModel
            ->where('section_code', $code)
            ->findAll();

        return $this->response->setJSON($subsections);
    }

    public function create()
    {
        $SubSectionsModel = new \App\Models\SubSectionsModel();
        $data = $this->request->getJSON(true); // decode JSON into array

        if (!$SubSectionsModel->validate($data)) {
            $response = [
                'status' => 'error',
                'message' => $SubSectionsModel->errors()
            ];
            return $this->response->setStatusCode(Response::HTTP_BAD_REQUEST)->setJSON($response);
        }

        // Insert and get the inserted ID
        $id = $SubSectionsModel->insert($data, true); // "true" returns the inserted ID

        // Fetch the full inserted record
        $newIndicator = $SubSectionsModel->find($id);

        $response = [
            'status' => 'success',
            'message' => 'Indicator added successfully',
            'data'    => $newIndicator
        ];

        return $this->response->setStatusCode(Response::HTTP_CREATED)->setJSON($response);
    }


    public function update($id = null)
    {
        $SubSectionsModel = new \App\Models\SubSectionsModel();
        $data = $this->request->getJSON();

        if (!$SubSectionsModel->validate($data)) {
            $response = array(
                'status' => 'error',
                'message' => $SubSectionsModel->errors()
            );
            return $this->response->setStatusCode(Response::HTTP_BAD_REQUEST)->setJSON($response);
        }

        $SubSectionsModel->update($id, $data);
        $response = array(
            'status' => 'success',
            'message' => 'Indicator updated successfully'
        );

        return $this->response->setStatusCode(Response::HTTP_OK)->setJSON($response);
    }
}
