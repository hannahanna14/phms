<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-[9999] space-y-2">
            <TransitionGroup
                name="toast"
                tag="div"
                class="space-y-2"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="bg-white rounded-lg shadow-lg border-l-4 p-4 max-w-sm min-w-80"
                    :class="getToastClass(toast.type)"
                >
                    <div class="flex items-start">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mr-3">
                            <div 
                                class="w-8 h-8 rounded-full flex items-center justify-center"
                                :class="getIconClass(toast.type)"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="toast.type === 'success'" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                    <path v-else-if="toast.type === 'error'" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                    <path v-else-if="toast.type === 'warning'" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                                    <path v-else d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ toast.title }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ toast.message }}
                            </p>
                            <p class="text-xs text-gray-400 mt-2">
                                {{ formatTime(toast.timestamp) }}
                            </p>
                        </div>

                        <!-- Close Button -->
                        <button
                            @click="removeToast(toast.id)"
                            class="flex-shrink-0 ml-2 text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Progress Bar -->
                    <div 
                        v-if="toast.duration > 0"
                        class="mt-3 bg-gray-200 rounded-full h-1 overflow-hidden"
                    >
                        <div 
                            class="h-full bg-current opacity-30 transition-all ease-linear"
                            :class="getProgressClass(toast.type)"
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
        'success': 'border-green-400',
        'error': 'border-red-400',
        'warning': 'border-yellow-400',
        'info': 'border-blue-400'
    }
    return classes[type] || classes.info
}

const getIconClass = (type) => {
    const classes = {
        'success': 'bg-green-100 text-green-600',
        'error': 'bg-red-100 text-red-600',
        'warning': 'bg-yellow-100 text-yellow-600',
        'info': 'bg-blue-100 text-blue-600'
    }
    return classes[type] || classes.info
}

const getProgressClass = (type) => {
    const classes = {
        'success': 'text-green-400',
        'error': 'text-red-400',
        'warning': 'text-yellow-400',
        'info': 'text-blue-400'
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

<style scoped>
/* Toast animations */
.toast-enter-active {
    transition: all 0.3s ease-out;
}

.toast-leave-active {
    transition: all 0.3s ease-in;
}

.toast-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.toast-leave-to {
    transform: translateX(100%);
    opacity: 0;
}

.toast-move {
    transition: transform 0.3s ease;
}
</style>
