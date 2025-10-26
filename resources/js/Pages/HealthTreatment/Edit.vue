<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Edit Health Treatment</h1>
            </div>


            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form @submit.prevent="updateTreatment">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
                            <InputText 
                                v-model="form.title" 
                                class="w-full" 
                                :class="{ 'border-red-500': titleExceeded }"
                                :disabled="!canEdit"
                                @input="handleInput('title', limits.title)"
                                :maxlength="limits.title"
                            />
                            <div class="flex justify-end mt-1">
                                <small :class="titleExceeded ? 'text-red-500 font-semibold' : 'text-gray-500'">
                                    {{ titleCount }}/{{ limits.title }}
                                </small>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Chief Complaint <span class="text-red-500">*</span></label>
                            <Textarea 
                                v-model="form.chief_complaint" 
                                rows="3" 
                                class="w-full" 
                                :class="{ 'border-red-500': chiefComplaintExceeded }"
                                :disabled="!canEdit"
                                @input="handleInput('chief_complaint', limits.chief_complaint)"
                                :maxlength="limits.chief_complaint"
                            />
                            <div class="flex justify-end mt-1">
                                <small :class="chiefComplaintExceeded ? 'text-red-500 font-semibold' : 'text-gray-500'">
                                    {{ chiefComplaintCount }}/{{ limits.chief_complaint }}
                                </small>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Treatment <span class="text-red-500">*</span></label>
                            <Textarea 
                                v-model="form.treatment" 
                                rows="4" 
                                class="w-full" 
                                :class="{ 'border-red-500': treatmentExceeded }"
                                :disabled="!canEdit"
                                @input="handleInput('treatment', limits.treatment)"
                                :maxlength="limits.treatment"
                            />
                            <div class="flex justify-end mt-1">
                                <small :class="treatmentExceeded ? 'text-red-500 font-semibold' : 'text-gray-500'">
                                    {{ treatmentCount }}/{{ limits.treatment }}
                                </small>
                            </div>
                        </div>
                        
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Remarks</label>
                            <Textarea 
                                v-model="form.remarks" 
                                rows="2" 
                                class="w-full" 
                                :class="{ 'border-red-500': remarksExceeded }"
                                :disabled="!canEdit"
                                @input="handleInput('remarks', limits.remarks)"
                                :maxlength="limits.remarks"
                            />
                            <div class="flex justify-end mt-1">
                                <small :class="remarksExceeded ? 'text-red-500 font-semibold' : 'text-gray-500'">
                                    {{ remarksCount }}/{{ limits.remarks }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <Button label="Cancel" outlined @click="goBack" />
                        <Button v-if="canEdit" label="Update" type="submit" :loading="loading" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';

const props = defineProps({
    treatment: Object,
    student: Object,
    timer_status: Object,
    remaining_minutes: Number
});

const loading = ref(false);

const form = ref({
    title: props.treatment.title,
    chief_complaint: props.treatment.chief_complaint,
    treatment: props.treatment.treatment,
    remarks: props.treatment.remarks || ''
});

// Character limits
const limits = {
    title: 100,
    chief_complaint: 100,
    treatment: 100,
    remarks: 100
};

// Computed properties for character counts and validation
const titleCount = computed(() => form.value.title.length);
const chiefComplaintCount = computed(() => form.value.chief_complaint.length);
const treatmentCount = computed(() => form.value.treatment.length);
const remarksCount = computed(() => form.value.remarks.length);

const titleExceeded = computed(() => titleCount.value > limits.title);
const chiefComplaintExceeded = computed(() => chiefComplaintCount.value > limits.chief_complaint);
const treatmentExceeded = computed(() => treatmentCount.value > limits.treatment);
const remarksExceeded = computed(() => remarksCount.value > limits.remarks);

// Prevent input when limit is exceeded
const handleInput = (field, maxLength) => {
    if (form.value[field].length > maxLength) {
        form.value[field] = form.value[field].substring(0, maxLength);
    }
};

const canEdit = computed(() => {
    return props.timer_status?.status !== 'expired';
});

const getAlertClass = () => {
    if (props.timer_status?.status === 'expired') return 'bg-red-100 text-red-800';
    if (props.timer_status?.status === 'active') return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
};

const updateTreatment = () => {
    if (!canEdit.value) return;
    
    loading.value = true;
    
    // Format date to YYYY-MM-DD to avoid timezone issues
    const formData = { ...form.value };
    if (formData.date instanceof Date) {
        const year = formData.date.getFullYear();
        const month = String(formData.date.getMonth() + 1).padStart(2, '0');
        const day = String(formData.date.getDate()).padStart(2, '0');
        formData.date = `${year}-${month}-${day}`;
    }
    
    // Use Inertia router for proper form submission
    router.put(route('health-treatment.update', props.treatment.id), formData, {
        onSuccess: () => {
            loading.value = false;
        },
        onError: (errors) => {
            console.error('Update failed:', errors);
            loading.value = false;
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};

const goBack = () => {
    // Go back to the health examination page with proper parameters
    router.visit(`/pupil-health/health-examination/${props.student.id}?grade=${props.treatment.grade_level}`);
};
</script>