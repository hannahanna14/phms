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
                <div class="flex items-center space-x-2">
                    <button 
                        v-if="unreadCount > 0"
                        @click="markAllAsRead"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Mark all as read
                    </button>
                    <button 
                        v-if="notifications.length > 0"
                        @click="clearAllNotifications"
                        class="text-sm text-red-600 hover:text-red-800 font-medium"
                        title="Clear all notifications"
                    >
                        Clear all
                    </button>
                </div>
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
                                <!-- Health Exam Icon -->
                                <svg v-if="notification.type === 'health_exam'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                
                                <!-- Treatment/Timer Icon -->
                                <svg v-else-if="notification.type === 'treatment' || notification.type === 'timer_expired' || notification.type === 'timer_warning'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                
                                <!-- Schedule Icon -->
                                <svg v-else-if="notification.type === 'schedule' || notification.type === 'schedule_reminder' || notification.type === 'schedule_today'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                
                                <!-- Report Icon -->
                                <svg v-else-if="notification.type === 'report'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                                
                                <!-- Incident Icon -->
                                <svg v-else-if="notification.type === 'incident'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                
                                <!-- Missing Records Icon -->
                                <svg v-else-if="notification.type === 'unrecorded_student' || notification.type === 'batch_unrecorded'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                
                                <!-- Default System Icon -->
                                <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
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

                        <!-- Actions -->
                        <div class="flex-shrink-0 flex items-center space-x-2">
                            <!-- Delete button -->
                            <button
                                @click.stop="deleteNotification(notification.id)"
                                class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200"
                                title="Delete notification"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            
                            <!-- Unread indicator -->
                            <div v-if="!notification.read">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-4 py-3 border-t border-gray-200 text-center">
                <button 
                    @click="viewAllNotifications"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                >
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
const emit = defineEmits(['mark-as-read', 'mark-all-as-read', 'delete-notification', 'clear-all-notifications', 'view-all-notifications'])

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

const deleteNotification = (notificationId) => {
    emit('delete-notification', notificationId)
}

const clearAllNotifications = () => {
    emit('clear-all-notifications')
}

const viewAllNotifications = () => {
    emit('view-all-notifications')
    closeDropdown()
}

const getNotificationIconClass = (type) => {
    const classes = {
        // Health-related notifications
        'health_exam': 'bg-green-100 text-green-600',
        'treatment': 'bg-blue-100 text-blue-600',
        'timer_expired': 'bg-red-100 text-red-600',
        'timer_warning': 'bg-orange-100 text-orange-600',
        
        // Schedule notifications
        'schedule': 'bg-indigo-100 text-indigo-600',
        'schedule_reminder': 'bg-indigo-100 text-indigo-600',
        'schedule_today': 'bg-indigo-100 text-indigo-600',
        
        // Report notifications
        'report': 'bg-purple-100 text-purple-600',
        
        // Incident notifications
        'incident': 'bg-yellow-100 text-yellow-600',
        
        // Missing records notifications
        'unrecorded_student': 'bg-red-100 text-red-600',
        'batch_unrecorded': 'bg-red-100 text-red-600',
        
        // System notifications
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
