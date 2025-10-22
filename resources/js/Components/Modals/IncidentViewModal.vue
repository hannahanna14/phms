<template>
    <Dialog 
        :visible="visible" 
        modal 
        :header="'View Incident Report'"
        :style="{ width: '50rem' }" 
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        @update:visible="$emit('close')"
    >
        <div v-if="incident" class="space-y-6">
            <!-- Student Info -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-2">Student Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><strong>Name:</strong> {{ student?.full_name }}</div>
                    <div><strong>LRN:</strong> {{ student?.lrn }}</div>
                    <div><strong>Grade:</strong> {{ incident.grade_level }}</div>
                    <div><strong>Date:</strong> {{ new Date(incident.date).toLocaleDateString() }}</div>
                </div>
            </div>

            <!-- Incident Details -->
            <div>
                <h3 class="font-semibold text-gray-700 mb-4">Incident Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Incident</label>
                        <div class="p-3 bg-gray-50 rounded border">{{ new Date(incident.date).toLocaleDateString() }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Complaint</label>
                        <div class="p-3 bg-gray-50 rounded border">{{ incident.complaint }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Actions Taken</label>
                        <div class="p-3 bg-gray-50 rounded border">{{ incident.actions_taken }}</div>
                    </div>
                </div>
            </div>


            <!-- Record Information -->
            <div class="pt-4 border-t">
                <h3 class="font-semibold text-gray-700 mb-2">Record Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                    <div><strong>Created:</strong> {{ new Date(incident.created_at).toLocaleString() }}</div>
                    <div><strong>Last Updated:</strong> {{ new Date(incident.updated_at).toLocaleString() }}</div>
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
import { integrateIncidentNotifications } from '@/Utils/notificationIntegration.js';

const props = defineProps({
    visible: Boolean,
    incident: Object,
    student: Object,
    timerStatus: Object,
    remainingMinutes: Number,
    userRole: String
});

const emit = defineEmits(['close', 'edit']);

const getAlertClass = () => {
    if (props.timerStatus?.status === 'expired') return 'bg-red-100 text-red-800';
    if (props.timerStatus?.status === 'active') return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
};

const editIncident = () => {
    emit('edit', props.incident);
};

// Initialize timer notifications with error handling
let startTimerMonitoring, stopTimerMonitoring;

try {
    const timerNotifications = useTimerNotifications('incident');
    startTimerMonitoring = timerNotifications.startTimerMonitoring;
    stopTimerMonitoring = timerNotifications.stopTimerMonitoring;
} catch (error) {
    console.error('❌ Failed to import incident timer notifications:', error);
}

// Watch for visibility changes to start/stop timer monitoring
watch(() => props.visible, (newVisible) => {
    if (newVisible && props.timerStatus?.status === 'active' && props.remainingMinutes > 0 && startTimerMonitoring) {
        console.log('✅ Starting timer monitoring for incident modal:', props.incident?.complaint);
        try {
            startTimerMonitoring(props.student, props.incident, props.remainingMinutes);
        } catch (error) {
            console.error('❌ Failed to start timer monitoring:', error);
        }
    } else if (!newVisible && stopTimerMonitoring) {
        console.log('🛑 Stopping timer monitoring for incident modal');
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
