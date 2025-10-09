<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">View Incident Report</h1>
                <div class="flex gap-3">
                    <Button 
                        v-if="userRole === 'nurse'" 
                        label="Edit" 
                        icon="pi pi-pencil" 
                        @click="editIncident"
                    />
                    <Button label="Back" icon="pi pi-arrow-left" outlined @click="goBack" />
                </div>
            </div>

            <!-- Incident Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Info -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2">Student Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><strong>Name:</strong> {{ student.full_name }}</div>
                            <div><strong>LRN:</strong> {{ student.lrn }}</div>
                            <div><strong>Grade:</strong> {{ incident.grade_level }}</div>
                            <div><strong>Date:</strong> {{ new Date(incident.date).toLocaleDateString() }}</div>
                        </div>
                    </div>

                    <!-- Incident Details -->
                    <div class="md:col-span-2">
                        <h3 class="font-semibold text-gray-700 mb-4">Incident Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Incident</label>
                                <div class="p-3 bg-gray-50 rounded border">{{ new Date(incident.date).toLocaleDateString() }}</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Complaint</label>
                                <div class="p-3 bg-gray-50 rounded border">{{ incident.complaint }}</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Actions Taken</label>
                                <div class="p-3 bg-gray-50 rounded border">{{ incident.actions_taken }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="md:col-span-2 pt-4 border-t">
                        <h3 class="font-semibold text-gray-700 mb-2">Record Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                            <div><strong>Created:</strong> {{ new Date(incident.created_at).toLocaleString() }}</div>
                            <div><strong>Last Updated:</strong> {{ new Date(incident.updated_at).toLocaleString() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import Button from 'primevue/button'

const { incident, student } = usePage().props
const userRole = usePage().props.auth?.user?.role

const editIncident = () => {
    window.location.href = `/pupil-health/incident/${incident.id}/edit`
}

const goBack = () => {
    window.history.back()
}
</script>

<style scoped>
/* Additional styling if needed */
</style>
