<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">View Oral Health Treatment</h1>
                <div class="flex gap-3">
                    <Button 
                        v-if="treatment.can_edit" 
                        label="Edit" 
                        icon="pi pi-pencil" 
                        @click="editTreatment"
                    />
                    <Button label="Back" icon="pi pi-arrow-left" outlined @click="goBack" />
                </div>
            </div>


            <!-- Treatment Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Info -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2">Student Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><strong>Name:</strong> {{ student.full_name }}</div>
                            <div><strong>LRN:</strong> {{ student.lrn }}</div>
                            <div><strong>Grade:</strong> {{ treatment.grade_level }}</div>
                            <div><strong>Date:</strong> {{ new Date(treatment.date).toLocaleDateString() }}</div>
                        </div>
                    </div>

                    <!-- Treatment Details -->
                    <div class="md:col-span-2">
                        <h3 class="font-semibold text-gray-700 mb-4">Oral Health Treatment Details</h3>
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
                    <div class="md:col-span-2 mt-6">
                        <h3 class="font-semibold text-gray-700 mb-4">Record Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><strong>Created:</strong> {{ formatDateTime(treatment.created_at) }}</div>
                            <div><strong>Last Updated:</strong> {{ formatDateTime(treatment.updated_at) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue';
import Button from 'primevue/button';
import { useTimerNotifications } from '@/Utils/timerMixin.js';
import { integrateOralHealthTreatmentNotifications } from '@/Utils/notificationIntegration.js';

const props = defineProps({
    treatment: Object,
    student: Object,
    timer_status: Object,
    remaining_minutes: Number
});

// Initialize timer notifications for oral health treatments
const { startTimerMonitoring, stopTimerMonitoring } = useTimerNotifications('oral_health');

const getAlertClass = () => {
    if (props.timer_status?.status === 'expired') return 'bg-red-100 text-red-800';
    if (props.timer_status?.status === 'active') return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
};

const editTreatment = () => {
    window.location.href = `/oral-health-treatment/${props.treatment.id}/edit`;
};

const goBack = () => {
    window.history.back();
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

// Timer control methods
const startTimer = async () => {
    try {
        const response = await fetch(`/api/oral-health-treatment/${props.treatment.id}/start-timer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Error starting timer:', error);
    }
};

const pauseTimer = async () => {
    try {
        const response = await fetch(`/api/oral-health-treatment/${props.treatment.id}/pause-timer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            stopTimerMonitoring();
            window.location.reload();
        }
    } catch (error) {
        console.error('Error pausing timer:', error);
    }
};

const resumeTimer = async () => {
    try {
        const response = await fetch(`/api/oral-health-treatment/${props.treatment.id}/resume-timer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Error resuming timer:', error);
    }
};

const completeTimer = async () => {
    try {
        const response = await fetch(`/api/oral-health-treatment/${props.treatment.id}/complete-timer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            // Trigger completion notification
            const integration = integrateOralHealthTreatmentNotifications();
            integration.handleStatusChange('completed', props.student, props.treatment);
            
            stopTimerMonitoring();
            window.location.reload();
        }
    } catch (error) {
        console.error('Error completing timer:', error);
    }
};

// Start monitoring timer when component mounts
onMounted(() => {
    if (props.timer_status?.status === 'active' && props.remaining_minutes > 0) {
        console.log('Starting timer monitoring for oral health treatment:', props.treatment.title);
        startTimerMonitoring(props.student, props.treatment, props.remaining_minutes);
    }
});

// Stop monitoring when component unmounts
onUnmounted(() => {
    stopTimerMonitoring();
});
</script>
