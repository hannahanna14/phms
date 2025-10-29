<template>
    <Head title="| Health Report Generator" />
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
                
                <!-- Step-by-step Guide -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                    <h3 class="font-semibold text-blue-900 mb-2 flex items-center">
                        <i class="pi pi-info-circle mr-2"></i>
                        How to Generate a Report
                    </h3>
                    <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
                        <li><strong>Step 1:</strong> Search and select specific pupils OR choose a grade level filter</li>
                        <li><strong>Step 2:</strong> Select which health fields you want to include in the report</li>
                        <li><strong>Step 3:</strong> Click "Generate Report" to create your PDF</li>
                    </ol>
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

            <!-- STEP 1: Student Selection Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">1</span>
                        Choose Pupils
                    </h2>
                    <span class="text-sm text-gray-500">Option A or Option B</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Option A: Search Specific Students -->
                    <div class="border-2 rounded-lg p-4" :class="selectedStudents.length > 0 ? 'border-blue-400 bg-blue-50' : 'border-gray-200 bg-white'">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="pi pi-search mr-2 text-blue-600"></i>
                            Option A: Search Specific Pupils
                        </h3>
                        
                        <InputText
                            v-model="searchQuery"
                            placeholder="Type name or LRN to search..."
                            class="w-full mb-2"
                            @input="onStudentSearch"
                        />

                        <!-- Search Results -->
                        <div v-if="studentOptions.length > 0 && searchQuery" class="bg-white border border-gray-200 rounded-lg max-h-48 overflow-y-auto mb-2">
                            <div
                                v-for="student in studentOptions"
                                :key="student.id"
                                @click="addStudent(student)"
                                class="p-2 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition-colors"
                            >
                                <div class="font-medium text-sm text-gray-900">{{ student.name }}</div>
                                <div class="text-xs text-gray-500">LRN: {{ student.lrn }} | Grade {{ student.grade_level }} - {{ student.section }}</div>
                            </div>
                        </div>

                        <!-- Selected Students -->
                        <div v-if="selectedStudents && selectedStudents.length > 0" class="mt-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-green-700">
                                    <i class="pi pi-check-circle mr-1"></i>
                                    {{ selectedStudents.length }} pupils selected
                                </span>
                                <Button
                                    label="Clear"
                                    size="small"
                                    text
                                    severity="danger"
                                    @click="clearAllStudents"
                                    class="text-xs"
                                />
                            </div>
                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                <div
                                    v-for="student in selectedStudents"
                                    :key="student.id"
                                    class="bg-white border border-green-200 rounded p-2 flex items-center text-sm"
                                >
                                    <Checkbox
                                        v-model="student.checked"
                                        :binary="true"
                                        class="mr-2"
                                    />
                                    <span class="flex-1 text-xs">{{ student.name }}</span>
                                    <Button
                                        icon="pi pi-times"
                                        size="small"
                                        text
                                        severity="danger"
                                        @click="removeStudent(student)"
                                    />
                                </div>
                            </div>
                        </div>
                        
                        <small class="text-blue-600 mt-2 block text-xs">
                            <i class="pi pi-info-circle mr-1"></i>
                            Search and click to add pupils individually
                        </small>
                    </div>

                    <!-- Option B: Filter by Grade/Section -->
                    <div class="border-2 border-gray-200 rounded-lg p-4" :class="selectedGrade && selectedGrade !== 'All' ? 'bg-gray-50' : 'bg-white'">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="pi pi-filter mr-2 text-gray-600"></i>
                            Option B: Filter by Grade/Section
                        </h3>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                                <Select
                                    v-model="selectedGrade"
                                    :options="gradeLevels"
                                    placeholder="Select grade level"
                                    class="w-full"
                                    :disabled="selectedStudents.length > 0"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Section (Optional)</label>
                                <Select
                                    v-model="section"
                                    :options="sectionOptions"
                                    placeholder="All sections"
                                    class="w-full"
                                    :disabled="selectedStudents.length > 0"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender (Optional)</label>
                                <Select
                                    v-model="genderFilter"
                                    :options="genderOptions"
                                    placeholder="All genders"
                                    class="w-full"
                                    :disabled="selectedStudents.length > 0"
                                />
                            </div>
                        </div>
                        
                        <small class="text-gray-600 mt-2 block text-xs">
                            <i class="pi pi-info-circle mr-1"></i>
                            Report will include all pupils matching these filters
                        </small>
                    </div>
                </div>

                <!-- Sort By (applies to both options) -->
                <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-sort-alt text-gray-600"></i>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Report By</label>
                            <Select
                                v-model="sortBy"
                                :options="sortOptions"
                                placeholder="Name (A-Z)"
                                class="w-full"
                            />
                        </div>
                        <small class="text-xs text-gray-600 self-end mb-1">
                            Applies to selected or filtered pupils
                        </small>
                    </div>
                </div>

                <!-- Teacher Quick Select -->
                <div v-if="userRole === 'teacher'" class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-purple-800">
                            <i class="pi pi-users mr-1"></i>
                            <strong>Teacher:</strong> Generate report for all your assigned pupils
                        </span>
                        <Button 
                            label="Select All My Pupils" 
                            icon="pi pi-check"
                            size="small"
                            @click="selectAllAssignedStudents"
                            class="!bg-purple-600 !border-purple-600 hover:!bg-purple-700"
                        />
                    </div>
                </div>
            </div>

            <!-- STEP 2: Health Fields Selection Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">2</span>
                        Select Health Fields
                    </h2>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold" :class="selectedHealthFields.length === 6 ? 'text-green-600' : selectedHealthFields.length >= 4 ? 'text-blue-600' : 'text-gray-600'">
                            {{ selectedHealthFields.length }}/6 fields selected
                        </span>
                        <div class="flex gap-2">
                            <Button
                                label="Clear All"
                                size="small"
                                outlined
                                severity="secondary"
                                @click="clearAllHealthFields"
                                class="text-xs"
                            />
                            <Button
                                label="Essential Fields"
                                size="small"
                                severity="info"
                                @click="selectEssentialFields"
                                class="text-xs"
                            />
                        </div>
                    </div>
                </div>

                <!-- Health Examination Fields -->
                <div>
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
                    
                    <!-- Field Count Recommendations -->
                    <div class="mt-4">
                        <!-- At Limit -->
                        <div v-if="selectedHealthFields.length === 6" class="flex items-center gap-2 text-sm text-green-700 bg-green-50 px-3 py-2 rounded-md">
                            <i class="pi pi-check-circle"></i>
                            <span><strong>Maximum Reached!</strong> 6 fields selected - Perfect for PDF layout</span>
                        </div>
                        
                        <!-- Good Range -->
                        <div v-else-if="selectedHealthFields.length >= 4 && selectedHealthFields.length < 6" class="flex items-center gap-2 text-sm text-blue-700 bg-blue-50 px-3 py-2 rounded-md">
                            <i class="pi pi-info-circle"></i>
                            <span>{{ selectedHealthFields.length }} fields selected - You can add {{ 6 - selectedHealthFields.length }} more</span>
                        </div>
                        
                        <!-- Few Fields -->
                        <div v-else-if="selectedHealthFields.length > 0 && selectedHealthFields.length < 4" class="flex items-center gap-2 text-sm text-yellow-700 bg-yellow-50 px-3 py-2 rounded-md">
                            <i class="pi pi-exclamation-triangle"></i>
                            <span>{{ selectedHealthFields.length }} fields selected - Consider adding more for a comprehensive report</span>
                        </div>
                        
                        <!-- No Fields -->
                        <div v-else-if="selectedHealthFields.length === 0" class="text-sm text-blue-700 bg-blue-50 px-3 py-2 rounded-md">
                            <i class="pi pi-info-circle mr-1"></i>
                            <strong>Tip:</strong> Click "Essential Fields" to quickly select 6 optimal health metrics (Maximum: 6 fields)
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Generate Report -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="bg-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">3</span>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Generate Report</h2>
                            <p class="text-sm text-gray-600">Review your selections and generate the PDF</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <Button
                            v-if="showDraftNotification"
                            label="Clear Draft"
                            icon="pi pi-trash"
                            @click="clearDraft"
                            outlined
                            severity="danger"
                            size="small"
                        />
                        <Button
                            label="Generate PDF Report"
                            icon="pi pi-file-pdf"
                            @click="generateReport"
                            :loading="loading"
                            :disabled="isGenerateDisabled"
                            size="large"
                            class="!bg-green-600 !border-green-600 hover:!bg-green-700 !px-8 !py-3"
                        />
                    </div>
                </div>

                <!-- Summary -->
                <div class="mt-4 grid grid-cols-3 gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="text-xs text-blue-600 font-medium mb-1">Pupils</div>
                        <div class="text-lg font-bold text-blue-900">
                            {{ selectedStudents.length > 0 ? selectedStudents.filter(s => s.checked).length + ' selected' : (selectedGrade ? 'Grade ' + selectedGrade : 'Not selected') }}
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <div class="text-xs text-green-600 font-medium mb-1">Health Fields</div>
                        <div class="text-lg font-bold text-green-900">{{ selectedHealthFields.length }} fields</div>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                        <div class="text-xs text-purple-600 font-medium mb-1">Status</div>
                        <div class="text-sm font-bold" :class="isGenerateDisabled ? 'text-red-600' : 'text-green-600'">
                            {{ isGenerateDisabled ? 'Incomplete' : 'Ready to Generate' }}
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                <div v-if="isGenerateDisabled" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center text-sm text-yellow-800">
                        <i class="pi pi-exclamation-triangle mr-2"></i>
                        <span>
                            <strong>Action Required:</strong> 
                            {{ selectedStudents.length === 0 && !selectedGrade ? 'Please select pupils or choose a grade level' : 'Please check at least one pupil from your selection' }}
                        </span>
                    </div>
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
        'All',
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
const sectionOptions = ['All', 'A', 'B', 'C', 'D', 'E', 'F'];
const genderOptions = ['All', 'Male', 'Female'];
const sortOptions = [
    'Name (A-Z)',
    'Name (Z-A)',
    'Grade Level (Lowest First)',
    'Grade Level (Highest First)',
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

// Calculate total field count for recommendations
const totalFieldCount = computed(() => {
    // Student fields that are always included: name, lrn, grade_level, section, gender, age
    const studentFieldCount = 6;
    const healthFieldCount = selectedHealthFields.value.length;
    return studentFieldCount + healthFieldCount;
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
const selectedFields = ['name', 'lrn', 'grade_level', 'section', 'gender', 'age'];

// Health examination fields
const healthExamFields = [
    { label: 'Temperature', value: 'temperature' },
    { label: 'Heart Rate', value: 'heart_rate' },
    { label: 'Height', value: 'height' },
    { label: 'Weight', value: 'weight' },
    { label: 'Nutritional Status (BMI)', value: 'nutritional_status_bmi' },
    { label: 'Nutritional Status (Height)', value: 'nutritional_status_height' },
    { label: 'Vision Screening', value: 'vision_screening' },
    { label: 'Auditory Screening', value: 'auditory_screening' },
    { label: 'Skin', value: 'skin' },
    { label: 'Scalp', value: 'scalp' },
    { label: 'Eye', value: 'eye' },
    { label: 'Ear', value: 'ear' },
    { label: 'Nose', value: 'nose' },
    { label: 'Mouth', value: 'mouth' },
    { label: 'Throat', value: 'throat' },
    { label: 'Neck', value: 'neck' },
    { label: 'Lungs/Heart', value: 'lungs_heart' },
    { label: 'Abdomen', value: 'abdomen' },
    { label: 'Deformities', value: 'deformities' },
    { label: 'Deworming Status', value: 'deworming_status' },
    { label: 'Iron Supplementation', value: 'iron_supplementation' },
    { label: 'SBFP Beneficiary', value: 'sbfp_beneficiary' },
    { label: '4Ps Beneficiary', value: 'four_ps_beneficiary' },
    { label: 'Immunization', value: 'immunization' },
    { label: 'Other Specify', value: 'other_specify' },
    { label: 'Remarks', value: 'remarks' }
];

// School year mapping
const getSchoolYearForGrade = (grade) => {
    const gradeToYear = {
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
        // Remove field
        selectedHealthFields.value.splice(index, 1);
    } else {
        // Check if limit reached
        if (selectedHealthFields.value.length >= 6) {
            alert('Maximum 6 health fields allowed for optimal PDF layout');
            return;
        }
        selectedHealthFields.value.push(fieldValue);
    }
    console.log('Selected fields:', selectedHealthFields.value);
};

// Clear all health fields
const clearAllHealthFields = () => {
    selectedHealthFields.value = [];
};

// Select essential fields (optimal for PDF)
const selectEssentialFields = () => {
    selectedHealthFields.value = [
        'height', 'weight', 'nutritional_status_bmi', 
        'vision_screening', 'temperature', 'heart_rate'
    ];
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
const removeStudent = (student) => {
    const studentId = typeof student === 'object' ? student.id : student;
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
    // Get only checked students
    const checkedStudents = selectedStudents.value ? selectedStudents.value.filter(student => student && student.checked) : [];

    console.log('Selected students:', selectedStudents.value);
    console.log('Checked students:', checkedStudents);
    console.log('Selected grade:', selectedGrade.value);

    if (!selectedGrade.value && checkedStudents.length === 0) {
        alert('Please select a grade level or check at least one student');
        return;
    }

    // Build URL parameters for direct PDF generation
    const params = new URLSearchParams();
    
    // Add grade level - use selected grade or 'All' if students are selected
    if (selectedGrade.value) {
        params.append('grade_level', selectedGrade.value.replace('Grade ', ''));
    } else if (checkedStudents.length > 0) {
        params.append('grade_level', 'All');
    }
    
    // Add school year
    params.append('school_year', checkedStudents.length > 0 ? '2024-2025' : getSchoolYearForGrade(selectedGrade.value));
    
    // Add section if specified
    if (section.value) {
        params.append('section', section.value);
    }
    
    // Add selected students (send only IDs)
    if (checkedStudents.length > 0) {
        checkedStudents.forEach((student, index) => {
            params.append(`selected_students[${index}]`, student.id);
        });
    }
    
    // Add basic student fields (always include these)
    const basicFields = ['name', 'lrn', 'grade_level', 'section', 'gender', 'age'];
    basicFields.forEach((field, index) => {
        params.append(`fields[${index}]`, field);
    });
    
    // Add health exam fields
    if (selectedHealthFields.value && selectedHealthFields.value.length > 0) {
        selectedHealthFields.value.forEach((field, index) => {
            params.append(`health_exam_fields[${index}]`, field);
        });
    }
    
    // Add filters
    if (genderFilter.value && genderFilter.value !== 'All') {
        params.append('gender_filter', genderFilter.value);
    }
    if (minAge.value) {
        params.append('min_age', minAge.value);
    }
    if (maxAge.value) {
        params.append('max_age', maxAge.value);
    }
    if (sortBy.value) {
        params.append('sort_by', sortBy.value);
    }
    
    console.log('Opening PDF with parameters:', params.toString());
    
    // Open PDF directly (server-side generation)
    const url = `/health-report/export-pdf?${params.toString()}`;
    window.open(url, '_blank');
    
    // Clear saved form data after successful generation
    onSubmitSuccess();
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
