<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Edit Incident Report</h1>
                <Button label="Back" icon="pi pi-arrow-left" outlined @click="goBack" />
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form @submit.prevent="updateIncident">
                    <!-- Student Info (Read-only) -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2">Student Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><strong>Name:</strong> {{ student.full_name }}</div>
                            <div><strong>LRN:</strong> {{ student.lrn }}</div>
                            <div><strong>Grade:</strong> {{ incident.grade_level }}</div>
                            <div><strong>School Year:</strong> {{ incident.school_year }}</div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                <i class="pi pi-calendar mr-1"></i>
                                Date of Incident
                            </label>
                            <DatePicker 
                                v-model="form.date" 
                                dateFormat="mm/dd/yy"
                                placeholder="MM/DD/YYYY"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.date }"
                            />
                            <small v-if="form.errors.date" class="text-red-500">{{ form.errors.date }}</small>
                        </div>
                        
                        <!-- Complaint and Actions Taken -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Complaint
                                </label>
                                <Textarea 
                                    v-model="form.complaint" 
                                    placeholder="Enter complaint details"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.complaint }"
                                />
                                <small v-if="form.errors.complaint" class="text-red-500">{{ form.errors.complaint }}</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Actions Taken
                                </label>
                                <Textarea 
                                    v-model="form.actions_taken" 
                                    placeholder="Enter actions taken"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.actions_taken }"
                                />
                                <small v-if="form.errors.actions_taken" class="text-red-500">{{ form.errors.actions_taken }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 pt-6 mt-6 border-t">
                        <Button 
                            label="Cancel" 
                            severity="secondary" 
                            outlined 
                            @click="goBack"
                            type="button"
                        />
                        <Button 
                            label="Update" 
                            type="submit"
                            :loading="form.processing"
                        />
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import Button from 'primevue/button'
import Textarea from 'primevue/textarea'
import DatePicker from 'primevue/datepicker'

const { incident, student } = usePage().props

// Initialize form with existing incident data
const form = useForm({
    date: new Date(incident.date),
    complaint: incident.complaint,
    actions_taken: incident.actions_taken
})

const updateIncident = () => {
    form.put(route('incident.update', incident.id), {
        onSuccess: () => {
            // Redirect back to incident list
            window.location.href = `/pupil-health/incident/${student.id}?grade=${encodeURIComponent(incident.grade_level)}`
        },
        onError: (errors) => {
            console.log('Form submission failed:', errors)
        }
    })
}

const goBack = () => {
    window.history.back()
}
</script>

<style scoped>
/* Additional styling if needed */
</style>
