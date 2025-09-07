<template>
    <Head title="Health Report Generator" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-chart-bar mr-2 text-green-600"></i>
                    Health Report Generator
                </h1>
            </div>

            <!-- Report Configuration Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Report Configuration</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Grade and Section Selection -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                            <Select 
                                v-model="selectedGrade" 
                                :options="gradeLevels" 
                                placeholder="Select a grade level"
                                class="w-full"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Section (Optional)</label>
                            <Select 
                                v-model="section" 
                                :options="sectionOptions"
                                placeholder="Select Section"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <!-- Student Filters -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender Filter</label>
                            <Select 
                                v-model="genderFilter" 
                                :options="genderOptions"
                                placeholder="Select Gender"
                                class="w-full"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Age Range</label>
                            <div class="grid grid-cols-2 gap-2">
                                <InputNumber 
                                    v-model="minAge" 
                                    placeholder="Min Age"
                                    :min="5"
                                    :max="18"
                                    class="w-full"
                                />
                                <InputNumber 
                                    v-model="maxAge" 
                                    placeholder="Max Age"
                                    :min="5"
                                    :max="18"
                                    class="w-full"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <Select 
                                v-model="sortBy" 
                                :options="sortOptions"
                                placeholder="Select Sort Order"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>

                <!-- Health Examination Fields -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Health Examination Fields</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <Button 
                            v-for="field in healthExamFields" 
                            :key="field.value"
                            :label="field.label"
                            :outlined="!selectedHealthFields.includes(field.value)"
                            :severity="selectedHealthFields.includes(field.value) ? 'success' : 'secondary'"
                            size="small"
                            @click="toggleHealthField(field.value)"
                            :class="[
                                'text-xs transition-all duration-200',
                                selectedHealthFields.includes(field.value) 
                                    ? '!bg-green-600 !border-green-600 !text-white' 
                                    : '!bg-white !border-gray-300 !text-gray-700 hover:!bg-gray-50'
                            ]"
                        />
                    </div>
                </div>

                <!-- Generate Button -->
                <div class="mt-6 flex gap-3">
                    <Button 
                        label="Generate Report" 
                        icon="pi pi-file-pdf"
                        @click="generateReport"
                        :loading="loading"
                        :disabled="!selectedGrade"
                        class="!bg-green-600 !border-green-600 hover:!bg-green-700"
                    />
                    <Button 
                        label="Preview" 
                        icon="pi pi-eye"
                        @click="previewReport"
                        :loading="loading"
                        :disabled="!selectedGrade"
                        outlined
                        severity="secondary"
                    />
                    <Button 
                        v-if="reportData.length > 0"
                        label="Print Report" 
                        icon="pi pi-print"
                        @click="printReport"
                        outlined
                        severity="info"
                    />
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Checkbox from 'primevue/checkbox';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import axios from 'axios';

const props = defineProps({
    gradeLevels: {
        type: Array,
        default: () => []
    }
});

// Form data
const selectedGrade = ref('');
const section = ref('');
const loading = ref(false);
const reportData = ref([]);

// Section options
const sectionOptions = ['A', 'B', 'C', 'D', 'E', 'F'];

// Filter options
const genderFilter = ref('All');
const genderOptions = ['All', 'Male', 'Female'];
const minAge = ref(null);
const maxAge = ref(null);

// Sort options
const sortBy = ref('Name (A-Z)');
const sortOptions = [
    'Name (A-Z)',
    'Name (Z-A)', 
    'Age (Youngest First)',
    'Age (Oldest First)'
];

// All student fields will be included by default
const selectedFields = ['name', 'lrn', 'grade_level', 'section', 'gender', 'age', 'birthdate'];

// Health examination fields
const selectedHealthFields = ref([]);
const healthExamFields = [
    { label: 'Height', value: 'height' },
    { label: 'Weight', value: 'weight' },
    { label: 'BMI', value: 'bmi' },
    { label: 'Temperature', value: 'temperature' },
    { label: 'Blood Pressure', value: 'blood_pressure' },
    { label: 'Heart Rate', value: 'heart_rate' },
    { label: 'Vision', value: 'vision' },
    { label: 'Hearing', value: 'hearing' },
    { label: 'Skin', value: 'skin' },
    { label: 'Scalp', value: 'scalp' },
    { label: 'Eyes', value: 'eyes' },
    { label: 'Ears', value: 'ears' },
    { label: 'Nose', value: 'nose' },
    { label: 'Mouth', value: 'mouth' },
    { label: 'Throat', value: 'throat' },
    { label: 'Neck', value: 'neck' },
    { label: 'Lungs', value: 'lungs' },
    { label: 'Heart', value: 'heart' },
    { label: 'Abdomen', value: 'abdomen' },
    { label: 'Deformities', value: 'deformities' }
];

// School year mapping
const getSchoolYearForGrade = (grade) => {
    const gradeToYear = {
        'Kinder 1': '2024-2025',
        'Kinder 2': '2024-2025',
        'Grade 1': '2023-2024',
        'Grade 2': '2022-2023',
        'Grade 3': '2021-2022',
        'Grade 4': '2020-2021',
        'Grade 5': '2019-2020',
        'Grade 6': '2018-2019'
    };
    return gradeToYear[grade] || '2024-2025';
};

const toggleHealthField = (fieldValue) => {
    console.log('Toggling field:', fieldValue);
    const index = selectedHealthFields.value.indexOf(fieldValue);
    if (index > -1) {
        selectedHealthFields.value.splice(index, 1);
    } else {
        selectedHealthFields.value.push(fieldValue);
    }
    console.log('Selected fields:', selectedHealthFields.value);
};

const previewReport = async () => {
    if (!selectedGrade.value) {
        alert('Please select a grade level');
        return;
    }

    loading.value = true;
    
    try {
        // Use Inertia to navigate to results page
        router.post('/api/health-report/generate', {
            grade_level: selectedGrade.value.replace('Grade ', ''),
            school_year: getSchoolYearForGrade(selectedGrade.value),
            section: section.value,
            fields: selectedFields,
            health_exam_fields: selectedHealthFields.value,
            gender_filter: genderFilter.value,
            min_age: minAge.value,
            max_age: maxAge.value,
            sort_by: sortBy.value
        });
    } catch (error) {
        console.error('Error generating report:', error);
        alert('Error generating report. Please try again.');
        loading.value = false;
    }
};

const generateReport = async () => {
    await previewReport();
    // TODO: Add PDF generation functionality
    alert('PDF generation will be implemented next');
};

const printReport = () => {
    const printWindow = window.open('', '_blank');
    const printContent = generatePrintHTML();
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
};

const generatePrintHTML = () => {
    const headerFields = selectedFields.value.map(field => {
        const fieldObj = availableFields.find(f => f.value === field);
        return fieldObj ? fieldObj.label : field;
    });
    
    const healthHeaders = [];
    if (includeHealthExam.value) healthHeaders.push('Health Exam');
    if (includeHealthTreatment.value) healthHeaders.push('Treatments');
    if (includeOralHealth.value) healthHeaders.push('Oral Health');
    if (includeIncidents.value) healthHeaders.push('Incidents');
    
    const allHeaders = [...headerFields, ...healthHeaders];
    
    let html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Health Report - ${selectedGrade.value} (${schoolYear.value})</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .header h1 { font-size: 24px; margin-bottom: 15px; }
                .report-info { display: flex; justify-content: space-around; flex-wrap: wrap; gap: 15px; }
                .report-info p { margin: 0; font-size: 14px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                th { background-color: #f5f5f5; font-weight: bold; text-align: center; }
                tbody tr:nth-child(even) { background-color: #f9f9f9; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Health Report - ${selectedGrade.value} (${schoolYear.value})</h1>
                <div class="report-info">
                    <p><strong>Grade Level:</strong> ${selectedGrade.value}</p>
                    <p><strong>School Year:</strong> ${schoolYear.value}</p>
                    <p><strong>Generated:</strong> ${new Date().toLocaleDateString()}</p>
                    <p><strong>Total Students:</strong> ${reportData.value.length}</p>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        ${allHeaders.map(header => `<th>${header}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${reportData.value.map(student => `
                        <tr>
                            ${selectedFields.value.map(field => `<td>${student[field] || 'N/A'}</td>`).join('')}
                            ${includeHealthExam.value ? `<td>${student.health_exam ? 'Yes' : 'No'}</td>` : ''}
                            ${includeHealthTreatment.value ? `<td>${student.health_treatments?.length || 0}</td>` : ''}
                            ${includeOralHealth.value ? `<td>${student.oral_treatments?.length || 0}</td>` : ''}
                            ${includeIncidents.value ? `<td>${student.incidents?.length || 0}</td>` : ''}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </body>
        </html>
    `;
    
    return html;
};

onMounted(() => {
    // Component mounted - no additional setup needed
});
</script>

<style scoped>
.p-datatable {
    font-size: 0.875rem;
}

.p-datatable .p-datatable-thead > tr > th {
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 600;
}
</style>
