<template>
    <Head title="Incident Report" />
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Enhanced Header -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 mb-8 backdrop-blur-sm">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-r from-red-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="pi pi-exclamation-triangle text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent mb-2">Incident Report</h1>
                            <p class="text-slate-600 font-medium">Comprehensive incident documentation and records</p>
                        </div>
                    </div>
                    <Link href="/pupil-health" class="no-underline">
                        <Button
                            label="Back to List"
                            icon="pi pi-arrow-left"
                            class="p-button-outlined p-button-lg shadow-sm hover:shadow-md transition-all duration-300"
                            style="border: 2px solid #e5e7eb; color: #374151; font-weight: 600; border-radius: 12px;"
                        />
                    </Link>
                </div>
            </div>

            <!-- Main Layout: Left and Right Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left Column: Student Details -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Enhanced Student Details Card -->
                    <SkeletonLoader
                        v-if="isLoading"
                        type="card"
                        :lines="8"
                        class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden backdrop-blur-sm"
                    />

                    <div v-else class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden backdrop-blur-sm">
                        <div class="bg-gradient-to-r from-emerald-500 via-blue-500 to-indigo-600 p-5">
                            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                <i class="pi pi-user text-white/90"></i>
                                Pupil Details
                            </h2>
                        </div>
                        <div class="p-8">
                            <div class="space-y-4 text-sm">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Name</div>
                                        <div class="font-bold text-slate-900 text-lg">{{ student.full_name }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">LRN</div>
                                        <div class="font-mono text-slate-900 text-lg font-bold">{{ student.lrn || '12345678901' }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Grade</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.grade_level }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">School Year</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.school_year || '2024-2025' }}</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mt-6">
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Age</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.age }} years</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Sex</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.sex }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Section</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.section || 'Not Assigned' }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Status</div>
                                        <div>
                                            <span v-if="student.is_active && student.school_year === usePage().props.currentSchoolYear" class="text-green-600 text-lg font-bold">Active</span>
                                            <span v-else class="text-red-600 text-lg font-bold">Inactive</span>
                                            <div class="text-xs text-gray-500 mt-1">Last Year: {{ student.school_year || 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grade Level Selector -->
                    <SkeletonLoader
                        v-if="isLoading"
                        type="form"
                        :fields="1"
                        class="border rounded-lg bg-white shadow p-4"
                    />

                    <div v-else class="border rounded-lg bg-white shadow p-4">
                        <h3 class="text-md font-semibold text-gray-700 mb-3">Select Grade Level</h3>
                        <Select
                            v-model="selectedGrade"
                            :options="gradeLevels"
                            placeholder="Select Grade Level"
                            class="w-full"
                            @change="onGradeChange"
                        />
                    </div>
                </div>

                <!-- Right Column: Incident Details -->
                <div class="lg:col-span-3">
                    <SkeletonLoader
                        v-if="isLoading"
                        type="card"
                        :lines="8"
                        class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden backdrop-blur-sm"
                    />

                    <div v-else class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden backdrop-blur-sm">
                        <div class="bg-gradient-to-r from-red-500 via-orange-500 to-amber-600 p-6 flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                    <i class="pi pi-exclamation-triangle text-white text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white mb-1">Incident Reports</h2>
                                    <p class="text-red-100 font-medium">Safety incident documentation</p>
                                </div>
                            </div>
                            <Button
                                v-if="userRole === 'nurse'"
                                label="Add Incident"
                                icon="pi pi-plus"
                                class="p-button-sm !bg-green-600 !text-white !border-green-600 hover:!bg-green-700"
                                @click="$inertia.visit(`/pupil-health/incident/${student.id}/create`)"
                            />
                        </div>
                        <div class="p-3">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-1">Date</th>
                                        <th class="text-left py-1">Complaint</th>
                                        <th class="text-left py-1">Actions Taken</th>
                                        <th class="text-left py-1">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="incidents.length === 0">
                                        <td colspan="4" class="text-center py-2 text-gray-500">No records available</td>
                                    </tr>
                                    <tr v-for="incident in incidents" :key="incident.id" class="border-b hover:bg-gray-50">
                                        <td class="py-2">{{ formatDate(incident.date) }}</td>
                                        <td class="py-2" style="max-width: 200px; word-break: break-all;">
                                            <div class="overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2;" v-tooltip.top="incident.complaint">
                                                {{ incident.complaint }}
                                            </div>
                                        </td>
                                        <td class="py-2" style="max-width: 200px; word-break: break-all;">
                                            <div class="overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2;" v-tooltip.top="incident.actions_taken">
                                                {{ incident.actions_taken }}
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <div class="flex gap-1">
                                                <Button
                                                    label="View"
                                                    icon="pi pi-eye"
                                                    size="small"
                                                    severity="info"
                                                    outlined
                                                    @click="viewIncident(incident)"
                                                    class="!p-1 !text-xs"
                                                    v-tooltip.top="'View Incident'"
                                                />
                                                <Button
                                                    v-if="userRole === 'nurse'"
                                                    icon="pi pi-pencil"
                                                    size="small"
                                                    severity="warning"
                                                    @click="editIncident(incident)"
                                                    class="!p-1 !text-xs"
                                                    v-tooltip.top="'Edit Incident'"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incident View Modal -->
        <IncidentViewModal
            :visible="showIncidentModal"
            :incident="selectedIncident"
            :student="student"
            :timer-status="incidentTimerStatus"
            :remaining-minutes="incidentRemainingMinutes"
            :user-role="userRole"
            @close="closeIncidentModal"
            @edit="editIncidentFromModal"
        />
    </div>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed, watch, onUnmounted } from 'vue'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Select from 'primevue/select'
import axios from 'axios'
import { useTimerNotifications } from '@/Utils/timerMixin.js'
import { integrateIncidentNotifications } from '@/Utils/notificationIntegration.js'
import IncidentViewModal from '@/Components/Modals/IncidentViewModal.vue'
import SkeletonLoader from '@/Components/SkeletonLoader.vue'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css'
// Import page-specific styles
import '../../../css/pages/Incident/Show.css'

const { student, userRole, currentGrade } = usePage().props

const props = defineProps({
    student: {
        type: Object,
        required: true
    },
    userRole: {
        type: String,
        default: 'admin'
    },
    currentGrade: {
        type: String,
        default: null
    }
})

const incidents = ref([])
const isLoading = ref(true)

// Modal state
const showIncidentModal = ref(false)
const selectedIncident = ref(null)
const incidentTimerStatus = ref(null)
const incidentRemainingMinutes = ref(0)

// Grade level management
const gradeLevels = computed(() => {
    const standardGrades = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
    // Convert student grade to match format (e.g., "6" becomes "Grade 6")
    const studentGradeFormatted = isNaN(student.grade_level) ? student.grade_level : `Grade ${student.grade_level}`;
    return standardGrades.includes(studentGradeFormatted) ? standardGrades : [...standardGrades, studentGradeFormatted];
});

// Check if we should refresh data on page load
onMounted(() => {
    const returnData = sessionStorage.getItem('returnToIncident');
    if (returnData) {
        const data = JSON.parse(returnData);
        if (data.shouldRefresh && data.studentId === student.id) {
            // Clear the flag
            sessionStorage.removeItem('returnToIncident');
            // Refresh the data after a short delay
            setTimeout(() => {
                refreshIncidentData();
            }, 500);
        }
    }
});

// Initialize selected grade
const getInitialGrade = () => {
    // Check URL parameter first
    const urlGrade = new URLSearchParams(window.location.search).get('grade');
    if (urlGrade) {
        const formattedGrade = isNaN(urlGrade) ? urlGrade : `Grade ${urlGrade}`;
        return formattedGrade;
    }

    // Use current grade prop if available
    if (currentGrade) {
        return isNaN(currentGrade) ? currentGrade : `Grade ${currentGrade}`;
    }

    // Default to student's current grade level
    return isNaN(student.grade_level) ? student.grade_level : `Grade ${student.grade_level}`;
};

const selectedGrade = ref(getInitialGrade());

const fetchIncidents = async () => {
    try {
        // Use full grade string for API call to match database format
        const response = await axios.get(`/api/incidents/student/${student.id}?grade=${selectedGrade.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        incidents.value = response.data;
    } catch (error) {
        console.error('Error fetching incidents:', error);
        incidents.value = [];
    } finally {
        isLoading.value = false;
    }
};

const onGradeChange = async () => {
    // Update URL without page reload
    const newUrl = new URL(window.location);
    newUrl.searchParams.set('grade', selectedGrade.value);
    window.history.replaceState({}, '', newUrl);

    // Show skeleton loaders when switching grades
    isLoading.value = true;

    // Fetch incidents for new grade
    await fetchIncidents();
};

// Watch for grade changes
watch(selectedGrade, () => {
    fetchIncidents();
});

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

// Initialize timer notifications for incidents
const { startTimerMonitoring, stopTimerMonitoring } = useTimerNotifications('incident')
const activeTimers = ref(new Map()) // Track active timers

// Timer control methods
const startIncidentTimer = async (incident) => {
    try {
        const response = await fetch(`/api/incidents/${incident.id}/start-timer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            // Start monitoring this incident timer
            startTimerMonitoring(student, incident, 120) // 2 hours = 120 minutes
            activeTimers.value.set(incident.id, true)

            // Refresh incidents to show updated timer status
            await fetchIncidents()
        }
    } catch (error) {
        console.error('Error starting incident timer:', error);
    }
};

const pauseIncidentTimer = async (incident) => {
    try {
        const response = await fetch(`/api/incidents/${incident.id}/pause-timer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            // Stop monitoring when paused
            if (activeTimers.value.has(incident.id)) {
                stopTimerMonitoring()
                activeTimers.value.delete(incident.id)
            }
            await fetchIncidents()
        }
    } catch (error) {
        console.error('Error pausing incident timer:', error);
    }
};

const resumeIncidentTimer = async (incident) => {
    try {
        const response = await fetch(`/api/incidents/${incident.id}/resume-timer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            // Resume monitoring
            startTimerMonitoring(student, incident, incident.remaining_minutes || 120)
            activeTimers.value.set(incident.id, true)
            await fetchIncidents()
        }
    } catch (error) {
        console.error('Error resuming incident timer:', error);
    }
};

const completeIncidentTimer = async (incident) => {
    try {
        const response = await fetch(`/api/incidents/${incident.id}/complete-timer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            // Trigger completion notification
            const integration = integrateIncidentNotifications()
            integration.handleTimerStatusUpdate('completed', student, incident)

            // Stop monitoring
            if (activeTimers.value.has(incident.id)) {
                stopTimerMonitoring()
                activeTimers.value.delete(incident.id)
            }
            await fetchIncidents()
        }
    } catch (error) {
        console.error('Error completing incident timer:', error);
    }
};

// Start monitoring active timers when component mounts
onMounted(() => {
    fetchIncidents().then(() => {
        // Check for active timers and start monitoring
        incidents.value.forEach(incident => {
            if (incident.timer_display?.status === 'active' && incident.remaining_minutes > 0) {
                console.log('Starting timer monitoring for incident:', incident.complaint);
                startTimerMonitoring(student, incident, incident.remaining_minutes)
                activeTimers.value.set(incident.id, true)
            }
        })
    })
});

// Stop all timer monitoring when component unmounts
onUnmounted(() => {
    stopTimerMonitoring()
    activeTimers.value.clear()
});

// View incident details
const viewIncident = async (incident) => {
    try {
        // Fetch full incident details with timer status
        const response = await fetch(`/api/incidents/timer-status/${incident.id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            selectedIncident.value = incident;
            incidentTimerStatus.value = data.timer_status;
            incidentRemainingMinutes.value = data.remaining_minutes || 0;
            showIncidentModal.value = true;
        } else {
            console.error('Failed to fetch incident details');
            // Fallback to basic modal without timer info
            selectedIncident.value = incident;
            incidentTimerStatus.value = null;
            incidentRemainingMinutes.value = 0;
            showIncidentModal.value = true;
        }
    } catch (error) {
        console.error('Error fetching incident details:', error);
        // Fallback to basic modal
        selectedIncident.value = incident;
        incidentTimerStatus.value = null;
        incidentRemainingMinutes.value = 0;
        showIncidentModal.value = true;
    }
};

const closeIncidentModal = () => {
    showIncidentModal.value = false;
    selectedIncident.value = null;
    incidentTimerStatus.value = null;
    incidentRemainingMinutes.value = 0;
};

const refreshIncidentData = async () => {
    try {
        const response = await fetch(`/api/incidents/student/${student.id}?grade=${selectedGrade.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            // Update the incidents data
            incidents.value = data.incidents || [];
            console.log('Incident data refreshed');
        }
    } catch (error) {
        console.error('Error refreshing incident data:', error);
    }
};

const editIncidentFromModal = (incident) => {
    closeIncidentModal();
    window.location.href = `/pupil-health/incident/${incident.id}/edit`;
};

// Edit incident
const editIncident = (incident) => {
    // Navigate to incident edit page (you can create this route)
    window.location.href = `/pupil-health/incident/${incident.id}/edit`;
};
</script>
