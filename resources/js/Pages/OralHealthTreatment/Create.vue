<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Enhanced Header -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 mb-8 backdrop-blur-sm">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <div class="w-14 h-14 bg-gradient-to-r from-emerald-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="pi pi-plus text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent mb-2">Create Oral Health Treatment</h1>
                            <p class="text-slate-600 font-medium">Add new dental treatment for {{ student.full_name }}</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-slate-600 font-semibold">Student:</span>
                                <div class="font-bold text-slate-900">{{ student.full_name }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Grade:</span>
                                <div class="font-bold text-slate-900">{{ student.grade_level }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">LRN:</span>
                                <div class="font-mono font-bold text-slate-900">{{ student.lrn || 'Not Available' }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Section:</span>
                                <div class="font-bold text-slate-900">{{ student.section || 'Not Assigned' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

    <!-- Confirmation Dialog -->
    <ConfirmDialog />
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import Card from 'primevue/card';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import { useToastStore } from '@/Stores/toastStore';
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css';

const { student } = usePage().props;

// Toast store
const confirm = useConfirm();
const { showSuccess, showError } = useToastStore();

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

console.log('=== ORAL HEALTH TREATMENT CREATE FORM INIT ===');
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
    // Show confirmation dialog
    confirm.require({
        message: 'Are you sure you want to create this oral health treatment record?',
        header: 'Confirm Creation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Create Record',
            severity: 'success'
        },
        accept: () => {
            // Proceed with form submission
            submitForm()
        }
    })
}

const submitForm = () => {
    // Get the current grade from sessionStorage to ensure we have the latest value
    const currentGrade = sessionStorage.getItem(`currentGrade_${student.id}`) || selectedGrade.value;
    const gradeLevel = currentGrade.replace('Grade ', '');

    // Ensure grade_level is not empty or null
    if (!gradeLevel || gradeLevel === 'undefined' || gradeLevel === 'null' || gradeLevel.trim() === '') {
        console.error('Grade level is invalid:', gradeLevel);
        console.error('Current grade from session:', currentGrade);
        console.error('Selected grade ref:', selectedGrade.value);
        showError('Invalid Grade Level', 'Invalid grade level. Please refresh and try again.');
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

    console.log('=== ORAL HEALTH TREATMENT FORM SUBMISSION DEBUG ===');
    console.log('Current Grade from session:', currentGrade);
    console.log('Selected Grade ref:', selectedGrade.value);
    console.log('Final Grade Level being saved:', form.grade_level);
    console.log('Grade Level type:', typeof form.grade_level);
    console.log('Formatted Date:', form.date);
    console.log('Complete Form Data:', form.data());
    console.log('==========================================');

    form.post(route('oral-health-treatment.store'), {
        onSuccess: () => {
            console.log('Oral health treatment created successfully');
            showSuccess('Success', 'Oral health treatment record created successfully!');
            // Use Inertia redirect instead of window.location to prevent full page reload
            // The backend redirect will handle this properly
        },
        onError: (errors) => {
            console.error('Oral health treatment submission errors:', errors);
            showError('Submission Error', 'Failed to create oral health treatment record. Please try again.');
        }
    });
}

const cancel = () => {
    window.history.back();
};
</script>
