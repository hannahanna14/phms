<template>
    <Head title="Health Report Results" />
    
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Back Button -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Health Report Results</h1>
                    <p class="text-gray-600">
                        {{ reportData.length }} students found 
                        <span v-if="selected_students.length > 0">
                            ({{ selected_students.length }} selected students)
                        </span>
                        <span v-else>
                            for {{ grade_level }} ({{ school_year }})
                        </span>
                    </p>
                </div>
                <div class="flex gap-3">
                    <Button 
                        label="Back to Report" 
                        icon="pi pi-arrow-left"
                        @click="goBack"
                        outlined
                        severity="secondary"
                    />
                    <Button 
                        label="Print Report" 
                        icon="pi pi-print"
                        @click="printReport"
                        class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                    />
                </div>
            </div>

            <!-- Report Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ reportData.length }}</div>
                    <div class="text-sm text-gray-600">Total Students</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-green-600">
                        {{ selected_students.length > 0 ? 'Selected' : grade_level }}
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ selected_students.length > 0 ? 'Students' : 'Grade Level' }}
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-purple-600">
                        {{ selected_students.length > 0 ? 'Mixed' : (section || 'All') }}
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ selected_students.length > 0 ? 'Grades' : 'Section' }}
                    </div>
                </div>
            </div>

            <!-- Print Content (Hidden on screen, visible only when printing) -->
            <div class="print-content" style="display: none;">
                <!-- Print Header -->
                <div class="print-header">
                    <div class="print-logo">
                        <div style="width: 60px; height: 60px; border: 2px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">LOGO</div>
                    </div>
                    <div class="print-school-name">MEDPORT ELEMENTARY SCHOOL</div>
                    <div class="print-region">Region X - Northern Mindanao</div>
                    <div class="print-title">Health Report - {{ grade_level }} {{ section ? '(Section ' + section + ')' : '' }}</div>
                </div>

                <!-- Print Table -->
                <table class="print-table">
                    <thead>
                        <tr>
                            <th v-if="fields.includes('name')">Name</th>
                            <th v-if="fields.includes('lrn')">LRN</th>
                            <th v-if="fields.includes('grade_level')">Grade</th>
                            <th v-if="fields.includes('section')">Section</th>
                            <th v-if="fields.includes('gender')">Gender</th>
                            <th v-if="fields.includes('age')">Age</th>
                            <th v-if="fields.includes('birthdate')">Birthdate</th>
                            <th v-if="health_exam_fields.includes('height')">Height</th>
                            <th v-if="health_exam_fields.includes('weight')">Weight</th>
                            <th v-if="health_exam_fields.includes('nutritional_status_bmi')">Nutritional Status/BMI</th>
                            <th v-if="health_exam_fields.includes('nutritional_status_height')">Nutritional Status/Height</th>
                            <th v-if="health_exam_fields.includes('temperature')">Temperature</th>
                            <th v-if="health_exam_fields.includes('heart_rate')">Heart Rate</th>
                            <th v-if="health_exam_fields.includes('vision_screening')">Vision</th>
                            <th v-if="health_exam_fields.includes('auditory_screening')">Hearing</th>
                            <th v-if="health_exam_fields.includes('skin')">Skin</th>
                            <th v-if="health_exam_fields.includes('scalp')">Scalp</th>
                            <th v-if="health_exam_fields.includes('eye')">Eyes</th>
                            <th v-if="health_exam_fields.includes('ear')">Ears</th>
                            <th v-if="health_exam_fields.includes('nose')">Nose</th>
                            <th v-if="health_exam_fields.includes('mouth')">Mouth</th>
                            <th v-if="health_exam_fields.includes('throat')">Throat</th>
                            <th v-if="health_exam_fields.includes('neck')">Neck</th>
                            <th v-if="health_exam_fields.includes('lungs_heart')">Lungs/Heart</th>
                            <th v-if="health_exam_fields.includes('abdomen')">Abdomen</th>
                            <th v-if="health_exam_fields.includes('deworming_status')">Deworming</th>
                            <th v-if="health_exam_fields.includes('iron_supplementation')">Iron Supplementation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="student in reportData" :key="student.id">
                            <td v-if="fields.includes('name')">{{ student.full_name }}</td>
                            <td v-if="fields.includes('lrn')">{{ student.lrn }}</td>
                            <td v-if="fields.includes('grade_level')">{{ student.grade_level }}</td>
                            <td v-if="fields.includes('section')">{{ student.section }}</td>
                            <td v-if="fields.includes('gender')">{{ student.sex }}</td>
                            <td v-if="fields.includes('age')">{{ student.age }}</td>
                            <td v-if="fields.includes('birthdate')">{{ student.birthdate }}</td>
                            <td v-if="health_exam_fields.includes('height')">{{ student.health_exam?.height || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('weight')">{{ student.health_exam?.weight || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('nutritional_status_bmi')">{{ student.health_exam?.nutritional_status_bmi || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('nutritional_status_height')">{{ student.health_exam?.nutritional_status_height || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('temperature')">{{ student.health_exam?.temperature || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('heart_rate')">{{ student.health_exam?.heart_rate || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('vision_screening')">{{ student.health_exam?.vision_screening || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('auditory_screening')">{{ student.health_exam?.auditory_screening || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('skin')">{{ student.health_exam?.skin || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('scalp')">{{ student.health_exam?.scalp || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('eye')">{{ student.health_exam?.eye || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('ear')">{{ student.health_exam?.ear || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('nose')">{{ student.health_exam?.nose || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('mouth')">{{ student.health_exam?.mouth || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('throat')">{{ student.health_exam?.throat || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('neck')">{{ student.health_exam?.neck || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('lungs_heart')">{{ student.health_exam?.lungs_heart || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('abdomen')">{{ student.health_exam?.abdomen || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('deworming_status')">{{ student.health_exam?.deworming_status || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('iron_supplementation')">{{ student.health_exam?.iron_supplementation || 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Print Footer -->
                <div class="print-footer">
                    <div>Printed by: Test User</div>
                    <div>Date: {{ new Date().toLocaleDateString() }}</div>
                </div>
            </div>

            <!-- Report Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <DataTable 
                    :value="reportData" 
                    :paginator="true" 
                    :rows="25"
                    :rowsPerPageOptions="[10, 25, 50, 100]"
                    stripedRows
                    :scrollable="true"
                    scrollHeight="600px"
                >
                        <!-- Dynamic columns based on selected fields -->
                        <Column v-if="fields.includes('name')" field="name" header="Name" sortable frozen />
                        <Column v-if="fields.includes('lrn')" field="lrn" header="LRN" sortable />
                        <Column v-if="fields.includes('grade_level')" field="grade_level" header="Grade" sortable />
                        <Column v-if="fields.includes('section')" field="section" header="Section" sortable />
                        <Column v-if="fields.includes('gender')" field="gender" header="Gender" sortable />
                        <Column v-if="fields.includes('age')" field="age" header="Age" sortable />
                        <Column v-if="fields.includes('birthdate')" field="birthdate" header="Birthdate" sortable />
                        
                        <!-- Health examination field columns -->
                        <Column v-if="health_exam_fields.includes('height')" header="Height">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.height || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('weight')" header="Weight">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.weight || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('temperature')" header="Temperature">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.temperature || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('heart_rate')" header="Heart Rate">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.heart_rate || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('nutritional_status_bmi')" header="Nutritional Status/BMI">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.nutritional_status_bmi || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('nutritional_status_height')" header="Nutritional Status/Height">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.nutritional_status_height || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('vision_screening')" header="Vision Screening">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.vision_screening || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('auditory_screening')" header="Auditory Screening">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.auditory_screening || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('skin')" header="Skin">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.skin || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('scalp')" header="Scalp">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.scalp || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('eye')" header="Eyes">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.eye || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('ear')" header="Ears">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.ear || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('nose')" header="Nose">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.nose || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('mouth')" header="Mouth">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.mouth || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('throat')" header="Throat">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.throat || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('neck')" header="Neck">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.neck || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('lungs')" header="Lungs/Heart">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.lungs_heart || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('heart')" header="Lungs/Heart">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.lungs_heart || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('abdomen')" header="Abdomen">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.abdomen || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('deformities')" header="Deformities">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.deformities || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('deworming_status')" header="Deworming Status">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.deworming_status || 'N/A' }}</span>
                            </template>
                        </Column>
                        
                        <Column v-if="health_exam_fields.includes('iron_supplementation')" header="Iron Supplementation">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.iron_supplementation || 'N/A' }}</span>
                            </template>
                        </Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';

const props = defineProps({
    reportData: {
        type: Array,
        default: () => []
    },
    grade_level: {
        type: String,
        required: true
    },
    school_year: {
        type: String,
        required: true
    },
    section: {
        type: String,
        default: null
    },
    fields: {
        type: Array,
        default: () => []
    },
    health_exam_fields: {
        type: Array,
        default: () => []
    },
    gender_filter: {
        type: String,
        default: null
    },
    min_age: {
        type: Number,
        default: null
    },
    max_age: {
        type: Number,
        default: null
    },
    sort_by: {
        type: String,
        default: null
    },
    selected_students: {
        type: Array,
        default: () => []
    }
});


const goBack = () => {
    router.visit('/health-report');
};

const printReport = async () => {
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            alert('CSRF token not found. Please refresh the page and try again.');
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('grade_level', props.grade_level.replace('Grade ', ''));
        formData.append('school_year', props.school_year);
        
        if (props.section) {
            formData.append('section', props.section);
        }
        
        // Add fields array
        props.fields.forEach(field => {
            formData.append('fields[]', field);
        });
        
        // Add health exam fields array
        if (props.health_exam_fields && props.health_exam_fields.length > 0) {
            props.health_exam_fields.forEach(field => {
                formData.append('health_exam_fields[]', field);
            });
        }
        
        // Add optional filters
        if (props.gender_filter) {
            formData.append('gender_filter', props.gender_filter);
        }
        if (props.min_age) {
            formData.append('min_age', props.min_age);
        }
        if (props.max_age) {
            formData.append('max_age', props.max_age);
        }
        if (props.sort_by) {
            formData.append('sort_by', props.sort_by);
        }
        
        // Add selected students if any
        if (props.selected_students && props.selected_students.length > 0) {
            props.selected_students.forEach(student => {
                // Handle both student objects and plain IDs
                const studentId = typeof student === 'object' ? student.id : student;
                formData.append('selected_students[]', studentId);
            });
        }

        // Make the request
        const response = await fetch('/health-report/export-pdf', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Get the blob and create download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        
        // Generate filename
        const gradeLevel = props.grade_level.replace('Grade ', '').toLowerCase().replace(' ', '-');
        const filename = `health-report-${gradeLevel}-${props.school_year}.pdf`;
        link.download = filename;
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        
    } catch (error) {
        console.error('PDF export failed:', error);
        alert('Failed to generate PDF. Please try again.');
    }
};
</script>

<style scoped>
.table-container {
    overflow-x: auto;
    max-width: 100%;
}

.table-container table {
    min-width: 100%;
}

/* Print Styles */
@media print {
    /* Hide all website elements */
    body * {
        visibility: hidden !important;
    }
    
    /* Hide specific website components */
    .sidebar, .navigation, .nav, .header, .menu, .p-paginator, .no-print {
        display: none !important;
        visibility: hidden !important;
    }
    
    /* Show only print content */
    .print-content, .print-content * {
        visibility: visible !important;
        display: block !important;
    }
    
    .print-content {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: white !important;
        padding: 20px !important;
        font-family: Arial, sans-serif !important;
        z-index: 9999 !important;
    }
    
    /* Set landscape orientation and margins */
    @page {
        size: A4 landscape;
        margin: 0.5in;
    }
    
    /* Hide navigation and buttons */
    .no-print {
        display: none !important;
    }
    
    /* Print header */
    .print-header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #000;
        padding-bottom: 20px;
    }
    
    .print-logo {
        width: 60px;
        height: 60px;
        margin: 0 auto 10px;
    }
    
    .print-school-name {
        font-size: 24px;
        font-weight: bold;
        margin: 10px 0 5px;
        color: #000;
    }
    
    .print-region {
        font-size: 16px;
        color: #666;
        margin-bottom: 15px;
    }
    
    .print-title {
        font-size: 20px;
        font-weight: bold;
        margin: 15px 0;
        color: #000;
    }
    
    /* Print table */
    .print-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 10px;
        table-layout: auto;
        display: table;
    }
    
    .print-table thead {
        display: table-header-group;
    }
    
    .print-table tbody {
        display: table-row-group;
    }
    
    .print-table tr {
        display: table-row;
        page-break-inside: avoid;
    }
    
    .print-table th,
    .print-table td {
        display: table-cell;
        border: 1px solid #000;
        padding: 3px 2px;
        text-align: center;
        word-wrap: break-word;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .print-table th {
        background-color: #f0f0f0;
        font-weight: bold;
        font-size: 9px;
    }
    
    .print-table td {
        font-size: 8px;
    }
    
    /* Print footer */
    .print-footer {
        margin-top: 30px;
        text-align: right;
        font-size: 12px;
        border-top: 1px solid #ccc;
        padding-top: 15px;
    }
    
    /* Ensure page breaks */
    .print-content {
        page-break-inside: avoid;
    }
    
    .p-datatable {
        font-size: 10px;
    }
    
    .p-paginator {
        display: none !important;
    }
}
</style>
