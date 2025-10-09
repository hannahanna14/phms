<template>
    <Head title="Create Schedule" />
    <div class="create-schedule">
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Create New Schedule</h1>
                <Button
                    @click="$inertia.visit(route('schedule-calendar.index'))"
                    icon="pi pi-arrow-left"
                    label="Back to Calendar"
                    class="p-button-text"
                />
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Title -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <InputText
                        v-model="form.title"
                        class="w-full"
                        placeholder="Enter schedule title"
                        required
                    />
                    <small v-if="errors && errors.title" class="text-red-500">{{ errors.title }}</small>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <Textarea
                        v-model="form.description"
                        class="w-full"
                        rows="3"
                        placeholder="Enter schedule description"
                    />
                    <small v-if="errors && errors.description" class="text-red-500">{{ errors.description }}</small>
                </div>

                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date & Time <span class="text-red-500">*</span>
                        </label>
                        <Calendar
                            v-model="form.start_datetime"
                            showTime
                            hourFormat="12"
                            class="w-full"
                            placeholder="Select start date and time"
                            required
                        />
                        <small v-if="errors && errors.start_datetime" class="text-red-500">{{ errors.start_datetime }}</small>
                    </div>

                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            End Date & Time <span class="text-red-500">*</span>
                        </label>
                        <Calendar
                            v-model="form.end_datetime"
                            showTime
                            hourFormat="12"
                            class="w-full"
                            placeholder="Select end date and time"
                            required
                        />
                        <small v-if="errors && errors.end_datetime" class="text-red-500">{{ errors.end_datetime }}</small>
                    </div>
                </div>

                <!-- Type and Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <Dropdown
                            v-model="form.type"
                            :options="typeOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select schedule type"
                            class="w-full"
                            required
                        />
                        <small v-if="errors && errors.type" class="text-red-500">{{ errors.type }}</small>
                    </div>

                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <Dropdown
                            v-model="form.status"
                            :options="statusOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select status"
                            class="w-full"
                            required
                        />
                        <small v-if="errors && errors.status" class="text-red-500">{{ errors.status }}</small>
                    </div>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <InputText
                        v-model="form.location"
                        class="w-full"
                        placeholder="Enter location (optional)"
                    />
                    <small v-if="errors && errors.location" class="text-red-500">{{ errors.location }}</small>
                </div>

                <!-- Attendees -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Attendees</label>
                    <div class="space-y-3">
                        <!-- User Picker -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Select Users</label>
                            <div v-if="usersLoading" class="text-center py-4 text-gray-500">
                                Loading users...
                            </div>
                            <div v-else-if="!availableUsers || availableUsers.length === 0" class="text-center py-4 text-gray-500">
                                No users available
                            </div>
                            <div v-else class="border rounded-lg p-4 max-h-60 overflow-y-auto">
                                <div class="space-y-2">
                                    <div v-for="user in availableUsers" :key="user.id" class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                                        <input 
                                            type="checkbox" 
                                            :id="`user-${user.id}`"
                                            :value="user.id"
                                            v-model="selectedUsers"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        >
                                        <div class="flex items-center space-x-2 flex-1">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ (user.full_name && user.full_name.length > 0) ? user.full_name.charAt(0).toUpperCase() : '?' }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-sm">{{ user.full_name || 'Unknown User' }}</div>
                                                <div class="text-xs text-gray-500 capitalize">{{ user.role || 'No role' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Selected Users Display -->
                            <div v-if="selectedUsers && selectedUsers.length > 0 && !usersLoading" class="mt-2">
                                <div class="text-xs font-medium text-gray-600 mb-1">Selected Users ({{ selectedUsers.length }})</div>
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="userId in selectedUsers" :key="userId" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ availableUsers && availableUsers.length > 0 ? getUserName(userId) : 'Loading...' }}
                                        <button @click="removeUser(userId)" class="ml-1 text-blue-600 hover:text-blue-800">
                                            ×
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Manual Entry (Optional) -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Additional Attendees (Manual Entry)</label>
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
                                    />
                                </div>
                                <Button
                                    @click="addAttendee"
                                    icon="pi pi-plus"
                                    label="Add External Attendee"
                                    class="p-button-text p-button-sm"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <Textarea
                        v-model="form.notes"
                        class="w-full"
                        rows="3"
                        placeholder="Additional notes (optional)"
                    />
                    <small v-if="errors && errors.notes" class="text-red-500">{{ errors.notes }}</small>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <Button
                        @click="router.visit('/schedule-calendar')"
                        label="Cancel"
                        class="p-button-text"
                        type="button"
                    />
                    <Button
                        type="submit"
                        label="Create Schedule"
                        icon="pi pi-check"
                        class="p-button-primary"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Calendar from 'primevue/calendar'
import Dropdown from 'primevue/dropdown'
// Remove unused import
// import AutoComplete from 'primevue/autocomplete'

const props = defineProps({
    date: String, // Optional pre-selected date
    users: {
        type: Array,
        default: () => []
    }
})

// Form validation errors
const errors = ref({})

// User selection
const selectedUsers = ref([])
const availableUsers = ref([])
const usersLoading = ref(true)

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

// Options
const typeOptions = [
    { label: 'Health Checkup', value: 'health_checkup' },
    { label: 'Vaccination', value: 'vaccination' },
    { label: 'Meeting', value: 'meeting' },
    { label: 'Training', value: 'training' },
    { label: 'Other', value: 'other' }
]

const statusOptions = [
    { label: 'Scheduled', value: 'scheduled' },
    { label: 'Completed', value: 'completed' },
    { label: 'Cancelled', value: 'cancelled' }
]

// Initialize form with pre-selected date if provided
onMounted(() => {
    try {
        if (props.date) {
            const selectedDate = new Date(props.date)
            selectedDate.setHours(9, 0, 0, 0) // Default to 9:00 AM
            form.start_datetime = selectedDate
            
            const endDate = new Date(selectedDate)
            endDate.setHours(10, 0, 0, 0) // Default to 10:00 AM (1 hour duration)
            form.end_datetime = endDate
        }
        
        // Load users if not provided as props
        if (props.users && props.users.length > 0) {
            availableUsers.value = props.users
            usersLoading.value = false
        } else {
            loadUsers().catch(error => {
                console.error('Failed to load users:', error)
                usersLoading.value = false
            })
        }
    } catch (error) {
        console.error('Error in onMounted:', error)
        usersLoading.value = false
    }
})

// Load users from API
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

// Get user name by ID
const getUserName = (userId) => {
    try {
        if (!availableUsers.value || !Array.isArray(availableUsers.value) || availableUsers.value.length === 0) {
            return 'Loading...'
        }
        const user = availableUsers.value.find(u => u && u.id === userId)
        return (user && user.full_name) ? user.full_name : 'Unknown User'
    } catch (error) {
        console.error('Error in getUserName:', error)
        return 'Error loading user'
    }
}

// Remove user from selection
const removeUser = (userId) => {
    const index = selectedUsers.value.indexOf(userId)
    if (index > -1) {
        selectedUsers.value.splice(index, 1)
    }
}

// Validation
const validateForm = () => {
    const validationErrors = {}
    
    if (!form.title) validationErrors.title = 'Title is required'
    if (!form.start_datetime) validationErrors.start_datetime = 'Start date and time is required'
    if (!form.end_datetime) validationErrors.end_datetime = 'End date and time is required'
    if (!form.type) validationErrors.type = 'Type is required'
    if (!form.status) validationErrors.status = 'Status is required'
    
    if (form.start_datetime && form.end_datetime && form.start_datetime >= form.end_datetime) {
        validationErrors.end_datetime = 'End time must be after start time'
    }
    
    return validationErrors
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

// Form submission
const submit = () => {
    try {
        // Client-side validation
        const validationErrors = validateForm()
        if (errors.value) {
            errors.value = validationErrors
        }
        
        if (Object.keys(validationErrors).length > 0) {
            return
        }
        
        // Clean up attendees array (remove empty strings)
        form.attendees = form.attendees.filter(attendee => attendee.trim() !== '')
        
        // Add selected users to the form
        form.selected_users = selectedUsers.value || []
        
        form.post(route('schedule-calendar.store'), {
            onSuccess: () => {
                if (errors.value) {
                    errors.value = {}
                }
            },
            onError: (serverErrors) => {
                if (errors.value) {
                    errors.value = { ...errors.value, ...serverErrors }
                }
            }
        })
    } catch (error) {
        console.error('Error in submit:', error)
    }
}
</script>

<style scoped>
.create-schedule {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 500;
    color: #374151;
}

:deep(.p-inputtext) {
    width: 100%;
}

:deep(.p-dropdown) {
    width: 100%;
}

:deep(.p-calendar) {
    width: 100%;
}

:deep(.p-calendar .p-inputtext) {
    width: 100%;
}
</style>
