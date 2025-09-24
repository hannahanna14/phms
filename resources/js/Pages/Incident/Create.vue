<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Pupil Incident Report</h1>
                <p class="text-gray-600">Naawan Central School</p>
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
                                    Date
                                </label>
                                <Calendar 
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
                                    Complaint
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
                                    Actions Taken
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

                        <!-- Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <Select 
                                    v-model="form.status" 
                                    :options="statusOptions"
                                    placeholder="Select status"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.status }"
                                />
                                <small v-if="form.errors.status" class="text-red-500">{{ form.errors.status }}</small>
                            </div>
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="flex items-center text-blue-700">
                                        <i class="pi pi-clock mr-2"></i>
                                        <span class="text-sm font-medium">Timer will start automatically (2 hours)</span>
                                    </div>
                                </div>
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
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import Select from 'primevue/select';
import Card from 'primevue/card';

const { student } = usePage().props;

const form = useForm({
    student_id: student.id,
    date: new Date(),
    complaint: '',
    actions_taken: '',
    status: 'pending'
});

const statusOptions = [
    { label: 'Pending', value: 'pending' },
    { label: 'In Progress', value: 'in_progress' },
    { label: 'Resolved', value: 'resolved' },
    { label: 'Closed', value: 'closed' }
];

const submit = () => {
    form.post(route('incident.store'), {
        onSuccess: () => {
            // Redirect back to incident page
            window.history.back();
        }
    });
};

const cancel = () => {
    window.history.back();
};
</script>

<style scoped>
/* Additional styling if needed */
</style>
