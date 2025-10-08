<?php

namespace App\Exports;

use App\Models\HealthExamination;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HealthExaminationsExport implements FromCollection, WithHeadings
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
        $query = HealthExamination::with('student')
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

        $examinations = $query->get();
        
        // Transform data for export
        return $examinations->map(function($examination) {
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

            // Health examination data
            $data = array_merge($data, [
                $examination->examination_date ? $examination->examination_date->format('Y-m-d') : 'N/A',
                $examination->school_year ?? 'N/A',
                $examination->age ?? 'N/A',
                $examination->weight ?? 'N/A',
                $examination->height ?? 'N/A',
                $examination->bmi ?? 'N/A',
                $examination->nutritional_status_bmi ?? 'N/A',
                $examination->nutritional_status_height ?? 'N/A',
                $examination->temperature ?? 'N/A',
                $examination->blood_pressure ?? 'N/A',
                $examination->heart_rate ?? 'N/A',
                $examination->pulse_rate ?? 'N/A',
                $examination->respiratory_rate ?? 'N/A',
                $examination->vision_screening ?? 'N/A',
                $examination->auditory_screening ?? 'N/A',
                $examination->skin ?? 'N/A',
                $examination->scalp ?? 'N/A',
                $examination->eyes ?? 'N/A',
                $examination->ears ?? 'N/A',
                $examination->nose ?? 'N/A',
                $examination->mouth ?? 'N/A',
                $examination->neck ?? 'N/A',
                $examination->throat ?? 'N/A',
                $examination->chest ?? 'N/A',
                $examination->back ?? 'N/A',
                $examination->heart ?? 'N/A',
                $examination->abdomen ?? 'N/A',
                $examination->deformities ?? 'N/A',
                $examination->iron_supplementation ?? 'N/A',
                $examination->deworming ?? 'N/A',
                $examination->immunization ?? 'N/A',
                $examination->sbfp_beneficiary ? 'Yes' : 'No',
                $examination->four_ps_beneficiary ? 'Yes' : 'No',
                $examination->menarche ?? 'N/A',
                $examination->remarks ?? 'N/A',
                $examination->created_at ? $examination->created_at->format('Y-m-d H:i:s') : 'N/A',
                $examination->updated_at ? $examination->updated_at->format('Y-m-d H:i:s') : 'N/A',
            ]);

            return $data;
        });
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

        // Health examination headers
        $headings = array_merge($headings, [
            'Examination Date',
            'School Year',
            'Age',
            'Weight (kg)',
            'Height (cm)',
            'BMI',
            'Nutritional Status (BMI)',
            'Nutritional Status (Height)',
            'Temperature (°C)',
            'Blood Pressure',
            'Heart Rate',
            'Pulse Rate',
            'Respiratory Rate',
            'Vision Screening',
            'Auditory Screening',
            'Skin',
            'Scalp',
            'Eyes',
            'Ears',
            'Nose',
            'Mouth',
            'Neck',
            'Throat',
            'Chest',
            'Back',
            'Heart',
            'Abdomen',
            'Deformities',
            'Iron Supplementation',
            'Deworming',
            'Immunization',
            'SBFP Beneficiary',
            '4Ps Beneficiary',
            'Menarche',
            'Remarks',
            'Created At',
            'Updated At'
        ]);

        return $headings;
    }

}
