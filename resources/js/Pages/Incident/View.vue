<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">View Incident Report</h1>
                <div class="flex gap-3">
                    <Button label="Back" icon="pi pi-arrow-left" outlined @click="goBack" />
                </div>
            </div>

            <!-- Incident Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Info -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2">Student Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><strong>Name:</strong> {{ student.full_name }}</div>
                            <div><strong>LRN:</strong> {{ student.lrn }}</div>
                            <div><strong>Grade:</strong> {{ incident.grade_level }}</div>
                            <div><strong>Date:</strong> {{ new Date(incident.date).toLocaleDateString() }}</div>
                        </div>
                    </div>

                    <!-- Incident Details -->
                    <div class="md:col-span-2">
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

                    <!-- Timestamps -->
                    <div class="md:col-span-2 pt-4 border-t">
                        <h3 class="font-semibold text-gray-700 mb-2">Record Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                            <div><strong>Created:</strong> {{ new Date(incident.created_at).toLocaleString() }}</div>
                            <div><strong>Last Updated:</strong> {{ new Date(incident.updated_at).toLocaleString() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3'
import Button from 'primevue/button'
import { useTimerNotifications } from '@/Utils/timerMixin.js';
import { integrateIncidentNotifications } from '@/Utils/notificationIntegration.js';

const { incident, student, timer_status, remaining_minutes } = usePage().props
const userRole = usePage().props.auth?.user?.role

const editIncident = () => {
    window.location.href = `/pupil-health/incident/${incident.id}/edit`
}

const goBack = () => {
    window.history.back()
}

// Initialize timer notifications with error handling
let startTimerMonitoring, stopTimerMonitoring;

try {
    const timerNotifications = useTimerNotifications('incident');
    startTimerMonitoring = timerNotifications.startTimerMonitoring;
    stopTimerMonitoring = timerNotifications.stopTimerMonitoring;
    console.log('✅ Incident timer notifications imported successfully');
} catch (error) {
    console.error('❌ Failed to import incident timer notifications:', error);
}

// Start monitoring timer when component mounts
onMounted(() => {
    console.log('🚀 Incident View mounted');
    console.log('Timer status:', timer_status);
    console.log('Remaining minutes:', remaining_minutes);
    console.log('Student:', student?.full_name);
    console.log('Incident:', incident?.complaint);
    
    // Test if we can trigger a manual notification
    try {
        const integration = integrateIncidentNotifications();
        console.log('✅ Incident notification integration loaded successfully');
        
        // If timer is at 30 minutes or less, trigger notification immediately for testing
        if (remaining_minutes <= 30 && remaining_minutes > 0) {
            console.log('🔔 Incident timer is at 30 minutes or less - triggering notification');
            integration.handleTimerCheck(remaining_minutes, student, incident);
        }
    } catch (error) {
        console.error('❌ Failed to load incident notification integration:', error);
    }
    
    // Start timer monitoring if timer is active and functions are available
    if (timer_status?.status === 'active' && remaining_minutes > 0 && startTimerMonitoring) {
        console.log('✅ Starting timer monitoring for incident:', incident?.complaint);
        try {
            startTimerMonitoring(student, incident, remaining_minutes);
            console.log('Incident timer monitoring started successfully');
        } catch (error) {
            console.error('❌ Failed to start incident timer monitoring:', error);
        }
    } else {
        console.log('❌ Incident timer monitoring not started. Status:', timer_status?.status, 'Minutes:', remaining_minutes, 'Function available:', !!startTimerMonitoring);
    }
});

// Stop monitoring when component unmounts
onUnmounted(() => {
    console.log('🛑 Stopping incident timer monitoring');
    if (stopTimerMonitoring) {
        try {
            stopTimerMonitoring();
        } catch (error) {
            console.error('Error stopping incident timer monitoring:', error);
        }
    }
});
</script>

<style scoped>
/* Additional styling if needed */
</style>
