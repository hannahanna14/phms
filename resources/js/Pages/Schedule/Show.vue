<template>
    <Head title="Schedule Details" />
    <div class="show-schedule">
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Schedule Details</h1>
                <div class="flex space-x-2">
                    <Button
                        @click="$inertia.visit(route('schedule-calendar.edit', schedule.id))"
                        icon="pi pi-pencil"
                        label="Edit"
                        class="p-button-primary"
                    />
                    <Button
                        @click="$inertia.visit(route('schedule-calendar.index'))"
                        icon="pi pi-arrow-left"
                        label="Back to Calendar"
                        class="p-button-text"
                    />
                </div>
            </div>

            <!-- Schedule Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title and Description -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ schedule.title }}</h2>
                        <p v-if="schedule.description" class="text-gray-700">{{ schedule.description }}</p>
                        <p v-else class="text-gray-500 italic">No description provided</p>
                    </div>

                    <!-- Date and Time -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-900 mb-3">Date & Time</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-blue-700">Start</div>
                                <div class="text-blue-900">{{ formatDateTime(schedule.start_datetime) }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-blue-700">End</div>
                                <div class="text-blue-900">{{ formatDateTime(schedule.end_datetime) }}</div>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <div class="text-sm font-medium text-blue-700">Duration</div>
                            <div class="text-blue-900">{{ getDuration() }}</div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div v-if="schedule.location" class="bg-green-50 rounded-lg p-4">
                        <h3 class="font-semibold text-green-900 mb-2">Location</h3>
                        <p class="text-green-800">{{ schedule.location }}</p>
                    </div>

                    <!-- Attendees -->
                    <div v-if="schedule.attendees && schedule.attendees.length > 0" class="bg-purple-50 rounded-lg p-4">
                        <h3 class="font-semibold text-purple-900 mb-3">Attendees</h3>
                        <div class="space-y-2">
                            <div
                                v-for="(attendee, index) in schedule.attendees"
                                :key="index"
                                class="flex items-center space-x-2"
                            >
                                <i class="pi pi-user text-purple-600"></i>
                                <span class="text-purple-800">{{ attendee }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div v-if="schedule.notes" class="bg-yellow-50 rounded-lg p-4">
                        <h3 class="font-semibold text-yellow-900 mb-2">Notes</h3>
                        <p class="text-yellow-800 whitespace-pre-wrap">{{ schedule.notes }}</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status and Type -->
                    <div class="bg-white border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Status & Type</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm font-medium text-gray-600">Status</div>
                                <Tag
                                    :value="schedule.status"
                                    :severity="getStatusSeverity(schedule.status)"
                                    class="mt-1"
                                />
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-600">Type</div>
                                <Tag
                                    :value="schedule.type.replace('_', ' ')"
                                    :severity="getTypeSeverity(schedule.type)"
                                    class="mt-1"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Created By -->
                    <div class="bg-white border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Created By</h3>
                        <div class="flex items-center space-x-3">
                            <Avatar
                                icon="pi pi-user"
                                class="bg-blue-500 text-white"
                                shape="circle"
                            />
                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ schedule.creator?.full_name || 'Unknown' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ formatDate(schedule.created_at) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Quick Actions</h3>
                        <div class="space-y-2">
                            <Button
                                v-if="schedule.status === 'scheduled'"
                                @click="updateStatus('completed')"
                                icon="pi pi-check"
                                label="Mark as Completed"
                                class="w-full p-button-success p-button-sm"
                            />
                            <Button
                                v-if="schedule.status === 'scheduled'"
                                @click="updateStatus('cancelled')"
                                icon="pi pi-times"
                                label="Cancel Schedule"
                                class="w-full p-button-danger p-button-sm"
                            />
                            <Button
                                v-if="schedule.status !== 'scheduled'"
                                @click="updateStatus('scheduled')"
                                icon="pi pi-refresh"
                                label="Reschedule"
                                class="w-full p-button-warning p-button-sm"
                            />
                            <Button
                                @click="deleteSchedule"
                                icon="pi pi-trash"
                                label="Delete Schedule"
                                class="w-full p-button-danger p-button-outlined p-button-sm"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Avatar from 'primevue/avatar'

const props = defineProps({
    schedule: Object
})

// Helper functions
const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatDate = (datetime) => {
    return new Date(datetime).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const getDuration = () => {
    const start = new Date(props.schedule.start_datetime)
    const end = new Date(props.schedule.end_datetime)
    const diffMs = end - start
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60))
    
    if (diffHours > 0) {
        return `${diffHours} hour${diffHours > 1 ? 's' : ''} ${diffMinutes > 0 ? `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''}` : ''}`
    } else {
        return `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''}`
    }
}

const getStatusSeverity = (status) => {
    const severities = {
        'scheduled': 'info',
        'completed': 'success',
        'cancelled': 'danger'
    }
    return severities[status] || 'secondary'
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

// Actions
const updateStatus = (newStatus) => {
    if (confirm(`Are you sure you want to mark this schedule as ${newStatus}?`)) {
        router.put(route('schedule-calendar.update', props.schedule.id), {
            ...props.schedule,
            status: newStatus
        }, {
            preserveScroll: true
        })
    }
}

const deleteSchedule = () => {
    if (confirm('Are you sure you want to delete this schedule? This action cannot be undone.')) {
        router.delete(route('schedule-calendar.destroy', props.schedule.id))
    }
}
</script>

<style scoped>
.show-schedule {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

:deep(.p-tag) {
    text-transform: capitalize;
}
</style>
