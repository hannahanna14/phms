<template>
    <Head title="| Pupil Management" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Pupil Management</h1>
                <p class="text-gray-600">Manage pupil promotions, sections, and teacher assignments</p>
            </div>

            <!-- Action Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            @click="activeTab = 'overview'"
                            :class="[
                                'py-2 px-1 border-b-2 font-medium text-sm',
                                activeTab === 'overview' 
                                    ? 'border-blue-500 text-blue-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            Pupil Overview
                        </button>
                        <button
                            @click="activeTab = 'promotion'"
                            :class="[
                                'py-2 px-1 border-b-2 font-medium text-sm',
                                activeTab === 'promotion' 
                                    ? 'border-blue-500 text-blue-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            Grade Promotion
                        </button>
                        <button
                            @click="activeTab = 'assignments'"
                            :class="[
                                'py-2 px-1 border-b-2 font-medium text-sm',
                                activeTab === 'assignments' 
                                    ? 'border-blue-500 text-blue-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            Teacher Assignments
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Student Overview Tab -->
            <div v-if="activeTab === 'overview'" class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Current Pupils</h2>
                    <p class="text-gray-600 mt-1">View and edit individual pupil information</p>
                </div>
                
                <!-- Search and Filter -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Pupils</label>
                            <div class="flex gap-2">
                                <InputText 
                                    v-model="searchQuery" 
                                    placeholder="Search by name or LRN..."
                                    class="w-full"
                                    @keyup.enter="applyFilters"
                                />
                                <Button 
                                    label="Search"
                                    icon="pi pi-search" 
                                    @click="applyFilters"
                                    severity="secondary"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                            <Select 
                                v-model="filterGrade" 
                                :options="['All', ...gradeLevels]" 
                                placeholder="All Grades"
                                class="w-full"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                            <Select 
                                v-model="filterSection" 
                                :options="['All', ...sections]" 
                                placeholder="All Sections"
                                class="w-full"
                            />
                        </div>
                        <div class="flex items-end">
                            <Button 
                                @click="clearFilters" 
                                label="Clear Filters" 
                                outlined 
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>

                <!-- Skeleton Loader -->
                <SkeletonLoader 
                    v-if="isLoading" 
                    type="table" 
                    :rows="10" 
                    :columns="6"
                    class="m-6"
                />

                <!-- Students Table -->
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LRN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade & Section</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Teacher</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="student in filteredStudents" :key="student.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-800">
                                                    {{ student.full_name.charAt(0) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ student.full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ student.sex }}, {{ student.age }} years old</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ student.lrn || 'Not assigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ student.grade_level }}</div>
                                    <div class="text-sm text-gray-500">Section {{ student.section || 'Not assigned' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ getCurrentTeacher(student) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ student.school_year }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Button 
                                        @click="editStudent(student)" 
                                        icon="pi pi-pencil" 
                                        size="small"
                                        severity="info"
                                        class="mr-2"
                                        title="Edit Pupil"
                                    />
                                    <Button 
                                        @click="viewHealthRecords(student)" 
                                        icon="pi pi-heart" 
                                        size="small"
                                        severity="success"
                                        title="View Health Records"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Grade Promotion Tab -->
            <div v-if="activeTab === 'promotion'" class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Grade Promotion</h2>
                    <p class="text-gray-600 mt-1">Promote students to the next grade level and assign new teachers</p>
                </div>

                <!-- Promotion Setup -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Grade Level</label>
                            <Select 
                                v-model="promotionCurrentGrade" 
                                :options="gradeLevels" 
                                placeholder="Select current grade"
                                class="w-full"
                                @change="loadStudentsForPromotion"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New School Year</label>
                            <InputText 
                                v-model="newSchoolYear" 
                                placeholder="e.g., 2024-2025"
                                class="w-full"
                            />
                        </div>
                        <div class="flex items-end">
                            <Button 
                                @click="setupBulkPromotion" 
                                label="Setup Bulk Promotion" 
                                :disabled="!promotionCurrentGrade || !newSchoolYear"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>

                <!-- Promotion Table -->
                <div v-if="promotionStudents.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <Checkbox 
                                            v-model="selectAllChecked"
                                            @change="toggleAllStudents" 
                                            binary
                                            class="scale-125"
                                        />
                                        <span class="text-sm font-medium">Select All</span>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Grade</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Section</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Teacher</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="promotion in promotionStudents" :key="promotion.student.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <Checkbox 
                                            v-model="promotion.selected"
                                            binary
                                            class="scale-125"
                                        />
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ promotion.student.full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ promotion.student.lrn }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ promotion.student.grade_level }}</div>
                                    <div class="text-sm text-gray-500">Section {{ promotion.student.section }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Select 
                                        v-model="promotion.new_grade" 
                                        :options="gradeLevels" 
                                        class="w-full"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Select 
                                        v-model="promotion.new_section" 
                                        :options="sections" 
                                        class="w-full"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Select 
                                        v-model="promotion.new_teacher_id" 
                                        :options="teacherOptions" 
                                        optionLabel="label"
                                        optionValue="value"
                                        class="w-full"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Promotion Actions -->
                    <div class="p-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                {{ selectedPromotions.length }} of {{ promotionStudents.length }} pupils selected
                            </div>
                            <div class="space-x-3">
                                <Button 
                                    @click="resetPromotions" 
                                    label="Reset" 
                                    outlined 
                                />
                                <Button 
                                    @click="executePromotions" 
                                    label="Promote Selected Pupils" 
                                    :disabled="selectedPromotions.length === 0"
                                    :loading="promotionLoading"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Assignments Tab -->
            <div v-if="activeTab === 'assignments'" class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Teacher Assignments</h2>
                    <p class="text-gray-600 mt-1">Assign teachers to students and manage class sections</p>
                </div>

                <!-- Assignment Setup -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                            <Select 
                                v-model="assignmentGrade" 
                                :options="gradeLevels" 
                                placeholder="Select grade"
                                class="w-full"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                            <Select 
                                v-model="assignmentSection" 
                                :options="sections" 
                                placeholder="Select section"
                                class="w-full"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
                            <Select 
                                v-model="assignmentTeacher" 
                                :options="teacherOptions" 
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Select teacher"
                                class="w-full"
                            />
                        </div>
                        <div class="flex items-end">
                            <Button 
                                @click="bulkAssignTeacher" 
                                label="Assign to All" 
                                :disabled="!assignmentGrade || !assignmentSection || !assignmentTeacher"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <!-- Assignment Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="teacher in teachers" :key="teacher.id" class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-medium text-gray-900">{{ teacher.full_name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ getTeacherAssignmentCount(teacher.id) }} students assigned
                            </p>
                            <div class="mt-2">
                                <div v-for="assignment in getTeacherAssignments(teacher.id)" :key="assignment" class="text-xs text-gray-500">
                                    {{ assignment }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { useToastStore } from '@/Stores/toastStore';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Checkbox from 'primevue/checkbox';

// Toast store
const { showSuccess, showError } = useToastStore();

const props = defineProps({
    students: Array,
    teachers: Array,
    gradeLevels: Array,
    sections: Array,
    currentSchoolYear: String
});

// Tab management
const activeTab = ref('overview');

// Pupil overview
const searchQuery = ref('');
const filterGrade = ref('All');
const filterSection = ref('All');

// Grade promotion
const promotionCurrentGrade = ref('');
const newSchoolYear = ref(props.currentSchoolYear);
const promotionStudents = ref([]);
const promotionLoading = ref(false);
const selectAllChecked = ref(false);

// Teacher assignments
const assignmentGrade = ref('');
const assignmentSection = ref('');
const assignmentTeacher = ref('');

// Computed properties
const filteredStudents = computed(() => {
    let filtered = props.students;
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(student => 
            student.full_name.toLowerCase().includes(query) ||
            (student.lrn && student.lrn.toLowerCase().includes(query))
        );
    }
    
    if (filterGrade.value && filterGrade.value !== 'All') {
        filtered = filtered.filter(student => student.grade_level === filterGrade.value);
    }
    
    if (filterSection.value && filterSection.value !== 'All') {
        filtered = filtered.filter(student => student.section === filterSection.value);
    }
    
    return filtered;
});

const teacherOptions = computed(() => {
    return props.teachers.map(teacher => ({
        label: teacher.full_name,
        value: teacher.id
    }));
});

const selectedPromotions = computed(() => {
    return promotionStudents.value.filter(p => p.selected);
});

const allStudentsSelected = computed(() => {
    return promotionStudents.value.length > 0 && promotionStudents.value.every(p => p.selected);
});

// Methods
const clearFilters = () => {
    searchQuery.value = '';
    filterGrade.value = 'All';
    filterSection.value = 'All';
};

const getCurrentTeacher = (student) => {
    if (student.teacher_assignments && student.teacher_assignments.length > 0) {
        const latestAssignment = student.teacher_assignments[0];
        const teacher = props.teachers.find(t => t.id === latestAssignment.teacher_id);
        return teacher ? teacher.full_name : 'Not assigned';
    }
    return 'Not assigned';
};

const editStudent = (student) => {
    // TODO: Implement student edit modal
    console.log('Edit student:', student);
};

const viewHealthRecords = (student) => {
    window.location.href = `/pupil-health/health-examination/${student.id}`;
};

const loadStudentsForPromotion = () => {
    if (!promotionCurrentGrade.value) return;
    
    const studentsInGrade = props.students.filter(s => s.grade_level === promotionCurrentGrade.value);
    promotionStudents.value = studentsInGrade.map(student => ({
        student,
        selected: false,
        new_grade: getNextGrade(student.grade_level),
        new_section: student.section || 'A',
        new_teacher_id: null
    }));
};

const getNextGrade = (currentGrade) => {
    const gradeMap = {
        'Kinder 2': 'Grade 1',
        'Grade 1': 'Grade 2',
        'Grade 2': 'Grade 3',
        'Grade 3': 'Grade 4',
        'Grade 4': 'Grade 5',
        'Grade 5': 'Grade 6',
        'Grade 6': 'Graduate'
    };
    return gradeMap[currentGrade] || currentGrade;
};

const setupBulkPromotion = () => {
    loadStudentsForPromotion();
};

const toggleAllStudents = () => {
    const newValue = selectAllChecked.value;
    promotionStudents.value.forEach(p => p.selected = newValue);
};

const resetPromotions = () => {
    promotionStudents.value = [];
    promotionCurrentGrade.value = '';
    selectAllChecked.value = false;
};

const executePromotions = async () => {
    if (selectedPromotions.value.length === 0) return;
    
    promotionLoading.value = true;
    
    try {
        const promotions = selectedPromotions.value.map(p => ({
            student_id: p.student.id,
            new_grade: p.new_grade,
            new_section: p.new_section,
            new_teacher_id: p.new_teacher_id
        }));
        
        const response = await axios.post('/api/student-management/promote', {
            promotions,
            new_school_year: newSchoolYear.value
        });
        
        if (response.data.success) {
            showSuccess('Promotion Completed', response.data.message);
            // Refresh page to show updated data
            window.location.reload();
        }
    } catch (error) {
        console.error('Promotion failed:', error);
        showError('Promotion Failed', 'Promotion failed: ' + (error.response?.data?.message || error.message));
    } finally {
        promotionLoading.value = false;
    }
};

const bulkAssignTeacher = async () => {
    const studentsInGradeSection = props.students.filter(s => 
        s.grade_level === assignmentGrade.value && 
        s.section === assignmentSection.value
    );
    
    if (studentsInGradeSection.length === 0) {
        showError('No Students Found', 'No students found in the selected grade and section.');
        return;
    }
    
    try {
        const response = await axios.post('/api/student-management/bulk-assign-teacher', {
            student_ids: studentsInGradeSection.map(s => s.id),
            teacher_id: assignmentTeacher.value,
            grade_level: assignmentGrade.value,
            section: assignmentSection.value,
            school_year: props.currentSchoolYear
        });
        
        if (response.data.success) {
            showSuccess('Assignment Completed', response.data.message);
            window.location.reload();
        }
    } catch (error) {
        console.error('Assignment failed:', error);
        showError('Assignment Failed', 'Assignment failed: ' + (error.response?.data?.message || error.message));
    }
};

const getTeacherAssignmentCount = (teacherId) => {
    return props.students.filter(student => {
        if (student.teacher_assignments && student.teacher_assignments.length > 0) {
            return student.teacher_assignments[0].teacher_id === teacherId;
        }
        return false;
    }).length;
};

const getTeacherAssignments = (teacherId) => {
    const assignments = new Set();
    props.students.forEach(student => {
        if (student.teacher_assignments && student.teacher_assignments.length > 0) {
            const assignment = student.teacher_assignments[0];
            if (assignment.teacher_id === teacherId) {
                assignments.add(`${assignment.grade_level} - Section ${assignment.section}`);
            }
        }
    });
    return Array.from(assignments);
};

// Watch for changes in individual selections to update select all checkbox
watch(() => promotionStudents.value.map(p => p.selected), () => {
    if (promotionStudents.value.length === 0) {
        selectAllChecked.value = false;
    } else {
        selectAllChecked.value = promotionStudents.value.every(p => p.selected);
    }
}, { deep: true });

onMounted(() => {
    console.log('Student Management loaded');
    console.log('Students:', props.students.length);
    console.log('Teachers:', props.teachers.length);
});
</script>
