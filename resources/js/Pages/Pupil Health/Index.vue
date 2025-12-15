<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Button from 'primevue/button';
import { useToastStore } from '@/Stores/toastStore';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';
// Import component styles
import '../../../css/pages/PupilHealthIndex.css';

// Toast store
const { showError } = useToastStore();

const page = usePage();
const isLoading = ref(true);
const currentYear = new Date().getFullYear();
const currentSchoolYear = computed(() => page.props.currentSchoolYear || `${currentYear}-${currentYear + 1}`);

// Set loading to false after component mounts
onMounted(() => {
    // Update school years from students data
    updateSchoolYears();

    // Simulate loading delay for skeleton effect
    setTimeout(() => {
        isLoading.value = false;
    }, 500);

    // Debug: Check what students we have
    const students = page.props.students || [];
    const uniqueYears = [...new Set(students.map(s => s.school_year).filter(Boolean))];
    console.log('Students loaded:', students.length);
    console.log('Unique school years found:', uniqueYears);
    console.log('Unique school years count:', uniqueYears.length);
    console.log('School years dropdown:', schoolYears.value);
    console.log('School years dropdown length:', schoolYears.value.length);
    console.log('Sample students:', students.slice(0, 5).map(s => ({
        name: s.full_name,
        year: s.school_year,
        active: s.is_active
    })));
});

// Get user role from props
const userRole = computed(() => page.props.userRole);

// Applied filters (only updated when Apply Filters is clicked)
const appliedSchoolYear = ref(null); // null means no filter applied
const appliedGradeLevel = ref(null); // null means no filter applied
const appliedSearchQuery = ref('');

// Temporary filters (UI state)
const tempSchoolYear = ref('All');
const tempGradeLevel = ref('All');
const tempSearchQuery = ref('');

// Get unique school years from students data - use ref for reactivity
const schoolYears = ref(['All']);

// Update school years when component mounts or students change
const updateSchoolYears = () => {
    const students = page.props.students || [];
    if (students && students.length > 0) {
        const uniqueYears = [...new Set(students.map(s => s.school_year).filter(Boolean))];
        uniqueYears.sort().reverse(); // Sort descending (newest first)
        schoolYears.value = ['All', ...uniqueYears];
        console.log('Updated school years:', schoolYears.value);
    } else {
        // Fallback to default years if no students loaded yet
        schoolYears.value = [
            'All',
            `${currentYear}-${currentYear + 1}`,
            `${currentYear - 1}-${currentYear}`,
            `${currentYear - 2}-${currentYear - 1}`,
            `${currentYear - 3}-${currentYear - 2}`,
            `${currentYear - 4}-${currentYear - 3}`,
            `${currentYear - 5}-${currentYear - 4}`,
            `${currentYear - 6}-${currentYear - 5}`,
            `${currentYear - 7}-${currentYear - 6}`,
            `${currentYear - 8}-${currentYear - 7}`
        ];
    }
};
// Grade levels
const gradeLevels = ref([
    'All',
    'Kinder 2',
    'Grade 1',
    'Grade 2',
    'Grade 3',
    'Grade 4',
    'Grade 5',
    'Grade 6',
    'Non-Graded'
]);


// Store record type selections separately (not in the data object)
const recordTypeSelections = ref({});

// Apply all filters
const applyFilters = () => {
    appliedGradeLevel.value = tempGradeLevel.value === 'All' ? null : tempGradeLevel.value;
    appliedSchoolYear.value = tempSchoolYear.value === 'All' ? null : tempSchoolYear.value;
    appliedSearchQuery.value = tempSearchQuery.value;
};

// Clear all filters
const clearFilters = () => {
    tempGradeLevel.value = 'All';
    tempSchoolYear.value = 'All';
    tempSearchQuery.value = '';
    appliedGradeLevel.value = null;
    appliedSchoolYear.value = null;
    appliedSearchQuery.value = '';
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

    // Filter by grade level if filter is applied (not null)
    if (appliedGradeLevel.value) {
        records = records.filter(student => {
            const studentGrade = student.grade_level?.toString();
            const filterGrade = appliedGradeLevel.value.toString();

            return studentGrade === filterGrade;
        });
    }

    // Filter by school year if filter is applied (not null)
    if (appliedSchoolYear.value) {
        records = records.filter(student => student.school_year === appliedSchoolYear.value);
    }

    // Filter by search query
    if (appliedSearchQuery.value) {
        const query = appliedSearchQuery.value.toLowerCase();
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
        showError('Record Type Required', 'Please select a record type first');
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
                <!-- Search Input -->
                <div class="search-field-container">
                    <label class="field-label">Search Students</label>
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

                <!-- Quick Search Button -->
                <Button
                    label="Search"
                    icon="pi pi-search"
                    @click="applyFilters"
                    class="search-button lg:hidden"
                />

                <!-- Filter Dropdowns -->
                <div class="filter-field-container">
                    <label class="field-label">Grade Level</label>
                    <Select
                        v-model="tempGradeLevel"
                        :options="gradeLevels"
                        placeholder="All Grades"
                        class="w-full"
                    />
                </div>

                <div class="filter-field-container">
                    <label class="field-label">School Year</label>
                    <Select
                        v-model="tempSchoolYear"
                        :options="schoolYears"
                        placeholder="All Years"
                        class="w-full"
                    />
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
                    <Button
                        label="Apply Filters"
                        icon="pi pi-check"
                        @click="applyFilters"
                        severity="success"
                        class="hidden lg:flex"
                    />
                    <Button
                        label="Clear All"
                        icon="pi pi-times"
                        @click="clearFilters"
                        severity="secondary"
                        outlined
                        class="w-full sm:w-auto"
                    />
                </div>
            </div>
        </div>

        <!-- Student List Section -->
        <div class="student-list-header">
            <h2 class="student-list-title">
                <i class="pi pi-users mr-2 sm:mr-3 text-indigo-600"></i>
                <span>Pupil Health Records</span>
                <span class="student-count">
                    {{ pupilRecords.length }} students
                </span>
            </h2>
        </div>

        <!-- Skeleton Loader -->
        <SkeletonLoader
            v-if="isLoading"
            type="table"
            :rows="10"
            :columns="7"
        />

        <!-- Pupil Health Table -->
        <DataTable
            v-else
            :value="pupilRecords"
            paginator
            :rows="10"
            :rowsPerPageOptions="[10, 25, 50]"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} students"
            class="pupil-table"
            responsiveLayout="scroll"
            paginatorPosition="top"
            scrollable
            scrollHeight="500px"
            :loading="false"
        >
            <Column field="formatted_name" header="Student Name" sortable class="name-column" style="min-width: 180px">
                <template #body="slotProps">
                    <div class="font-medium text-gray-900">
                        {{ slotProps.data.formatted_name }}
                    </div>
                </template>
            </Column>

            <Column field="grade_sort_value" header="Grade" sortable style="min-width: 100px">
                <template #body="slotProps">
                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-sm font-medium">
                        {{ slotProps.data.grade_level }}
                    </span>
                </template>
            </Column>

            <Column field="section" header="Section" sortable style="min-width: 80px" class="hidden sm:table-cell">
                <template #body="slotProps">
                    {{ slotProps.data.section || '-' }}
                </template>
            </Column>

            <Column field="lrn" header="LRN" sortable style="min-width: 120px" class="hidden md:table-cell">
                <template #body="slotProps">
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                        {{ slotProps.data.lrn || 'N/A' }}
                    </code>
                </template>
            </Column>

            <Column field="school_year" header="School Year" sortable style="min-width: 110px" class="hidden lg:table-cell">
                <template #body="slotProps">
                    {{ slotProps.data.school_year || 'N/A' }}
                </template>
            </Column>



            <Column header="Health Record" style="min-width: 160px">
                <template #body="slotProps">
                    <Select
                        v-model="recordTypeSelections[slotProps.data.id]"
                        :options="['Health Examination', 'Oral Health Examination', 'Incident']"
                        placeholder="Select Type"
                        class="record-select"
                    />
                </template>
            </Column>

            <Column header="Actions" style="min-width: 120px">
                <template #body="slotProps">
                    <Button
                        label="View"
                        icon="pi pi-eye"
                        @click="viewHealthExam(slotProps.data)"
                        severity="info"
                        outlined
                        size="small"
                        class="px-3 py-2"
                    />
                </template>
            </Column>
        </DataTable>
    </div>
</template>
