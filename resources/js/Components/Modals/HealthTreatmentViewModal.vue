<template>
    <Dialog 
        :visible="visible" 
        modal 
        :header="'View Health Treatment'"
        :style="{ width: '50rem' }" 
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        @update:visible="$emit('close')"
    >
        <div v-if="treatment" class="modal-content">
            <!-- Student Info -->
            <div class="student-info-section">
                <h3 class="section-title">Student Information</h3>
                <div class="info-grid">
                    <div><strong>Name:</strong> {{ student?.full_name }}</div>
                    <div><strong>LRN:</strong> {{ student?.lrn }}</div>
                    <div><strong>Grade:</strong> {{ treatment.grade_level }}</div>
                    <div><strong>Date:</strong> {{ new Date(treatment.date).toLocaleDateString() }}</div>
                </div>
            </div>

            <!-- Treatment Details -->
            <div>
                <h3 class="section-title-large">Treatment Details</h3>
                <div class="details-section">
                    <div class="field-container">
                        <label class="field-label">Title</label>
                        <div class="field-value">{{ treatment.title }}</div>
                    </div>
                    
                    <div class="field-container">
                        <label class="field-label">Chief Complaint</label>
                        <div class="field-value">{{ treatment.chief_complaint }}</div>
                    </div>
                    
                    <div class="field-container">
                        <label class="field-label">Treatment</label>
                        <div class="field-value">{{ treatment.treatment }}</div>
                    </div>
                    
                    <div v-if="treatment.remarks" class="field-container">
                        <label class="field-label">Remarks</label>
                        <div class="field-value">{{ treatment.remarks }}</div>
                    </div>
                </div>
            </div>


            <!-- Record Information -->
            <div class="record-info-section">
                <h3 class="section-title-large">Record Information</h3>
                <div class="record-info-grid">
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
// Import component styles
import '../../../css/components/modals/HealthTreatmentViewModal.css';

const props = defineProps({
    visible: Boolean,
    treatment: Object,
    student: Object,
    timerStatus: Object,
    remainingMinutes: Number
});

const emit = defineEmits(['close', 'edit']);

const getAlertClass = () => {
    if (props.timerStatus?.status === 'expired') return 'alert-expired';
    if (props.timerStatus?.status === 'active') return 'alert-active';
    return 'alert-default';
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
        try {
            startTimerMonitoring(props.student, props.treatment, props.remainingMinutes);
        } catch (error) {
            console.error('Failed to start timer monitoring:', error);
        }
    } else if (!newVisible && stopTimerMonitoring) {
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
