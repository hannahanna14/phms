<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import Card from 'primevue/card';

const { student } = usePage().props;

// Get grade context from sessionStorage or URL
const getGradeContext = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const urlGrade = urlParams.get('grade');
    const sessionGrade = sessionStorage.getItem(`currentGrade_${student.id}`);
    return urlGrade || sessionGrade || student.grade_level;
};

const getSchoolYearForGrade = (grade) => {
    const gradeToYear = {
        'Grade K': '2024-2025',
        'Grade 1': '2023-2024',
        'Grade 2': '2022-2023',
        'Grade 3': '2021-2022',
        'Grade 4': '2020-2021',
        'Grade 5': '2019-2020',
        'Grade 6': '2018-2019'
    };
    return gradeToYear[grade] || '2024-2025';
};

const selectedGrade = ref(getGradeContext());
const schoolYear = getSchoolYearForGrade(selectedGrade.value);

console.log('=== TREATMENT CREATE FORM INIT ===');
console.log('Initial selected grade:', selectedGrade.value);
console.log('Initial grade level for form:', selectedGrade.value.replace('Grade ', ''));
console.log('School year:', schoolYear);

const form = useForm({
    student_id: student.id,
    date: new Date(),
    title: '',
    chief_complaint: '',
    treatment: '',
    remarks: '',
    grade_level: selectedGrade.value.replace('Grade ', ''), // Store as "4" not "Grade 4"
    school_year: schoolYear
});

// Character limits
const limits = {
    title: 100,
    chief_complaint: 100,
    treatment: 100,
    remarks: 100
};

// Computed properties for character counts and validation
const titleCount = computed(() => form.title.length);
const chiefComplaintCount = computed(() => form.chief_complaint.length);
const treatmentCount = computed(() => form.treatment.length);
const remarksCount = computed(() => form.remarks.length);

const titleExceeded = computed(() => titleCount.value > limits.title);
const chiefComplaintExceeded = computed(() => chiefComplaintCount.value > limits.chief_complaint);
const treatmentExceeded = computed(() => treatmentCount.value > limits.treatment);
const remarksExceeded = computed(() => remarksCount.value > limits.remarks);

// Prevent input when limit is exceeded
const handleInput = (field, maxLength) => {
    if (form[field].length > maxLength) {
        form[field] = form[field].substring(0, maxLength);
    }
};

const submit = () => {
    // Get the current grade from sessionStorage to ensure we have the latest value
    const currentGrade = sessionStorage.getItem(`currentGrade_${student.id}`) || selectedGrade.value;
    const gradeLevel = currentGrade.replace('Grade ', '');
    
    // Ensure grade_level is not empty or null
    if (!gradeLevel || gradeLevel === 'undefined' || gradeLevel === 'null' || gradeLevel.trim() === '') {
        console.error('Grade level is invalid:', gradeLevel);
        console.error('Current grade from session:', currentGrade);
        console.error('Selected grade ref:', selectedGrade.value);
        alert('Error: Invalid grade level. Please refresh and try again.');
        return;
    }
    
    form.grade_level = gradeLevel;
    
    // Format date to YYYY-MM-DD to avoid timezone issues
    if (form.date instanceof Date) {
        const year = form.date.getFullYear();
        const month = String(form.date.getMonth() + 1).padStart(2, '0');
        const day = String(form.date.getDate()).padStart(2, '0');
        form.date = `${year}-${month}-${day}`;
    }
    
    console.log('=== TREATMENT FORM SUBMISSION DEBUG ===');
    console.log('Current Grade from session:', currentGrade);
    console.log('Selected Grade ref:', selectedGrade.value);
    console.log('Final Grade Level being saved:', form.grade_level);
    console.log('Grade Level type:', typeof form.grade_level);
    console.log('Formatted Date:', form.date);
    console.log('Complete Form Data:', form.data());
    console.log('==========================================');

    form.post(route('health-treatment.store'), {
        onSuccess: () => {
            console.log('Health treatment created successfully');
            // Use Inertia redirect instead of window.location to prevent full page reload
            // The backend redirect will handle this properly
        },
        onError: (errors) => {
            console.error('Health treatment submission errors:', errors);
        }
    });
};

const cancel = () => {
    // Navigate back to health examination show page for the student
    const grade = getGradeContext();
    window.location.href = `/pupil-health/health-examination/${student.id}?grade=${encodeURIComponent(grade)}`;
};
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Pupil Health Treatment</h1>
                <p class="text-gray-600">Naawan Central School</p>
            </div>

            <!-- Student Info -->
            <Card class="mb-6">
                <template #content>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><strong>Student:</strong> {{ student.full_name }}</p>
                            <p><strong>LRN:</strong> {{ student.lrn }}</p>
                        </div>
                        <div>
                            <p><strong>Grade Level:</strong> {{ student.grade_level }}</p>
                            <p><strong>Section:</strong> {{ student.section || 'Not Assigned' }}</p>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Treatment Form -->
            <Card>
                <template #content>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="pi pi-calendar mr-1"></i>
                                    Date <span class="text-red-500">*</span>
                                </label>
                                <Calendar
                                    v-model="form.date"
                                    dateFormat="mm/dd/yy"
                                    placeholder="MM/DD/YYYY"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.date }"
                                />
                                <small v-if="form.errors.date" class="text-red-500">{{ form.errors.date }}</small>
                            </div>
                        </div>

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <InputText
                                v-model="form.title"
                                placeholder="Title"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.title, 'border-red-500': titleExceeded }"
                                @input="handleInput('title', limits.title)"
                                :maxlength="limits.title"
                            />
                            <div class="flex justify-between items-center mt-1">
                                <small v-if="form.errors.title" class="text-red-500">{{ form.errors.title }}</small>
                                <small :class="titleExceeded ? 'text-red-500 font-semibold' : 'text-gray-500'" class="ml-auto">
                                    {{ titleCount }}/{{ limits.title }}
                                </small>
                            </div>
                        </div>

                        <!-- Chief Complaint and Treatment -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Chief Complaint <span class="text-red-500">*</span>
                                </label>
                                <Textarea
                                    v-model="form.chief_complaint"
                                    placeholder="Enter text"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.chief_complaint, 'border-red-500': chiefComplaintExceeded }"
                                    @input="handleInput('chief_complaint', limits.chief_complaint)"
                                    :maxlength="limits.chief_complaint"
                                />
                                <div class="flex justify-between items-center mt-1">
                                    <small v-if="form.errors.chief_complaint" class="text-red-500">{{ form.errors.chief_complaint }}</small>
                                    <small :class="chiefComplaintExceeded ? 'text-red-500 font-semibold' : 'text-gray-500'" class="ml-auto">
                                        {{ chiefComplaintCount }}/{{ limits.chief_complaint }}
                                    </small>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Treatment <span class="text-red-500">*</span>
                                </label>
                                <Textarea
                                    v-model="form.treatment"
                                    placeholder="Enter text"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.treatment, 'border-red-500': treatmentExceeded }"
                                    @input="handleInput('treatment', limits.treatment)"
                                    :maxlength="limits.treatment"
                                />
                                <div class="flex justify-between items-center mt-1">
                                    <small v-if="form.errors.treatment" class="text-red-500">{{ form.errors.treatment }}</small>
                                    <small :class="treatmentExceeded ? 'text-red-500 font-semibold' : 'text-gray-500'" class="ml-auto">
                                        {{ treatmentCount }}/{{ limits.treatment }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Remarks
                            </label>
                            <Textarea
                                v-model="form.remarks"
                                placeholder="Enter text"
                                rows="3"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.remarks, 'border-red-500': remarksExceeded }"
                                @input="handleInput('remarks', limits.remarks)"
                                :maxlength="limits.remarks"
                            />
                            <div class="flex justify-between items-center mt-1">
                                <small v-if="form.errors.remarks" class="text-red-500">{{ form.errors.remarks }}</small>
                                <small :class="remarksExceeded ? 'text-red-500 font-semibold' : 'text-gray-500'" class="ml-auto">
                                    {{ remarksCount }}/{{ limits.remarks }}
                                </small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-4">
                            <Button
                                label="Cancel"
                                severity="secondary"
                                outlined
                                @click="cancel"
                                type="button"
                            />
                            <Button
                                label="Add"
                                type="submit"
                                :loading="form.processing"
                            />
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </div>
</template>

<style scoped>
/* Additional styling if needed */
</style>
