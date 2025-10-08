<?php

namespace App\Exports;

use App\Models\OralHealthTreatment;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OralHealthTreatmentsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = OralHealthTreatment::with('student')
            ->orderBy('treatment_date', 'desc');

        // Apply filters
        if (!empty($this->filters['date_from'])) {
            $query->where('treatment_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('treatment_date', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['grade_level'])) {
            $query->where(function($q) {
                $gradeLevel = $this->filters['grade_level'];
                $q->where('grade_level', $gradeLevel)
                  ->orWhere('grade_level', "Grade {$gradeLevel}");
            });
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Student Name',
            'LRN',
            'Grade Level',
            'Section',
            'Treatment Date',
            'School Year',
            'Chief Complaint',
            'Treatment Given',
            'Health Personnel',
            'Remarks',
            'Timer Status',
            'Started At',
            'Expires At',
            'Is Expired',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $treatment
     * @return array
     */
    public function map($treatment): array
    {
        return [
            $treatment->student->full_name ?? 'N/A',
            $treatment->student->lrn ?? 'N/A',
            $treatment->student->grade_level ?? 'N/A',
            $treatment->student->section ?? 'N/A',
            $treatment->treatment_date ? $treatment->treatment_date->format('Y-m-d') : 'N/A',
            $treatment->school_year ?? 'N/A',
            $treatment->chief_complaint ?? 'N/A',
            $treatment->treatment_given ?? 'N/A',
            $treatment->health_personnel ?? 'N/A',
            $treatment->remarks ?? 'N/A',
            $treatment->timer_status ?? 'N/A',
            $treatment->started_at ? $treatment->started_at->format('Y-m-d H:i:s') : 'N/A',
            $treatment->expires_at ? $treatment->expires_at->format('Y-m-d H:i:s') : 'N/A',
            $treatment->is_expired ? 'Yes' : 'No',
            $treatment->created_at ? $treatment->created_at->format('Y-m-d H:i:s') : 'N/A',
            $treatment->updated_at ? $treatment->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Oral Health Treatments';
    }
}
