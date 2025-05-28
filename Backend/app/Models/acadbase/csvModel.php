<?php

namespace App\Models\acadbase;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

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
        // Normalize keys to lowercase for case-insensitive matching
        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(trim(str_replace(' ', '_', $key)));
            $normalizedRow[$normalizedKey] = $value;
        }

        // Map your CSV headers to expected fields
        $lrn = $this->getLrnValue($normalizedRow);
        $name = $this->getValueByKeys($normalizedRow, ['student_name', 'name', 'student']);
        $track = $this->getValueByKeys($normalizedRow, ['academic_track', 'track']);
        $curriculum = $this->getValueByKeys($normalizedRow, ['curriculum']);
        $batch = $this->getValueByKeys($normalizedRow, ['batch', 's.y_batch', 'sy_batch', 'school_year']);
        $birthdate = $this->getValueByKeys($normalizedRow, ['birthday', 'birthdate', 'birth_date']);

        // Skip if required fields are missing
        if (empty($lrn) || empty($name)) {
            return null;
        }

        // Format birthdate
        $formattedBirthdate = $this->formatBirthdate($birthdate);

        return new MasterlistModel([
            'lrn' => $lrn,
            'name' => $name,
            'track' => $track ?: 'SPJ',
            'batch' => $batch ?: (date('Y') . '-' . (date('Y') + 1)),
            'curriculum' => $curriculum ?: 'JHS',
            'status' => 'Unreleased',
            'birthdate' => $formattedBirthdate,
            'faculty_name' => 'System'
        ]);
    }

    private function getLrnValue($row)
    {
        $lrn = $this->getValueByKeys($row, ['lrn']);
        
        if (empty($lrn)) {
            return null;
        }

        // Handle scientific notation
        if (strpos($lrn, 'E+') !== false) {
            $lrn = number_format($lrn, 0, '', '');
        }

        // Remove any decimal points and ensure it's a string
        $lrn = str_replace('.', '', (string)$lrn);
        
        return $lrn;
    }

    private function getValueByKeys($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && !empty(trim($row[$key]))) {
                return trim($row[$key]);
            }
        }
        return null;
    }

    private function formatBirthdate($birthdate)
    {
        if (empty($birthdate)) {
            return null;
        }

        // Trim and clean the birthdate string
        $birthdate = trim($birthdate);
        if (empty($birthdate)) {
            return null;
        }

        try {
            // Handle different date formats
            if (strpos($birthdate, '/') !== false) {
                $parts = explode('/', $birthdate);
                if (count($parts) === 3) {
                    // Assume MM/DD/YYYY if first part <= 12, otherwise DD/MM/YYYY
                    if ($parts[0] <= 12) {
                        return Carbon::createFromFormat('m/d/Y', $birthdate)->format('Y-m-d');
                    } else {
                        return Carbon::createFromFormat('d/m/Y', $birthdate)->format('Y-m-d');
                    }
                }
            } elseif (strpos($birthdate, '-') !== false) {
                // Already in YYYY-MM-DD format or similar
                return Carbon::parse($birthdate)->format('Y-m-d');
            } else {
                // Try to parse any other format
                return Carbon::parse($birthdate)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error("Failed to parse birthdate: " . $birthdate . " - Error: " . $e->getMessage());
            return null;
        }
    }
}