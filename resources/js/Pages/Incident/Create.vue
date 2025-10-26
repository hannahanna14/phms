<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Pupil Incident Report</h1>
                <p class="text-gray-600">Naawan Central School</p>
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

            <!-- Student Info -->
            <Card class="mb-6">
                <template #content>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><strong>Student:</strong> {{ student.full_name }}</p>
                            <p><strong>LRN:</strong> {{ student.lrn }}</p>
                        </div>
                        <div>
                            <p><strong>Grade Level:</strong> {{ student.grade_level }}</p>
                            <p><strong>Section:</strong> {{ student.section || 'Not Assigned' }}</p>
                        </div>
                    </div>
                </template>
            </Card>

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
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Textarea from 'primevue/textarea';
import DatePicker from 'primevue/datepicker';
import Card from 'primevue/card';
import { useFormPersistence } from '@/composables/useFormPersistence';

const { student } = usePage().props;

// Initialize form
const form = useForm({
    student_id: student.id,
    date: new Date(),
    complaint: '',
    actions_taken: ''
});

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
    // Format date to YYYY-MM-DD to avoid timezone issues
    if (form.date instanceof Date) {
        const year = form.date.getFullYear();
        const month = String(form.date.getMonth() + 1).padStart(2, '0');
        const day = String(form.date.getDate()).padStart(2, '0');
        form.date = `${year}-${month}-${day}`;
    }
    
    form.post(route('incident.store'), {
        onSuccess: (response) => {
            // Clear saved form data on successful submission
            onSubmitSuccess();
            // Backend handles the redirect automatically
        },
        onError: (errors) => {
            // Keep saved data if there's an error
            console.log('Form submission failed, keeping saved data');
            console.log('Validation errors:', errors);
        }
    });
};

const cancel = () => {
    // Check if there are unsaved changes
    const hasChanges = form.complaint || form.actions_taken;
    onCancel(hasChanges);
    window.history.back();
};
</script>

<style scoped>
/* Additional styling if needed */
</style>
