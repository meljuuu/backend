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
        // Validate required fields
        if (empty($row['lrn']) || empty($row['name'])) {
            return null; // Skip invalid rows
        }

        return new MasterlistModel([
            'lrn' => $row['lrn'],
            'name' => $row['name'],
            'track' => $row['track'] ?? 'SPJ', // Default value
            'batch' => $row['batch'] ?? '2023-2024', // Default value
            'curriculum' => $row['curriculum'] ?? 'JHS', // Default value
            'status' => $row['status'] ?? 'Unreleased', // Default value
            'birthdate' => $row['birthdate'] ?? null,
            'faculty_name' => $row['faculty_name'] ?? 'System', // Default value
        ]);
    }
}

