<template>
    <Head title="Oral Health Report Results" />

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Oral Health Report Results</h1>
                        <p class="text-gray-600 mt-1">{{ grade_level }} {{ section ? '- Section ' + section : '' }}</p>
                    </div>
                    <div class="flex gap-3">
                        <Button
                            label="Back to Report"
                            icon="pi pi-arrow-left"
                            @click="goBack"
                            severity="secondary"
                        />
                        <Button
                            label="Export PDF"
                            icon="pi pi-download"
                            class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                            @click="printReport"
                        />
                    </div>
                </div>
            </div>

            <!-- Report Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ reportData.length }}</div>
                    <div class="text-sm text-gray-600">Total Students</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-green-600">{{ grade_level }}</div>
                    <div class="text-sm text-gray-600">Grade Level</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ section || 'All' }}</div>
                    <div class="text-sm text-gray-600">Section</div>
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
                        <!-- Student information columns (always shown) -->
                        <Column field="name" header="Name" sortable frozen />
                        <Column field="lrn" header="LRN" sortable />
                        <Column field="grade_level" header="Grade" sortable />
                        <Column field="section" header="Section" sortable />
                        <Column field="gender" header="Gender" sortable />
                        <Column field="age" header="Age" sortable />

                        <!-- Permanent Teeth Columns -->
                        <Column v-if="hasRangeSet('permanent_teeth_decayed') || hasSelectedStudents()" header="Perm. Decayed">
                            <template #body="{ data }">
                                <span>{{ data.permanent_teeth_decayed || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('permanent_teeth_filled') || hasSelectedStudents()" header="Perm. Filled">
                            <template #body="{ data }">
                                <span>{{ data.permanent_teeth_filled || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('permanent_for_extraction') || hasSelectedStudents()" header="Perm. For Extraction">
                            <template #body="{ data }">
                                <span>{{ data.permanent_for_extraction || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('permanent_for_filling') || hasSelectedStudents()" header="Perm. For Filling">
                            <template #body="{ data }">
                                <span>{{ data.permanent_for_filling || 'N/A' }}</span>
                            </template>
                        </Column>

                        <!-- Temporary Teeth Columns -->
                        <Column v-if="hasRangeSet('temporary_teeth_decayed') || hasSelectedStudents()" header="Temp. Decayed">
                            <template #body="{ data }">
                                <span>{{ data.temporary_teeth_decayed || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('temporary_teeth_filled') || hasSelectedStudents()" header="Temp. Filled">
                            <template #body="{ data }">
                                <span>{{ data.temporary_teeth_filled || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('temporary_for_extraction') || hasSelectedStudents()" header="Temp. For Extraction">
                            <template #body="{ data }">
                                <span>{{ data.temporary_for_extraction || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('temporary_for_filling') || hasSelectedStudents()" header="Temp. For Filling">
                            <template #body="{ data }">
                                <span>{{ data.temporary_for_filling || 'N/A' }}</span>
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
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const props = defineProps({
    reportData: Array,
    grade_level: String,
    section: String,
    fields: Array,
    oral_exam_fields: Array,
    gender_filter: String,
    min_age: Number,
    max_age: Number,
    sort_by: String,
    minValues: Object,
    maxValues: Object,
    selected_students: Array
});

// Check if a range was set for a field (not [0,0] or null)
const hasRangeSet = (fieldKey) => {
    if (!props.minValues && !props.maxValues) return false;
    
    const minVal = props.minValues?.[fieldKey];
    const maxVal = props.maxValues?.[fieldKey];
    
    // Show column if either min or max is set and not zero
    return (minVal !== null && minVal !== undefined && minVal > 0) || 
           (maxVal !== null && maxVal !== undefined && maxVal > 0);
};

// Check if specific students were selected
const hasSelectedStudents = () => {
    return props.selected_students && Array.isArray(props.selected_students) && props.selected_students.length > 0;
};

const goBack = () => {
    router.visit('/oral-health-report');
};

const printReport = async () => {
    try {
        // Get CSRF token from multiple sources
        let csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content || 
                       document.querySelector('meta[name="csrf-token"]')?.content ||
                       window.Laravel?.csrfToken || '';

        // Try to get from Inertia page props as fallback
        if (!csrfToken && window.page?.props?.csrf_token) {
            csrfToken = window.page.props.csrf_token;
        }

        if (!csrfToken) {
            alert('CSRF token not found. Please refresh the page and try again.');
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('grade_level', props.grade_level ? props.grade_level.replace('Grade ', '') : '');

        if (props.section) {
            formData.append('section', props.section);
        }

        // Add fields array
        props.fields.forEach(field => {
            formData.append('fields[]', field);
        });

        // Add oral exam fields array
        if (props.oral_exam_fields && props.oral_exam_fields.length > 0) {
            props.oral_exam_fields.forEach(field => {
                formData.append('oral_exam_fields[]', field);
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

        // Add min/max values if any
        if (props.minValues) {
            Object.keys(props.minValues).forEach(key => {
                formData.append(`minValues[${key}]`, props.minValues[key]);
            });
        }
        if (props.maxValues) {
            Object.keys(props.maxValues).forEach(key => {
                formData.append(`maxValues[${key}]`, props.maxValues[key]);
            });
        }

        // Add selected students if any
        if (props.selected_students && props.selected_students.length > 0) {
            props.selected_students.forEach(student => {
                const studentId = typeof student === 'object' ? student.id : student;
                formData.append('selected_students[]', studentId);
            });
        }

        // Set CSRF token in axios defaults
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        
        // Get data from server for browser-based PDF generation
        const response = await window.axios.post('/oral-health-report/export-pdf', formData, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.success) {
            // Use browser's print functionality to generate PDF
            generateBrowserPDF(response.data.data);
        } else {
            throw new Error('Failed to get report data');
        }

    } catch (error) {
        console.error('PDF export failed:', error);
        
        if (error.response && error.response.status === 419) {
            alert('Session expired. Please refresh the page and try again.');
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
            <div style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; width: 100%;">
                <div style="display: flex; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 15px;">
                    <div style="margin-right: 20px;">
                        <img src="/images/logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;" onerror="this.style.display='none';">
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">NAAWAN CENTRAL SCHOOL</div>
                        <div style="font-size: 16px; color: #666; margin-bottom: 10px;">Region X - Northern Mindanao</div>
                        <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">Oral Health Report</div>
                        <div style="font-size: 14px; color: #666;">
                            ${data.grade_level ? 'Grade ' + data.grade_level : 'Selected Students'}${data.section ? ' - Section ' + data.section : ''}
                        </div>
                    </div>
                </div>

                <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px;">
                    <thead>
                        <tr style="background-color: #f3f4f6;">
                            ${data.fields.includes('name') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-size: 11px;">Name</th>' : ''}
                            ${data.fields.includes('lrn') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-size: 11px;">LRN</th>' : ''}
                            ${data.fields.includes('grade_level') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-size: 11px;">Grade</th>' : ''}
                            ${data.fields.includes('section') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-size: 11px;">Section</th>' : ''}
                            ${data.fields.includes('gender') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-size: 11px;">Gender</th>' : ''}
                            ${data.fields.includes('age') ? '<th style="border: 1px solid #000; padding: 8px; text-align: center; font-size: 11px;">Age</th>' : ''}
                            ${data.oral_exam_fields.map(field => 
                                `<th style="border: 1px solid #000; padding: 8px; text-align: center; font-size: 11px;">${field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()).replace('Permanent', 'Perm.').replace('Temporary', 'Temp.')}</th>`
                            ).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${data.reportData && data.reportData.length > 0 ? data.reportData.map(student => `
                            <tr>
                                ${data.fields.includes('name') ? `<td style="border: 1px solid #000; padding: 6px; font-size: 10px;">${student.name || 'N/A'}</td>` : ''}
                                ${data.fields.includes('lrn') ? `<td style="border: 1px solid #000; padding: 6px; font-size: 10px;">${student.lrn || 'N/A'}</td>` : ''}
                                ${data.fields.includes('grade_level') ? `<td style="border: 1px solid #000; padding: 6px; font-size: 10px;">${student.grade_level || 'N/A'}</td>` : ''}
                                ${data.fields.includes('section') ? `<td style="border: 1px solid #000; padding: 6px; font-size: 10px;">${student.section || 'N/A'}</td>` : ''}
                                ${data.fields.includes('gender') ? `<td style="border: 1px solid #000; padding: 6px; font-size: 10px;">${student.gender || 'N/A'}</td>` : ''}
                                ${data.fields.includes('age') ? `<td style="border: 1px solid #000; padding: 6px; font-size: 10px;">${student.age || 'N/A'}</td>` : ''}
                                ${data.oral_exam_fields.map(field => 
                                    `<td style="border: 1px solid #000; padding: 6px; font-size: 10px; text-align: center;">${student[field] !== undefined && student[field] !== null ? student[field] : 'N/A'}</td>`
                                ).join('')}
                            </tr>
                        `).join('') : '<tr><td colspan="100%" style="text-align: center; padding: 20px; font-size: 12px;">No data available</td></tr>'}
                    </tbody>
                </table>

                <div style="margin-top: 30px; font-size: 10px; color: #666;">
                    <div>Printed by: ${data.user_name || 'System'}</div>
                    <div>Date: ${new Date().toLocaleDateString()}</div>
                </div>
            </div>
        `;

        // Add debug logging
        console.log('PDF Data:', data);
        console.log('Report Data Length:', data.reportData ? data.reportData.length : 0);
        console.log('Oral Exam Fields:', data.oral_exam_fields);
        
        // Configure PDF options
        const options = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: `oral-health-report-grade-${data.grade_level || 'selected'}${data.section ? '-section-' + data.section : ''}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 1,
                useCORS: true,
                letterRendering: true,
                allowTaint: false,
                scrollX: 0,
                scrollY: 0
            },
            jsPDF: { 
                unit: 'in', 
                format: 'a4', 
                orientation: 'landscape',
                compress: true
            }
        };

        // Generate and download PDF
        window.html2pdf().set(options).from(element).save();
    };
    
    document.head.appendChild(script);
};
</script>
