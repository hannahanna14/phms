<template>
    <div class="relative">
        <!-- Notification Bell Icon -->
        <button 
            @click="toggleDropdown"
            class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors duration-200"
            :class="{ 'bg-gray-100': isOpen }"
        >
            <!-- Bell Icon -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            
            <!-- Notification Badge -->
            <span 
                v-if="unreadCount > 0" 
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown Menu -->
        <div 
            v-if="isOpen"
            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
            @click.stop
        >
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <button 
                    v-if="unreadCount > 0"
                    @click="markAllAsRead"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                >
                    Mark all as read
                </button>
            </div>

            <!-- Notifications List -->
            <div class="max-h-96 overflow-y-auto">
                <div v-if="notifications.length === 0" class="px-4 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p>No notifications yet</p>
                </div>

                <div 
                    v-for="notification in notifications" 
                    :key="notification.id"
                    class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                    :class="{ 'bg-blue-50': !notification.read }"
                    @click="markAsRead(notification.id)"
                >
                    <div class="flex items-start space-x-3">
                        <!-- Icon based on type -->
                        <div class="flex-shrink-0 mt-1">
                            <div 
                                class="w-8 h-8 rounded-full flex items-center justify-center"
                                :class="getNotificationIconClass(notification.type)"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="notification.type === 'health_exam'" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    <path v-else-if="notification.type === 'treatment'" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    <path v-else-if="notification.type === 'report'" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/>
                                    <path v-else d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ notification.title }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ notification.message }}
                            </p>
                            <p class="text-xs text-gray-400 mt-2">
                                {{ formatTime(notification.created_at) }}
                            </p>
                        </div>

                        <!-- Unread indicator -->
                        <div v-if="!notification.read" class="flex-shrink-0">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-4 py-3 border-t border-gray-200 text-center">
                <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    View all notifications
                </button>
            </div>
        </div>
    </div>

    <!-- Backdrop -->
    <div 
        v-if="isOpen" 
        class="fixed inset-0 z-40" 
        @click="closeDropdown"
    ></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

// Props
const props = defineProps({
    notifications: {
        type: Array,
        default: () => []
    }
})

// Emits
const emit = defineEmits(['mark-as-read', 'mark-all-as-read'])

// State
const isOpen = ref(false)

// Computed
const unreadCount = computed(() => {
    return props.notifications.filter(n => !n.read).length
})

// Methods
const toggleDropdown = () => {
    isOpen.value = !isOpen.value
}

const closeDropdown = () => {
    isOpen.value = false
}

const markAsRead = (notificationId) => {
    emit('mark-as-read', notificationId)
}

const markAllAsRead = () => {
    emit('mark-all-as-read')
}

const getNotificationIconClass = (type) => {
    const classes = {
        'health_exam': 'bg-green-100 text-green-600',
        'treatment': 'bg-blue-100 text-blue-600',
        'report': 'bg-purple-100 text-purple-600',
        'system': 'bg-gray-100 text-gray-600',
        'default': 'bg-gray-100 text-gray-600'
    }
    return classes[type] || classes.default
}

const formatTime = (timestamp) => {
    const now = new Date()
    const time = new Date(timestamp)
    const diffInSeconds = Math.floor((now - time) / 1000)
    
    if (diffInSeconds < 60) {
        return 'Just now'
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60)
        return `${minutes}m ago`
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600)
        return `${hours}h ago`
    } else if (diffInSeconds < 604800) {
        const days = Math.floor(diffInSeconds / 86400)
        return `${days}d ago`
    } else {
        return time.toLocaleDateString()
    }
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        closeDropdown()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>
