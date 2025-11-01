<template>
    <Head title="| Schedule Calendar" />
    <div class="schedule-calendar">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Schedule Calendar</h1>
                <Button
                    v-if="['admin', 'nurse'].includes($page.props.auth.user.role)"
                    @click="openCreateModal()"
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
                                <div class="font-medium truncate" :title="schedule.title">{{ schedule.title }}</div>
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
                                <div class="font-medium truncate" :title="schedule.title">{{ schedule.title }}</div>
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

        <!-- Create/Edit Schedule Modal -->
        <Dialog
            v-model:visible="showFormModal"
            :header="isEditMode ? 'Edit Schedule' : 'Create New Schedule'"
            modal
            :style="{ width: '800px', maxHeight: '90vh' }"
            :closable="!form.processing"
        >
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <InputText
                        v-model="form.title"
                        class="w-full"
                        placeholder="Enter schedule title"
                        maxlength="50"
                        required
                    />
                    <small v-if="formErrors.title" class="text-red-500">{{ formErrors.title }}</small>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <Textarea
                        v-model="form.description"
                        class="w-full"
                        rows="2"
                        placeholder="Enter schedule description"
                    />
                </div>

                <!-- Date and Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Start Date & Time <span class="text-red-500">*</span>
                        </label>
                        <Calendar
                            v-model="form.start_datetime"
                            showTime
                            hourFormat="12"
                            class="w-full"
                            placeholder="Select start date and time"
                            :minDate="new Date()"
                            :showButtonBar="true"
                            required
                        />
                        <small v-if="formErrors.start_datetime" class="text-red-500">{{ formErrors.start_datetime }}</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            End Date & Time <span class="text-red-500">*</span>
                        </label>
                        <Calendar
                            v-model="form.end_datetime"
                            showTime
                            hourFormat="12"
                            class="w-full"
                            placeholder="Select end date and time"
                            :minDate="form.start_datetime || new Date()"
                            required
                        />
                        <small v-if="formErrors.end_datetime" class="text-red-500">{{ formErrors.end_datetime }}</small>
                    </div>
                </div>

                <!-- Type and Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <Dropdown
                            v-model="form.type"
                            :options="typeOptionsForm"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select schedule type"
                            class="w-full"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <Dropdown
                            v-model="form.status"
                            :options="statusOptionsForm"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select status"
                            class="w-full"
                            required
                        />
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <InputText
                        v-model="form.location"
                        class="w-full"
                        placeholder="Enter location (optional)"
                    />
                </div>

                <!-- Attendees -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Attendees</label>
                    <div class="space-y-3">
                        <!-- User Picker -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Select Users</label>
                            <div v-if="usersLoading" class="text-center py-4 text-gray-500">
                                Loading users...
                            </div>
                            <div v-else class="border rounded-lg p-3 max-h-48 overflow-y-auto">
                                <div v-if="availableUsers.length === 0" class="text-center py-4 text-gray-500">
                                    No users available
                                </div>
                                <div v-else>
                                    <div class="flex items-center space-x-2 p-2 bg-gray-100 rounded mb-2">
                                        <input 
                                            type="checkbox" 
                                            id="select-all-users"
                                            :checked="isAllUsersSelected"
                                            @change="toggleSelectAllUsers"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded"
                                        >
                                        <label for="select-all-users" class="font-semibold text-xs text-gray-700 cursor-pointer">
                                            Select All ({{ availableUsers.length }} users)
                                        </label>
                                    </div>
                                    <div class="space-y-1">
                                        <div v-for="user in availableUsers" :key="user.id" class="flex items-center space-x-2 p-1 hover:bg-gray-50 rounded">
                                            <input 
                                                type="checkbox" 
                                                :id="`user-${user.id}`"
                                                :value="user.id"
                                                v-model="selectedUsers"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded"
                                            >
                                            <label :for="`user-${user.id}`" class="flex items-center space-x-2 flex-1 cursor-pointer">
                                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                                    {{ user.full_name ? user.full_name.charAt(0).toUpperCase() : '?' }}
                                                </div>
                                                <div class="text-xs">
                                                    <div class="font-medium">{{ user.full_name || 'Unknown User' }}</div>
                                                    <div class="text-gray-500 capitalize">{{ user.role || 'No role' }}</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Manual Entry -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Additional Attendees</label>
                            <div class="space-y-2">
                                <div v-for="(attendee, index) in form.attendees" :key="index" class="flex items-center space-x-2">
                                    <InputText
                                        v-model="form.attendees[index]"
                                        class="flex-1"
                                        placeholder="Enter external attendee name"
                                    />
                                    <Button
                                        @click="removeAttendee(index)"
                                        icon="pi pi-trash"
                                        class="p-button-danger p-button-text p-button-sm"
                                        type="button"
                                    />
                                </div>
                                <Button
                                    @click="addAttendee"
                                    icon="pi pi-plus"
                                    label="Add External Attendee"
                                    class="p-button-text p-button-sm"
                                    type="button"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <Textarea
                        v-model="form.notes"
                        class="w-full"
                        rows="2"
                        placeholder="Additional notes (optional)"
                    />
                </div>
            </form>
            <template #footer>
                <div class="flex justify-end space-x-2">
                    <Button
                        @click="closeFormModal"
                        label="Cancel"
                        class="p-button-text"
                        type="button"
                        :disabled="form.processing"
                    />
                    <Button
                        @click="submitForm"
                        :label="isEditMode ? 'Update Schedule' : 'Create Schedule'"
                        icon="pi pi-check"
                        class="p-button-primary"
                        :loading="form.processing"
                    />
                </div>
            </template>
        </Dialog>

        <!-- Schedule Detail Dialog -->
        <Dialog
            v-model:visible="showScheduleDialog"
            modal
            :style="{ width: '600px' }"
        >
            <template #header>
                <div style="word-wrap: break-word; overflow-wrap: break-word; max-width: 520px; white-space: normal;">
                    {{ selectedSchedule?.title }}
                </div>
            </template>
            <div v-if="selectedSchedule" class="space-y-4">
                <div>
                    <strong>Description:</strong>
                    <p class="mt-1 break-words">{{ selectedSchedule.description || 'No description' }}</p>
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
                    <p class="break-words">{{ selectedSchedule.location }}</p>
                </div>
                <div v-if="selectedSchedule.notes">
                    <strong>Notes:</strong>
                    <p class="break-words">{{ selectedSchedule.notes }}</p>
                </div>
                <div>
                    <strong>Created by:</strong>
                    <p>{{ selectedSchedule.creator?.full_name || 'Unknown' }}</p>
                </div>
            </div>
            <template #footer>
                <div class="flex justify-end space-x-2">
                    <Button
                        v-if="['admin', 'nurse'].includes($page.props.auth.user.role)"
                        @click="openEditModal"
                        icon="pi pi-pencil"
                        label="Edit"
                        class="p-button-text"
                    />
                    <Button
                        v-if="['admin', 'nurse'].includes($page.props.auth.user.role)"
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
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'
import Button from 'primevue/button'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css'
import '../../../css/pages/Schedule/Index.css'
import Dropdown from 'primevue/dropdown'
import Dialog from 'primevue/dialog'
import Tag from 'primevue/tag'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Calendar from 'primevue/calendar'
import { Calendar as FullCalendar } from '@fullcalendar/core'
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

// Form modal state
const showFormModal = ref(false)
const isEditMode = ref(false)
const formErrors = ref({})
const selectedUsers = ref([])
const availableUsers = ref([])
const usersLoading = ref(false)

// Form data
const form = useForm({
    title: '',
    description: '',
    start_datetime: null,
    end_datetime: null,
    type: 'other',
    status: 'scheduled',
    location: '',
    attendees: [''],
    selected_users: [],
    notes: ''
})

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

const typeOptionsForm = [
    { label: 'Health Checkup', value: 'health_checkup' },
    { label: 'Vaccination', value: 'vaccination' },
    { label: 'Meeting', value: 'meeting' },
    { label: 'Training', value: 'training' },
    { label: 'Other', value: 'other' }
]

const statusOptionsForm = [
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
    // Extended range: 5 years back to 10 years forward for better coverage
    for (let i = currentYear - 5; i <= currentYear + 10; i++) {
        years.push({ label: i.toString(), value: i })
    }
    return years
})

// Initialize calendar
onMounted(() => {
    initializeCalendar()
    loadUsers()
})

const initializeCalendar = () => {
    const calendarEl = document.getElementById('calendar')
    
    calendar = new FullCalendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialDate: `${filters.value.year}-${String(filters.value.month).padStart(2, '0')}-01`,
        events: (fetchInfo, successCallback, failureCallback) => {
            // Build URL with filter parameters
            const params = new URLSearchParams({
                start: fetchInfo.startStr,
                end: fetchInfo.endStr
            })
            
            // Add filter parameters if they're not 'all' or default values
            if (filters.value.type && filters.value.type !== 'all') {
                params.append('type', filters.value.type)
            }
            if (filters.value.status && filters.value.status !== 'all') {
                params.append('status', filters.value.status)
            }
            if (filters.value.month) {
                params.append('month', filters.value.month)
            }
            if (filters.value.year) {
                params.append('year', filters.value.year)
            }
            
            // Fetch events with filters
            fetch(`/api/schedule/events?${params.toString()}`)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => {
                    console.error('Error fetching calendar events:', error)
                    failureCallback(error)
                })
        },
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
            // Prevent creating schedules for past dates
            const clickedDate = new Date(info.dateStr)
            const today = new Date()
            today.setHours(0, 0, 0, 0)
            
            if (clickedDate < today) {
                return // Don't open modal for past dates
            }
            
            // Open create modal with selected date
            openCreateModal(info.dateStr)
        },
        selectAllow: (selectInfo) => {
            // Allow selection only for today and future dates
            const today = new Date()
            today.setHours(0, 0, 0, 0)
            return new Date(selectInfo.start) >= today
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
    // Navigate to the selected month/year
    if (calendar && filters.value.month && filters.value.year) {
        const dateStr = `${filters.value.year}-${String(filters.value.month).padStart(2, '0')}-01`
        calendar.gotoDate(dateStr)
    }
    
    // Refresh the calendar events with new filters
    if (calendar) {
        calendar.refetchEvents()
    }
    
    // Also update the page data for sidebar schedules
    router.visit(route('schedule-calendar.index'), {
        data: filters.value,
        preserveState: true,
        preserveScroll: true
    })
}

// Load users
const loadUsers = async () => {
    try {
        usersLoading.value = true
        const response = await fetch('/api/users')
        const users = await response.json()
        availableUsers.value = users
    } catch (error) {
        console.error('Error loading users:', error)
    } finally {
        usersLoading.value = false
    }
}

// User selection
const isAllUsersSelected = computed(() => {
    return availableUsers.value.length > 0 && 
           selectedUsers.value.length === availableUsers.value.length
})

const toggleSelectAllUsers = () => {
    if (isAllUsersSelected.value) {
        selectedUsers.value = []
    } else {
        selectedUsers.value = availableUsers.value.map(user => user.id)
    }
}

// Attendee management
const addAttendee = () => {
    form.attendees.push('')
}

const removeAttendee = (index) => {
    if (form.attendees.length > 1) {
        form.attendees.splice(index, 1)
    }
}

// Open create modal
const openCreateModal = (dateStr = null) => {
    isEditMode.value = false
    formErrors.value = {}
    selectedUsers.value = []
    
    // Reset form
    form.reset()
    form.title = ''
    form.description = ''
    form.type = 'other'
    form.status = 'scheduled'
    form.location = ''
    form.attendees = ['']
    form.notes = ''
    
    // Set dates
    if (dateStr) {
        const selectedDate = new Date(dateStr)
        const now = new Date()
        
        // If selected date is today, set time to current time + 1 hour
        if (selectedDate.toDateString() === now.toDateString()) {
            now.setMinutes(now.getMinutes() + 60) // Add 1 hour
            selectedDate.setHours(now.getHours(), now.getMinutes(), 0, 0)
        } else {
            // For future dates, default to 9:00 AM
            selectedDate.setHours(9, 0, 0, 0)
        }
        
        form.start_datetime = selectedDate
        
        const endDate = new Date(selectedDate)
        endDate.setHours(selectedDate.getHours() + 1, selectedDate.getMinutes(), 0, 0)
        form.end_datetime = endDate
    } else {
        form.start_datetime = null
        form.end_datetime = null
    }
    
    showFormModal.value = true
}

// Open edit modal
const openEditModal = () => {
    if (!selectedSchedule.value) return
    
    isEditMode.value = true
    formErrors.value = {}
    
    // Populate form with schedule data
    form.title = selectedSchedule.value.title || ''
    form.description = selectedSchedule.value.description || ''
    form.start_datetime = selectedSchedule.value.start_datetime ? new Date(selectedSchedule.value.start_datetime) : null
    form.end_datetime = selectedSchedule.value.end_datetime ? new Date(selectedSchedule.value.end_datetime) : null
    form.type = selectedSchedule.value.type || 'other'
    form.status = selectedSchedule.value.status || 'scheduled'
    form.location = selectedSchedule.value.location || ''
    form.attendees = selectedSchedule.value.attendees?.length > 0 ? selectedSchedule.value.attendees : ['']
    form.notes = selectedSchedule.value.notes || ''
    
    // Load selected users
    selectedUsers.value = selectedSchedule.value.selected_users || []
    
    showScheduleDialog.value = false
    showFormModal.value = true
}

// Close form modal
const closeFormModal = () => {
    if (!form.processing) {
        showFormModal.value = false
    }
}

// Submit form
const submitForm = () => {
    // Validate
    const errors = {}
    const now = new Date()
    
    if (!form.title) errors.title = 'Title is required'
    if (!form.start_datetime) errors.start_datetime = 'Start date is required'
    if (!form.end_datetime) errors.end_datetime = 'End date is required'
    
    // Check if start datetime is in the past (only for new schedules, not edits)
    if (!isEditMode.value && form.start_datetime && new Date(form.start_datetime) < now) {
        errors.start_datetime = 'Start time cannot be in the past'
    }
    
    if (form.start_datetime && form.end_datetime && form.start_datetime >= form.end_datetime) {
        errors.end_datetime = 'End time must be after start time'
    }
    
    if (Object.keys(errors).length > 0) {
        formErrors.value = errors
        return
    }
    
    // Clean up attendees - filter out empty strings
    form.attendees = form.attendees.filter(a => a && a.trim() !== '')
    
    // Ensure attendees is an array (even if empty)
    if (!Array.isArray(form.attendees)) {
        form.attendees = []
    }
    
    // Set selected users
    form.selected_users = selectedUsers.value || []
    
    console.log('Submitting schedule:', {
        title: form.title,
        description: form.description,
        attendees: form.attendees,
        selected_users: form.selected_users,
        start_datetime: form.start_datetime,
        end_datetime: form.end_datetime
    })
    
    if (isEditMode.value) {
        // Update
        form.put(route('schedule-calendar.update', selectedSchedule.value.id), {
            onSuccess: () => {
                showFormModal.value = false
                if (calendar) calendar.refetchEvents()
                alert('Schedule updated successfully!')
            },
            onError: (errors) => {
                console.error('Schedule update error:', errors)
                formErrors.value = errors
                alert('Failed to update schedule. Please check the form and try again.')
            }
        })
    } else {
        // Create
        form.post(route('schedule-calendar.store'), {
            onSuccess: () => {
                showFormModal.value = false
                if (calendar) calendar.refetchEvents()
                alert('Schedule created successfully!')
            },
            onError: (errors) => {
                console.error('Schedule creation error:', errors)
                formErrors.value = errors
                alert('Failed to create schedule. Please check the form and try again.')
            }
        })
    }
}

const deleteSchedule = () => {
    if (selectedSchedule.value && confirm('Are you sure you want to delete this schedule?')) {
        router.delete(route('schedule-calendar.destroy', selectedSchedule.value.id), {
            onSuccess: () => {
                showScheduleDialog.value = false
                if (calendar) calendar.refetchEvents()
            }
        })
    }
}
</script>
