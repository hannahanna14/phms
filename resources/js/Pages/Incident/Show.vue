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

            <!-- Main Layout: Left and Right Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left Column: Student Details -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Student Details Card -->
                    <div class="border rounded-lg bg-white shadow p-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Student Details</h2>
                        <div class="space-y-2 text-sm">
                            <div><strong>Name:</strong> {{ student.full_name }}</div>
                            <div><strong>LRN:</strong> {{ student.lrn || '12345678901' }}</div>
                            <div><strong>Grade Level:</strong> {{ student.grade_level }}</div>
                            <div><strong>School Year:</strong> {{ student.school_year || '2024-2025' }}</div>
                        </div>
                        
                        <h3 class="text-md font-semibold text-gray-700 mt-4 mb-2">Personal Info</h3>
                        <div class="space-y-2 text-sm">
                            <div><strong>Age:</strong> {{ student.age }} years</div>
                            <div><strong>Sex:</strong> {{ student.sex }}</div>
                            <div><strong>Section:</strong> {{ student.section || 'Not Assigned' }}</div>
                            <div><strong>Status:</strong> <span class="text-green-600 font-semibold">Active</span></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Incident Details -->
                <div class="lg:col-span-3">
                    <div class="border rounded-lg bg-white shadow">
                        <div class="bg-red-700 text-white p-3 text-sm flex justify-between items-center">
                            <span>Incident Reports</span>
                            <Button 
                                v-if="userRole !== 'teacher'"
                                label="Add Record" 
                                icon="pi pi-plus" 
                                class="p-button-sm !bg-green-600 !text-white !border-green-600 hover:!bg-green-700" 
                                @click="$inertia.visit(`/pupil-health/incident/${student.id}/create`)"
                            />
                        </div>
                        <div class="p-6">
                            <div v-if="incidents.length === 0" class="text-center py-8 text-gray-500">
                                No incident reports found. Click the + button to add a new record.
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="incident in incidents" :key="incident.id" class="border rounded-lg p-4 bg-gray-50">
                                    <!-- Timer Status Display -->
                                    <div v-if="incident.timer_display" class="mb-4 p-3 rounded-lg" :class="getTimerAlertClass(incident.timer_display)">
                                        <strong>Timer:</strong> {{ incident.timer_display.display }}
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium">Date:</span>
                                            <span class="ml-2">{{ formatDate(incident.date) }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Status:</span>
                                            <Tag 
                                                :value="incident.status" 
                                                :severity="getStatusSeverity(incident.status)" 
                                                class="ml-2" 
                                            />
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="mb-2">
                                            <span class="font-medium">Complaint:</span>
                                            <p class="text-gray-700 mt-1">{{ incident.complaint }}</p>
                                        </div>
                                        <div>
                                            <span class="font-medium">Actions Taken:</span>
                                            <p class="text-gray-700 mt-1">{{ incident.actions_taken }}</p>
                                        </div>
                                    </div>
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
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import axios from 'axios'

const props = defineProps({
    student: {
        type: Object,
        required: true
    },
    userRole: {
        type: String,
        default: 'admin'
    }
})

const incidents = ref([])

const fetchIncidents = async () => {
    try {
        const response = await axios.get(`/api/incidents/student/${props.student.id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        incidents.value = response.data;
    } catch (error) {
        console.error('Error fetching incidents:', error);
        incidents.value = [];
    }
};

onMounted(() => {
    fetchIncidents();
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const getStatusSeverity = (status) => {
    switch (status) {
        case 'pending': return 'warning'
        case 'in_progress': return 'info'
        case 'resolved': return 'success'
        case 'closed': return 'secondary'
        default: return 'info'
    }
}

const getTimerStatusLabel = (timerStatus) => {
    switch (timerStatus) {
        case 'not_started': return 'Not Started'
        case 'active': return 'Active'
        case 'paused': return 'Paused'
        case 'completed': return 'Completed'
        case 'expired': return 'Expired'
        default: return 'Not Started'
    }
}

const getTimerAlertClass = (timerDisplay) => {
    if (timerDisplay?.status === 'expired') return 'bg-red-100 text-red-800';
    if (timerDisplay?.status === 'active') return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
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
