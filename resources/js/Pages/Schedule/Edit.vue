<template>
    <Head title="Edit Schedule" />
    <div class="edit-schedule">
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Edit Schedule</h1>
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
                    <small v-if="errors.title" class="text-red-500">{{ errors.title }}</small>
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
                    <small v-if="errors.description" class="text-red-500">{{ errors.description }}</small>
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
                        <small v-if="errors.start_datetime" class="text-red-500">{{ errors.start_datetime }}</small>
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
                        <small v-if="errors.end_datetime" class="text-red-500">{{ errors.end_datetime }}</small>
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
                        <small v-if="errors.type" class="text-red-500">{{ errors.type }}</small>
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
                        <small v-if="errors.status" class="text-red-500">{{ errors.status }}</small>
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
                    <small v-if="errors.location" class="text-red-500">{{ errors.location }}</small>
                </div>

                <!-- Attendees -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Attendees</label>
                    <div class="space-y-2">
                        <div v-for="(attendee, index) in form.attendees" :key="index" class="flex items-center space-x-2">
                            <InputText
                                v-model="form.attendees[index]"
                                class="flex-1"
                                placeholder="Enter attendee name"
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
                            label="Add Attendee"
                            class="p-button-text p-button-sm"
                        />
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
                    <small v-if="errors.notes" class="text-red-500">{{ errors.notes }}</small>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <Button
                        @click="$inertia.visit(route('schedule-calendar.index'))"
                        label="Cancel"
                        class="p-button-text"
                        type="button"
                    />
                    <Button
                        type="submit"
                        label="Update Schedule"
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
import { Head, useForm } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Calendar from 'primevue/calendar'
import Dropdown from 'primevue/dropdown'

const props = defineProps({
    schedule: Object
})

// Form validation errors
const errors = ref({})

// Helper function to parse dates safely
const parseDate = (dateString) => {
    if (!dateString) return null
    const date = new Date(dateString)
    return isNaN(date.getTime()) ? null : date
}

// Initialize form with empty data first
const form = useForm({
    title: '',
    description: '',
    start_datetime: null,
    end_datetime: null,
    type: 'other',
    status: 'scheduled',
    location: '',
    attendees: [''],
    notes: ''
})

// Debug logging and form population
onMounted(() => {
    console.log('Props received:', props)
    console.log('Schedule data:', props.schedule)
    console.log('Schedule keys:', props.schedule ? Object.keys(props.schedule) : 'No schedule')
    
    // If schedule data exists, populate the form
    if (props.schedule) {
        console.log('Raw schedule data:', props.schedule)
        
        form.title = props.schedule.title || ''
        form.description = props.schedule.description || ''
        form.start_datetime = parseDate(props.schedule.start_datetime)
        form.end_datetime = parseDate(props.schedule.end_datetime)
        form.type = props.schedule.type || 'other'
        form.status = props.schedule.status || 'scheduled'
        form.location = props.schedule.location || ''
        form.attendees = props.schedule.attendees?.length > 0 ? props.schedule.attendees : ['']
        form.notes = props.schedule.notes || ''
        
        console.log('Form populated with:', {
            title: form.title,
            description: form.description,
            start_datetime: form.start_datetime,
            end_datetime: form.end_datetime,
            type: form.type,
            status: form.status,
            location: form.location,
            attendees: form.attendees,
            notes: form.notes
        })
    } else {
        console.error('No schedule data received!')
    }
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
    // Client-side validation
    const validationErrors = validateForm()
    errors.value = validationErrors
    
    if (Object.keys(validationErrors).length > 0) {
        return
    }
    
    // Filter out empty attendees
    form.attendees = form.attendees.filter(attendee => attendee.trim() !== '')
    
    form.put(route('schedule-calendar.update', props.schedule.id), {
        onSuccess: () => {
            errors.value = {}
        },
        onError: (serverErrors) => {
            errors.value = { ...errors.value, ...serverErrors }
        }
    })
}
</script>

<style scoped>
.edit-schedule {
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
