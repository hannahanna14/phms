// Timer Mixin for Health Treatment Components
// Add this to your existing Health Treatment and Oral Health Treatment components

import { ref, onMounted, onUnmounted } from 'vue'
import { integrateHealthTreatmentNotifications, integrateOralHealthTreatmentNotifications, integrateIncidentNotifications } from '@/Utils/notificationIntegration.js'

export const useTimerNotifications = (treatmentType = 'health') => {
    const timerInterval = ref(null)
    const notificationsTriggered = ref({
        thirtyMin: false,
        fifteenMin: false
    })

    // Get appropriate integration functions
    const getIntegrationFunctions = () => {
        if (treatmentType === 'health') {
            return integrateHealthTreatmentNotifications()
        } else if (treatmentType === 'oral_health') {
            return integrateOralHealthTreatmentNotifications()
        } else if (treatmentType === 'incident') {
            return integrateIncidentNotifications()
        }
        return null
    }

    // Start monitoring timer for notifications
    const startTimerMonitoring = (student, treatment, remainingMinutes) => {
        const integration = getIntegrationFunctions()
        if (!integration) return

        // Clear any existing interval
        if (timerInterval.value) {
            clearInterval(timerInterval.value)
        }

        // Reset notification flags
        notificationsTriggered.value = {
            thirtyMin: false,
            fifteenMin: false
        }

        console.log(`Starting timer monitoring for ${treatmentType} treatment:`, treatment.title || treatment.complaint)

        // Check every minute for notification triggers
        timerInterval.value = setInterval(async () => {
            try {
                // Build correct API endpoint
                let apiEndpoint
                if (treatmentType === 'health') {
                    apiEndpoint = `/api/health-treatment/timer-status/${treatment.id}`
                } else if (treatmentType === 'oral_health') {
                    apiEndpoint = `/api/oral-health-treatment/timer-status/${treatment.id}`
                } else if (treatmentType === 'incident') {
                    // For incidents, we need to check via the incidents API
                    apiEndpoint = `/api/incidents/timer-status/${treatment.id}`
                }

                console.log(`Checking timer status at: ${apiEndpoint}`)
                
                // Fetch current timer status from backend
                const response = await fetch(apiEndpoint)
                const data = await response.json()
                
                const currentRemainingMinutes = data.remaining_minutes
                console.log(`Current remaining minutes: ${currentRemainingMinutes}`)

                // Trigger 30-minute warning (when between 29 and 31 minutes)
                if (currentRemainingMinutes <= 30 && currentRemainingMinutes > 29 && !notificationsTriggered.value.thirtyMin) {
                    integration.handleTimerCheck(30, student, treatment)
                    notificationsTriggered.value.thirtyMin = true
                }

                // Trigger 15-minute warning (when between 14 and 16 minutes)
                if (currentRemainingMinutes <= 15 && currentRemainingMinutes > 14 && !notificationsTriggered.value.fifteenMin) {
                    integration.handleTimerCheck(15, student, treatment)
                    notificationsTriggered.value.fifteenMin = true
                }

                // Timer expired
                if (currentRemainingMinutes <= 0 || data.timer_status === 'expired') {
                    integration.handleStatusChange('expired', student, treatment)
                    clearInterval(timerInterval.value)
                }

                // Timer completed
                if (data.timer_status === 'completed') {
                    integration.handleStatusChange('completed', student, treatment)
                    clearInterval(timerInterval.value)
                }

            } catch (error) {
                console.error('Error checking timer status:', error)
            }
        }, 60000) // Check every minute
    }

    // Stop timer monitoring
    const stopTimerMonitoring = () => {
        if (timerInterval.value) {
            clearInterval(timerInterval.value)
            timerInterval.value = null
        }
    }

    // Cleanup on unmount
    onUnmounted(() => {
        stopTimerMonitoring()
    })

    return {
        startTimerMonitoring,
        stopTimerMonitoring
    }
}

// Example usage in your Health Treatment Show.vue:
/*
<script setup>
import { useTimerNotifications } from '@/Utils/timerMixin.js'

const props = defineProps({
    treatment: Object,
    student: Object,
    timer_status: Object,
    remaining_minutes: Number
})

// Initialize timer notifications
const { startTimerMonitoring, stopTimerMonitoring } = useTimerNotifications('health')

// Start monitoring when component mounts and timer is active
onMounted(() => {
    if (props.timer_status?.status === 'active' && props.remaining_minutes > 0) {
        startTimerMonitoring(props.student, props.treatment, props.remaining_minutes)
    }
})
</script>
*/
