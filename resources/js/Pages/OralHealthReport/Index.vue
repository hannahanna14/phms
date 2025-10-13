<template>
    <Head title="Oral Health Report" />
    
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Oral Health Report Generator</h1>
                    <p class="text-gray-600">Generate comprehensive oral health reports for students</p>
                </div>
            </div>

            <!-- Draft Restored Notification -->
            <div v-if="showDraftNotification" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <i class="pi pi-info-circle text-blue-600 mr-2"></i>
                    <span class="text-blue-800 text-sm">
                        <strong>Draft restored:</strong> Your previous oral health report settings have been recovered.
                    </span>
                    <button @click="showDraftNotification = false" class="ml-auto text-blue-600 hover:text-blue-800">
                        <i class="pi pi-times"></i>
                    </button>
                </div>
            </div>

            <!-- Student Selection Card -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
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
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="pi pi-cog mr-2 text-gray-600"></i>
                    Report Configuration
                </h2>
                
                <form @submit.prevent="generateReport" class="space-y-6">
                    <div>
                        
                        <!-- Teacher Notice -->
                        <div v-if="userRole === 'teacher'" class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-6">
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column (Hidden for Teachers) -->
                            <div v-if="userRole !== 'teacher'" class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                                    <Dropdown 
                                        v-model="form.grade_level" 
                                        :options="gradeOptions" 
                                        placeholder="Select Grade Level"
                                        class="w-full"
                                        :class="{ 'p-invalid': form.errors.grade_level }"
                                        :disabled="selectedStudents.length > 0"
                                    />
                                    <small v-if="form.errors.grade_level" class="text-red-500">{{ form.errors.grade_level }}</small>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Section (Optional)</label>
                                    <Dropdown 
                                        v-model="form.section" 
                                        :options="sectionOptions" 
                                        placeholder="Select Section"
                                        class="w-full"
                                        :disabled="selectedStudents.length > 0"
                                    />
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender Filter</label>
                                    <Dropdown 
                                        v-model="form.gender_filter" 
                                        :options="genderOptions" 
                                        placeholder="All"
                                        class="w-full"
                                        :disabled="selectedStudents.length > 0"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Age Range</label>
                                    <div class="flex space-x-2">
                                        <InputNumber 
                                            v-model="form.min_age" 
                                            placeholder="Min Age" 
                                            :min="5" 
                                            :max="18"
                                            class="w-full"
                                            :disabled="selectedStudents.length > 0"
                                        />
                                        <InputNumber 
                                            v-model="form.max_age" 
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
                                    <Dropdown 
                                        v-model="form.sort_by" 
                                        :options="sortOptions" 
                                        placeholder="Name (A-Z)"
                                        class="w-full"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Oral Examination Fields -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Oral Examination Fields
                            <span v-if="getCheckedStudents().length > 0" class="text-sm text-blue-600 font-normal">(Select fields to include in report)</span>
                        </label>
                        <TabView class="w-full">
                            <!-- Permanent Teeth Tab -->
                            <TabPanel header="Permanent Teeth">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div v-for="field in permanentTeethFields" :key="field.key" class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                        <div class="mb-3 flex items-center justify-between">
                                            <label class="text-sm font-medium text-gray-700">{{ field.label }}</label>
                                            <Checkbox 
                                                v-model="form.selectedFields[field.key]" 
                                                :binary="true"
                                                class="ml-2"
                                            />
                                        </div>
                                        
                                        <!-- Range Slider -->
                                        <div class="space-y-3">
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
                                    <div v-for="field in temporaryTeethFields" :key="field.key" class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                        <div class="mb-3 flex items-center justify-between">
                                            <label class="text-sm font-medium text-gray-700">{{ field.label }}</label>
                                            <Checkbox 
                                                v-model="form.selectedFields[field.key]" 
                                                :binary="true"
                                                class="ml-2"
                                            />
                                        </div>
                                        
                                        <!-- Range Slider -->
                                        <div class="space-y-3">
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
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-start gap-4 pt-6">
                        <Button 
                            type="submit" 
                            label="Generate Report" 
                            icon="pi pi-file-export"
                            :loading="form.processing"
                            :disabled="buttonDisabled"
                            class="!bg-green-600 !border-green-600 hover:!bg-green-700"
                        />
                        <Button
                            v-if="showDraftNotification"
                            type="button"
                            label="Clear Draft"
                            icon="pi pi-trash"
                            @click="clearDraft"
                            outlined
                            severity="danger"
                            size="small"
                        />
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted, computed, watch } from 'vue';
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import Slider from 'primevue/slider'
import Button from 'primevue/button'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import { useFormPersistence } from '@/composables/useFormPersistence';
import axios from 'axios';

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

const sectionOptions = ['A', 'B', 'C', 'D', 'E'];

const genderOptions = ['All', 'Male', 'Female'];
const sortOptions = ['Name (A-Z)', 'Name (Z-A)', 'Age (Youngest First)', 'Age (Oldest First)'];

// Computed property for button disabled state
const isGenerateDisabled = computed(() => {
    try {
        console.log('Oral Health - Computing button state...');
        console.log('Oral Health - Form processing:', form.processing);
        
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
        
        // If no students selected, require grade level
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
        buttonDisabled.value = !form.grade_level;
        console.log('Oral Health - No students, grade level:', form.grade_level);
        console.log('Oral Health - Button disabled:', buttonDisabled.value);
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
    const currentStudents = selectedStudents.value || [];
    const exists = currentStudents.find(s => s.id === student.id);
    if (!exists) {
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
        alert('Please select a grade level or check at least one student');
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
});
</script>

<style scoped>
.p-invalid {
    border-color: #ef4444;
}
</style>
