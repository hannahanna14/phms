<template>
    <Head title="Schedule Calendar" />
    <div class="schedule-calendar">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Schedule Calendar</h1>
                <Button
                    v-if="$page.props.auth.user.role === 'admin'"
                    @click="$inertia.visit(route('schedule-calendar.create'))"
                    icon="pi pi-plus"
                    label="Add Schedule"
                    class="p-button-primary"
                />
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <Dropdown
                        v-model="filters.type"
                        :options="typeOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="All Types"
                        class="w-full"
                        @change="applyFilters"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <Dropdown
                        v-model="filters.status"
                        :options="statusOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="All Status"
                        class="w-full"
                        @change="applyFilters"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                    <Dropdown
                        v-model="filters.month"
                        :options="monthOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Current Month"
                        class="w-full"
                        @change="applyFilters"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                    <Dropdown
                        v-model="filters.year"
                        :options="yearOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Current Year"
                        class="w-full"
                        @change="applyFilters"
                    />
                </div>
            </div>

            <!-- Calendar View -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Calendar -->
                <div class="lg:col-span-3">
                    <div id="calendar" class="bg-white rounded-lg border"></div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Today's Schedules -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-900 mb-3">Today's Schedule</h3>
                        <div v-if="todaySchedules.length === 0" class="text-sm text-blue-600">
                            No schedules for today
                        </div>
                        <div v-else class="space-y-2">
                            <div
                                v-for="schedule in todaySchedules"
                                :key="schedule.id"
                                class="bg-white rounded p-3 text-sm"
                            >
                                <div class="font-medium">{{ schedule.title }}</div>
                                <div class="text-gray-600">{{ formatTime(schedule.start_datetime) }}</div>
                                <div class="flex items-center mt-1">
                                    <Tag
                                        :value="schedule.type.replace('_', ' ')"
                                        :severity="getTypeSeverity(schedule.type)"
                                        class="text-xs"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Schedules -->
                    <div class="bg-green-50 rounded-lg p-4">
                        <h3 class="font-semibold text-green-900 mb-3">Upcoming</h3>
                        <div v-if="upcomingSchedules.length === 0" class="text-sm text-green-600">
                            No upcoming schedules
                        </div>
                        <div v-else class="space-y-2">
                            <div
                                v-for="schedule in upcomingSchedules"
                                :key="schedule.id"
                                class="bg-white rounded p-3 text-sm"
                            >
                                <div class="font-medium">{{ schedule.title }}</div>
                                <div class="text-gray-600">{{ formatDate(schedule.start_datetime) }}</div>
                                <div class="flex items-center mt-1">
                                    <Tag
                                        :value="schedule.type.replace('_', ' ')"
                                        :severity="getTypeSeverity(schedule.type)"
                                        class="text-xs"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Detail Dialog -->
        <Dialog
            v-model:visible="showScheduleDialog"
            :header="selectedSchedule?.title"
            modal
            :style="{ width: '500px' }"
        >
            <div v-if="selectedSchedule" class="space-y-4">
                <div>
                    <strong>Description:</strong>
                    <p class="mt-1">{{ selectedSchedule.description || 'No description' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <strong>Start:</strong>
                        <p>{{ formatDateTime(selectedSchedule.start_datetime) }}</p>
                    </div>
                    <div>
                        <strong>End:</strong>
                        <p>{{ formatDateTime(selectedSchedule.end_datetime) }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <strong>Type:</strong>
                        <Tag
                            :value="selectedSchedule.type.replace('_', ' ')"
                            :severity="getTypeSeverity(selectedSchedule.type)"
                        />
                    </div>
                    <div>
                        <strong>Status:</strong>
                        <Tag
                            :value="selectedSchedule.status"
                            :severity="getStatusSeverity(selectedSchedule.status)"
                        />
                    </div>
                </div>
                <div v-if="selectedSchedule.location">
                    <strong>Location:</strong>
                    <p>{{ selectedSchedule.location }}</p>
                </div>
                <div v-if="selectedSchedule.notes">
                    <strong>Notes:</strong>
                    <p>{{ selectedSchedule.notes }}</p>
                </div>
                <div>
                    <strong>Created by:</strong>
                    <p>{{ selectedSchedule.creator?.full_name || 'Unknown' }}</p>
                </div>
            </div>
            <template #footer>
                <div class="flex justify-end space-x-2">
                    <Button
                        v-if="$page.props.auth.user.role === 'admin'"
                        @click="editSchedule"
                        icon="pi pi-pencil"
                        label="Edit"
                        class="p-button-text"
                    />
                    <Button
                        v-if="$page.props.auth.user.role === 'admin'"
                        @click="deleteSchedule"
                        icon="pi pi-trash"
                        label="Delete"
                        class="p-button-text p-button-danger"
                    />
                    <Button
                        @click="showScheduleDialog = false"
                        label="Close"
                        class="p-button-text"
                    />
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'
import Button from 'primevue/button'
import Dropdown from 'primevue/dropdown'
import Dialog from 'primevue/dialog'
import Tag from 'primevue/tag'
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

const props = defineProps({
    schedules: Array,
    upcomingSchedules: Array,
    todaySchedules: Array,
    filters: Object,
    currentDate: String
})

// Calendar instance
let calendar = null
const showScheduleDialog = ref(false)
const selectedSchedule = ref(null)

// Filter options
const filters = ref({
    type: props.filters?.type || 'all',
    status: props.filters?.status || 'all',
    month: props.filters?.month || new Date().getMonth() + 1,
    year: props.filters?.year || new Date().getFullYear()
})

const typeOptions = [
    { label: 'All Types', value: 'all' },
    { label: 'Health Checkup', value: 'health_checkup' },
    { label: 'Vaccination', value: 'vaccination' },
    { label: 'Meeting', value: 'meeting' },
    { label: 'Training', value: 'training' },
    { label: 'Other', value: 'other' }
]

const statusOptions = [
    { label: 'All Status', value: 'all' },
    { label: 'Scheduled', value: 'scheduled' },
    { label: 'Completed', value: 'completed' },
    { label: 'Cancelled', value: 'cancelled' }
]

const monthOptions = [
    { label: 'January', value: 1 },
    { label: 'February', value: 2 },
    { label: 'March', value: 3 },
    { label: 'April', value: 4 },
    { label: 'May', value: 5 },
    { label: 'June', value: 6 },
    { label: 'July', value: 7 },
    { label: 'August', value: 8 },
    { label: 'September', value: 9 },
    { label: 'October', value: 10 },
    { label: 'November', value: 11 },
    { label: 'December', value: 12 }
]

const yearOptions = computed(() => {
    const currentYear = new Date().getFullYear()
    const years = []
    for (let i = currentYear - 2; i <= currentYear + 2; i++) {
        years.push({ label: i.toString(), value: i })
    }
    return years
})

// Initialize calendar
onMounted(() => {
    initializeCalendar()
})

const initializeCalendar = () => {
    const calendarEl = document.getElementById('calendar')
    
    calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '/api/schedule/events',
        eventClick: (info) => {
            // Find the full schedule data
            const scheduleId = parseInt(info.event.id)
            const schedule = props.schedules.find(s => s.id === scheduleId)
            if (schedule) {
                selectedSchedule.value = schedule
                showScheduleDialog.value = true
            }
        },
        dateClick: (info) => {
            // Navigate to create page with selected date
            router.visit(route('schedule-calendar.create', { date: info.dateStr }))
        }
    })
    
    calendar.render()
}

// Helper functions
const formatTime = (datetime) => {
    return new Date(datetime).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatDate = (datetime) => {
    return new Date(datetime).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    })
}

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getTypeSeverity = (type) => {
    const severities = {
        'health_checkup': 'success',
        'vaccination': 'info',
        'meeting': 'warning',
        'training': 'help',
        'other': 'secondary'
    }
    return severities[type] || 'secondary'
}

const getStatusSeverity = (status) => {
    const severities = {
        'scheduled': 'info',
        'completed': 'success',
        'cancelled': 'danger'
    }
    return severities[status] || 'secondary'
}

const applyFilters = () => {
    router.visit(route('schedule-calendar.index'), {
        data: filters.value,
        preserveState: true,
        preserveScroll: true
    })
}

const editSchedule = () => {
    if (selectedSchedule.value) {
        router.visit(route('schedule-calendar.edit', selectedSchedule.value.id))
    }
}

const deleteSchedule = () => {
    if (selectedSchedule.value && confirm('Are you sure you want to delete this schedule?')) {
        router.delete(route('schedule-calendar.destroy', selectedSchedule.value.id))
        showScheduleDialog.value = false
    }
}
</script>

<style scoped>
.schedule-calendar {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

:deep(.fc-toolbar-title) {
    font-size: 1.5rem;
    font-weight: 600;
}

:deep(.fc-button-primary) {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

:deep(.fc-button-primary:hover) {
    background-color: #2563EB;
    border-color: #2563EB;
}

:deep(.fc-event) {
    cursor: pointer;
}

:deep(.fc-daygrid-event) {
    margin: 1px;
    border-radius: 3px;
}
</style>
