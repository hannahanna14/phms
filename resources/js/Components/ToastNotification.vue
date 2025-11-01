<template>
    <Teleport to="body">
        <div class="toast-container">
            <TransitionGroup
                name="toast"
                tag="div"
                class="toast-list"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="[
                        'toast-item',
                        getToastClass(toast.type)
                    ]"
                >
                    <div class="toast-content-wrapper">
                        <!-- Icon -->
                        <div class="toast-icon-wrapper">
                            <div 
                                :class="[
                                    'toast-icon-container',
                                    getIconClass(toast.type)
                                ]"
                            >
                                <svg class="toast-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="toast.type === 'success'" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                    <path v-else-if="toast.type === 'error'" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                    <path v-else-if="toast.type === 'warning'" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                                    <path v-else d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="toast-content">
                            <p class="toast-title">
                                {{ toast.title }}
                            </p>
                            <p class="toast-message">
                                {{ toast.message }}
                            </p>
                            <p class="toast-time">
                                {{ formatTime(toast.timestamp) }}
                            </p>
                        </div>

                        <!-- Close Button -->
                        <button
                            @click="removeToast(toast.id)"
                            class="toast-close-btn"
                        >
                            <svg class="toast-close-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Progress Bar -->
                    <div 
                        v-if="toast.duration > 0"
                        class="toast-progress-container"
                    >
                        <div 
                            :class="[
                                'toast-progress-bar',
                                getProgressClass(toast.type)
                            ]"
                            :style="{ 
                                width: `${(toast.remainingTime / toast.duration) * 100}%`,
                                transitionDuration: '100ms'
                            }"
                        ></div>
                    </div>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
// Import component styles
import '../../css/components/ToastNotification.css'

// Props
const props = defineProps({
    toasts: {
        type: Array,
        default: () => []
    }
})

// Emits
const emit = defineEmits(['remove-toast'])

// Methods
const removeToast = (toastId) => {
    emit('remove-toast', toastId)
}

const getToastClass = (type) => {
    const classes = {
        'success': 'toast-success',
        'error': 'toast-error',
        'warning': 'toast-warning',
        'info': 'toast-info'
    }
    return classes[type] || classes.info
}

const getIconClass = (type) => {
    const classes = {
        'success': 'toast-icon-success',
        'error': 'toast-icon-error',
        'warning': 'toast-icon-warning',
        'info': 'toast-icon-info'
    }
    return classes[type] || classes.info
}

const getProgressClass = (type) => {
    const classes = {
        'success': 'toast-progress-success',
        'error': 'toast-progress-error',
        'warning': 'toast-progress-warning',
        'info': 'toast-progress-info'
    }
    return classes[type] || classes.info
}

const formatTime = (timestamp) => {
    return new Date(timestamp).toLocaleTimeString([], { 
        hour: '2-digit', 
        minute: '2-digit' 
    })
}

// Timer for progress bars
let progressInterval = null

onMounted(() => {
    // Update progress bars every 100ms
    progressInterval = setInterval(() => {
        props.toasts.forEach(toast => {
            if (toast.duration > 0 && toast.remainingTime > 0) {
                toast.remainingTime -= 100
                if (toast.remainingTime <= 0) {
                    removeToast(toast.id)
                }
            }
        })
    }, 100)
})

onUnmounted(() => {
    if (progressInterval) {
        clearInterval(progressInterval)
    }
})
</script>
