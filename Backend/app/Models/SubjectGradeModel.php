<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectGradeModel extends Model
{
    use HasFactory;


    protected $table = 'subject_grades';
    protected $primaryKey = 'Grade_ID';

    protected $fillable = [
        'Class_ID',
        'Q1', 'Q2', 'Q3', 'Q4', 'FinalGrade', 'Remarks',
        'Student_ID', 'Teacher_ID', 'Subject_ID', 'Status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'Q1' => 'float',
        'Q2' => 'float',
        'Q3' => 'float',
        'Q4' => 'float',
        'FinalGrade' => 'float'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the student that owns the grade.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentModel::class, 'Student_ID', 'Student_ID');
    }

    /**
     * Get the subject that owns the grade.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(SubjectModel::class, 'Subject_ID', 'Subject_ID');
    }

    /**
     * Get the teacher that owns the grade.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID', 'Teacher_ID');
    }

    /**
     * Calculate the final grade based on quarter grades
     */
    public function calculateFinalGrade(): float
    {
        $quarters = [$this->Q1, $this->Q2, $this->Q3, $this->Q4];
        $validGrades = array_filter($quarters, function($grade) {
            return $grade !== null;
        });

        if (empty($validGrades)) {
            return 0;
        }

        return round(array_sum($validGrades) / count($validGrades), 2);
    }

    /**
     * Determine remarks based on final grade
     */
    public function determineRemarks(): string
    {
        $finalGrade = $this->FinalGrade ?? $this->calculateFinalGrade();
        return $finalGrade >= 75 ? 'Passed' : 'Failed';
    }

    /**
     * Check if all quarters are graded
     */
    public function isComplete(): bool
    {
        return !in_array(null, [$this->Q1, $this->Q2, $this->Q3, $this->Q4]);
    }

    /**
     * Get grade for specific quarter
     */
    public function getQuarterGrade(int $quarter): ?float
    {
        $quarterKey = "Q{$quarter}";
        return $this->$quarterKey;
    }

    /**
     * Set grade for specific quarter
     */
    public function setQuarterGrade(int $quarter, float $grade): void
    {
        if ($grade < 0 || $grade > 100) {
            throw new \InvalidArgumentException('Grade must be between 0 and 100');
        }

        $quarterKey = "Q{$quarter}";
        $this->$quarterKey = $grade;
        
        // Recalculate final grade if all quarters are present
        if ($this->isComplete()) {
            $this->FinalGrade = $this->calculateFinalGrade();
            $this->Remarks = $this->determineRemarks();
        }
    }

    /**
     * Scope a query to only include grades for a specific subject
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('Subject_ID', $subjectId);
    }

    /**
     * Scope a query to only include grades for a specific student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('Student_ID', $studentId);
    }

    /**
     * Scope a query to only include grades for a specific teacher
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('Teacher_ID', $teacherId);
    }

    /**
     * Scope a query to only include passing grades
     */
    public function scopePassing($query)
    {
        return $query->where('FinalGrade', '>=', 75);
    }

    /**
     * Scope a query to only include failing grades
     */
    public function scopeFailing($query)
    {
        return $query->where('FinalGrade', '<', 75);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically calculate final grade and remarks before saving
        static::saving(function ($grade) {
            if ($grade->isComplete()) {
                $grade->FinalGrade = $grade->calculateFinalGrade();
                $grade->Remarks = $grade->determineRemarks();
            }
        });
    }
}
