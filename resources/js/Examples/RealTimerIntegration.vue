<!-- Example: Real Timer Integration in Health Treatment Component -->
<template>
    <div class="treatment-timer">
        <h3>{{ treatment.title || treatment.chief_complaint }}</h3>
        <div class="timer-display">
            {{ formatTime(remainingTime) }}
        </div>
        <div class="timer-controls">
            <button @click="startTimer">Start</button>
            <button @click="pauseTimer">Pause</button>
            <button @click="completeTimer">Complete</button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { integrateHealthTreatmentNotifications } from '@/Utils/notificationIntegration.js'

const props = defineProps({
    student: Object,
    treatment: Object,  // This comes from your database
    initialTime: Number // Timer duration in milliseconds
})

// Get notification integration
const { handleTimerCheck, handleStatusChange } = integrateHealthTreatmentNotifications()

const remainingTime = ref(props.initialTime)
const timerInterval = ref(null)
const timerStatus = ref('not_started')

// Timer countdown logic
const startTimer = () => {
    if (timerStatus.value === 'not_started' || timerStatus.value === 'paused') {
        timerStatus.value = 'active'
        
        timerInterval.value = setInterval(() => {
            remainingTime.value -= 1000 // Decrease by 1 second
            
            const remainingMinutes = Math.floor(remainingTime.value / 60000)
            
            // Check for notification triggers
            if (remainingMinutes === 30) {
                // Use REAL treatment data from database
                handleTimerCheck(30, props.student, {
                    title: props.treatment.title || props.treatment.chief_complaint,
                    // You can also use other fields:
                    // treatment_done: props.treatment.treatment_done,
                    // description: props.treatment.description
                })
            } else if (remainingMinutes === 15) {
                handleTimerCheck(15, props.student, {
                    title: props.treatment.title || props.treatment.chief_complaint
                })
            }
            
            // Timer expired
            if (remainingTime.value <= 0) {
                clearInterval(timerInterval.value)
                timerStatus.value = 'expired'
                
                // Trigger expiry notification with REAL data
                handleStatusChange('expired', props.student, {
                    title: props.treatment.title || props.treatment.chief_complaint
                })
            }
        }, 1000)
    }
}

const completeTimer = () => {
    clearInterval(timerInterval.value)
    timerStatus.value = 'completed'
    
    // Trigger completion notification with REAL data
    handleStatusChange('completed', props.student, {
        title: props.treatment.title || props.treatment.chief_complaint
    })
}

const pauseTimer = () => {
    clearInterval(timerInterval.value)
    timerStatus.value = 'paused'
}

const formatTime = (ms) => {
    const minutes = Math.floor(ms / 60000)
    const seconds = Math.floor((ms % 60000) / 1000)
    return `${minutes}:${seconds.toString().padStart(2, '0')}`
}

onUnmounted(() => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value)
    }
})
</script>

<!-- 
REAL DATA EXAMPLES:

If your treatment record looks like:
{
    id: 123,
    student_id: 456,
    title: "Physical Therapy Session",
    chief_complaint: "Back pain treatment",
    treatment_done: "Stretching exercises and heat therapy",
    timer_duration: 1800000, // 30 minutes in milliseconds
    timer_status: "active"
}

The notifications would show:
- "Physical Therapy Session timer expires in 30 minutes"
- "Physical Therapy Session timer expires in 15 minutes"  
- "Physical Therapy Session timer completed"
- "Physical Therapy Session timer expired"

OR if you prefer chief_complaint:
- "Back pain treatment timer expires in 30 minutes"
- etc.
-->
