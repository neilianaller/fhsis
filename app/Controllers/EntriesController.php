<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;

class EntriesController extends ResourceController
{
    public function index()
    {
        //
    }


    /**
     * @var IncomingRequest
     */
    protected $request;

    public function save($code)
    {
        $entriesModel = new \App\Models\EntriesModel();

        $barangay_code = $this->request->getPost('barangay_code');
        $report_month  = $this->request->getPost('report_month');
        $report_year   = $this->request->getPost('report_year');
        $user_type   = $this->request->getPost('user_type');
        $indicatorId   = $this->request->getPost('indicatorId');
        $entries       = $this->request->getPost('entries');
        $subsection       = $this->request->getPost('subsection');

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
                'agegroup'      => $entry['agegroup'] ?? '',
                'sex'      => $entry['sex'] ?? '',
                'user_type'     => $user_type,
                'subsection'     => $subsection,
                'indicator_id'     => $indicatorId
            ])->first();

            if ($existing) {
                // Update existing record
                $entriesModel->update($existing['id'], [
                    'value'      => $entry['value'],
                    'section_code'      => $code,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Insert new record
                $entriesModel->insert([
                    'section_code'      => $code,
                    'indicator_id' => $indicatorId,
                    'barangay_code' => $barangay_code,
                    'report_month'  => $report_month,
                    'report_year'   => $report_year,
                    'agegroup'      => $entry['agegroup'] ?? '',
                    'sex'      => $entry['sex'] ?? '',
                    'user_type'     => $user_type,
                    'subsection'     => $subsection,
                    'value'         => $entry['value'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            }

            if ($code === 'A') {
                log_message('info', 'Section A entry processed.');

                // Step 1: Fetch current month's relevant data to calculate current_user_end
                $ageGroups = ['10-14', '15-19', '20-49'];
                $currentEnd = [];

                foreach ($ageGroups as $ageGroup) {
                    $current_user_beginning = $entriesModel->where([
                        'barangay_code' => $barangay_code,
                        'report_month'  => $report_month,
                        'report_year'   => $report_year,
                        'user_type'     => 'current_user_beginning',
                        'agegroup'      => $ageGroup,
                        'indicator_id'  => $indicatorId
                    ])->first()['value'] ?? 0;

                    $new_acceptor_previous = $entriesModel->where([
                        'barangay_code' => $barangay_code,
                        'report_month'  => $report_month,
                        'report_year'   => $report_year,
                        'user_type'     => 'new_acceptor_previous',
                        'agegroup'      => $ageGroup,
                        'indicator_id'  => $indicatorId
                    ])->first()['value'] ?? 0;

                    $other_acceptor_present = $entriesModel->where([
                        'barangay_code' => $barangay_code,
                        'report_month'  => $report_month,
                        'report_year'   => $report_year,
                        'user_type'     => 'other_acceptor_present',
                        'agegroup'      => $ageGroup,
                        'indicator_id'  => $indicatorId
                    ])->first()['value'] ?? 0;

                    $drop_outs = $entriesModel->where([
                        'barangay_code' => $barangay_code,
                        'report_month'  => $report_month,
                        'report_year'   => $report_year,
                        'user_type'     => 'drop_outs',
                        'agegroup'      => $ageGroup,
                        'indicator_id'  => $indicatorId
                    ])->first()['value'] ?? 0;

                    $new_acceptor_present = 0;
                    if ($user_type === 'new_acceptor_present') {
                        foreach ($entries as $entry) {
                            if ($entry['agegroup'] === $ageGroup) {
                                $new_acceptor_present = $entry['value'];
                                break;
                            }
                        }
                    }

                    $currentEnd[$ageGroup] = $current_user_beginning + $new_acceptor_previous + $other_acceptor_present + $new_acceptor_present - $drop_outs;
                }

                // Step 2: Determine next month & year
                $nextMonth = $report_month == 12 ? 1 : $report_month + 1;
                $nextYear  = $report_month == 12 ? $report_year + 1 : $report_year;

                // Step 3: Insert/update next month's current_user_beginning
                foreach ($ageGroups as $ageGroup) {
                    $existingNextMonth = $entriesModel->where([
                        'barangay_code' => $barangay_code,
                        'report_month'  => $nextMonth,
                        'report_year'   => $nextYear,
                        'agegroup'      => $ageGroup,
                        'user_type'     => 'current_user_beginning',
                        'indicator_id'  => $indicatorId,
                    ])->first();

                    if ($existingNextMonth) {
                        $entriesModel->update($existingNextMonth['id'], [
                            'value' => $currentEnd[$ageGroup],
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    } else {
                        $entriesModel->insert([
                            'section_code'  => $code,
                            'indicator_id'  => $indicatorId,
                            'barangay_code' => $barangay_code,
                            'report_month'  => $nextMonth,
                            'report_year'   => $nextYear,
                            'agegroup'      => $ageGroup,
                            'user_type'     => 'current_user_beginning',
                            'value'         => $currentEnd[$ageGroup],
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Entries saved successfully.'
        ]);
    }


    public function get($code)
    {
        $entriesModel = new \App\Models\EntriesModel();

        $barangayCode = $this->request->getGet('barangay_code');
        $reportMonth  = $this->request->getGet('report_month');
        $reportYear   = $this->request->getGet('report_year');
        $indicator_id   = $this->request->getGet('indicator_id');

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
            ->where('section_code', $code)
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $entries
        ]);
    }
}
