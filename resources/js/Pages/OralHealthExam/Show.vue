
<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import Dropdown from 'primevue/dropdown';

// Destructure page props
const { student, oralHealth } = usePage().props;

// Log student data for debugging
console.log('Student Data:', student);

// Dynamic grade levels based on student's current grade
const gradeLevels = computed(() => {
    // Predefined list of standard grade levels
    const standardGrades = [
        'Kinder 1', 'Kinder 2',
        'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'
    ];

    // Find the index of the current student's grade
    const currentIndex = standardGrades.indexOf(student.grade_level);

    // If the current grade is not found, return full grade range
    if (currentIndex === -1) {
        console.warn(`Grade not found: ${student.grade_level}`);
        return standardGrades;
    }

    // Create a range of grades around the current grade
    const startIndex = Math.max(0, currentIndex - 2);
    const endIndex = Math.min(standardGrades.length - 1, currentIndex + 2);

    return standardGrades.slice(startIndex, endIndex + 1);
});

const schoolYears = ref(['2023-2024', '2024-2025']);
const selectedGrade = ref(student.grade_level); 
const selectedYear = ref('2023-2024');
const healthRecords = ref([]);

// Watch for changes and log
watch(selectedGrade, (newValue) => {
    console.log('Selected Grade Changed:', newValue);
});

onMounted(() => {
    console.log('Initial Selected Grade:', selectedGrade.value);
    console.log('Available Grade Levels:', gradeLevels.value);
});
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="pi pi-tooth mr-3 text-indigo-600"></i>
                    Oral Health Examination Report
                </h1>
                <Link href="/pupil-health" class="no-underline">
                    <Button 
                        label="Back to Pupil Health" 
                        icon="pi pi-arrow-left" 
                        outlined 
                        severity="secondary"
                    />
                </Link>
            </div>

            <!-- Student Information Card -->
            <Card class="mb-6">
                <template #content>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                                Student Details
                            </h2>
                            <div class="space-y-3">
                                <p class="text-lg"><strong class="text-xl mr-2">Name:</strong> {{ student.full_name }}</p>
                                <p class="text-lg"><strong class="text-xl mr-2">LRN:</strong> {{ student.lrn }}</p>
                                <p class="text-lg"><strong class="text-xl mr-2">Grade Level:</strong> {{ student.grade_level }}</p>
                                <p class="text-lg"><strong class="text-xl mr-2">School Year:</strong> {{ student.school_year }}</p>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                                Additional Information
                            </h2>
                            <div class="space-y-3">
                                <p class="text-lg"><strong class="text-xl mr-2">Age:</strong> {{ student.age }} years</p>
                                <p class="text-lg"><strong class="text-xl mr-2">Sex:</strong> {{ student.sex }}</p>
                                <p class="text-lg"><strong class="text-xl mr-2">Section:</strong> {{ student.section || 'Not Assigned' }}</p>
                                <p class="text-lg"><strong class="text-xl mr-2">Status:</strong> 
                                    <span class="text-green-600 font-semibold">Active</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Dropdowns -->
            <div class="flex gap-4 mt-4">
                <Dropdown 
                    v-model="selectedGrade" 
                    :options="gradeLevels" 
                    class="w-40" 
                />
                <Dropdown 
                    v-model="selectedYear" 
                    :options="schoolYears" 
                    class="w-40" 
                />
            </div>

            <!-- Oral Health Examination -->
            <div class="border rounded-lg bg-white shadow-md mt-4">
                <div class="bg-blue-700 text-white p-2">Oral Health Examination</div>
                <div class="p-4 text-center" v-if="!oralHealth">
                    <p>No Oral Health Record Found</p>
                    <Button label="Add" class="mt-2 p-button-primary" />
                </div>
                <div v-else class="p-4">
                    <!-- Add oral health specific details here -->
                    <p>Oral Health Examination Details</p>
                </div>
            </div>

            <!-- Dental Treatment -->
            <div class="border rounded-lg bg-white shadow-md mt-4">
                <div class="bg-blue-700 text-white p-2 flex justify-between items-center">
                    <span>Dental Treatment</span>
                    <Button icon="pi pi-plus" class="p-button-text text-white" />
                </div>
                <div class="p-4">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Dental Procedure</th>
                                <th class="text-left">Treatment</th>
                                <th class="text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center py-4">Empty</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Additional custom styling can be added here if needed */
</style>