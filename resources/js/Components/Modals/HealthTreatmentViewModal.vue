<template>
    <Dialog 
        :visible="visible" 
        modal 
        :header="'View Health Treatment'"
        :style="{ width: '50rem' }" 
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        @update:visible="$emit('close')"
    >
        <div v-if="treatment" class="space-y-6">
            <!-- Student Info -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-2">Student Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><strong>Name:</strong> {{ student?.full_name }}</div>
                    <div><strong>LRN:</strong> {{ student?.lrn }}</div>
                    <div><strong>Grade:</strong> {{ treatment.grade_level }}</div>
                    <div><strong>Date:</strong> {{ new Date(treatment.date).toLocaleDateString() }}</div>
                </div>
            </div>

            <!-- Treatment Details -->
            <div>
                <h3 class="font-semibold text-gray-700 mb-4">Treatment Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <div class="p-3 bg-gray-50 rounded border">{{ treatment.title }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chief Complaint</label>
                        <div class="p-3 bg-gray-50 rounded border">{{ treatment.chief_complaint }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment</label>
                        <div class="p-3 bg-gray-50 rounded border">{{ treatment.treatment }}</div>
                    </div>
                    
                    <div v-if="treatment.remarks">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <div class="p-3 bg-gray-50 rounded border">{{ treatment.remarks }}</div>
                    </div>
                </div>
            </div>


            <!-- Record Information -->
            <div class="pt-4 border-t">
                <h3 class="font-semibold text-gray-700 mb-4">Record Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><strong>Created:</strong> {{ formatDateTime(treatment.created_at) }}</div>
                    <div><strong>Last Updated:</strong> {{ formatDateTime(treatment.updated_at) }}</div>
                </div>
            </div>
        </div>

    </Dialog>
</template>

<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { useTimerNotifications } from '@/Utils/timerMixin.js';
import { integrateHealthTreatmentNotifications } from '@/Utils/notificationIntegration.js';

const props = defineProps({
    visible: Boolean,
    treatment: Object,
    student: Object,
    timerStatus: Object,
    remainingMinutes: Number
});

const emit = defineEmits(['close', 'edit']);

const getAlertClass = () => {
    if (props.timerStatus?.status === 'expired') return 'bg-red-100 text-red-800';
    if (props.timerStatus?.status === 'active') return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
};

const editTreatment = () => {
    emit('edit', props.treatment);
};

const formatDateTime = (dateTime) => {
    if (!dateTime) return 'N/A';
    return new Date(dateTime).toLocaleString('en-US', {
        month: '2-digit',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
};

// Initialize timer notifications with error handling
let startTimerMonitoring, stopTimerMonitoring;

try {
    const timerNotifications = useTimerNotifications('health');
    startTimerMonitoring = timerNotifications.startTimerMonitoring;
    stopTimerMonitoring = timerNotifications.stopTimerMonitoring;
} catch (error) {
    console.error('❌ Failed to import timer notifications:', error);
}

// Watch for visibility changes to start/stop timer monitoring
watch(() => props.visible, (newVisible) => {
    if (newVisible && props.timerStatus?.status === 'active' && props.remainingMinutes > 0 && startTimerMonitoring) {
        console.log('✅ Starting timer monitoring for health treatment modal:', props.treatment?.title);
        try {
            startTimerMonitoring(props.student, props.treatment, props.remainingMinutes);
        } catch (error) {
            console.error('❌ Failed to start timer monitoring:', error);
        }
    } else if (!newVisible && stopTimerMonitoring) {
        console.log('🛑 Stopping timer monitoring for health treatment modal');
        try {
            stopTimerMonitoring();
        } catch (error) {
            console.error('Error stopping timer monitoring:', error);
        }
    }
});

// Stop monitoring when component unmounts
onUnmounted(() => {
    if (stopTimerMonitoring) {
        try {
            stopTimerMonitoring();
        } catch (error) {
            console.error('Error stopping timer monitoring:', error);
        }
    }
});
</script>
