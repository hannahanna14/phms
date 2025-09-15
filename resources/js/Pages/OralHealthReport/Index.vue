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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
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
                    <div :class="{ 'opacity-50 pointer-events-none': getCheckedStudents().length > 0 }">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Oral Examination Fields
                            <span v-if="getCheckedStudents().length > 0" class="text-sm text-gray-500 font-normal">(Disabled - Students Selected)</span>
                        </label>
                        <TabView class="w-full">
                            <!-- Permanent Teeth Tab -->
                            <TabPanel header="Permanent Teeth">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div v-for="field in permanentTeethFields" :key="field.key" class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                        <div class="mb-3">
                                            <label class="text-sm font-medium text-gray-700">{{ field.label }}</label>
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
                                        <div class="mb-3">
                                            <label class="text-sm font-medium text-gray-700">{{ field.label }}</label>
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
                            :disabled="((selectedStudents.length || 0) === 0 ? !form.grade_level : (selectedStudents.filter(s => s && s.checked).length || 0) === 0) || form.processing"
                            class="!bg-green-600 !border-green-600 hover:!bg-green-700"
                        />
                        <Button 
                            type="button"
                            label="Preview" 
                            icon="pi pi-eye"
                            severity="secondary"
                            outlined
                            @click="previewReport"
                            :disabled="((selectedStudents.length || 0) === 0 ? !form.grade_level : (selectedStudents.filter(s => s && s.checked).length || 0) === 0) || form.processing"
                            class="!border-gray-300 !text-gray-700 hover:!bg-gray-50"
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
import { ref } from 'vue';
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import Slider from 'primevue/slider'
import Button from 'primevue/button'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import axios from 'axios';

const props = defineProps({
    gradeLevels: Array
});

const form = useForm({
    grade_level: '',
    section: '',
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
    selected_students: []
});

// Student search data
const selectedStudents = ref([]);
const studentOptions = ref([]);
const searchLoading = ref(false);
const searchQuery = ref('');

// Grade options will come from the controller
const gradeOptions = props.gradeLevels || [];


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

const sortOptions = [
    'Name (A-Z)', 'Name (Z-A)', 
    'Age (Youngest First)', 'Age (Oldest First)'
];


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
        selectedStudents.value.push({
            ...student,
            checked: true
        });
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
const removeStudent = (studentToRemove) => {
    selectedStudents.value = selectedStudents.value.filter(student => student.id !== studentToRemove.id);
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

const previewReport = () => {
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
    
    // Add oral exam fields that have ranges set
    const oralExamFields = [];
    Object.keys(form.minValues).forEach(fieldKey => {
        const minVal = form.minValues[fieldKey];
        const maxVal = form.maxValues[fieldKey];
        if ((minVal !== null && minVal > 0) || (maxVal !== null && maxVal > 0)) {
            oralExamFields.push(fieldKey);
        }
    });
    form.oral_exam_fields = oralExamFields;
    
    // Submit form to generate results page (same as generate but different handling)
    form.get('/oral-health-report/generate', {
        onSuccess: (page) => {
            // Handle success - results page will be shown
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
        }
    });
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
    
    // Add oral exam fields that have ranges set
    const oralExamFields = [];
    Object.keys(form.minValues).forEach(fieldKey => {
        const minVal = form.minValues[fieldKey];
        const maxVal = form.maxValues[fieldKey];
        if ((minVal !== null && minVal > 0) || (maxVal !== null && maxVal > 0)) {
            oralExamFields.push(fieldKey);
        }
    });
    form.oral_exam_fields = oralExamFields;
    
    form.post('/oral-health-report/generate', {
        onSuccess: (page) => {
            // Handle success - redirect to results page
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
        }
    });
};
</script>

<style scoped>
.p-invalid {
    border-color: #ef4444;
}
</style>
