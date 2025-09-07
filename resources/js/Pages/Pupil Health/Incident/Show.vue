<template>
    <Head title="Incident Report" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header with Back Button -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-exclamation-triangle mr-2 text-red-600"></i>
                    Incident Report
                </h1>
                <Link href="/pupil-health" class="no-underline">
                    <Button label="Back" icon="pi pi-arrow-left" outlined severity="secondary" class="text-sm" />
                </Link>
            </div>

            <!-- Student Details Section -->
            <div class="border rounded-lg bg-white shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Student Details</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div><strong>Name:</strong> {{ student.full_name }}</div>
                    <div><strong>LRN:</strong> {{ student.lrn || '12345678901' }}</div>
                    <div><strong>Grade Level:</strong> {{ student.grade_level }}</div>
                    <div><strong>School Year:</strong> {{ student.school_year || '2024-2025' }}</div>
                    <div><strong>Age:</strong> {{ student.age }} years</div>
                    <div><strong>Sex:</strong> {{ student.sex }}</div>
                    <div><strong>Section:</strong> {{ student.section || 'Not Assigned' }}</div>
                    <div><strong>Status:</strong> <span class="text-green-600 font-semibold">Active</span></div>
                </div>
            </div>

            <!-- Grade Selection -->
            <div class="flex gap-2 mb-6">
                <Select v-model="selectedGrade" :options="gradeLevels" class="w-32 text-sm" @change="watchGradeChange" />
            </div>

            <!-- Incident Reports Section -->
            <div class="border rounded-lg bg-white shadow">
                <div class="bg-blue-700 text-white p-3 text-sm flex justify-between items-center">
                    <span>Incident Reports</span>
                    <Button 
                        label="Add Record" 
                        icon="pi pi-plus" 
                        class="p-button-sm !bg-green-600 !text-white !border-green-600 hover:!bg-green-700" 
                        @click="$inertia.visit(`/pupil-health/incident/${student.id}/create?grade=${selectedGrade}`)"
                    />
                </div>
                <div class="p-6">
                    <!-- Table Header -->
                    <div class="grid grid-cols-4 gap-4 p-3 bg-gray-100 font-medium text-sm border-b">
                        <div>Complaint</div>
                        <div>Actions Taken</div>
                        <div>Status</div>
                        <div>Date</div>
                    </div>
                    <!-- Table Content -->
                    <div v-if="incidents.length === 0" class="text-center py-8 text-gray-500 border-b">
                        No incident reports found. Click the + button to add a new record.
                    </div>
                    <div v-else>
                        <!-- Table Rows -->
                        <div v-for="incident in incidents" :key="incident.id" class="grid grid-cols-4 gap-4 p-3 border-b text-sm hover:bg-gray-50">
                            <div class="text-gray-700">{{ incident.complaint || 'No complaint recorded' }}</div>
                            <div class="text-gray-700">{{ incident.actions_taken || 'No actions recorded' }}</div>
                            <div>
                                <Tag 
                                    :value="incident.status || 'pending'" 
                                    :severity="getStatusSeverity(incident.status)" 
                                />
                            </div>
                            <div class="text-gray-700">{{ formatDate(incident.date) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Select from 'primevue/select'

const props = defineProps({
    student: Object,
    incidents: Array
})

// Grade level management
const selectedGrade = ref('')
const gradeLevels = [
    'Kinder 1', 'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 
    'Grade 4', 'Grade 5', 'Grade 6'
]

// Watch for grade changes and reload data
const watchGradeChange = () => {
    const grade = selectedGrade.value
    sessionStorage.setItem('selectedGrade', grade)
    
    // Reload page with new grade parameter
    window.location.href = `/pupil-health/incident/${props.student.id}?grade=${grade}`
}

onMounted(() => {
    // Initialize grade from URL parameter, sessionStorage, or student's current grade
    const urlParams = new URLSearchParams(window.location.search)
    const gradeFromUrl = urlParams.get('grade')
    const gradeFromSession = sessionStorage.getItem('selectedGrade')
    
    selectedGrade.value = gradeFromUrl || gradeFromSession || props.student.grade_level || 'Grade 6'
    
    // Store in session for consistency
    sessionStorage.setItem('selectedGrade', selectedGrade.value)
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getStatusSeverity = (status) => {
    const severityMap = {
        'pending': 'warning',
        'in_progress': 'info',
        'resolved': 'success',
        'closed': 'secondary'
    }
    return severityMap[status] || 'info'
}
</script>

<style scoped>
.incident-show {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

.white-container {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.text-gray-600 {
    color: #6b7280;
}

.border-b {
    border-bottom: 1px solid #e5e7eb;
}
</style>
