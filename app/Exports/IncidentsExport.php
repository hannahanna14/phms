<?php

namespace App\Exports;

use App\Models\Incident;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IncidentsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
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
        $query = Incident::with('student')
            ->orderBy('date', 'desc');

        // Apply filters
        if (!empty($this->filters['date_from'])) {
            $query->where('date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('date', '<=', $this->filters['date_to']);
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
            'Incident Date',
            'School Year',
            'Complaint',
            'Actions Taken',
            'Status',
            'Timer Status',
            'Started At',
            'Expires At',
            'Is Expired',
            'Remaining Minutes',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $incident
     * @return array
     */
    public function map($incident): array
    {
        return [
            $incident->student->full_name ?? 'N/A',
            $incident->student->lrn ?? 'N/A',
            $incident->student->grade_level ?? 'N/A',
            $incident->student->section ?? 'N/A',
            $incident->date ? $incident->date->format('Y-m-d') : 'N/A',
            $incident->school_year ?? 'N/A',
            $incident->complaint ?? 'N/A',
            $incident->actions_taken ?? 'N/A',
            $incident->status ?? 'N/A',
            $incident->timer_status ?? 'N/A',
            $incident->started_at ? $incident->started_at->format('Y-m-d H:i:s') : 'N/A',
            $incident->expires_at ? $incident->expires_at->format('Y-m-d H:i:s') : 'N/A',
            $incident->is_expired ? 'Yes' : 'No',
            $incident->getRemainingMinutes() ?? 'N/A',
            $incident->created_at ? $incident->created_at->format('Y-m-d H:i:s') : 'N/A',
            $incident->updated_at ? $incident->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Incidents';
    }
}
