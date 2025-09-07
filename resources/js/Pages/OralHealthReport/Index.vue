<template>
    <Head title="Oral Health Report" />
    
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Oral Health Report Generator</h1>
                    <p class="text-gray-600">Generate comprehensive oral health reports for students</p>
                </div>

                <form @submit.prevent="generateReport" class="space-y-6">
                    <!-- Report Configuration -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Configuration</h2>
                        
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
                                        />
                                        <InputNumber 
                                            v-model="form.max_age" 
                                            placeholder="Max Age" 
                                            :min="5" 
                                            :max="18"
                                            class="w-full"
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
                        <label class="block text-sm font-medium text-gray-700 mb-3">Oral Examination Fields</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="field in oralExamFields" :key="field.key" class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                <div class="mb-3">
                                    <label class="text-sm font-medium text-gray-700">{{ field.label }}</label>
                                </div>
                                
                                <!-- Min-Max inputs -->
                                <div class="flex space-x-2">
                                    <InputNumber 
                                        v-model="form.minValues[field.key]" 
                                        placeholder="Min" 
                                        :min="0" 
                                        :max="32"
                                        class="w-full"
                                    />
                                    <InputNumber 
                                        v-model="form.maxValues[field.key]" 
                                        placeholder="Max" 
                                        :min="0" 
                                        :max="32"
                                        class="w-full"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-start gap-4 pt-6">
                        <Button 
                            type="submit" 
                            label="Generate Report" 
                            icon="pi pi-file-export"
                            :loading="form.processing"
                            :disabled="!form.grade_level"
                            class="!bg-green-600 !border-green-600 hover:!bg-green-700"
                        />
                        <Button 
                            type="button"
                            label="Preview" 
                            icon="pi pi-eye"
                            severity="secondary"
                            outlined
                            @click="previewReport"
                            :disabled="!form.grade_level"
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

const props = defineProps({
    gradeLevels: Array
});

const form = useForm({
    grade_level: '',
    section: '',
    minValues: {
        decayed_teeth: null,
        missing_teeth: null,
        filled_teeth: null,
        total_dmft: null,
        sealant: null,
        fluoride_application: null
    },
    maxValues: {
        decayed_teeth: null,
        missing_teeth: null,
        filled_teeth: null,
        total_dmft: null,
        sealant: null,
        fluoride_application: null
    },
    gender_filter: 'All',
    min_age: null,
    max_age: null,
    sort_by: 'Name (A-Z)'
});

// Grade options will come from the controller
const gradeOptions = props.gradeLevels || [];


const oralExamFields = [
    { key: 'decayed_teeth', label: 'Decayed Teeth' },
    { key: 'missing_teeth', label: 'Missing Teeth' },
    { key: 'filled_teeth', label: 'Filled Teeth' },
    { key: 'total_dmft', label: 'Total DMFT' },
    { key: 'sealant', label: 'Sealant' },
    { key: 'fluoride_application', label: 'Fluoride Application' }
];

const sectionOptions = ['A', 'B', 'C', 'D', 'E'];

const genderOptions = ['All', 'Male', 'Female'];

const sortOptions = [
    'Name (A-Z)', 'Name (Z-A)', 
    'Age (Youngest First)', 'Age (Oldest First)'
];


const previewReport = () => {
    // Validate required fields first
    if (!form.grade_level) {
        alert('Please select a grade level');
        return;
    }
    
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
