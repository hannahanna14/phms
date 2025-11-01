<template>
    <Dialog 
        :visible="visible" 
        modal 
        :header="'View Incident Report'"
        :style="{ width: '50rem' }" 
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        @update:visible="$emit('close')"
    >
        <div v-if="incident" class="modal-content">
            <!-- Student Info -->
            <div class="student-info-section">
                <h3 class="section-title">Student Information</h3>
                <div class="info-grid">
                    <div><strong>Name:</strong> {{ student?.full_name }}</div>
                    <div><strong>LRN:</strong> {{ student?.lrn }}</div>
                    <div><strong>Grade:</strong> {{ incident.grade_level }}</div>
                    <div><strong>Date:</strong> {{ new Date(incident.date).toLocaleDateString() }}</div>
                </div>
            </div>

            <!-- Incident Details -->
            <div>
                <h3 class="section-title-large">Incident Details</h3>
                <div class="details-section">
                    <div class="field-container">
                        <label class="field-label">Date of Incident</label>
                        <div class="field-value">{{ new Date(incident.date).toLocaleDateString() }}</div>
                    </div>
                    
                    <div class="field-container">
                        <label class="field-label">Complaint</label>
                        <div class="field-value">{{ incident.complaint }}</div>
                    </div>
                    
                    <div class="field-container">
                        <label class="field-label">Actions Taken</label>
                        <div class="field-value">{{ incident.actions_taken }}</div>
                    </div>
                </div>
            </div>


            <!-- Record Information -->
            <div class="record-info-section">
                <h3 class="section-title">Record Information</h3>
                <div class="record-info-grid">
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
// Import component styles
import '../../../css/components/modals/IncidentViewModal.css';

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
    if (props.timerStatus?.status === 'expired') return 'alert-expired';
    if (props.timerStatus?.status === 'active') return 'alert-active';
    return 'alert-default';
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
