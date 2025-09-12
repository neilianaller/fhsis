<?php

namespace App\Models;

use CodeIgniter\Model;

class SubSectionsModel extends Model
{
    protected $table            = 'subsections';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // column names sa database table
    protected $allowedFields    = [
        'id',
        'code',
        'name',
        'section_code'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        // 'registryno' => 'required|max_length[100]',
        // 'populationrefno' => 'required|max_length[100]',
        // 'dateofregistration' => 'valid_date[Y-m-d]',
        // 'firstname' => 'required|max_length[100]',
        // 'surname' => 'required|max_length[100]',
        // 'gender' => 'required|max_length[100]',
        // 'birthdate' => 'valid_date[Y-m-d]',
        // 'birthplace' => 'required|max_length[100]',
        // 'firstname_mother' => 'required|max_length[100]',
        // 'middlename_mother' => 'required|max_length[100]',
        // 'surname_mother' => 'required|max_length[100]',
        // 'citizenship_mother' => 'required|max_length[100]',

    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
