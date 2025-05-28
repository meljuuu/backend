<?php

namespace App\Models\acadbase;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CsvModel extends Model implements ToModel, WithHeadingRow
{
    protected $table = 'acadbase';

    protected $fillable = [
        'lrn',
        'name',
        'track',
        'batch',
        'curriculum',
        'status',
        'birthdate',
        'faculty_name',
    ];

    public function model(array $row)
    {
        // Convert scientific notation LRN to string
        $lrn = isset($row['lrn']) 
            ? str_replace('.', '', (string)$row['lrn'])
            : null;

        if (empty($lrn) || empty($row['name'])) {
            return null;
        }

        return new MasterlistModel([
            'lrn' => $lrn,
            'name' => $row['name'] ?? '',
            'track' => $row['track'] ?? 'SPJ',
            'batch' => $row['batch'] ?? date('Y') . '-' . (date('Y') + 1),
            'curriculum' => $row['curriculum'] ?? 'JHS',
            'status' => $row['status'] ?? 'Unreleased',
            'birthdate' => $row['birthdate'] ?? null,
            'faculty_name' => $row['faculty_name'] ?? 'System'
        ]);
    }
}

