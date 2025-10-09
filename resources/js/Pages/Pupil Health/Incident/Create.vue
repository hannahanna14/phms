<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-blue-700 mb-2">Pupil Incident Report</h1>
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

            <!-- Incident Form -->
            <Card>
                <template #content>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="pi pi-calendar mr-1"></i>
                                    Date
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

                        <!-- Complaint and Actions Taken -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Complaint
                                </label>
                                <Textarea 
                                    v-model="form.complaint" 
                                    placeholder="Enter text"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.complaint }"
                                />
                                <small v-if="form.errors.complaint" class="text-red-500">{{ form.errors.complaint }}</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Actions Taken
                                </label>
                                <Textarea 
                                    v-model="form.actions_taken" 
                                    placeholder="Enter text"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.actions_taken }"
                                />
                                <small v-if="form.errors.actions_taken" class="text-red-500">{{ form.errors.actions_taken }}</small>
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
                                type="button"
                                class="!bg-green-600 !text-white !border-green-600 hover:!bg-green-700"
                                @click="submitForm"
                            />
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import Select from 'primevue/select';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';

const { student } = usePage().props;

const form = useForm({
    student_id: student.id,
    date: '',
    complaint: '',
    actions_taken: '',
    grade_level: '',
    school_year: ''
});

// Initialize grade context
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search)
    const gradeFromUrl = urlParams.get('grade')
    const gradeFromSession = sessionStorage.getItem('selectedGrade')
    
    form.grade_level = gradeFromUrl || gradeFromSession || student.grade_level || 'Grade 6'
    form.school_year = getSchoolYearForGrade(form.grade_level)
})

const getSchoolYearForGrade = (grade) => {
    const gradeToYear = {
        'Kinder 2': '2023-2024', 
        'Grade 1': '2024-2025',
        'Grade 2': '2023-2024',
        'Grade 3': '2022-2023',
        'Grade 4': '2021-2022',
        'Grade 5': '2020-2021',
        'Grade 6': '2019-2020'
    }
    return gradeToYear[grade] || '2024-2025'
}

const statusOptions = [
    { label: 'Pending', value: 'pending' },
    { label: 'In Progress', value: 'in_progress' },
    { label: 'Resolved', value: 'resolved' },
    { label: 'Closed', value: 'closed' }
]

const submitForm = () => {
    console.log('Submit button clicked!');
    console.log('Form data before submit:', form.data());
    console.log('Grade level:', form.grade_level);
    console.log('School year:', form.school_year);
    
    // Check if required fields are filled
    if (!form.date || !form.complaint || !form.actions_taken) {
        console.error('Missing required fields');
        return;
    }
    
    form.post('/pupil-health/incident', {
        onSuccess: (response) => {
            console.log('Success response:', response);
            // Redirect back to incident page with grade context
            const grade = new URLSearchParams(window.location.search).get('grade') || sessionStorage.getItem('selectedGrade');
            window.location.href = `/pupil-health/incident/${student.id}?grade=${grade}`;
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
        },
        onFinish: () => {
            console.log('Form submission finished');
        }
    });
};

const cancel = () => {
    window.history.back();
};
</script>

<style scoped>
/* Additional styling if needed */
</style>
