<template>
    <Head title="Incident Management" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-exclamation-triangle mr-2 text-red-600"></i>
                    Incident Management
                </h1>
                <Button 
                    label="Back to Dashboard" 
                    icon="pi pi-arrow-left" 
                    outlined 
                    severity="secondary" 
                    @click="$inertia.visit('/pupil-health')"
                />
            </div>

            <!-- Search -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <span class="p-input-icon-left w-full">
                    <i class="pi pi-search" />
                    <InputText 
                        v-model="searchQuery" 
                        placeholder="Search by student name..." 
                        class="w-full"
                    />
                </span>
            </div>

            <!-- Students with Incidents -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Students with Incidents</h2>
                </div>
                
                <div v-if="filteredStudents.length === 0" class="p-8 text-center text-gray-500">
                    No students found.
                </div>
                
                <div v-else class="divide-y divide-gray-200">
                    <div 
                        v-for="student in filteredStudents" 
                        :key="student.id"
                        class="p-4 hover:bg-gray-50 cursor-pointer"
                        @click="viewStudentIncidents(student)"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ student.full_name }}</h3>
                                <p class="text-sm text-gray-500">Grade {{ student.grade_level }} • Age {{ student.age }} • {{ student.sex }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <Tag 
                                    :value="`${student.incidents_count || 0} Incidents`" 
                                    severity="info"
                                />
                                <i class="pi pi-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Tag from 'primevue/tag'

const props = defineProps({
    students: {
        type: Array,
        default: () => []
    },
    userRole: {
        type: String,
        default: 'admin'
    }
})

const searchQuery = ref('')

const filteredStudents = computed(() => {
    if (!searchQuery.value) return props.students
    
    return props.students.filter(student => 
        student.full_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

const viewStudentIncidents = (student) => {
    window.location.href = `/pupil-health/incident/${student.id}?grade=${student.grade_level}`
}
</script>

<style scoped>
.health-examination {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

.search-container {
    margin-bottom: 20px;
}

.search-input {
    width: 100%;
    padding: 8px 8px 8px 35px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background-color: white;
}

.student-table-container {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.student-table {
    width: 100%;
    border-collapse: collapse;
}

.student-table th,
.student-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.student-table th {
    font-weight: 600;
    color: #374151;
}

.student-table tr:hover {
    background-color: #f9fafb;
}
</style>
