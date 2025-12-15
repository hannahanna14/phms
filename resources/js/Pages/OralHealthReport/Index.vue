<template>
    <Head title="| Oral Health Report Generator" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-6xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="pi pi-chart-bar mr-2 text-blue-600"></i>
                        Oral Health Report Generator
                    </h1>
                    <p class="text-gray-600">Generate comprehensive oral health reports for students</p>
                </div>

                <!-- Step-by-step Guide -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                    <h3 class="font-semibold text-blue-900 mb-2 flex items-center">
                        <i class="pi pi-info-circle mr-2"></i>
                        How to Generate a Report
                    </h3>
                    <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
                        <li><strong>Step 1:</strong> Search and select specific pupils OR choose a grade level filter</li>
                        <li><strong>Step 2:</strong> Select which oral health fields you want to include in the report</li>
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
                    <div class="border-2 border-gray-200 rounded-lg p-4" :class="form.grade_level && form.grade_level !== 'All' ? 'bg-gray-50' : 'bg-white'">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="pi pi-filter mr-2 text-gray-600"></i>
                            Option B: Filter by Grade/Section
                        </h3>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                                <Dropdown
                                    v-model="form.grade_level"
                                    :options="gradeOptions"
                                    placeholder="Select grade level"
                                    class="w-full"
                                    :disabled="selectedStudents.length > 0"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Section (Optional)</label>
                                <Dropdown
                                    v-model="form.section"
                                    :options="availableSections"
                                    placeholder="All sections"
                                    :disabled="form.grade_level === 'All' || selectedStudents.length > 0"
                                    class="w-full"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender (Optional)</label>
                                <Dropdown
                                    v-model="form.gender_filter"
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
                            <Dropdown
                                v-model="form.sort_by"
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

            <!-- STEP 2: Oral Health Fields Selection Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <span class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">2</span>
                        Select Oral Health Fields
                    </h2>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold" :class="selectedFieldsCount === 10 ? 'text-green-600' : selectedFieldsCount >= 5 ? 'text-blue-600' : 'text-gray-600'">
                            {{ selectedFieldsCount }}/10 fields selected
                        </span>
                        <div class="flex gap-2">
                            <Button
                                label="Clear All"
                                size="small"
                                outlined
                                severity="secondary"
                                @click="clearAllOralFields"
                                class="text-xs"
                            />
                            <Button
                                label="Essential Fields"
                                size="small"
                                severity="info"
                                @click="selectEssentialOralFields"
                                class="text-xs"
                            />
                        </div>
                    </div>
                </div>

                <!-- Oral Examination Fields -->
                <TabView class="w-full">
                    <!-- Permanent Teeth Tab -->
                    <TabPanel header="Permanent Teeth">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div v-for="field in permanentTeethFields" :key="field.key"
                                class="border-2 rounded-lg p-4 transition-all duration-200"
                                :class="form.selectedFields[field.key] ? 'border-green-400 bg-green-50' : 'border-gray-200 bg-white hover:border-blue-300'">
                                <div class="mb-3 flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700">{{ field.label }}</label>
                                    <Checkbox
                                        v-model="form.selectedFields[field.key]"
                                        :binary="true"
                                        class="ml-2"
                                    />
                                </div>

                                <!-- Range Slider -->
                                <div v-if="form.selectedFields[field.key]" class="space-y-3">
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>Min: {{ form.minValues[field.key] || 0 }}</span>
                                        <span>Max: {{ form.maxValues[field.key] || 32 }}</span>
                                    </div>
                                    <div class="px-2">
                                        <Slider
                                            v-model="form.rangeValues[field.key]"
                                            :min="0"
                                            :max="32"
                                            :range="true"
                                            class="w-full"
                                            @update:modelValue="updateRange(field.key, $event)"
                                        />
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-400">
                                        <span>0</span>
                                        <span>32</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabPanel>

                    <!-- Temporary Teeth Tab -->
                    <TabPanel header="Temporary Teeth">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div v-for="field in temporaryTeethFields" :key="field.key"
                                class="border-2 rounded-lg p-4 transition-all duration-200"
                                :class="form.selectedFields[field.key] ? 'border-green-400 bg-green-50' : 'border-gray-200 bg-white hover:border-blue-300'">
                                <div class="mb-3 flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700">{{ field.label }}</label>
                                    <Checkbox
                                        v-model="form.selectedFields[field.key]"
                                        :binary="true"
                                        class="ml-2"
                                    />
                                </div>

                                <!-- Range Slider -->
                                <div v-if="form.selectedFields[field.key]" class="space-y-3">
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>Min: {{ form.minValues[field.key] || 0 }}</span>
                                        <span>Max: {{ form.maxValues[field.key] || 20 }}</span>
                                    </div>
                                    <div class="px-2">
                                        <Slider
                                            v-model="form.rangeValues[field.key]"
                                            :min="0"
                                            :max="20"
                                            :range="true"
                                            class="w-full"
                                            @update:modelValue="updateRange(field.key, $event)"
                                        />
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-400">
                                        <span>0</span>
                                        <span>20</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                </TabView>

                <!-- Field Count Recommendations -->
                <div class="mt-4">
                    <!-- At Limit -->
                    <div v-if="selectedFieldsCount === 10" class="flex items-center gap-2 text-sm text-green-700 bg-green-50 px-3 py-2 rounded-md">
                        <i class="pi pi-check-circle"></i>
                        <span><strong>Maximum Reached!</strong> All 10 fields selected</span>
                    </div>

                    <!-- Good Range -->
                    <div v-else-if="selectedFieldsCount >= 5 && selectedFieldsCount < 10" class="flex items-center gap-2 text-sm text-blue-700 bg-blue-50 px-3 py-2 rounded-md">
                        <i class="pi pi-info-circle"></i>
                        <span>{{ selectedFieldsCount }} fields selected - You can add {{ 10 - selectedFieldsCount }} more</span>
                    </div>

                    <!-- Few Fields -->
                    <div v-else-if="selectedFieldsCount > 0 && selectedFieldsCount < 5" class="flex items-center gap-2 text-sm text-yellow-700 bg-yellow-50 px-3 py-2 rounded-md">
                        <i class="pi pi-exclamation-triangle"></i>
                        <span>{{ selectedFieldsCount }} fields selected - Consider adding more for a comprehensive report</span>
                    </div>

                    <!-- No Fields -->
                    <div v-else-if="selectedFieldsCount === 0" class="text-sm text-blue-700 bg-blue-50 px-3 py-2 rounded-md">
                        <i class="pi pi-info-circle mr-1"></i>
                        <strong>Tip:</strong> Click "Essential Fields" to quickly select key oral health metrics
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
                            :loading="form.processing"
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
                            {{ selectedStudents.length > 0 ? selectedStudents.filter(s => s.checked).length + ' selected' : (form.grade_level ? 'Grade ' + form.grade_level : 'Not selected') }}
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <div class="text-xs text-green-600 font-medium mb-1">Oral Health Fields</div>
                        <div class="text-lg font-bold text-green-900">{{ selectedFieldsCount }} fields</div>
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
                            {{ selectedStudents.length === 0 && !form.grade_level ? 'Please select pupils or choose a grade level' : 'Please check at least one pupil from your selection' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css';
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import Slider from 'primevue/slider'
import Button from 'primevue/button'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import { useFormPersistence } from '@/composables/useFormPersistence';
import { useToastStore } from '@/Stores/toastStore';
import axios from 'axios';

// Toast store
const { showError, showWarning } = useToastStore();

const props = defineProps({
    gradeLevels: Array,
    userRole: {
        type: String,
        default: 'admin'
    }
});

const form = useForm({
    grade_level: '',
    section: '',
    selectedFields: {
        // Permanent teeth
        permanent_index_dft: false,
        permanent_teeth_decayed: false,
        permanent_teeth_filled: false,
        permanent_for_extraction: false,
        permanent_for_filling: false,
        // Temporary teeth
        temporary_index_dft: false,
        temporary_teeth_decayed: false,
        temporary_teeth_filled: false,
        temporary_for_extraction: false,
        temporary_for_filling: false
    },
    minValues: {
        // Permanent teeth
        permanent_index_dft: null,
        permanent_teeth_decayed: null,
        permanent_teeth_filled: null,
        permanent_for_extraction: null,
        permanent_for_filling: null,
        // Temporary teeth
        temporary_index_dft: null,
        temporary_teeth_decayed: null,
        temporary_teeth_filled: null,
        temporary_for_extraction: null,
        temporary_for_filling: null
    },
    maxValues: {
        // Permanent teeth
        permanent_index_dft: null,
        permanent_teeth_decayed: null,
        permanent_teeth_filled: null,
        permanent_for_extraction: null,
        permanent_for_filling: null,
        // Temporary teeth
        temporary_index_dft: null,
        temporary_teeth_decayed: null,
        temporary_teeth_filled: null,
        temporary_for_extraction: null,
        temporary_for_filling: null
    },
    rangeValues: {
        // Permanent teeth
        permanent_index_dft: [0, 0],
        permanent_teeth_decayed: [0, 0],
        permanent_teeth_filled: [0, 0],
        permanent_for_extraction: [0, 0],
        permanent_for_filling: [0, 0],
        // Temporary teeth
        temporary_index_dft: [0, 0],
        temporary_teeth_decayed: [0, 0],
        temporary_teeth_filled: [0, 0],
        temporary_for_extraction: [0, 0],
        temporary_for_filling: [0, 0]
    },
    gender_filter: 'All',
    min_age: null,
    max_age: null,
    sort_by: 'Name (A-Z)',
    selected_students: [],
    selectedStudents: []
});

// Student search data - sync with form for persistence
const selectedStudents = computed({
    get: () => form.selectedStudents || [],
    set: (value) => form.selectedStudents = value
});
const studentOptions = ref([]);
const searchLoading = ref(false);
const searchQuery = ref('');

// Simple reactive variable for button state
const buttonDisabled = ref(true);

// Set up form persistence
const {
    showDraftNotification,
    initializeForm,
    setupAutoSave,
    onSubmitSuccess,
    clearSavedFormData
} = useFormPersistence('oral_health_report_form', form, {
    excludeFields: [], // Save all form fields
    autoSave: true,
    showNotification: true
});

// Grade options will come from the controller (already includes "All")
const gradeOptions = computed(() => {
    return props.gradeLevels || [];
});


// Permanent teeth fields based on the image
const permanentTeethFields = [
    { key: 'permanent_index_dft', label: 'Index d.f.t.' },
    { key: 'permanent_teeth_decayed', label: 'Teeth Decayed' },
    { key: 'permanent_teeth_filled', label: 'Teeth Filled' },
    { key: 'permanent_for_extraction', label: 'For Extraction' },
    { key: 'permanent_for_filling', label: 'For Filling' }
];

// Temporary teeth fields based on the image
const temporaryTeethFields = [
    { key: 'temporary_index_dft', label: 'Index d.f.t.' },
    { key: 'temporary_teeth_decayed', label: 'Teeth Decayed' },
    { key: 'temporary_teeth_filled', label: 'Teeth Filled' },
    { key: 'temporary_for_extraction', label: 'For Extraction' },
    { key: 'temporary_for_filling', label: 'For Filling' }
];

// Sections organized by grade level
const sectionsByGrade = ref({
    'All': ['All'],
    'Kinder 2': [
        'Generous AM',
        'Generous PM',
        'Good AM',
        'Good PM',
        'SNED – Kindergarten (DHH) (SPED)'
    ],
    'Grade 1': [
        'Admirable',
        'Adorable',
        'Affectionate',
        'Alert',
        'Amazing'
    ],
    'Grade 2': [
        'Beloved',
        'Beneficent',
        'Benevolent',
        'Blessed',
        'Blissful',
        'Blossom',
        'SNED – Grade 2 (DHH) (SPED)'
    ],
    'Grade 3': [
        'Calm',
        'Candor',
        'Charitable',
        'Cheerful',
        'Clever',
        'Curious'
    ],
    'Grade 4': [
        'Dainty',
        'Dedicated',
        'Demure',
        'Devoted',
        'Dynamic',
        'SNED (Graded) (SPED)'
    ],
    'Grade 5': [
        'Effective',
        'Efficient',
        'Endurance',
        'Energetic',
        'Everlasting'
    ],
    'Grade 6': [
        'Fair',
        'Faithful',
        'Flexible',
        'Forbearance',
        'Fortitude',
        'Friendly'
    ],
    'Non-Graded': [
        'Gracious (SPED)',
        'Grateful (SPED)'
    ]
});

// Available sections based on selected grade
const availableSections = computed(() => {
    if (form.grade_level === 'All') {
        return ['All'];
    }
    const gradeSections = sectionsByGrade.value[form.grade_level] || [];
    return ['All', ...gradeSections];
});

// Update section when grade changes
watch(() => form.grade_level, (newGrade) => {
    if (newGrade !== form.section) {
        form.section = 'All';
    }
});

const genderOptions = ['All', 'Male', 'Female'];
const sortOptions = ['Name (A-Z)', 'Name (Z-A)', 'Age (Youngest First)', 'Age (Oldest First)'];

// Computed property for selected fields count
const selectedFieldsCount = computed(() => {
    return Object.values(form.selectedFields).filter(val => val === true).length;
});

// Clear all oral health fields
const clearAllOralFields = () => {
    Object.keys(form.selectedFields).forEach(key => {
        form.selectedFields[key] = false;
    });
};

// Select essential oral health fields
const selectEssentialOralFields = () => {
    // Select key permanent teeth fields
    form.selectedFields.permanent_teeth_decayed = true;
    form.selectedFields.permanent_teeth_filled = true;
    form.selectedFields.permanent_for_filling = true;

    // Select key temporary teeth fields
    form.selectedFields.temporary_teeth_decayed = true;
    form.selectedFields.temporary_teeth_filled = true;
    form.selectedFields.temporary_for_filling = true;
};

// Computed property for button disabled state
const isGenerateDisabled = computed(() => {
    try {
        console.log('Oral Health - Computing button state...');
        console.log('Oral Health - Form processing:', form.processing);
        console.log('Oral Health - User role:', props.userRole);

        if (form.processing) return true;

        // Force reactivity by accessing the length property
        const studentsCount = selectedStudents.value?.length || 0;
        console.log('Oral Health - Students count:', studentsCount);

        // If students are selected, check if any are checked
        if (studentsCount > 0) {
            const checkedStudents = selectedStudents.value.filter(s => s && s.checked === true);
            console.log('Oral Health - Selected students:', selectedStudents.value);
            console.log('Oral Health - Checked students:', checkedStudents);
            console.log('Oral Health - Checked count:', checkedStudents.length);
            const shouldEnable = checkedStudents.length > 0;
            console.log('Oral Health - Button should be enabled:', shouldEnable);
            return !shouldEnable; // Return opposite because this is "disabled"
        }

        // For teachers, if no students are selected, they can still generate reports for all their assigned students
        if (props.userRole === 'teacher') {
            console.log('Oral Health - Teacher with no students selected, enabling button');
            return false; // Enable button for teachers even without grade level or gender filter
        }

        // For non-teachers, require grade level if no students selected
        const hasGradeLevel = !!form.grade_level;
        console.log('Oral Health - No students selected, grade level:', form.grade_level);
        console.log('Oral Health - Has grade level:', hasGradeLevel);
        console.log('Oral Health - Button should be enabled:', hasGradeLevel);
        return !hasGradeLevel; // Return opposite because this is "disabled"
    } catch (error) {
        console.error('Error in isGenerateDisabled computed:', error);
        return true; // Disable button if there's an error
    }
});

// Function to update button state
const updateButtonState = () => {
    console.log('Oral Health - Updating button state...');

    if (form.processing) {
        buttonDisabled.value = true;
        console.log('Oral Health - Button disabled: form processing');
        return;
    }

    if (selectedStudents.value.length > 0) {
        const checkedCount = selectedStudents.value.filter(s => s.checked === true).length;
        buttonDisabled.value = checkedCount === 0;
        console.log('Oral Health - Students selected, checked count:', checkedCount);
        console.log('Oral Health - Button disabled:', buttonDisabled.value);
    } else {
        // For teachers, enable button even without grade level (they can generate reports for all assigned students)
        if (props.userRole === 'teacher') {
            buttonDisabled.value = false;
            console.log('Oral Health - Teacher with no students, enabling button');
        } else {
            buttonDisabled.value = !form.grade_level;
            console.log('Oral Health - No students, grade level:', form.grade_level);
            console.log('Oral Health - Button disabled:', buttonDisabled.value);
        }
    }
};

// Watch selectedStudents for changes
watch(selectedStudents, (newStudents) => {
    console.log('Oral Health - Students changed:', newStudents);
    console.log('Oral Health - Students length:', newStudents.length);
    updateButtonState();
}, { deep: true });

// Watch form.grade_level for changes
watch(() => form.grade_level, (newGrade) => {
    console.log('Oral Health - Grade level changed:', newGrade);
    updateButtonState();
});

// Watch form.processing for changes
watch(() => form.processing, (newProcessing) => {
    console.log('Oral Health - Form processing changed:', newProcessing);
    updateButtonState();
});

// Student search functionality with debouncing
let searchTimeout = null;

const onStudentSearch = () => {
    const query = searchQuery.value;

    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    if (!query || query.length < 1) {
        studentOptions.value = [];
        searchLoading.value = false;
        return;
    }

    searchLoading.value = true;

    // Debounce: wait 300ms after user stops typing
    searchTimeout = setTimeout(async () => {
        try {
            const response = await axios.get('/api/oral-health-report/students/search', {
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
    }, 300); // 300ms delay
};

// Add student to selection
const addStudent = (student) => {
    // Check if student is already selected
    const currentStudents = selectedStudents.value || [];
    const exists = currentStudents.find(s => s.id === student.id);
    if (!exists) {
        const page = usePage();
        const currentSchoolYear = page.props.currentSchoolYear;

        // Prevent adding students from a different school year
        if (student.school_year && currentSchoolYear && student.school_year !== currentSchoolYear) {
            showWarning('Inactive Student', 'This pupil is not in the current school year and will not be included in the report.');
            return;
        }
        const newStudent = {
            ...student,
            checked: true
        };
        // Create new array to trigger reactivity
        selectedStudents.value = [...currentStudents, newStudent];
        console.log('Oral Health - Added student:', newStudent);
        console.log('Oral Health - All selected students:', selectedStudents.value);

        // Update button state immediately
        updateButtonState();

        // Clear search after adding
        searchQuery.value = '';
        studentOptions.value = [];
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
        const response = await axios.get('/api/oral-health-report/students/search', {
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
        showError('Loading Failed', 'Failed to load assigned students.');
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

// Get checked students helper function
const getCheckedStudents = () => {
    return selectedStudents.value ? selectedStudents.value.filter(student => student && student.checked) : [];
};

// Update range values
const updateRange = (fieldKey, rangeValue) => {
    form.minValues[fieldKey] = rangeValue[0];
    form.maxValues[fieldKey] = rangeValue[1];
};


const generateReport = () => {
    // Get only checked students
    const checkedStudents = getCheckedStudents();

    // Validate required fields first
    if (!form.grade_level && checkedStudents.length === 0) {
        showError('Selection Required', 'Please select a grade level or check at least one student');
        return;
    }

    // Update form with selected students
    form.selected_students = checkedStudents.map(s => ({
        id: s.id,
        name: s.name,
        lrn: s.lrn,
        grade_level: s.grade_level,
        section: s.section
    }));

    // Add oral exam fields that are selected (checked)
    const oralExamFields = [];
    Object.keys(form.selectedFields).forEach(fieldKey => {
        if (form.selectedFields[fieldKey]) {
            oralExamFields.push(fieldKey);
        }
    });
    form.oral_exam_fields = oralExamFields;

    // Generate PDF directly like health examination (no Results page needed)
    const params = new URLSearchParams();

    // Add grade level if selected
    if (form.grade_level) {
        params.append('grade_level', form.grade_level);
    }

    // Add section if specified
    if (form.section) {
        params.append('section', form.section);
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

    // Add oral exam fields
    oralExamFields.forEach((field, index) => {
        params.append(`oral_exam_fields[${index}]`, field);
    });

    // Add optional filters
    if (form.gender_filter) {
        params.append('gender_filter', form.gender_filter);
    }
    if (form.min_age) {
        params.append('min_age', form.min_age);
    }
    if (form.max_age) {
        params.append('max_age', form.max_age);
    }

    // Add sort_by parameter
    if (form.sort_by) {
        params.append('sort_by', form.sort_by);
    }

    console.log('Opening PDF with parameters:', params.toString());

    // Open PDF directly (server-side generation, works offline)
    const url = `/oral-health-report/export-pdf?${params.toString()}`;
    window.open(url, '_blank');

    // Clear saved form data after successful generation
    onSubmitSuccess();
};

const clearDraft = () => {
    if (confirm('Are you sure you want to clear your saved draft? This action cannot be undone.')) {
        clearSavedFormData();
        // Reset form to defaults
        form.reset();
        // Reset selected students
        selectedStudents.value = [];
        // Update button state
        updateButtonState();
    }
};

onMounted(() => {
    // Initialize form persistence
    initializeForm();
    setupAutoSave();

    // Reset form processing state on mount
    form.processing = false;
    console.log('Oral Health - Reset form processing state');

    // Initial button state update
    updateButtonState();
    }
);
</script>
