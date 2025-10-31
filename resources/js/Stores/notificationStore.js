import { ref, computed } from 'vue'

// Global notification state
const notifications = ref([])
const isLoading = ref(false)

// User-specific dismissed notifications tracking
const getDismissedKey = (userId) => `phms_dismissed_notifications_${userId}`

const loadDismissedNotifications = (userId) => {
    try {
        const stored = localStorage.getItem(getDismissedKey(userId))
        return stored ? new Set(JSON.parse(stored)) : new Set()
    } catch {
        return new Set()
    }
}

const saveDismissedNotifications = (userId, dismissedSet) => {
    try {
        localStorage.setItem(getDismissedKey(userId), JSON.stringify([...dismissedSet]))
    } catch (error) {
        console.error('Error saving dismissed notifications:', error)
    }
}

// Track dismissed notifications per user
const dismissedNotifications = ref(new Set())

// Role-based notification filtering
const getFilteredNotifications = (userRole) => {
    return notifications.value.filter(notification => {
        // First filter out dismissed notifications
        if (notification.dismissed) {
            return false
        }
        // Define which notification types each role should see
        const rolePermissions = {
            'admin': ['all'], // Admins see everything
            'nurse': ['health_exam', 'treatment', 'oral_health_treatment', 'incident', 'system', 'timer_expired', 'timer_warning', 'schedule', 'unrecorded_student', 'batch_unrecorded'],
            'teacher': ['schedule_reminder', 'schedule_today'] // Teachers only see schedule notifications
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
    return notifications.value.filter(n => !n.read && !n.dismissed).length
})

// Actions
const initializeNotifications = (userId) => {
    // Load dismissed notifications for this user
    if (userId) {
        dismissedNotifications.value = loadDismissedNotifications(userId)
    }
    notifications.value = []
    console.log('📱 Notifications initialized')
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

const addNotification = (notification, userId) => {
    // Create a unique key for this notification
    const notificationKey = `${notification.type}_${notification.message}`
    
    // Skip if already dismissed by this user
    if (dismissedNotifications.value.has(notificationKey)) {
        return
    }
    
    // Check for duplicate notifications
    const isDuplicate = notifications.value.some(existing => 
        existing.type === notification.type &&
        existing.message === notification.message &&
        !existing.dismissed
    )
    
    if (isDuplicate) {
        return
    }
    
    const newNotification = {
        id: Date.now(),
        notificationKey,
        read: false,
        dismissed: false,
        created_at: new Date(),
        ...notification
    }
    notifications.value.unshift(newNotification)
}

const markAsRead = (notificationId) => {
    const notification = notifications.value.find(n => n.id === notificationId)
    if (notification) {
        notification.read = true
    }
}

const markAllAsRead = (userId) => {
    // Mark all as read AND dismiss them so they don't reappear
    notifications.value.forEach(n => {
        n.read = true
        n.dismissed = true
        
        // Add to dismissed set
        if (n.notificationKey && userId) {
            dismissedNotifications.value.add(n.notificationKey)
        }
    })
    
    // Save dismissed notifications
    if (userId) {
        saveDismissedNotifications(userId, dismissedNotifications.value)
    }
    console.log('📖 All notifications marked as read and dismissed')
}

const removeNotification = (notificationId, userId) => {
    const notification = notifications.value.find(n => n.id === notificationId)
    if (notification) {
        notification.dismissed = true
        
        // Add to dismissed set and save
        if (notification.notificationKey && userId) {
            dismissedNotifications.value.add(notification.notificationKey)
            saveDismissedNotifications(userId, dismissedNotifications.value)
        }
        console.log('🗑️ Notification dismissed')
    }
}

const clearAllNotifications = () => {
    notifications.value = []
    console.log('🧹 All notifications cleared')
}

const clearOldReadNotifications = () => {
    // Remove read or dismissed notifications
    notifications.value = notifications.value.filter(n => !n.read && !n.dismissed)
    console.log('🧹 Read/dismissed notifications cleared')
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
    schedule: {
        icon: 'calendar',
        color: 'blue',
        title: 'Schedule'
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

// Clear notifications for role change or reset
const clearNotificationsForRole = (userRole) => {
    // When switching roles, clear notifications that don't apply
    if (userRole === 'teacher') {
        // Teachers should only see schedule notifications
        notifications.value = notifications.value.filter(notification => {
            return ['schedule_reminder', 'schedule_today'].includes(notification.type)
        })
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
        clearOldReadNotifications,
        getFilteredNotifications,
        clearNotificationsForRole,
        
        // Note: Notification creator functions can be added here when needed
        
        // Config
        notificationTypes
    }
}
