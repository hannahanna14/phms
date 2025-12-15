<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Enhanced Header -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 mb-8 backdrop-blur-sm">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <div class="w-14 h-14 bg-gradient-to-r from-emerald-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="pi pi-pencil text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent mb-2">Edit Health Treatment</h1>
                            <p class="text-slate-600 font-medium">Update medical treatment record</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-slate-600 font-semibold">Patient:</span>
                                <div class="font-bold text-slate-900">{{ props.student.full_name }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Grade:</span>
                                <div class="font-bold text-slate-900">{{ props.treatment.grade_level }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Date:</span>
                                <div class="font-bold text-slate-900">{{ new Date(props.treatment.date).toLocaleDateString() }}</div>
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
                                required
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
                                required
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
                                required
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
    </div>

    <!-- Confirmation Dialog -->
    <ConfirmDialog></ConfirmDialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import { useToastStore } from '@/Stores/toastStore';
import '../../../css/pages/shared/CrudForm.css';
import '../../../css/pages/HealthTreatment/Edit.css';

const props = defineProps({
    treatment: Object,
    student: Object,
    timer_status: Object,
    remaining_minutes: Number
});

// Toast store and confirm dialog
const { showSuccess } = useToastStore();
const confirm = useConfirm();

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

    // Show confirmation dialog
    confirm.require({
        message: 'Are you sure you want to update this health treatment record?',
        header: 'Confirm Update',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-text p-button-secondary',
        acceptClass: 'p-button-primary',
        acceptLabel: 'Yes, Update',
        rejectLabel: 'Cancel',
        accept: () => {
            performUpdate();
        }
    });
}

const performUpdate = () => {
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
            showSuccess('Health Treatment Updated', 'The health treatment record has been updated successfully!');
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

<style scoped>
/* Invalid field styling */
input:invalid:not(:placeholder-shown),
textarea:invalid:not(:placeholder-shown) {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}

input:invalid:focus:not(:placeholder-shown),
textarea:invalid:focus:not(:placeholder-shown) {
    outline: 2px solid #ef4444 !important;
    outline-offset: 0px;
}

:deep(.p-inputtext:invalid:not(:placeholder-shown)),
:deep(.p-inputtextarea:invalid:not(:placeholder-shown)) {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}
</style>