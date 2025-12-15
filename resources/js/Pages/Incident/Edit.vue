<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Enhanced Header -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 mb-8 backdrop-blur-sm">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <div class="w-14 h-14 bg-gradient-to-r from-red-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="pi pi-pencil text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent mb-2">Edit Incident Report</h1>
                            <p class="text-slate-600 font-medium">Update incident documentation</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-slate-600 font-semibold">Student:</span>
                                <div class="font-bold text-slate-900">{{ student.full_name }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Grade:</span>
                                <div class="font-bold text-slate-900">{{ incident.grade_level }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Date:</span>
                                <div class="font-bold text-slate-900">{{ new Date(incident.date).toLocaleDateString() }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Status:</span>
                                <div class="font-bold text-slate-900">{{ canEdit ? 'Editable' : 'Locked' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 backdrop-blur-sm">

            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form @submit.prevent="updateIncident">
                    <div class="space-y-4">
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                <i class="pi pi-calendar mr-1"></i>
                                Date of Incident <span class="text-red-500">*</span>
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
                                    Complaint <span class="text-red-500">*</span>
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
                                    Actions Taken <span class="text-red-500">*</span>
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
    </div>

    <!-- Confirmation Dialog -->
    <ConfirmDialog />
</template>

<script setup>
import { useForm, usePage, router } from '@inertiajs/vue3'
import Button from 'primevue/button'
import Textarea from 'primevue/textarea'
import DatePicker from 'primevue/datepicker'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from 'primevue/useconfirm'
import { useFormPersistence } from '@/composables/useFormPersistence';
import { useToastStore } from '@/Stores/toastStore'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css';
// Import page-specific styles
import '../../../css/pages/Incident/Edit.css';

const props = defineProps({
    incident: Object,
    student: Object
})

const { incident, student } = props

// Initialize composables
const confirm = useConfirm()
const { showSuccess, showError } = useToastStore()

// Initialize form with existing incident data
const form = useForm({
    date: new Date(incident.date),
    complaint: incident.complaint,
    actions_taken: incident.actions_taken
})

const updateIncident = () => {
    // Show confirmation dialog
    confirm.require({
        message: 'Are you sure you want to update this incident report?',
        header: 'Confirm Update',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Update Report',
            severity: 'success'
        },
        accept: () => {
            // Proceed with form submission
            submitForm()
        }
    })
}

const submitForm = () => {
    // Format date to YYYY-MM-DD to avoid timezone issues
    if (form.date instanceof Date) {
        const year = form.date.getFullYear();
        const month = String(form.date.getMonth() + 1).padStart(2, '0');
        const day = String(form.date.getDate()).padStart(2, '0');
        form.date = `${year}-${month}-${day}`;
    }

    form.put(route('incident.update', incident.id), {
        onSuccess: () => {
            showSuccess('Success', 'Incident report updated successfully!');
            // Redirect back to incident list using router
            router.visit(`/pupil-health/incident/${student.id}?grade=${encodeURIComponent(incident.grade_level)}`)
        },
        onError: (errors) => {
            console.log('Form submission failed:', errors)
            showError('Update Error', 'Failed to update incident report. Please try again.');
        }
    })
}

const goBack = () => {
    // Go back to the incident page with proper parameters
    router.visit(`/pupil-health/incident/${student.id}?grade=${encodeURIComponent(incident.grade_level)}`)
};
</script>
