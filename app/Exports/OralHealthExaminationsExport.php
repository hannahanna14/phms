<?php

namespace App\Exports;

use App\Models\OralHealthExamination;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OralHealthExaminationsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
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
        $query = OralHealthExamination::with('student')
            ->orderBy('examination_date', 'desc');

        // Apply filters
        if (!empty($this->filters['date_from'])) {
            $query->where('examination_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('examination_date', '<=', $this->filters['date_to']);
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
        $headings = [];

        // Personal information headers (if enabled)
        if ($this->filters['include_personal_info'] ?? true) {
            $headings = array_merge($headings, [
                'Student Name',
                'LRN',
                'Grade Level',
                'Section',
                'Gender',
                'Date of Birth',
            ]);
        }

        // Oral health examination headers
        $headings = array_merge($headings, [
            'Examination Date',
            'School Year',
            // Permanent Teeth
            'Permanent - Index DFT',
            'Permanent - Teeth Decayed',
            'Permanent - Teeth Filled',
            'Permanent - Total DFT',
            'Permanent - For Extraction',
            'Permanent - For Filling',
            // Temporary Teeth
            'Temporary - Index DFT',
            'Temporary - Teeth Decayed',
            'Temporary - Teeth Filled',
            'Temporary - Total DFT',
            'Temporary - For Extraction',
            'Temporary - For Filling',
            // Tooth Symbols and Conditions
            'Tooth Symbols',
            'Oral Health Conditions',
            'Remarks',
            'Created At',
            'Updated At'
        ]);

        return $headings;
    }

    /**
     * @param mixed $examination
     * @return array
     */
    public function map($examination): array
    {
        $data = [];

        // Personal information (if enabled)
        if ($this->filters['include_personal_info'] ?? true) {
            $data = array_merge($data, [
                $examination->student->full_name ?? 'N/A',
                $examination->student->lrn ?? 'N/A',
                $examination->student->grade_level ?? 'N/A',
                $examination->student->section ?? 'N/A',
                $examination->student->gender ?? 'N/A',
                $examination->student->date_of_birth ? $examination->student->date_of_birth->format('Y-m-d') : 'N/A',
            ]);
        }

        // Format tooth symbols for export
        $toothSymbols = 'N/A';
        if ($examination->tooth_symbols && is_array($examination->tooth_symbols)) {
            $symbols = [];
            foreach ($examination->tooth_symbols as $tooth => $conditions) {
                if (is_array($conditions)) {
                    $symbols[] = $tooth . ': ' . implode(', ', $conditions);
                } else {
                    $symbols[] = $tooth . ': ' . $conditions;
                }
            }
            $toothSymbols = implode(' | ', $symbols);
        }

        // Format conditions for export
        $conditions = 'N/A';
        if ($examination->conditions && is_array($examination->conditions)) {
            $conditionList = [];
            foreach ($examination->conditions as $condition => $date) {
                $conditionList[] = $condition . ' (' . $date . ')';
            }
            $conditions = implode(' | ', $conditionList);
        }

        // Oral health examination data
        $data = array_merge($data, [
            $examination->examination_date ? $examination->examination_date->format('Y-m-d') : 'N/A',
            $examination->school_year ?? 'N/A',
            // Permanent Teeth
            $examination->index_dft ?? 'N/A',
            $examination->teeth_decayed ?? 'N/A',
            $examination->teeth_filled ?? 'N/A',
            $examination->total_dft ?? 'N/A',
            $examination->for_extraction ?? 'N/A',
            $examination->for_filling ?? 'N/A',
            // Temporary Teeth
            $examination->temporary_index_dft ?? 'N/A',
            $examination->temporary_teeth_decayed ?? 'N/A',
            $examination->temporary_teeth_filled ?? 'N/A',
            $examination->temporary_total_dft ?? 'N/A',
            $examination->temporary_for_extraction ?? 'N/A',
            $examination->temporary_for_filling ?? 'N/A',
            // Tooth Symbols and Conditions
            $toothSymbols,
            $conditions,
            $examination->remarks ?? 'N/A',
            $examination->created_at ? $examination->created_at->format('Y-m-d H:i:s') : 'N/A',
            $examination->updated_at ? $examination->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ]);

        return $data;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Oral Health Examinations';
    }
}
