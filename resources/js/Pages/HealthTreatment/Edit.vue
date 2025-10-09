<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Edit Health Treatment</h1>
                <Button label="Back" icon="pi pi-arrow-left" outlined @click="goBack" />
            </div>


            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form @submit.prevent="updateTreatment">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Title</label>
                            <InputText v-model="form.title" class="w-full" :disabled="!canEdit" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Chief Complaint</label>
                            <Textarea v-model="form.chief_complaint" rows="3" class="w-full" :disabled="!canEdit" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Treatment</label>
                            <Textarea v-model="form.treatment" rows="4" class="w-full" :disabled="!canEdit" />
                        </div>
                        
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Remarks</label>
                            <Textarea v-model="form.remarks" rows="2" class="w-full" :disabled="!canEdit" />
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
import { ref, computed } from 'vue';
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

const canEdit = computed(() => {
    return props.timer_status?.status !== 'expired';
});

const getAlertClass = () => {
    if (props.timer_status?.status === 'expired') return 'bg-red-100 text-red-800';
    if (props.timer_status?.status === 'active') return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
};

const updateTreatment = async () => {
    if (!canEdit.value) return;
    
    loading.value = true;
    try {
        await axios.put(`/api/health-treatment/${props.treatment.id}`, form.value);
        alert('Treatment updated successfully!');
        goBack();
    } catch (error) {
        alert('Failed to update treatment.');
    } finally {
        loading.value = false;
    }
};

const goBack = () => {
    window.history.back();
};
</script>