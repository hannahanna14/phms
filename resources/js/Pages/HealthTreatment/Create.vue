<script setup>
import { ref } from 'vue';
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
    const sessionGrade = sessionStorage.getItem('selectedGrade');
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

const selectedGrade = getGradeContext();
const schoolYear = getSchoolYearForGrade(selectedGrade);

const form = useForm({
    student_id: student.id,
    date: new Date(),
    title: '',
    chief_complaint: '',
    treatment: '',
    remarks: '',
    status: 'pending',
    grade_level: selectedGrade,
    school_year: schoolYear
});

const submit = () => {
    console.log('Health Treatment Form Data:', form.data());

    form.post(route('health-treatment.store'), {
        onSuccess: () => {
            console.log('Health treatment created successfully');
            // Redirect back to student health examination page with grade context
            window.location.href = `/pupil-health/health-examination/${student.id}?grade=${selectedGrade}`;
        },
        onError: (errors) => {
            console.error('Health treatment submission errors:', errors);
        }
    });
};

const cancel = () => {
    window.history.back();
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

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Title
                            </label>
                            <InputText
                                v-model="form.title"
                                placeholder="Title"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.title }"
                            />
                            <small v-if="form.errors.title" class="text-red-500">{{ form.errors.title }}</small>
                        </div>

                        <!-- Chief Complaint and Treatment -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Chief Complaint
                                </label>
                                <Textarea
                                    v-model="form.chief_complaint"
                                    placeholder="Enter text"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.chief_complaint }"
                                />
                                <small v-if="form.errors.chief_complaint" class="text-red-500">{{ form.errors.chief_complaint }}</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Treatment
                                </label>
                                <Textarea
                                    v-model="form.treatment"
                                    placeholder="Enter text"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.treatment }"
                                />
                                <small v-if="form.errors.treatment" class="text-red-500">{{ form.errors.treatment }}</small>
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
                                :class="{ 'p-invalid': form.errors.remarks }"
                            />
                            <small v-if="form.errors.remarks" class="text-red-500">{{ form.errors.remarks }}</small>
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
