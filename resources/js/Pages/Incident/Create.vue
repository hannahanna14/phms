<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Enhanced Header -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 mb-8 backdrop-blur-sm">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <div class="w-14 h-14 bg-gradient-to-r from-red-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="pi pi-plus text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent mb-2">Create Incident Report</h1>
                            <p class="text-slate-600 font-medium">Document safety incident for {{ student.full_name }}</p>
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
                                <div class="font-bold text-slate-900">{{ student.grade_level }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">LRN:</span>
                                <div class="font-mono font-bold text-slate-900">{{ student.lrn || 'Not Available' }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Section:</span>
                                <div class="font-bold text-slate-900">{{ student.section || 'Not Assigned' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Draft Restored Notification -->
            <div v-if="showDraftNotification" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <i class="pi pi-info-circle text-blue-600 mr-2"></i>
                    <span class="text-blue-800 text-sm">
                        <strong>Draft restored:</strong> Your previous form data has been recovered.
                    </span>
                    <button @click="showDraftNotification = false" class="ml-auto text-blue-600 hover:text-blue-800">
                        <i class="pi pi-times"></i>
                    </button>
                </div>
            </div>

            <!-- Incident Form -->
            <Card>
                <template #content>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="pi pi-calendar mr-1"></i>
                                    Date <span class="text-red-500">*</span>
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
                        </div>

                        <!-- Complaint and Actions Taken -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Complaint <span class="text-red-500">*</span>
                                </label>
                                <Textarea 
                                    v-model="form.complaint" 
                                    placeholder="Enter text"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.complaint }"
                                />
                                <small v-if="form.errors.complaint" class="text-red-500">{{ form.errors.complaint }}</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Actions Taken <span class="text-red-500">*</span>
                                </label>
                                <Textarea 
                                    v-model="form.actions_taken" 
                                    placeholder="Enter text"
                                    rows="4"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.actions_taken }"
                                />
                                <small v-if="form.errors.actions_taken" class="text-red-500">{{ form.errors.actions_taken }}</small>
                            </div>
                        </div>


                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-4">
                            <Button 
                                label="Cancel" 
                                severity="secondary" 
                                outlined 
                                @click="cancel"
                                type="button"
                            />
                            <Button 
                                label="Add" 
                                type="submit"
                                :loading="form.processing"
                            />
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </div>

    <!-- Confirmation Dialog -->
    <ConfirmDialog />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Textarea from 'primevue/textarea';
import DatePicker from 'primevue/datepicker';
import Card from 'primevue/card';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import { useFormPersistence } from '@/composables/useFormPersistence';
import { useToastStore } from '@/Stores/toastStore';
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css';
// Import page-specific styles
import '../../../css/pages/Incident/Create.css';

const { student } = usePage().props;

// Initialize form
const form = useForm({
    student_id: student.id,
    date: new Date(),
    complaint: '',
    actions_taken: ''
});

// Initialize composables
const confirm = useConfirm();
const { showSuccess, showError } = useToastStore();

// Set up form persistence
const {
    showDraftNotification,
    initializeForm,
    setupAutoSave,
    onSubmitSuccess,
    onCancel,
    hasUnsavedChanges
} = useFormPersistence(`incident_form_${student.id}`, form, {
    excludeFields: ['student_id'], // Don't save student_id as it's always the same
    autoSave: true,
    showNotification: true
});


// Initialize form persistence
onMounted(() => {
    initializeForm();
    setupAutoSave();
});

const submit = () => {
    // Show confirmation dialog
    confirm.require({
        message: 'Are you sure you want to create this incident report?',
        header: 'Confirm Creation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Create Report',
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

    form.post(route('incident.store'), {
        onSuccess: (response) => {
            showSuccess('Success', 'Incident report created successfully!');
            // Clear saved form data on successful submission
            onSubmitSuccess();
            // Backend handles the redirect automatically
        },
        onError: (errors) => {
            // Keep saved data if there's an error
            console.log('Form submission failed, keeping saved data');
            console.log('Validation errors:', errors);
            showError('Submission Error', 'Failed to create incident report. Please try again.');
        }
    });
}

const cancel = () => {
    // Check if there are unsaved changes
    const hasChanges = form.complaint || form.actions_taken;
    onCancel(hasChanges);
    window.history.back();
};
</script>
