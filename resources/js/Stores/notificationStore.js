import { ref, computed } from 'vue'

// Global notification state
const notifications = ref([])
const isLoading = ref(false)

// LocalStorage key for persistence
const STORAGE_KEY = 'phms_notifications'

// Load notifications from localStorage
const loadNotificationsFromStorage = () => {
    try {
        const stored = localStorage.getItem(STORAGE_KEY)
        if (stored) {
            const parsedNotifications = JSON.parse(stored)
            // Convert date strings back to Date objects
            return parsedNotifications.map(notification => ({
                ...notification,
                created_at: new Date(notification.created_at),
                timestamp: notification.timestamp ? new Date(notification.timestamp) : new Date(notification.created_at)
            }))
        }
    } catch (error) {
        console.error('Error loading notifications from storage:', error)
    }
    return []
}

// Save notifications to localStorage
const saveNotificationsToStorage = () => {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(notifications.value))
    } catch (error) {
        console.error('Error saving notifications to storage:', error)
    }
}

// Role-based notification filtering
const getFilteredNotifications = (userRole) => {
    return notifications.value.filter(notification => {
        // Define which notification types each role should see
        const rolePermissions = {
            'admin': ['all'], // Admins see everything
            'nurse': ['health_exam', 'treatment', 'oral_health_treatment', 'incident', 'system', 'timer_expired', 'timer_warning'],
            'teacher': ['health_exam', 'report', 'system'] // Teachers only see basic health info and reports
        }
        
        const allowedTypes = rolePermissions[userRole] || []
        
        // If admin, show all notifications
        if (allowedTypes.includes('all')) {
            return true
        }
        
        // Filter based on notification type or source
        const notificationType = notification.type || notification.source || 'system'
        
        // Block timer-related notifications for teachers
        if (userRole === 'teacher') {
            if (notification.title && (
                notification.title.includes('Timer Expired') ||
                notification.title.includes('Timer Warning') ||
                notification.title.includes('Treatment') ||
                notification.source === 'health' ||
                notification.source === 'oral_health'
            )) {
                return false
            }
        }
        
        return allowedTypes.includes(notificationType)
    })
}

// Computed
const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.read).length
})

// Actions
const initializeNotifications = () => {
    // Load existing notifications from localStorage
    notifications.value = loadNotificationsFromStorage()
    console.log(`📱 Loaded ${notifications.value.length} notifications from storage`)
}

// Check for students without health records (to be called periodically)
const checkUnrecordedStudents = async () => {
    // TODO: API call to get students without health examinations
    // This should be called on dashboard load or periodically
}

// Check for expired timers (to be called when timer completes)
const checkExpiredTimers = async () => {
    // TODO: API call to get completed/expired treatment timers
    // This should be called when treatment timers complete
}

const addNotification = (notification) => {
    // Check for duplicate notifications (same type, student, and treatment)
    const isDuplicate = notifications.value.some(existing => 
        existing.type === notification.type &&
        existing.student_id === notification.student_id &&
        existing.treatment_id === notification.treatment_id &&
        existing.message === notification.message &&
        !existing.read // Only check unread notifications
    )
    
    if (isDuplicate) {
        console.log('🔄 Duplicate notification prevented:', notification.title)
        return
    }
    
    const newNotification = {
        id: Date.now(),
        read: false,
        created_at: new Date(),
        ...notification
    }
    notifications.value.unshift(newNotification)
    
    // Save to localStorage for persistence
    saveNotificationsToStorage()
    console.log('💾 Notification saved to storage:', newNotification.title)
}

const markAsRead = (notificationId) => {
    const notification = notifications.value.find(n => n.id === notificationId)
    if (notification) {
        notification.read = true
        saveNotificationsToStorage()
    }
}

const markAllAsRead = () => {
    notifications.value.forEach(n => n.read = true)
    saveNotificationsToStorage()
    console.log('📖 All notifications marked as read and saved')
}

const removeNotification = (notificationId) => {
    const index = notifications.value.findIndex(n => n.id === notificationId)
    if (index > -1) {
        notifications.value.splice(index, 1)
        saveNotificationsToStorage()
        console.log('🗑️ Notification removed and saved')
    }
}

const clearAllNotifications = () => {
    notifications.value = []
    saveNotificationsToStorage()
    console.log('🧹 All notifications cleared and saved')
}

// Notification types and their configurations
const notificationTypes = {
    health_exam: {
        icon: 'check-circle',
        color: 'green',
        title: 'Health Examination'
    },
    treatment: {
        icon: 'user',
        color: 'blue',
        title: 'Treatment'
    },
    report: {
        icon: 'document',
        color: 'purple',
        title: 'Report'
    },
    system: {
        icon: 'information-circle',
        color: 'gray',
        title: 'System'
    }
}

// Main notification helpers for PHMS - focused on the two priority scenarios

// 1. Timer expiry notifications
const createTimerExpiryNotification = (studentName, treatmentType) => {
    return {
        type: 'treatment',
        title: 'Treatment Timer Complete',
        message: `${treatmentType} timer completed for ${studentName}`,
        priority: 'high'
    }
}

// 2. Students without health records notifications
const createUnrecordedStudentNotification = (studentName, missingType) => {
    return {
        type: 'health_exam',
        title: 'Student Not Recorded',
        message: `${studentName} has no ${missingType} record`,
        priority: 'medium'
    }
}

// Batch notification for multiple unrecorded students
const createBatchUnrecordedNotification = (count, recordType) => {
    return {
        type: 'system',
        title: `${count} Students Not Recorded`,
        message: `${count} students are missing ${recordType} records`,
        priority: 'medium'
    }
}

// Export the store
export const useNotificationStore = () => {
    return {
        // State
        notifications: notifications,
        isLoading,
        
        // Computed
        unreadCount,
        
        // Actions
        initializeNotifications,
        addNotification,
        markAsRead,
        markAllAsRead,
        removeNotification,
        clearAllNotifications,
        getFilteredNotifications,
        
        // Note: Notification creator functions can be added here when needed
        
        // Config
        notificationTypes
    }
}
