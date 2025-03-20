<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import Dropdown from 'primevue/dropdown';

const { student, healthExamination } = usePage().props;

const gradeLevels = computed(() => {
    const standardGrades = ['Kinder 1', 'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
    return standardGrades.includes(student.grade_level) ? standardGrades : [...standardGrades, student.grade_level];
});

const schoolYears = computed(() => {
    const years = ['2023-2024', '2024-2025'];
    return years.includes(student.school_year) ? years : [...years, student.school_year];
});

const selectedGrade = ref(student.grade_level || gradeLevels.value[0]);
const selectedYear = ref(student.school_year || schoolYears.value[0]);
const healthRecords = ref([]);
const recordId = ref('');
const recordDetails = ref({});

const fetchRecord = async (id) => {
    try {
        const response = await axios.get(`/api/health-examination/${id}`);
        recordDetails.value = response.data;
    } catch (error) {
        console.error('Error fetching record details:', error);
    }
};

onMounted(() => {
    recordId.value = usePage().props.recordId;
    fetchRecord(recordId.value);
});
</script>

<template>
    <div class="p-4 bg-gray-100 min-h-screen flex justify-center">
        <div class="w-full max-w-3xl">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-file-medical mr-2 text-indigo-600"></i>
                    Health Examination Report
                </h1>
                <Link href="/pupil-health" class="no-underline">
                    <Button label="Back" icon="pi pi-arrow-left" outlined severity="secondary" class="text-sm" />
                </Link>
            </div>

            <Card class="mb-4">
                <template #content>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 mb-2">Student Details</h2>
                            <p><strong>Name:</strong> {{ student.full_name }}</p>
                            <p><strong>LRN:</strong> {{ student.lrn }}</p>
                            <p><strong>Grade Level:</strong> {{ student.grade_level }}</p>
                            <p><strong>School Year:</strong> {{ student.school_year }}</p>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 mb-2">Additional Information</h2>
                            <p><strong>Age:</strong> {{ student.age }} years</p>
                            <p><strong>Sex:</strong> {{ student.sex }}</p>
                            <p><strong>Section:</strong> {{ student.section || 'Not Assigned' }}</p>
                            <p><strong>Status:</strong> <span class="text-green-600 font-semibold">Active</span></p>
                        </div>
                    </div>
                </template>
            </Card>

            <div class="flex gap-2">
                <Dropdown v-model="selectedGrade" :options="gradeLevels" class="w-32 text-sm" />
                <Dropdown v-model="selectedYear" :options="schoolYears" class="w-32 text-sm" placeholder="Select Year" />
            </div>

            <div class="border rounded-lg bg-white shadow mt-4">
                <div class="bg-blue-700 text-white p-2 text-sm">Pupil Health Examination</div>
                <div class="p-3 text-center text-sm" v-if="healthRecords.length === 0">
                    <p>No Record Found</p>
                    <Button label="Add" class="mt-2 text-xs" @click="$inertia.visit(route('health-examination.create', student.id))" />
                </div>
            </div>

            <div class="border rounded-lg bg-white shadow mt-4">
                <div class="bg-blue-700 text-white p-2 flex justify-between items-center text-sm">
                    <span>Health Treatment</span>
                    <Button icon="pi pi-plus" class="p-button-text text-white text-xs" />
                </div>
                <div class="p-3">
                    <table class="w-full text-xs">
                        <thead>
                            <tr>
                                <th class="text-left">Chief Complaint</th>
                                <th class="text-left">Treatment</th>
                                <th class="text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center py-2">No records available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Additional compact styling can be added here if needed */
</style>
