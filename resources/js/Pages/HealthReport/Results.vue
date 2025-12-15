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
                        @click="downloadPdfFromServer"
                        class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                    />
                    <Button
                        label="Download PDF (Client)"
                        icon="pi pi-file-pdf"
                        @click="downloadPdfClient"
                        class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                    />
                    <Button
                        label="Queue Export (Large)"
                        icon="pi pi-cloud-upload"
                        @click="downloadPdfQueued"
                        class="!bg-yellow-600 !border-yellow-600 hover:!bg-yellow-700"
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
                    <div class="text-sm text-gray-600">{{ selected_students.length > 0 ? 'Pupils' : 'Grade Level' }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ section || 'All' }}</div>
                    <div class="text-sm text-gray-600">Section</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <DataTable :value="reportData" :paginator="true" :rows="25" :rowsPerPageOptions="[10,25,50,100]" class="break-words-table" stripedRows scrollHeight="600px">
                    <Column v-if="health_exam_fields.includes('nutritional_status_height')" header="Nutritional Status/Height">
                        <template #body="{ data }">
                            <span>{{ data.health_exam?.nutritional_status_height || 'N/A' }}</span>
                        </template>
                    </Column>

                    <Column v-if="health_exam_fields.includes('vision_screening')" header="Vision Screening" :style="{ width: '180px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                        <template #body="{ data }">
                            {{ data.health_exam?.vision_screening || 'N/A' }}
                        </template>
                    </Column>

                    <Column v-if="health_exam_fields.includes('auditory_screening')" header="Auditory Screening" :style="{ width: '180px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                        <template #body="{ data }">
                            {{ data.health_exam?.auditory_screening || 'N/A' }}
                        </template>
                    </Column>

                        <Column v-if="health_exam_fields.includes('skin')" header="Skin" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.skin || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('scalp')" header="Scalp" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.scalp || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('eye')" header="Eyes" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.eye || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('ear')" header="Ears" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.ear || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('nose')" header="Nose" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.nose || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('mouth')" header="Mouth" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.mouth || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('throat')" header="Throat" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.throat || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('neck')" header="Neck" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.neck || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('lungs_heart')" header="Lungs/Heart" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.lungs_heart || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('abdomen')" header="Abdomen" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.abdomen || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('deformities')" header="Deformities" :style="{ width: '150px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.deformities || 'N/A' }}
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

                        <Column v-if="health_exam_fields.includes('other_specify')" header="Other Specify" :style="{ width: '180px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.other_specify || 'N/A' }}
                            </template>
                        </Column>

                        <Column v-if="health_exam_fields.includes('remarks')" header="Remarks" :style="{ width: '180px' }" :bodyStyle="{ 'word-break': 'break-all', 'white-space': 'normal', 'overflow-wrap': 'anywhere' }">
                            <template #body="{ data }">
                                {{ data.health_exam?.remarks || 'N/A' }}
                            </template>
                        </Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { useToastStore } from '@/Stores/toastStore'
import html2pdf from 'html2pdf.js'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css'
// Import page-specific styles
import '../../../css/pages/HealthReport/Results.css'

// Toast store
const { showError, showSuccess } = useToastStore();

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
                showError('CSRF Token Error', 'Unable to get CSRF token. Please refresh the page and try again.');
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
            showError('Session Expired', 'Session expired. The system attempted to refresh your session but failed. Please refresh the page and try again.');
        } else if (error.response && error.response.status === 422) {
            showError('Validation Error', 'Please check your filters and try again.');
        } else if (error.response && error.response.data && error.response.data.message) {
            showError('Error', error.response.data.message);
        } else {
            showError('PDF Generation Failed', 'Failed to generate PDF. Please try again.');
        }
    }
};

const generateBrowserPDF = (data) => {
    try {
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

        const opt = {
            margin: 10,
            filename: `health-report-${(data.grade_level || 'selected').toString().toLowerCase().replace(/\s+/g, '-')}-${data.school_year || ''}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save();
    } catch (err) {
        console.error('PDF generation failed', err);
        showError('PDF Generation Failed', 'Failed to generate PDF.');
    }
};

const downloadPdfFromServer = async () => {
    // Get CSRF token helper
    const getFreshCSRFToken = async () => {
        try {
            const response = await window.axios.get('/');
            const parser = new DOMParser();
            const doc = parser.parseFromString(response.data, 'text/html');
            const token = doc.querySelector('meta[name="csrf-token"]')?.content;
            if (token) {
                const currentMeta = document.head.querySelector('meta[name="csrf-token"]');
                if (currentMeta) currentMeta.content = token;
                return token;
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
        }
        return null;
    };

    let csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content || '';
    if (!csrfToken) {
        csrfToken = await getFreshCSRFToken();
        if (!csrfToken) {
            showError('CSRF Token Error', 'Unable to get CSRF token. Please refresh the page and try again.');
            return;
        }
    }

    const formData = new FormData();
    formData.append('grade_level', props.grade_level.replace('Grade ', ''));
    formData.append('school_year', props.school_year);
    if (props.section) formData.append('section', props.section);
    props.fields.forEach(f => formData.append('fields[]', f));
    if (props.health_exam_fields) props.health_exam_fields.forEach(f => formData.append('health_exam_fields[]', f));
    if (props.gender_filter) formData.append('gender_filter', props.gender_filter);
    if (props.min_age) formData.append('min_age', props.min_age);
    if (props.max_age) formData.append('max_age', props.max_age);
    if (props.sort_by) formData.append('sort_by', props.sort_by);
    if (props.selected_students) props.selected_students.forEach(s => formData.append('selected_students[]', typeof s === 'object' ? s.id : s));

    try {
        let response;
        try {
            response = await window.axios.post('/health-report/export-pdf', formData, {
                responseType: 'blob',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/pdf'
                }
            });
        } catch (error) {
            if (error.response && error.response.status === 419) {
                const fresh = await getFreshCSRFToken();
                if (fresh) {
                    response = await window.axios.post('/health-report/export-pdf', formData, {
                        responseType: 'blob',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': fresh,
                            'Accept': 'application/pdf'
                        }
                    });
                } else {
                    throw new Error('Unable to refresh CSRF token');
                }
            } else if (error.response && error.response.data) {
                try {
                    const reader = new FileReader();
                    const text = await new Promise((resolve, reject) => {
                        reader.onload = () => resolve(reader.result);
                        reader.onerror = reject;
                        reader.readAsText(error.response.data);
                    });
                    const parsed = JSON.parse(text);
                    showError('Export Failed', parsed.error || parsed.message || 'Export failed');
                    return;
                } catch (parseErr) {
                    throw error;
                }
            } else {
                throw error;
            }
        }

        if (response && response.data) {
            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `health-report-${props.grade_level || 'selected'}-${new Date().toISOString().slice(0,10)}.pdf`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            showSuccess('Download Started', 'Health Report PDF is downloading.');
            return;
        }

        showSuccess('Export Started', 'Health Report PDF generation started.');
    } catch (error) {
        console.error('Export failed:', error);
        showError('Export Failed', 'Export failed. Please try again.');
    }
};

const downloadPdfClient = async () => {
    const maxClientPdf = 200;
    if (props.reportData.length > maxClientPdf) {
        showError('Too Many Records', `Client-side PDF generation is limited to ${maxClientPdf} students. Please use CSV or server batch export.`);
        return;
    }

    const printEl = document.querySelector('.print-content');
    if (!printEl) {
        showError('Print Error', 'Printable content not found.');
        return;
    }

    const clone = printEl.cloneNode(true);
    clone.style.display = 'block';
    clone.style.position = 'fixed';
    clone.style.top = '-9999px';
    document.body.appendChild(clone);

    try {
        const opt = {
            margin:       0.5,
            filename:     `health-report-${props.grade_level || 'selected'}-${new Date().toISOString().slice(0,10)}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
        };

        await html2pdf().set(opt).from(clone).save();
        showSuccess('Download Started', 'Client-side PDF generated and downloaded.');
    } catch (err) {
        console.error('Client PDF generation failed', err);
        showError('PDF Generation Failed', 'Client-side PDF generation failed. Try server PDF or CSV export.');
    } finally {
        clone.remove();
    }
}

const downloadPdfQueued = async () => {
    // helper to get fresh CSRF if needed
    const getFreshCSRFToken = async () => {
        try {
            const response = await window.axios.get('/');
            const parser = new DOMParser();
            const doc = parser.parseFromString(response.data, 'text/html');
            const token = doc.querySelector('meta[name="csrf-token"]')?.content;
            if (token) {
                const currentMeta = document.head.querySelector('meta[name="csrf-token"]');
                if (currentMeta) currentMeta.content = token;
                return token;
            }
        } catch (e) {
            console.error('Failed to refresh CSRF token', e);
        }
        return null;
    };

    let csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content || '';
    if (!csrfToken) {
        csrfToken = await getFreshCSRFToken();
        if (!csrfToken) {
            showError('CSRF Token Error', 'Unable to get CSRF token. Please refresh the page and try again.');
            return;
        }
    }

    const formData = new FormData();
    formData.append('grade_level', props.grade_level.replace('Grade ', ''));
    formData.append('school_year', props.school_year);
    if (props.section) formData.append('section', props.section);
    props.fields.forEach(f => formData.append('fields[]', f));
    if (props.health_exam_fields) props.health_exam_fields.forEach(f => formData.append('health_exam_fields[]', f));
    if (props.gender_filter) formData.append('gender_filter', props.gender_filter);
    if (props.min_age) formData.append('min_age', props.min_age);
    if (props.max_age) formData.append('max_age', props.max_age);
    if (props.sort_by) formData.append('sort_by', props.sort_by);
    if (props.selected_students) props.selected_students.forEach(s => formData.append('selected_students[]', typeof s === 'object' ? s.id : s));

    try {
        const resp = await window.axios.post('/health-report/export-pdf/queued', formData, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        if (resp.data && resp.data.id) {
            const exportId = resp.data.id;
            showSuccess('Export Queued', 'Export is queued. Waiting for completion...');

            // Poll for status
            const poll = setInterval(async () => {
                try {
                    const status = await window.axios.get(`/health-report/export-pdf/status/${exportId}`);
                    if (status.data && status.data.ready) {
                        clearInterval(poll);
                        // Download
                        const downloadResp = await window.axios.get(`/health-report/export-pdf/download/${exportId}`, { responseType: 'blob' });
                        const blob = new Blob([downloadResp.data]);
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `health-report-${exportId}.zip`;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                        showSuccess('Download Ready', 'Export ready and downloaded.');
                    } else if (status.data && status.data.failed) {
                        clearInterval(poll);
                        showError('Export Failed', status.data.message || 'Export failed during processing.');
                    }
                } catch (err) {
                    // ignore transient errors but log
                    console.error('Polling error', err);
                }
            }, 3000);

            return;
        }

        showError('Queue Failed', 'Failed to queue export');
    } catch (err) {
        console.error('Queue request failed', err);
        showError('Queue Failed', 'Failed to queue export.');
    }
}
</script>

<style scoped>
.table-container {
    overflow-x: auto;
    max-width: 100%;
}

.table-container table {
    min-width: 100%;
}

/* Force word wrapping in DataTable cells */
:deep(.break-words-table) {
    table-layout: fixed !important;
    width: 100% !important;
}

:deep(.break-words-table .p-datatable-tbody > tr > td) {
    word-wrap: break-word !important;
    word-break: break-all !important;
    white-space: normal !important;
    overflow-wrap: break-word !important;
    overflow: visible !important;
    text-overflow: clip !important;
    padding: 8px !important;
    vertical-align: top !important;
}

:deep(.break-words-table .p-datatable-thead > tr > th) {
    word-break: break-word !important;
    white-space: normal !important;
    overflow-wrap: break-word !important;
}

/* Specific styling for long text columns */
:deep(.break-words-table .p-datatable-tbody > tr > td div) {
    word-wrap: break-word !important;
    word-break: break-all !important;
    overflow-wrap: break-word !important;
    white-space: normal !important;
    max-width: 100% !important;
}

/* Ensure table layout allows wrapping */
:deep(.break-words-table .p-datatable-table) {
    table-layout: fixed !important;
}

:deep(.break-words-table .p-datatable-wrapper) {
    overflow-x: auto !important;
}

/* Remove any text truncation */
:deep(.break-words-table .p-datatable-tbody > tr > td span) {
    white-space: normal !important;
    word-break: break-all !important;
    overflow-wrap: break-word !important;
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
