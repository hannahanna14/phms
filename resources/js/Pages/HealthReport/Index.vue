<template>
    <Head title="Health Report Generator" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-6xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="pi pi-chart-bar mr-2 text-green-600"></i>
                        Health Report Generator
                    </h1>
                    <p class="text-gray-600">Generate comprehensive health reports for students</p>
                </div>
            </div>

            <!-- Draft Restored Notification -->
            <div v-if="showDraftNotification" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <i class="pi pi-info-circle text-blue-600 mr-2"></i>
                    <span class="text-blue-800 text-sm">
                        <strong>Draft restored:</strong> Your previous report settings have been recovered.
                    </span>
                    <button @click="showDraftNotification = false" class="ml-auto text-blue-600 hover:text-blue-800">
                        <i class="pi pi-times"></i>
                    </button>
                </div>
            </div>

            <!-- Student Selection Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="pi pi-users mr-2 text-blue-600"></i>
                    Student Selection
                </h2>

                <!-- Student Search Section -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="pi pi-search mr-1"></i>
                        Search Students
                    </label>
                    <InputText
                        v-model="searchQuery"
                        placeholder="Type to search students by name or LRN..."
                        class="w-full mb-3"
                        @input="onStudentSearch"
                    />

                    <!-- Search Results -->
                    <div v-if="studentOptions.length > 0 && searchQuery" class="bg-white border border-gray-200 rounded-lg max-h-60 overflow-y-auto">
                        <div
                            v-for="student in studentOptions"
                            :key="student.id"
                            @click="addStudent(student)"
                            class="p-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition-colors"
                        >
                            <div class="font-medium text-gray-900">{{ student.name }}</div>
                            <div class="text-sm text-gray-500">LRN: {{ student.lrn }} | Grade {{ student.grade_level }} - {{ student.section }}</div>
                        </div>
                    </div>

                    <small class="text-blue-600 mt-1 block">
                        <i class="pi pi-info-circle mr-1"></i>
                        Click on a student from search results to add them to your selection.
                    </small>
                </div>

                <!-- Selected Students List -->
                <div v-if="selectedStudents && selectedStudents.length > 0" class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-green-800 mb-3">
                        <i class="pi pi-list mr-2"></i>
                        Selected Students ({{ selectedStudents.length }})
                    </h3>
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        <div
                            v-for="student in selectedStudents"
                            :key="student.id"
                            class="bg-white border border-green-200 rounded-lg p-3 shadow-sm"
                        >
                            <div class="flex items-center">
                                <Checkbox
                                    v-model="student.checked"
                                    :binary="true"
                                    @change="toggleStudent(student)"
                                    class="mr-3"
                                />
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ student.name }}</h4>
                                    <p class="text-sm text-gray-600">LRN: {{ student.lrn }} | Grade {{ student.grade_level }} - {{ student.section }}</p>
                                </div>
                                <Button
                                    icon="pi pi-times"
                                    size="small"
                                    text
                                    severity="danger"
                                    @click="removeStudent(student)"
                                    class="ml-2"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <div class="text-sm text-green-700">
                            <i class="pi pi-info-circle mr-1"></i>
                            Report will be generated only for checked students.
                        </div>
                        <div class="space-x-2">
                            <Button
                                label="Select All"
                                size="small"
                                outlined
                                @click="selectAllStudents"
                                class="text-xs"
                            />
                            <Button
                                label="Clear All"
                                size="small"
                                outlined
                                severity="danger"
                                @click="clearAllStudents"
                                class="text-xs !text-red-600 !border-red-600 hover:!bg-red-50"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Configuration Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="pi pi-cog mr-2 text-gray-600"></i>
                    Report Configuration
                </h2>

                <!-- Student Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Teacher Notice -->
                    <div v-if="userRole === 'teacher'" class="md:col-span-2 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="pi pi-info-circle text-blue-600 mr-2"></i>
                                <span class="text-blue-800 font-medium">Teacher View:</span>
                                <span class="text-blue-700 ml-1">You can only generate reports for your assigned students.</span>
                            </div>
                            <Button 
                                label="Select All My Students" 
                                icon="pi pi-users"
                                size="small"
                                @click="selectAllAssignedStudents"
                                class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                            />
                        </div>
                    </div>
                    
                    <!-- Grade and Section Selection (Hidden for Teachers) -->
                    <div v-if="userRole !== 'teacher'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                            <Select
                                v-model="selectedGrade"
                                :options="gradeLevels"
                                placeholder="Select a grade level"
                                class="w-full"
                                :disabled="selectedStudents.length > 0"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Section (Optional)</label>
                            <Select
                                v-model="section"
                                :options="sectionOptions"
                                placeholder="Select Section"
                                class="w-full"
                                :disabled="selectedStudents.length > 0"
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
                                :disabled="selectedStudents.length > 0"
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
                                    :disabled="selectedStudents.length > 0"
                                />
                                <InputNumber
                                    v-model="maxAge"
                                    placeholder="Max Age"
                                    :min="5"
                                    :max="18"
                                    class="w-full"
                                    :disabled="selectedStudents.length > 0"
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
                        :disabled="isGenerateDisabled"
                        class="!bg-green-600 !border-green-600 hover:!bg-green-700"
                    />
                    <Button
                        label="Preview"
                        icon="pi pi-eye"
                        @click="previewReport"
                        :loading="loading"
                        :disabled="isGenerateDisabled"
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
                    <Button
                        v-if="showDraftNotification"
                        label="Clear Draft"
                        icon="pi pi-trash"
                        @click="clearDraft"
                        outlined
                        severity="danger"
                        size="small"
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
import { useFormPersistence } from '@/composables/useFormPersistence';

import axios from 'axios';

const props = defineProps({
    gradeLevels: {
        type: Array,
        default: () => []
    },
    userRole: {
        type: String,
        default: 'admin'
    }
});

// Format grade levels properly
const gradeLevels = computed(() => {
    const standardGrades = [
        'Kinder 1',
        'Kinder 2', 
        'Grade 1',
        'Grade 2',
        'Grade 3',
        'Grade 4',
        'Grade 5',
        'Grade 6'
    ];
    return standardGrades;
});

// Form data - consolidated for persistence
const formData = ref({
    selectedGrade: '',
    section: '',
    genderFilter: 'All',
    minAge: null,
    maxAge: null,
    sortBy: 'Name (A-Z)',
    selectedStudents: [],
    selectedHealthFields: [],
    includeHealthExam: false,
    includeHealthTreatment: false,
    includeOralHealth: false,
    includeIncidents: false
});

// Non-persistent data
const loading = ref(false);
const reportData = ref([]);
const studentOptions = ref([]);
const searchLoading = ref(false);
const searchQuery = ref('');

// Options
const sectionOptions = ['A', 'B', 'C', 'D', 'E', 'F'];
const genderOptions = ['All', 'Male', 'Female'];
const sortOptions = [
    'Name (A-Z)',
    'Name (Z-A)',
    'Age (Youngest First)',
    'Age (Oldest First)'
];

// Computed property for button disabled state
const isGenerateDisabled = computed(() => {
    if (loading.value) return true;
    
    // If students are selected, check if any are checked
    if (selectedStudents.value && selectedStudents.value.length > 0) {
        const checkedStudents = selectedStudents.value.filter(s => s && s.checked === true);
        console.log('Health Report - Selected students:', selectedStudents.value);
        console.log('Health Report - Checked students:', checkedStudents);
        console.log('Health Report - Button should be enabled:', checkedStudents.length > 0);
        return checkedStudents.length === 0;
    }
    
    // If no students selected, require grade level
    console.log('Health Report - No students selected, grade level:', selectedGrade.value);
    console.log('Health Report - Button should be enabled:', !!selectedGrade.value);
    return !selectedGrade.value;
});

// Set up form persistence
const {
    showDraftNotification,
    initializeForm,
    setupAutoSave,
    onSubmitSuccess,
    clearSavedFormData
} = useFormPersistence('health_report_form', formData.value, {
    excludeFields: [], // Save all form fields
    autoSave: true,
    showNotification: true
});

// Create reactive references for easier access
const selectedGrade = computed({
    get: () => formData.value.selectedGrade,
    set: (value) => formData.value.selectedGrade = value
});

const section = computed({
    get: () => formData.value.section,
    set: (value) => formData.value.section = value
});

const genderFilter = computed({
    get: () => formData.value.genderFilter,
    set: (value) => formData.value.genderFilter = value
});

const minAge = computed({
    get: () => formData.value.minAge,
    set: (value) => formData.value.minAge = value
});

const maxAge = computed({
    get: () => formData.value.maxAge,
    set: (value) => formData.value.maxAge = value
});

const sortBy = computed({
    get: () => formData.value.sortBy,
    set: (value) => formData.value.sortBy = value
});

const selectedStudents = computed({
    get: () => formData.value.selectedStudents,
    set: (value) => formData.value.selectedStudents = value
});

const selectedHealthFields = computed({
    get: () => formData.value.selectedHealthFields,
    set: (value) => formData.value.selectedHealthFields = value
});

const includeHealthExam = computed({
    get: () => formData.value.includeHealthExam,
    set: (value) => formData.value.includeHealthExam = value
});

const includeHealthTreatment = computed({
    get: () => formData.value.includeHealthTreatment,
    set: (value) => formData.value.includeHealthTreatment = value
});

const includeOralHealth = computed({
    get: () => formData.value.includeOralHealth,
    set: (value) => formData.value.includeOralHealth = value
});

const includeIncidents = computed({
    get: () => formData.value.includeIncidents,
    set: (value) => formData.value.includeIncidents = value
});

// All student fields will be included by default
const selectedFields = ['name', 'lrn', 'grade_level', 'section', 'gender', 'age', 'birthdate'];

// Health examination fields
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

// Student search functionality
const onStudentSearch = async () => {
    const query = searchQuery.value;

    if (!query || query.length < 1) {
        studentOptions.value = [];
        return;
    }

    searchLoading.value = true;

    try {
        const response = await axios.get('/api/students/search', {
            params: { query },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        studentOptions.value = response.data;
    } catch (error) {
        console.error('Error searching students:', error);
        studentOptions.value = [];
    } finally {
        searchLoading.value = false;
    }
};

// Add student to selection
const addStudent = (student) => {
    // Check if student is already selected
    const exists = selectedStudents.value.find(s => s.id === student.id);
    if (!exists) {
        const newStudent = {
            ...student,
            checked: true
        };
        selectedStudents.value.push(newStudent);
        console.log('Health Report - Added student:', newStudent);
        console.log('Health Report - All selected students:', selectedStudents.value);
        
        // Clear search after adding
        searchQuery.value = '';
        studentOptions.value = [];
    } else {
        console.log('Health Report - Student already exists:', student);
    }
};

// Toggle student checkbox
const toggleStudent = (student) => {
    // This is handled by v-model on the checkbox
};

// Remove student from selection
const removeStudent = (studentId) => {
    selectedStudents.value = selectedStudents.value.filter(s => s.id !== studentId);
};

// Function to select all assigned students for teachers
const selectAllAssignedStudents = async () => {
    if (props.userRole !== 'teacher') return;
    
    try {
        // Get all assigned students by searching with empty query
        const response = await axios.get('/api/students/search', {
            params: { query: '' }, // Empty query to get all assigned students
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });

        // Add all returned students to selection
        const assignedStudents = response.data.map(student => ({
            ...student,
            checked: true
        }));
        
        selectedStudents.value = assignedStudents;
        
        // Clear search
        searchQuery.value = '';
        studentOptions.value = [];
        
        console.log('Selected all assigned students:', assignedStudents.length);
    } catch (error) {
        console.error('Error loading assigned students:', error);
        alert('Failed to load assigned students.');
    }
};

// Select all students
const selectAllStudents = () => {
    selectedStudents.value.forEach(student => {
        student.checked = true;
    });
};

// Clear all student selections
const clearAllStudents = () => {
    selectedStudents.value = [];
};

const previewReport = async () => {
    // Get only checked students
    const checkedStudents = selectedStudents.value ? selectedStudents.value.filter(student => student && student.checked) : [];

    console.log('Selected students:', selectedStudents.value);
    console.log('Checked students:', checkedStudents);
    console.log('Selected grade:', selectedGrade.value);

    if (!selectedGrade.value && checkedStudents.length === 0) {
        alert('Please select a grade level or check at least one student');
        return;
    }

    loading.value = true;

    try {
        const reportData = {
            grade_level: checkedStudents.length > 0 ? 'All' : selectedGrade.value.replace('Grade ', ''),
            school_year: checkedStudents.length > 0 ? '2024-2025' : getSchoolYearForGrade(selectedGrade.value),
            section: section.value,
            fields: selectedFields,
            health_exam_fields: selectedHealthFields.value,
            gender_filter: genderFilter.value,
            min_age: minAge.value,
            max_age: maxAge.value,
            sort_by: sortBy.value,
            selected_students: checkedStudents.map(s => ({
                id: s.id,
                name: s.name,
                lrn: s.lrn,
                grade_level: s.grade_level,
                section: s.section
            }))
        };

        console.log('Sending report data:', reportData);

        // Use Inertia to navigate to results page
        await router.post('/api/health-report/generate', reportData);
    } catch (error) {
        console.error('Error generating report:', error);
        alert('Error generating report. Please try again.');
    } finally {
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

const clearDraft = () => {
    if (confirm('Are you sure you want to clear your saved draft? This action cannot be undone.')) {
        clearSavedFormData();
        // Reset form to defaults
        formData.value = {
            selectedGrade: '',
            section: '',
            genderFilter: 'All',
            minAge: null,
            maxAge: null,
            sortBy: 'Name (A-Z)',
            selectedStudents: [],
            selectedHealthFields: [],
            includeHealthExam: false,
            includeHealthTreatment: false,
            includeOralHealth: false,
            includeIncidents: false
        };
    }
};

onMounted(() => {
    // Initialize form persistence
    initializeForm();
    setupAutoSave();
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
