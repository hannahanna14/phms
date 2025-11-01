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
// Import component styles
import '../../../css/pages/PupilHealthIndex.css';

const page = usePage();
const currentYear = new Date().getFullYear();

// Get user role from props
const userRole = computed(() => page.props.userRole);

// Applied filters (after clicking Apply)
const schoolYear = ref('All');
const gradeLevel = ref('All');
const searchQuery = ref('');

// Temporary filters (before clicking Apply)
const tempSchoolYear = ref('All');
const tempGradeLevel = ref('All');
const tempSearchQuery = ref('');

const schoolYears = ref([
    'All',
    `${currentYear}-${currentYear + 1}`,
    `${currentYear - 1}-${currentYear}`,
    `${currentYear - 2}-${currentYear - 1}`,
    `${currentYear - 3}-${currentYear - 2}`
]);
const gradeLevels = ref(['All', 'K-2', 1, 2, 3, 4, 5, 6]);

// Store record type selections separately (not in the data object)
const recordTypeSelections = ref({});

// Apply all filters
const applyFilters = () => {
    gradeLevel.value = tempGradeLevel.value;
    schoolYear.value = tempSchoolYear.value;
    searchQuery.value = tempSearchQuery.value;
};

// Clear all filters
const clearFilters = () => {
    tempGradeLevel.value = 'All';
    tempSchoolYear.value = 'All';
    tempSearchQuery.value = '';
    gradeLevel.value = 'All';
    schoolYear.value = 'All';
    searchQuery.value = '';
};

// Helper function to format name as "Lastname, Firstname"
const formatName = (fullName) => {
    if (!fullName) return 'N/A';
    const parts = fullName.trim().split(' ');
    if (parts.length < 2) return fullName;
    
    const lastName = parts[parts.length - 1];
    const firstName = parts.slice(0, -1).join(' ');
    return `${lastName}, ${firstName}`;
};

// Helper function to convert grade level to sortable number
const getGradeSortValue = (gradeLevel) => {
    if (!gradeLevel) return 0;
    const grade = gradeLevel.toString();
    
    if (grade.includes('Kinder') || grade.includes('K-')) return 0;
    
    // Extract numeric value from "Grade 1", "Grade 2", etc.
    const match = grade.match(/\d+/);
    return match ? parseInt(match[0]) : 0;
};

// Computed property for pupil records with flexible filtering
const pupilRecords = computed(() => {
    let records = page.props.students || [];

    // Add computed fields for sorting
    records = records.map(student => ({
        ...student,
        formatted_name: formatName(student.full_name || student.name),
        grade_sort_value: getGradeSortValue(student.grade_level)
    }));

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
        records = records.filter(student => {
            const nameMatch = (student.full_name || student.name || '')?.toLowerCase().includes(query);
            const lrnMatch = (student.lrn || '')?.toLowerCase().includes(query);
            return nameMatch || lrnMatch;
        });
    }

    return records;
});

// Method to view health record for a student
const viewHealthExam = (student) => {
    const recordType = recordTypeSelections.value[student.id];
    
    if (!recordType) {
        alert('Please select a record type first');
        return;
    }

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
    <div class="pupil-health-container">
        <!-- Search and Filters -->
        <div class="filters-card">
            <div class="filters-row">
                <div class="search-field-container">
                    <label class="field-label">Search Pupils</label>
                    <IconField iconPosition="left" class="w-full">
                        <InputIcon class="pi pi-search text-gray-400" />
                        <InputText
                            v-model="tempSearchQuery"
                            placeholder="Search by name or LRN"
                            class="w-full"
                            @keyup.enter="applyFilters"
                        />
                    </IconField>
                </div>
                <Button 
                    label="Search"
                    icon="pi pi-search" 
                    @click="applyFilters"
                    class="search-button"
                />
                <div class="flex-spacer"></div>
                <div class="filter-field-container">
                    <label class="field-label">Grade Level</label>
                    <Select
                        v-model="tempGradeLevel"
                        :options="gradeLevels"
                        placeholder="All"
                        class="w-full"
                    />
                </div>
                <div class="filter-field-container">
                    <label class="field-label">School Year</label>
                    <Select
                        v-model="tempSchoolYear"
                        :options="schoolYears"
                        placeholder="All"
                        class="w-full"
                    />
                </div>
                <Button 
                    label="Apply" 
                    icon="pi pi-check" 
                    @click="applyFilters"
                    severity="success"
                />
                <Button 
                    label="Clear" 
                    icon="pi pi-times" 
                    @click="clearFilters"
                    severity="secondary"
                    outlined
                />
            </div>
        </div>

        <!-- Student List Section -->
        <div class="student-list-header">
            <h2 class="student-list-title">
                <i class="pi pi-users mr-3 text-indigo-600"></i>
                Pupil List
                <span class="student-count">
                    ({{ pupilRecords.length }} pupils)
                </span>
            </h2>
        </div>

        <!-- Pupil Health Table -->
        <DataTable
            :value="pupilRecords"
            paginator
            :rows="25"
            :rowsPerPageOptions="[10, 25, 50, 100, 200, 500]"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} pupils"
            tableStyle="min-width: 50rem"
            class="pupil-table"
            paginatorPosition="both"
        >
            <Column field="formatted_name" header="Name" sortable class="name-column">
                <template #body="slotProps">
                    {{ slotProps.data.formatted_name }}
                </template>
            </Column>
            <Column field="grade_sort_value" header="Grade Level" sortable>
                <template #body="slotProps">
                    {{ slotProps.data.grade_level }}
                </template>
            </Column>
            <Column field="section" header="Section" sortable></Column>
            <Column field="lrn" header="LRN" sortable></Column>
            <Column field="school_year" header="School Year" sortable></Column>
            <Column field="health_record" header="Health Record">
                <template #body="slotProps">
                    <Select
                        v-model="recordTypeSelections[slotProps.data.id]"
                        :options="['Health Examination', 'Oral Health Examination', 'Incident']"
                        class="record-select" placeholder="Select Record Type"
                    />
                </template>
            </Column>
            <Column header="Actions">
                <template #body="slotProps">
                    <Button 
                        label="View" 
                        icon="pi pi-eye" 
                        @click="viewHealthExam(slotProps.data)" 
                        size="small"
                        severity="info"
                        outlined
                    />
                </template>
            </Column>
        </DataTable>
    </div>
</template>
