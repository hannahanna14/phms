import { ref } from 'vue'

// Global toast state
const toasts = ref([])

// Toast configuration
const defaultDuration = 5000 // 5 seconds
const maxToasts = 5

// Actions
const addToast = (toast) => {
    const newToast = {
        id: Date.now() + Math.random(),
        type: 'info',
        duration: defaultDuration,
        timestamp: new Date(),
        ...toast,
        remainingTime: toast.duration || defaultDuration
    }

    // Add to beginning of array
    toasts.value.unshift(newToast)

    // Remove excess toasts
    if (toasts.value.length > maxToasts) {
        toasts.value = toasts.value.slice(0, maxToasts)
    }

    // Auto remove after duration (if duration > 0)
    if (newToast.duration > 0) {
        setTimeout(() => {
            removeToast(newToast.id)
        }, newToast.duration)
    }

    return newToast.id
}

const removeToast = (toastId) => {
    const index = toasts.value.findIndex(t => t.id === toastId)
    if (index > -1) {
        toasts.value.splice(index, 1)
    }
}

const clearAllToasts = () => {
    toasts.value = []
}

// Convenience methods for different toast types
const showSuccess = (title, message, options = {}) => {
    return addToast({
        type: 'success',
        title,
        message,
        ...options
    })
}

const showError = (title, message, options = {}) => {
    return addToast({
        type: 'error',
        title,
        message,
        duration: 0, // Don't auto-dismiss errors
        ...options
    })
}

const showWarning = (title, message, options = {}) => {
    return addToast({
        type: 'warning',
        title,
        message,
        ...options
    })
}

const showInfo = (title, message, options = {}) => {
    return addToast({
        type: 'info',
        title,
        message,
        ...options
    })
}

// Health-specific toast helpers
const showHealthExamComplete = (studentName, grade) => {
    return showSuccess(
        'Health Examination Complete',
        `Health examination completed for ${studentName} (${grade})`,
        { duration: 4000 }
    )
}

const showTreatmentScheduled = (studentName, treatmentType) => {
    return showInfo(
        'Treatment Scheduled',
        `${treatmentType} scheduled for ${studentName}`,
        { duration: 4000 }
    )
}

const showReportGenerated = (reportType, details) => {
    return showSuccess(
        'Report Generated',
        `${reportType} report ${details}`,
        { duration: 4000 }
    )
}

const showSystemAlert = (title, message, type = 'warning') => {
    return addToast({
        type,
        title,
        message,
        duration: type === 'error' ? 0 : 6000
    })
}

// Main toast helpers for PHMS - focused on priority scenarios

// 1. Timer expiry toast
const showTimerExpiry = (studentName, treatmentType) => {
    return showSuccess(
        'Timer Complete',
        `${treatmentType} timer completed for ${studentName}`,
        { duration: 6000 }
    )
}

// 2. Unrecorded students toast
const showUnrecordedStudentAlert = (studentName, missingType) => {
    return showWarning(
        'Student Not Recorded',
        `${studentName} has no ${missingType} record`,
        { duration: 8000 }
    )
}

// Batch unrecorded students alert
const showBatchUnrecordedAlert = (count, recordType) => {
    return showWarning(
        'Multiple Students Not Recorded',
        `${count} students are missing ${recordType} records`,
        { duration: 10000 }
    )
}

// Export the store
export const useToastStore = () => {
    return {
        // State
        toasts,
        
        // Actions
        addToast,
        removeToast,
        clearAllToasts,
        
        // Convenience methods
        showSuccess,
        showError,
        showWarning,
        showInfo,
        
        // Health-specific helpers (keeping for compatibility)
        showHealthExamComplete,
        showTreatmentScheduled,
        showReportGenerated,
        showSystemAlert,
        
        // Main PHMS helpers (focused on priority scenarios)
        showTimerExpiry,
        showUnrecordedStudentAlert,
        showBatchUnrecordedAlert
    }
}
