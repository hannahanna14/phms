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
                        label="Export PDF"
                        icon="pi pi-download"
                        @click="printReport"
                        class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                    />
                </div>
            </div>

            <!-- Report Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ reportData.length }}</div>
                    <div class="text-sm text-gray-600">Total Pupils</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-green-600">
                        {{ selected_students.length > 0 ? 'Selected' : grade_level }}
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ selected_students.length > 0 ? 'Pupils' : 'Grade Level' }}
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
                    <img :src="logoUrl" alt="School Logo" style="width: 60px; height: 60px; object-fit: contain;" />
                </div>
                    <div class="print-school-name">Naawan Central School</div>
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
                            <th v-if="health_exam_fields.includes('deformities')">Deformities</th>
                            <th v-if="health_exam_fields.includes('deworming_status')">Deworming</th>
                            <th v-if="health_exam_fields.includes('iron_supplementation')">Iron Supplementation</th>
                            <th v-if="health_exam_fields.includes('sbfp_beneficiary')">SBFP Beneficiary</th>
                            <th v-if="health_exam_fields.includes('four_ps_beneficiary')">4Ps Beneficiary</th>
                            <th v-if="health_exam_fields.includes('immunization')">Immunization</th>
                            <th v-if="health_exam_fields.includes('other_specify')">Other Specify</th>
                            <th v-if="health_exam_fields.includes('remarks')">Remarks</th>
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
                            <td v-if="health_exam_fields.includes('deformities')">{{ student.health_exam?.deformities || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('deworming_status')">{{ student.health_exam?.deworming_status || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('iron_supplementation')">{{ student.health_exam?.iron_supplementation || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('sbfp_beneficiary')">{{ student.health_exam?.sbfp_beneficiary ? 'Yes' : 'No' }}</td>
                            <td v-if="health_exam_fields.includes('four_ps_beneficiary')">{{ student.health_exam?.four_ps_beneficiary ? 'Yes' : 'No' }}</td>
                            <td v-if="health_exam_fields.includes('immunization')">{{ student.health_exam?.immunization || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('other_specify')">{{ student.health_exam?.other_specify || 'N/A' }}</td>
                            <td v-if="health_exam_fields.includes('remarks')">{{ student.health_exam?.remarks || 'N/A' }}</td>
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

                        <Column v-if="health_exam_fields.includes('lungs_heart')" header="Lungs/Heart">
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

                        <Column v-if="health_exam_fields.includes('sbfp_beneficiary')" header="SBFP Beneficiary">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.sbfp_beneficiary ? 'Yes' : 'No' }}</span>
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('four_ps_beneficiary')" header="4Ps Beneficiary">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.four_ps_beneficiary ? 'Yes' : 'No' }}</span>
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('immunization')" header="Immunization">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.immunization || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('other_specify')" header="Other Specify">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.other_specify || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('remarks')" header="Remarks">
                            <template #body="{ data }">
                                <span>{{ data.health_exam?.remarks || 'N/A' }}</span>
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
import logoUrl from '../../assets/logo.png';

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

// logoUrl is now imported directly

const printReport = async () => {
    try {
        // Function to get fresh CSRF token
        const getFreshCSRFToken = async () => {
            try {
                const response = await window.axios.get('/');
                const parser = new DOMParser();
                const doc = parser.parseFromString(response.data, 'text/html');
                const token = doc.querySelector('meta[name="csrf-token"]')?.content;
                if (token) {
                    // Update the current page's CSRF token
                    const currentMeta = document.head.querySelector('meta[name="csrf-token"]');
                    if (currentMeta) {
                        currentMeta.content = token;
                    }
                    return token;
                }
            } catch (error) {
                console.error('Failed to refresh CSRF token:', error);
            }
            return null;
        };

        // Get CSRF token from multiple sources
        let csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content || '';

        if (!csrfToken) {
            // Try to get a fresh token
            csrfToken = await getFreshCSRFToken();
            if (!csrfToken) {
                alert('Unable to get CSRF token. Please refresh the page and try again.');
                return;
            }
        }

        // Prepare form data
        const formData = new FormData();
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

        // Function to make the request with retry logic
        const makeRequest = async (token) => {
            return await window.axios.post('/health-report/export-pdf', formData, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });
        };

        // Try the request with current token
        let response;
        try {
            response = await makeRequest(csrfToken);
        } catch (error) {
            // If we get a 419 error (CSRF token mismatch), try to refresh token and retry
            if (error.response && error.response.status === 419) {
                console.log('CSRF token expired, attempting to refresh...');
                const freshToken = await getFreshCSRFToken();
                if (freshToken) {
                    console.log('Got fresh CSRF token, retrying request...');
                    response = await makeRequest(freshToken);
                } else {
                    throw new Error('Unable to refresh CSRF token');
                }
            } else {
                throw error;
            }
        }

        if (response.data.success) {
            // Use browser's print functionality to generate PDF
            generateBrowserPDF(response.data.data);
        } else {
            throw new Error('Failed to get report data');
        }

    } catch (error) {
        console.error('PDF export failed:', error);
        
        if (error.response && error.response.status === 419) {
            alert('Session expired. The system attempted to refresh your session but failed. Please refresh the page and try again.');
        } else if (error.response && error.response.status === 422) {
            alert('Validation error. Please check your filters and try again.');
        } else if (error.response && error.response.data && error.response.data.message) {
            alert('Error: ' + error.response.data.message);
        } else {
            alert('Failed to generate PDF. Please try again.');
        }
    }
};

const generateBrowserPDF = (data) => {
    // Load html2pdf.js library for better compatibility
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
    script.onload = () => {
        // Create HTML content for PDF
        const element = document.createElement('div');
        element.innerHTML = `
            <div style="font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4;">
                <div style="display: flex; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 15px;">
                    <div style="margin-right: 20px;">
                        <img src="${logoUrl}" alt="School Logo" style="width: 60px; height: 60px; object-fit: contain;">
                    </div>
                    <div>
                        <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">NAAWAN CENTRAL SCHOOL</div>
                        <div style="font-size: 14px; color: #666; margin-bottom: 10px;">Region X - Northern Mindanao</div>
                        <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">Health Report</div>
                        <div style="font-size: 12px; color: #666;">
                            Grade ${data.grade_level}${data.section ? ' - Section ' + data.section : ''}
                        </div>
                    </div>
                </div>

                <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 10px;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            ${data.fields.includes('name') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">Name</th>' : ''}
                            ${data.fields.includes('lrn') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">LRN</th>' : ''}
                            ${data.fields.includes('grade_level') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">Grade</th>' : ''}
                            ${data.fields.includes('section') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">Section</th>' : ''}
                            ${data.fields.includes('gender') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">Gender</th>' : ''}
                            ${data.fields.includes('age') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">Age</th>' : ''}
                            ${data.fields.includes('birthdate') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">Birthdate</th>' : ''}
                            ${data.health_exam_fields.map(field => `<th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">${field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${data.reportData.map(student => `
                            <tr>
                                ${data.fields.includes('name') ? `<td style="border: 1px solid #000; padding: 6px;">${student.name || 'N/A'}</td>` : ''}
                                ${data.fields.includes('lrn') ? `<td style="border: 1px solid #000; padding: 6px;">${student.lrn || 'N/A'}</td>` : ''}
                                ${data.fields.includes('grade_level') ? `<td style="border: 1px solid #000; padding: 6px;">${student.grade_level || 'N/A'}</td>` : ''}
                                ${data.fields.includes('section') ? `<td style="border: 1px solid #000; padding: 6px;">${student.section || 'N/A'}</td>` : ''}
                                ${data.fields.includes('gender') ? `<td style="border: 1px solid #000; padding: 6px;">${student.gender || 'N/A'}</td>` : ''}
                                ${data.fields.includes('age') ? `<td style="border: 1px solid #000; padding: 6px;">${student.age || 'N/A'}</td>` : ''}
                                ${data.fields.includes('birthdate') ? `<td style="border: 1px solid #000; padding: 6px;">${student.birthdate || 'N/A'}</td>` : ''}
                                ${data.health_exam_fields.map(field => `<td style="border: 1px solid #000; padding: 6px;">${student.health_exam?.[field] || 'N/A'}</td>`).join('')}
                            </tr>
                        `).join('')}
                    </tbody>
                </table>

                <div style="margin-top: 30px; text-align: right; font-size: 10px; border-top: 1px solid #ccc; padding-top: 15px;">
                    <div>Printed by: Test User</div>
                    <div>Date: ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                </div>
            </div>
        `;

        // Configure PDF options
        const opt = {
            margin: 10,
            filename: `health-report-${data.grade_level.toLowerCase().replace(' ', '-')}-${data.school_year}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        // Generate and download PDF
        html2pdf().set(opt).from(element).save();
    };
    
    script.onerror = () => {
        console.error('Failed to load html2pdf library');
        alert('Failed to load PDF library. Please try again.');
    };
    
    document.head.appendChild(script);
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
