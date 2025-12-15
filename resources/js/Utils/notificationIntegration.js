// Integration examples for PHMS notification system
// This file shows how to integrate notifications into existing components

import { useNotificationStore } from '@/Stores/notificationStore.js'
import { useToastStore } from '@/Stores/toastStore.js'

// Example 1: Timer completion integration
// Use this when ANY treatment timer completes (Health, Oral Health, or Incident)
export const handleTimerCompletion = (studentName, treatmentType, timerSource = 'treatment') => {
    const { addNotification, createTimerExpiryNotification } = useNotificationStore()
    const { showTimerExpiry } = useToastStore()
    
    // Add to notification dropdown with source context
    const notification = createTimerExpiryNotification(studentName, treatmentType)
    notification.source = timerSource // 'health', 'oral_health', or 'incident'
    
    addNotification(notification)
    
    // Show immediate toast
    showTimerExpiry(studentName, treatmentType)
}

// Example 2: Check for unrecorded students
// Use this on dashboard load or periodically
export const checkForUnrecordedStudents = async () => {
    const { addNotification, createUnrecordedStudentNotification, createBatchUnrecordedNotification } = useNotificationStore()
    const { showUnrecordedStudentAlert, showBatchUnrecordedAlert } = useToastStore()
    
    try {
        // TODO: Replace with actual API call
        const response = await fetch('/api/students/unrecorded')
        const data = await response.json()
        
        if (data.individual && data.individual.length > 0) {
            // Add individual notifications for each unrecorded student
            data.individual.forEach(student => {
                addNotification(createUnrecordedStudentNotification(
                    student.name, 
                    student.missing_type // e.g., "health examination", "oral health record"
                ))
            })
        }
        
        if (data.summary && data.summary.total > 0) {
            // Show batch notification for multiple students
            addNotification(createBatchUnrecordedNotification(
                data.summary.total,
                data.summary.record_type
            ))
            
            // Show toast for immediate attention
            showBatchUnrecordedAlert(data.summary.total, data.summary.record_type)
        }
        
    } catch (error) {
        console.error('Error checking unrecorded students:', error)
    }
}

// Example 3: Integration with ALL treatment timer components
// Add this to your existing timer components (Health, Oral Health, Incident)

// For Health Treatment timers
export const integrateWithHealthTreatmentTimer = () => {
    return {
        onTimerComplete: (studentName, treatmentType) => {
            handleTimerCompletion(studentName, treatmentType, 'health')
        },
        
        // 30 minutes before expiration warning
        onTimer30MinWarning: (studentName, treatmentType, remainingTime) => {
            const { addNotification } = useNotificationStore()
            const { showWarning } = useToastStore()

            addNotification({
                type: 'treatment',
                title: 'Health Treatment Timer Warning',
                message: `Health treatment "${treatmentType}" for ${studentName} expires in 30 minutes`,
                source: 'health',
                priority: 'medium'
            })

            showWarning(
                'Timer Warning - 30 Minutes Left',
                `${treatmentType} for ${studentName} expires soon`,
                { duration: 8000 }
            )
        },
        
        // 15 minutes before expiration warning
        onTimer15MinWarning: (studentName, treatmentType, remainingTime) => {
            const { addNotification } = useNotificationStore()
            const { showWarning } = useToastStore()
            
            addNotification({
                type: 'treatment',
                title: 'Health Treatment Timer Alert',
                message: `Health treatment "${treatmentType}" for ${studentName} expires in 15 minutes`,
                source: 'health',
                priority: 'high'
            })
            
            showWarning(
                'Timer Alert - 15 Minutes Left',
                `${treatmentType} for ${studentName} expires very soon!`,
                { duration: 10000 }
            )
        },
        
        onTimerExpired: (studentName, treatmentType) => {
            const { addNotification, createTimerExpiryNotification } = useNotificationStore()
            const { showError } = useToastStore()

            addNotification({
                ...createTimerExpiryNotification(studentName, treatmentType),
                title: 'Health Treatment Timer Expired',
                message: `Health treatment "${treatmentType}" expired for ${studentName}`,
                source: 'health'
            })

            showError('Health Timer Expired', `${treatmentType} timer expired for ${studentName}`)
        }
    }
}

// For Oral Health Treatment timers
export const integrateWithOralHealthTreatmentTimer = () => {
    return {
        onTimerComplete: (studentName, treatmentType) => {
            handleTimerCompletion(studentName, treatmentType, 'oral_health')
        },
        
        // 30 minutes before expiration warning
        onTimer30MinWarning: (studentName, treatmentType, remainingTime) => {
            const { addNotification } = useNotificationStore()
            const { showWarning } = useToastStore()
            
            addNotification({
                type: 'treatment',
                title: 'Oral Health Timer Warning',
                message: `Oral health treatment "${treatmentType}" for ${studentName} expires in 30 minutes`,
                source: 'oral_health',
                priority: 'medium'
            })
            
            showWarning(
                'Oral Health Timer - 30 Minutes Left',
                `${treatmentType} for ${studentName} expires soon`,
                { duration: 8000 }
            )
        },
        
        // 15 minutes before expiration warning
        onTimer15MinWarning: (studentName, treatmentType, remainingTime) => {
            const { addNotification } = useNotificationStore()
            const { showWarning } = useToastStore()
            
            addNotification({
                type: 'treatment',
                title: 'Oral Health Timer Alert',
                message: `Oral health treatment "${treatmentType}" for ${studentName} expires in 15 minutes`,
                source: 'oral_health',
                priority: 'high'
            })
            
            showWarning(
                'Oral Health Alert - 15 Minutes Left',
                `${treatmentType} for ${studentName} expires very soon!`,
                { duration: 10000 }
            )
        },
        
        onTimerExpired: (studentName, treatmentType) => {
            const { addNotification, createTimerExpiryNotification } = useNotificationStore()
            const { showError } = useToastStore()
            
            addNotification({
                ...createTimerExpiryNotification(studentName, treatmentType),
                title: 'Oral Health Treatment Timer Expired',
                message: `Oral health treatment "${treatmentType}" expired for ${studentName}`,
                source: 'oral_health'
            })
            
            showError('Oral Health Timer Expired', `${treatmentType} timer expired for ${studentName}`)
        }
    }
}

// For Incident Management Show.vue component
export const integrateWithIncidentTimer = () => {
    return {
        onTimerComplete: (studentName, treatmentType) => {
            handleTimerCompletion(studentName, treatmentType, 'incident')
        },
        
        // 30 minutes before expiration warning
        onTimer30MinWarning: (studentName, treatmentType, remainingTime) => {
            const { addNotification } = useNotificationStore()
            const { showWarning } = useToastStore()
            
            addNotification({
                type: 'treatment',
                title: 'Incident Timer Warning',
                message: `Incident "${treatmentType}" for ${studentName} expires in 30 minutes`,
                source: 'incident',
                priority: 'medium'
            })
            
            showWarning(
                'Incident Timer - 30 Minutes Left',
                `${treatmentType} for ${studentName} expires soon`,
                { duration: 8000 }
            )
        },
        
        // 15 minutes before expiration warning
        onTimer15MinWarning: (studentName, treatmentType, remainingTime) => {
            const { addNotification } = useNotificationStore()
            const { showWarning } = useToastStore()
            
            addNotification({
                type: 'treatment',
                title: 'Incident Timer Alert',
                message: `Incident "${treatmentType}" for ${studentName} expires in 15 minutes`,
                source: 'incident',
                priority: 'high'
            })
            
            showWarning(
                'Incident Alert - 15 Minutes Left',
                `${treatmentType} for ${studentName} expires very soon!`,
                { duration: 10000 }
            )
        },
        
        onTimerExpired: (studentName, treatmentType) => {
            const { addNotification, createTimerExpiryNotification } = useNotificationStore()
            const { showError } = useToastStore()
            
            addNotification({
                ...createTimerExpiryNotification(studentName, treatmentType),
                title: 'Incident Timer Expired',
                message: `Incident "${treatmentType}" expired for ${studentName}`,
                source: 'incident'
            })
            
            showError('Incident Timer Expired', `${treatmentType} timer expired for ${studentName}`)
        }
    }
}

// Example 4: Specific integration examples for each treatment system

// For Health Treatment Create.vue component
export const integrateHealthTreatmentNotifications = (healthTreatmentComponent) => {
    const { 
        onTimerComplete, 
        onTimerExpired, 
        onTimer30MinWarning, 
        onTimer15MinWarning 
    } = integrateWithHealthTreatmentTimer()
    
    // Add to your existing timer status update logic
    const handleStatusChange = (newStatus, student, treatment) => {
        if (newStatus === 'completed') {
            onTimerComplete(student.full_name, treatment.title)
        } else if (newStatus === 'expired') {
            onTimerExpired(student.full_name, treatment.title)
        }
    }
    
    // Add to your timer countdown logic (call every minute)
    const handleTimerCheck = (remainingMinutes, student, treatment) => {
        if (remainingMinutes === 30) {
            onTimer30MinWarning(student.full_name, treatment.title, remainingMinutes)
        } else if (remainingMinutes === 15) {
            onTimer15MinWarning(student.full_name, treatment.title, remainingMinutes)
        }
    }
    
    return { handleStatusChange, handleTimerCheck }
}

// For Oral Health Treatment Create.vue component
export const integrateOralHealthTreatmentNotifications = (oralHealthTreatmentComponent) => {
    const { 
        onTimerComplete, 
        onTimerExpired, 
        onTimer30MinWarning, 
        onTimer15MinWarning 
    } = integrateWithOralHealthTreatmentTimer()
    
    const handleStatusChange = (newStatus, student, treatment) => {
        if (newStatus === 'completed') {
            onTimerComplete(student.full_name, treatment.title)
        } else if (newStatus === 'expired') {
            onTimerExpired(student.full_name, treatment.title)
        }
    }
    
    // Add to your timer countdown logic (call every minute)
    const handleTimerCheck = (remainingMinutes, student, treatment) => {
        if (remainingMinutes === 30) {
            onTimer30MinWarning(student.full_name, treatment.title, remainingMinutes)
        } else if (remainingMinutes === 15) {
            onTimer15MinWarning(student.full_name, treatment.title, remainingMinutes)
        }
    }
    
    return { handleStatusChange, handleTimerCheck }
}

// For Incident Management Show.vue component
export const integrateIncidentNotifications = (incidentComponent) => {
    const { 
        onTimerComplete, 
        onTimerExpired, 
        onTimer30MinWarning, 
        onTimer15MinWarning 
    } = integrateWithIncidentTimer()
    
    const handleTimerStatusUpdate = (newStatus, student, incident) => {
        if (newStatus === 'completed') {
            onTimerComplete(student.full_name, incident.title || 'Incident Treatment')
        } else if (newStatus === 'expired') {
            onTimerExpired(student.full_name, incident.title || 'Incident Treatment')
        }
    }
    
    // Add to your timer countdown logic (call every minute)
    const handleTimerCheck = (remainingMinutes, student, incident) => {
        if (remainingMinutes === 30) {
            onTimer30MinWarning(student.full_name, incident.title || 'Incident Treatment', remainingMinutes)
        } else if (remainingMinutes === 15) {
            onTimer15MinWarning(student.full_name, incident.title || 'Incident Treatment', remainingMinutes)
        }
    }
    
    return { handleTimerStatusUpdate, handleTimerCheck }
}

// Example 5: Dashboard integration
// Call this when the dashboard loads
export const initializeDashboardNotifications = async () => {
    // Check for unrecorded students
    await checkForUnrecordedStudents()
    
    // Check for active timers across all treatment types
    await checkActiveTimers()
}

// Check for active/completed timers across all treatment types
export const checkActiveTimers = async () => {
    try {
        // Check health treatment timers
        const healthTimers = await fetch('/api/health-treatments/timers/status')
        const healthTimerData = await healthTimers.json()
        
        // Check oral health treatment timers
        const oralHealthTimers = await fetch('/api/oral-health-treatments/timers/status')
        const oralHealthTimerData = await oralHealthTimers.json()
        
        // Check incident timers
        const incidentTimers = await fetch('/api/incidents/timers/status')
        const incidentTimerData = await incidentTimers.json()
        
        // Process completed timers from all sources
        const allCompletedTimers = [
            ...(healthTimerData.completed || []),
            ...(oralHealthTimerData.completed || []),
            ...(incidentTimerData.completed || [])
        ]
        
        allCompletedTimers.forEach(timer => {
            handleTimerCompletion(
                timer.student_name, 
                timer.treatment_type, 
                timer.source
            )
        })
            
    } catch (error) {
        console.error('Error checking active timers:', error)
    }
}

// Example 5: API endpoint suggestions for backend
// These are the API endpoints you'll need to create:

/*
Backend API endpoints needed for ALL treatment types:

1. GET /api/students/unrecorded
   - Returns students without ANY health records
   - Response format:
   {
     "individual": [
       {
         "id": 1,
         "name": "John Doe",
         "grade": "Grade 5",
         "missing_type": "health examination"
       }
     ],
     "summary": {
       "total": 12,
       "record_type": "health examinations"
     }
   }

2. GET /api/health-treatments/timers/status
   - Returns health treatment timer status
   - Response format:
   {
     "completed": [
       {
         "student_name": "Maria Santos",
         "treatment_type": "Physical Therapy",
         "completed_at": "2024-01-15T10:30:00Z",
         "source": "health"
       }
     ],
     "active": [...],
     "expired": [...]
   }

3. GET /api/oral-health-treatments/timers/status
   - Returns oral health treatment timer status
   - Response format:
   {
     "completed": [
       {
         "student_name": "Alex Johnson",
         "treatment_type": "Dental Cleaning",
         "completed_at": "2024-01-15T11:00:00Z",
         "source": "oral_health"
       }
     ],
     "active": [...],
     "expired": [...]
   }

4. GET /api/incidents/timers/status
   - Returns incident timer status
   - Response format:
   {
     "completed": [
       {
         "student_name": "Sarah Wilson",
         "treatment_type": "First Aid Response",
         "completed_at": "2024-01-15T09:45:00Z",
         "source": "incident"
       }
     ],
     "active": [...],
     "expired": [...]
   }

5. POST /api/notifications/mark-read/{id}
   - Mark notification as read

6. POST /api/notifications/mark-all-read
   - Mark all notifications as read

Integration Notes:
- Health Treatment: Timer status in HealthTreatment model
- Oral Health Treatment: Timer status in OralHealthTreatment model  
- Incident: Timer status in Incident model (already implemented)
- All three systems should trigger notifications on status change
*/
