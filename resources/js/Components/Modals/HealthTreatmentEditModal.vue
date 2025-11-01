<template>
    <Dialog 
        :visible="visible" 
        modal 
        :header="'Edit Health Treatment'"
        :style="{ width: '50rem' }" 
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        @update:visible="$emit('close')"
    >
        <div v-if="treatment" class="space-y-4">
            <form @submit.prevent="updateTreatment">
                <!-- Title Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input 
                        type="text"
                        v-model="form.title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>

                <!-- Chief Complaint Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chief Complaint</label>
                    <textarea 
                        v-model="form.chief_complaint"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    ></textarea>
                </div>

                <!-- Treatment Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Treatment</label>
                    <textarea 
                        v-model="form.treatment"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    ></textarea>
                </div>

                <!-- Remarks Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea 
                        v-model="form.remarks"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                </div>
            </form>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <Button 
                    label="Cancel" 
                    icon="pi pi-times" 
                    outlined 
                    @click="$emit('close')" 
                />
                <Button 
                    label="Update" 
                    icon="pi pi-check" 
                    @click="updateTreatment"
                    :loading="updating"
                />
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
// Import modal styles (shared with other modals)
import '../../../css/components/modals/HealthTreatmentViewModal.css';

const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    },
    treatment: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'updated']);

const updating = ref(false);
const form = ref({
    title: '',
    chief_complaint: '',
    treatment: '',
    remarks: ''
});

// Watch for treatment changes to populate form
watch(() => props.treatment, (newTreatment) => {
    if (newTreatment) {
        form.value = {
            title: newTreatment.title || '',
            chief_complaint: newTreatment.chief_complaint || '',
            treatment: newTreatment.treatment || '',
            remarks: newTreatment.remarks || ''
        };
    }
}, { immediate: true });

const updateTreatment = async () => {
    if (!props.treatment) return;
    
    updating.value = true;
    
    try {
        const response = await fetch(`/api/health-treatment/${props.treatment.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(form.value)
        });
        
        if (response.ok) {
            const updatedTreatment = await response.json();
            emit('updated', updatedTreatment);
            emit('close');
        } else {
            console.error('Failed to update treatment');
        }
    } catch (error) {
        console.error('Error updating treatment:', error);
    } finally {
        updating.value = false;
    }
};
</script>
