<script setup>
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useRouter } from 'vue-router';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const page = usePage();
const router = useRouter();
const currentYear = new Date().getFullYear();

// Record Generation Options
const recordTypes = [
    { 
        id: 1,
        name: 'Health Examination Report', 
        description: 'Comprehensive health assessment for students',
        icon: 'pi pi-file-medical',
        requiresStudentSelection: true
    },
    { 
        id: 2,
        name: 'Nutritional Status Report', 
        description: 'Detailed analysis of student nutritional health',
        icon: 'pi pi-chart-bar',
        requiresStudentSelection: true
    },
    { 
        id: 3,
        name: 'Dental Health Record', 
        description: 'Oral health examination and tracking',
        icon: 'pi pi-heart',
        requiresStudentSelection: true
    },
    { 
        id: 4,
        name: 'Immunization History', 
        description: 'Vaccination and immunization tracking',
        icon: 'pi pi-shield',
        requiresStudentSelection: true
    },
    { 
        id: 5,
        name: 'Incident Report', 
        description: 'Detailed log of student health-related incidents',
        icon: 'pi pi-exclamation-triangle',
        requiresStudentSelection: false
    }
];

// Form State
const selectedRecordType = ref(null);
const schoolYear = ref(`${currentYear - 1}-${currentYear}`);
const schoolYears = ref([
    `${currentYear}-${currentYear + 1}`,
    `${currentYear - 1}-${currentYear}`,
    `${currentYear - 2}-${currentYear - 1}`,
    `${currentYear - 3}-${currentYear - 2}`
]);
const gradeLevel = ref('All');
const gradeLevels = ref(['All', 'K-1', 'K-2', 1, 2, 3, 4, 5, 6]);

// Student Selection
const searchQuery = ref('');
const selectedStudents = ref([]);
const isStudentSelectionRequired = ref(false);

// Watch for changes in record type to determine student selection requirement
watch(selectedRecordType, (newType) => {
    if (newType) {
        isStudentSelectionRequired.value = newType.requiresStudentSelection;
        
        // Reset student selection if not required
        if (!isStudentSelectionRequired.value) {
            selectedStudents.value = [];
        }
    }
});

// Computed property for filtered students
const filteredStudents = computed(() => {
    let students = page.props.students || [];

    // Filter by grade level
    if (gradeLevel.value !== 'All') {
        students = students.filter(student => 
            student.grade_level?.toString() === gradeLevel.value.toString()
        );
    }

    // Filter by school year
    if (schoolYear.value !== 'All') {
        students = students.filter(student => student.school_year === schoolYear.value);
    }

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        students = students.filter(student => 
            student.name.toLowerCase().includes(query) ||
            student.lrn?.toLowerCase().includes(query)
        );
    }

    return students;
});

// Method to handle record type selection
const selectRecordType = (type) => {
    selectedRecordType.value = type;
};

// Method to view health examination for a student
const viewHealthExam = (student) => {
    // Use Inertia to navigate to the health exam show page
    // Ensure the route matches the backend route name
    router.visit(route('health-examination.show', { student: student.id }));
};

// Generate Record Method
const generateRecord = () => {
    // Validation
    if (!selectedRecordType.value) {
        alert('Please select a record type');
        return;
    }

    // Validate student selection if required
    if (isStudentSelectionRequired.value && selectedStudents.value.length === 0) {
        alert('Please select at least one student');
        return;
    }

    // Prepare generation parameters
    const params = {
        recordType: selectedRecordType.value.name,
        schoolYear: schoolYear.value,
        gradeLevel: gradeLevel.value,
        students: selectedStudents.value.map(student => student.id)
    };

    // TODO: Implement actual record generation logic
    console.log('Generating Record:', params);
    // Typically, this would involve an API call to your backend
};
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="pi pi-file-export mr-3 text-indigo-600"></i>
                Generate Health Records
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Record Type Selection -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        Select Record Type
                    </h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div 
                            v-for="type in recordTypes" 
                            :key="type.id"
                            @click="selectRecordType(type)"
                            class="cursor-pointer p-4 rounded-lg transition-all duration-300 hover:shadow-lg border"
                            :class="{
                                'border-2 border-indigo-500 bg-indigo-50': selectedRecordType?.id === type.id,
                                'border-gray-200 hover:border-gray-300': selectedRecordType?.id !== type.id
                            }"
                        >
                            <div class="flex flex-col items-center text-center">
                                <i :class="[
                                    type.icon, 
                                    'text-4xl mb-3', 
                                    selectedRecordType?.id === type.id 
                                        ? 'text-indigo-600' 
                                        : 'text-gray-500'
                                ]"></i>
                                <h3 class="font-semibold text-gray-800">{{ type.name }}</h3>
                                <p class="text-sm text-gray-500 mt-2">{{ type.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Record Generation Options -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        Generation Parameters
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                School Year
                            </label>
                            <Dropdown 
                                v-model="schoolYear" 
                                :options="schoolYears" 
                                placeholder="Select School Year" 
                                class="w-full" 
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Grade Level
                            </label>
                            <Dropdown 
                                v-model="gradeLevel" 
                                :options="gradeLevels" 
                                placeholder="Select Grade Level" 
                                class="w-full"
                            />
                        </div>

                        <Button 
                            label="Generate Record" 
                            icon="pi pi-file-export"
                            @click="generateRecord"
                            class="w-full mt-4"
                            :disabled="!selectedRecordType"
                        />
                    </div>
                </div>
            </div>

            <!-- Student Selection Section -->
            <div 
                v-if="isStudentSelectionRequired" 
                class="mt-8 bg-white p-6 rounded-lg shadow-md"
            >
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="pi pi-users mr-2 text-blue-500"></i>
                    Select Students for Record
                    <span class="ml-3 text-sm text-gray-500 font-normal">
                        ({{ selectedStudents.length }} selected)
                    </span>
                </h3>

                <!-- Search and Filters -->
                <div class="mb-4 flex space-x-4">
                    <InputText 
                        v-model="searchQuery" 
                        placeholder="Search students by name or LRN" 
                        class="flex-grow" 
                    />
                </div>

                <!-- Student Selection Table -->
                <DataTable 
                    :value="filteredStudents" 
                    :selection="selectedStudents"
                    @selection-change="selectedStudents = $event.value"
                    paginator 
                    :rows="5"
                    tableStyle="min-width: 50rem"
                    class="shadow-md rounded-lg"
                >
                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                    <Column field="name" header="Name"></Column>
                    <Column field="grade_level" header="Grade Level"></Column>
                    <Column field="lrn" header="LRN"></Column>
                    <Column field="school_year" header="School Year"></Column>
                    <Column header="Actions" style="width: 10%" v-if="selectedRecordType?.name === 'Health Examination Report'">
                        <template #body="{ data }">
                            <Button 
                                icon="pi pi-eye" 
                                class="p-button-rounded p-button-info p-button-outlined"
                                @click="viewHealthExam(data)"
                                title="View Health Examination"
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="pi pi-info-circle mr-2 text-blue-500"></i>
                    How to Generate Records
                </h3>
                <ol class="list-decimal list-inside text-gray-600 space-y-2">
                    <li>Select the type of health record you want to generate</li>
                    <li>Choose a school year (optional)</li>
                    <li>Select a grade level (optional)</li>
                    <li v-if="isStudentSelectionRequired">Search and select specific students</li>
                    <li>Click "Generate Record" to create the report</li>
                </ol>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Additional custom styling can be added here if needed */
</style>
