<template>
    <Head title="All Notifications" />
    
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">All Notifications</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ notifications.length }} total notifications
                            <span v-if="unreadCount > 0" class="text-blue-600">
                                ({{ unreadCount }} unread)
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button 
                            v-if="unreadCount > 0"
                            @click="markAllAsRead"
                            class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors"
                        >
                            Mark all as read
                        </button>
                        <button 
                            v-if="notifications.length > 0"
                            @click="clearAllNotifications"
                            class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors"
                        >
                            Clear all
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center space-x-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mr-2">Filter by type:</label>
                        <select 
                            v-model="selectedFilter" 
                            class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="all">All Types</option>
                            <option value="schedule">Schedule</option>
                            <option value="timer">Timer Alerts</option>
                            <option value="health">Health Records</option>
                            <option value="system">System</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 mr-2">Status:</label>
                        <select 
                            v-model="selectedStatus" 
                            class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="all">All</option>
                            <option value="unread">Unread</option>
                            <option value="read">Read</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="divide-y divide-gray-200">
                <div v-if="filteredNotifications.length === 0" class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications found</h3>
                    <p class="text-gray-600">
                        {{ selectedFilter !== 'all' || selectedStatus !== 'all' ? 'Try adjusting your filters.' : 'You\'re all caught up!' }}
                    </p>
                </div>

                <div 
                    v-for="notification in filteredNotifications" 
                    :key="notification.id"
                    class="px-6 py-4 hover:bg-gray-50 transition-colors"
                    :class="{ 'bg-blue-50': !notification.read }"
                >
                    <div class="flex items-start space-x-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-1">
                            <div 
                                class="w-10 h-10 rounded-full flex items-center justify-center"
                                :class="getNotificationIconClass(notification.type)"
                            >
                                <!-- Schedule Icon -->
                                <svg v-if="notification.type === 'schedule_reminder' || notification.type === 'schedule_today'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                
                                <!-- Timer Icon -->
                                <svg v-else-if="notification.type === 'timer_expired' || notification.type === 'timer_warning'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                
                                <!-- Health Icon -->
                                <svg v-else-if="notification.type === 'unrecorded_student' || notification.type === 'batch_unrecorded'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                
                                <!-- Default Icon -->
                                <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-base font-medium text-gray-900">
                                        {{ notification.title }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ notification.message }}
                                    </p>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-xs text-gray-400">
                                            {{ formatTime(notification.created_at) }}
                                        </span>
                                        <span 
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                            :class="getTypeClass(notification.type)"
                                        >
                                            {{ getTypeLabel(notification.type) }}
                                        </span>
                                        <span 
                                            v-if="notification.priority"
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                            :class="getPriorityClass(notification.priority)"
                                        >
                                            {{ notification.priority.charAt(0).toUpperCase() + notification.priority.slice(1) }} Priority
                                        </span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2 ml-4">
                                    <button
                                        v-if="!notification.read"
                                        @click="markAsRead(notification.id)"
                                        class="p-2 text-gray-400 hover:text-blue-500 transition-colors"
                                        title="Mark as read"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="deleteNotification(notification.id)"
                                        class="p-2 text-gray-400 hover:text-red-500 transition-colors"
                                        title="Delete notification"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Unread indicator -->
                        <div v-if="!notification.read" class="flex-shrink-0">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination (if needed in the future) -->
            <div v-if="filteredNotifications.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="text-sm text-gray-600 text-center">
                    Showing {{ filteredNotifications.length }} of {{ notifications.length }} notifications
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { useNotificationStore } from '@/Stores/notificationStore.js'

// Props
const props = defineProps({
    notifications: {
        type: Array,
        default: () => []
    }
})

// Notification store
const { 
    markNotificationAsRead, 
    markAllNotificationsAsRead, 
    removeNotification, 
    clearAllNotifications 
} = useNotificationStore()

// State
const selectedFilter = ref('all')
const selectedStatus = ref('all')

// Computed
const unreadCount = computed(() => {
    return props.notifications.filter(n => !n.read).length
})

const filteredNotifications = computed(() => {
    let filtered = [...props.notifications]
    
    // Filter by type
    if (selectedFilter.value !== 'all') {
        filtered = filtered.filter(notification => {
            switch (selectedFilter.value) {
                case 'schedule':
                    return ['schedule_reminder', 'schedule_today'].includes(notification.type)
                case 'timer':
                    return ['timer_expired', 'timer_warning'].includes(notification.type)
                case 'health':
                    return ['unrecorded_student', 'batch_unrecorded'].includes(notification.type)
                case 'system':
                    return ['system'].includes(notification.type)
                default:
                    return true
            }
        })
    }
    
    // Filter by status
    if (selectedStatus.value !== 'all') {
        filtered = filtered.filter(notification => {
            return selectedStatus.value === 'read' ? notification.read : !notification.read
        })
    }
    
    // Sort by created_at (newest first)
    return filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

// Methods
const markAsRead = (notificationId) => {
    markNotificationAsRead(notificationId)
}

const markAllAsRead = () => {
    markAllNotificationsAsRead()
}

const deleteNotification = (notificationId) => {
    removeNotification(notificationId)
}

const getNotificationIconClass = (type) => {
    const classes = {
        'schedule_reminder': 'bg-indigo-100 text-indigo-600',
        'schedule_today': 'bg-indigo-100 text-indigo-600',
        'timer_expired': 'bg-red-100 text-red-600',
        'timer_warning': 'bg-orange-100 text-orange-600',
        'unrecorded_student': 'bg-red-100 text-red-600',
        'batch_unrecorded': 'bg-red-100 text-red-600',
        'system': 'bg-gray-100 text-gray-600'
    }
    return classes[type] || 'bg-gray-100 text-gray-600'
}

const getTypeClass = (type) => {
    const classes = {
        'schedule_reminder': 'bg-indigo-100 text-indigo-800',
        'schedule_today': 'bg-indigo-100 text-indigo-800',
        'timer_expired': 'bg-red-100 text-red-800',
        'timer_warning': 'bg-orange-100 text-orange-800',
        'unrecorded_student': 'bg-red-100 text-red-800',
        'batch_unrecorded': 'bg-red-100 text-red-800',
        'system': 'bg-gray-100 text-gray-800'
    }
    return classes[type] || 'bg-gray-100 text-gray-800'
}

const getTypeLabel = (type) => {
    const labels = {
        'schedule_reminder': 'Schedule',
        'schedule_today': 'Schedule',
        'timer_expired': 'Timer Alert',
        'timer_warning': 'Timer Alert',
        'unrecorded_student': 'Health Record',
        'batch_unrecorded': 'Health Record',
        'system': 'System'
    }
    return labels[type] || 'System'
}

const getPriorityClass = (priority) => {
    const classes = {
        'high': 'bg-red-100 text-red-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'low': 'bg-green-100 text-green-800'
    }
    return classes[priority] || 'bg-gray-100 text-gray-800'
}

const formatTime = (timestamp) => {
    const now = new Date()
    const time = new Date(timestamp)
    const diffInSeconds = Math.floor((now - time) / 1000)
    
    if (diffInSeconds < 60) {
        return 'Just now'
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60)
        return `${minutes} minute${minutes === 1 ? '' : 's'} ago`
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600)
        return `${hours} hour${hours === 1 ? '' : 's'} ago`
    } else if (diffInSeconds < 604800) {
        const days = Math.floor(diffInSeconds / 86400)
        return `${days} day${days === 1 ? '' : 's'} ago`
    } else {
        return time.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })
    }
}
</script>
