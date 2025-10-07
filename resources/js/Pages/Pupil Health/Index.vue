<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Button from 'primevue/button';

const page = usePage();
const currentYear = new Date().getFullYear();
const schoolYear = ref('All');
const schoolYears = ref([
    'All',
    `${currentYear}-${currentYear + 1}`,
    `${currentYear - 1}-${currentYear}`,
    `${currentYear - 2}-${currentYear - 1}`,
    `${currentYear - 3}-${currentYear - 2}`
]);
const gradeLevel = ref('All');
const gradeLevels = ref(['All', 'K-2', 1, 2, 3, 4, 5, 6]);
const searchQuery = ref('');

// Computed property for pupil records with flexible filtering
const pupilRecords = computed(() => {
    let records = page.props.students || [];

    // Filter by grade level if not 'All'
    if (gradeLevel.value !== 'All') {
        records = records.filter(student => {
            const studentGrade = student.grade_level?.toString();
            const filterGrade = gradeLevel.value.toString();
            
            // Handle both formats: "Grade 1" and "1", "Kinder 2" and "K-2"
            if (filterGrade === 'K-2') {
                return studentGrade === 'Kinder 2' || studentGrade === 'K-2';
            } else {
                // For numeric grades, check both "Grade X" and "X" formats
                return studentGrade === filterGrade || 
                       studentGrade === `Grade ${filterGrade}`;
            }
        });
    }

    // Filter by school year if not 'All'
    if (schoolYear.value !== 'All') {
        records = records.filter(student => student.school_year === schoolYear.value);
    }

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        records = records.filter(student =>
            student.full_name?.toLowerCase().includes(query) ||
            student.lrn?.toLowerCase().includes(query)
        );
    }

    return records;
});

// Method to view health record for a student
const viewHealthExam = (student) => {
    const recordType = student.health_record;

    switch(recordType) {
        case 'Health Examination':
            router.visit(route('pupil-health.health-exam.show', { student: student.id }));
            break;
        case 'Oral Health Examination':
            router.visit(route('pupil-health.oral-health.show', { student: student.id }));
            break;
        case 'Incident':
            router.visit(route('pupil-health.incident', { student: student.id }));
            break;
        default:
            console.error('Unknown record type:', recordType);
    }
};
</script>

<template>
    <div class="p-4 bg-gray-50 rounded-lg">
        <!-- Filters and Search -->
        <div class="grid grid-cols-12 gap-4 mb-6">
            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                <Select
                    v-model="gradeLevel"
                    :options="gradeLevels"
                    placeholder="Select Grade"
                    class="w-full"
                />
            </div>
            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">School Year</label>
                <Select
                    v-model="schoolYear"
                    :options="schoolYears"
                    placeholder="Select Year"
                    class="w-full"
                />
            </div>
            <div class="col-span-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Students</label>
                <IconField iconPosition="left">
                    <InputIcon class="pi pi-search text-gray-400" />
                    <InputText
                        v-model="searchQuery"
                        placeholder="Search by name or LRN"
                        class="w-full pl-8 py-2 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm"
                    />
                </IconField>
            </div>
        </div>

        <!-- Student List Section -->
        <div class="mb-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="pi pi-users mr-3 text-indigo-600"></i>
                Student List
                <span class="ml-3 text-sm text-gray-500 font-normal">
                    ({{ pupilRecords.length }} students)
                </span>
            </h2>
        </div>

        <!-- Pupil Health Table -->
        <DataTable
            :value="pupilRecords"
            paginator
            :rows="5"
            tableStyle="min-width: 50rem"
            class="shadow-md rounded-lg"
        >
            <Column field="name" header="Name" class="font-semibold"></Column>
            <Column field="grade_level" header="Grade Level"></Column>
            <Column field="lrn" header="LRN"></Column>
            <Column field="school_year" header="School Year"></Column>
            <Column field="health_record" header="Health Record">
                <template #body="slotProps">
                    <Select
                        v-model="slotProps.data.health_record"
                        :options="['Health Examination', 'Oral Health Examination', 'Incident']"
                        class="w-60" placeholder="Select Record Type"
                    />
                </template>
            </Column>
            <Column header="Actions">
                <template #body="slotProps">
                    <Button icon="pi pi-eye" class="p-button-text p-button-rounded" @click="viewHealthExam(slotProps.data)" />
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<style scoped>
/* Additional custom styling can be added here if needed */
</style>
