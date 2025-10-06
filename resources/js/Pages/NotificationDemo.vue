<template>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Facebook-Style Notification System Demo</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Notification Dropdown Demo -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Notification Dropdown
                </h2>
                <p class="text-gray-600 mb-4">
                    Click the bell icon in the top-right corner to see the notification dropdown with unread badges.
                </p>
                
                <div class="space-y-3">
                    <button 
                        @click="addHealthExamNotification"
                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors"
                    >
                        Add Health Exam Notification
                    </button>
                    
                    <button 
                        @click="addTreatmentNotification"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Add Treatment Notification
                    </button>
                    
                    <button 
                        @click="addReportNotification"
                        class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors"
                    >
                        Add Report Notification
                    </button>
                    
                    <button 
                        @click="addSystemNotification"
                        class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        Add System Notification
                    </button>
                </div>
            </div>

            <!-- Toast Notifications Demo -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z" />
                    </svg>
                    Toast Notifications
                </h2>
                <p class="text-gray-600 mb-4">
                    Real-time toast notifications appear in the top-right corner with auto-dismiss and progress bars.
                </p>
                
                <div class="space-y-3">
                    <button 
                        @click="showSuccessToast"
                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors"
                    >
                        Show Success Toast
                    </button>
                    
                    <button 
                        @click="showErrorToast"
                        class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Show Error Toast
                    </button>
                    
                    <button 
                        @click="showWarningToast"
                        class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
                    >
                        Show Warning Toast
                    </button>
                    
                    <button 
                        @click="showInfoToast"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Show Info Toast
                    </button>
                </div>
            </div>
        </div>

        <!-- Features Overview -->
        <div class="mt-12 bg-gray-50 rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-6">Features Overview</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-lg mb-2 text-blue-600">📱 Facebook-Style Dropdown</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Bell icon with unread badge</li>
                        <li>• Dropdown with notification list</li>
                        <li>• Mark as read functionality</li>
                        <li>• Different icons for different types</li>
                        <li>• Time formatting (5m ago, 2h ago)</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-lg mb-2 text-green-600">🍞 Toast Notifications</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Real-time slide-in animations</li>
                        <li>• Auto-dismiss with progress bar</li>
                        <li>• Different types (success, error, warning, info)</li>
                        <li>• Manual close button</li>
                        <li>• Stack management (max 5)</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-lg mb-2 text-purple-600">🏥 Health-Specific</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Health examination notifications</li>
                        <li>• Treatment scheduling alerts</li>
                        <li>• Report generation notices</li>
                        <li>• System maintenance alerts</li>
                        <li>• Custom medical icons</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Usage Instructions -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-blue-800">How to Use in Your Components</h2>
            
            <div class="bg-white rounded-lg p-4 mb-4">
                <h3 class="font-semibold mb-2">1. Import the stores:</h3>
                <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto"><code>import { useNotificationStore } from '@/Stores/notificationStore.js'
import { useToastStore } from '@/Stores/toastStore.js'</code></pre>
            </div>
            
            <div class="bg-white rounded-lg p-4 mb-4">
                <h3 class="font-semibold mb-2">2. Use in your component:</h3>
                <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto"><code>const { addNotification, createHealthExamNotification } = useNotificationStore()
const { showSuccess, showHealthExamComplete } = useToastStore()

// Add notification to dropdown
addNotification(createHealthExamNotification('John Doe', 'Grade 5'))

// Show toast notification
showHealthExamComplete('John Doe', 'Grade 5')</code></pre>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useNotificationStore } from '@/Stores/notificationStore.js'
import { useToastStore } from '@/Stores/toastStore.js'

// Notification store
const { 
    addNotification, 
    createHealthExamNotification,
    createTreatmentNotification,
    createReportNotification,
    createSystemNotification
} = useNotificationStore()

// Toast store
const { 
    showSuccess, 
    showError, 
    showWarning, 
    showInfo,
    showHealthExamComplete,
    showTreatmentScheduled,
    showReportGenerated
} = useToastStore()

// Notification methods
const addHealthExamNotification = () => {
    const students = ['John Doe', 'Maria Santos', 'Alex Johnson', 'Sarah Wilson']
    const grades = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6']
    
    const randomStudent = students[Math.floor(Math.random() * students.length)]
    const randomGrade = grades[Math.floor(Math.random() * grades.length)]
    
    addNotification(createHealthExamNotification(randomStudent, randomGrade))
}

const addTreatmentNotification = () => {
    const students = ['Emma Brown', 'Michael Davis', 'Lisa Garcia', 'David Miller']
    const treatments = ['Follow-up checkup', 'Dental cleaning', 'Vision screening', 'Hearing test']
    
    const randomStudent = students[Math.floor(Math.random() * students.length)]
    const randomTreatment = treatments[Math.floor(Math.random() * treatments.length)]
    
    addNotification(createTreatmentNotification(randomStudent, randomTreatment))
}

const addReportNotification = () => {
    const reports = ['Monthly Health Report', 'Quarterly BMI Report', 'Annual Vaccination Report', 'Dental Health Summary']
    const details = ['is ready for review', 'has been generated', 'is available for download', 'requires your attention']
    
    const randomReport = reports[Math.floor(Math.random() * reports.length)]
    const randomDetail = details[Math.floor(Math.random() * details.length)]
    
    addNotification(createReportNotification(randomReport, randomDetail))
}

const addSystemNotification = () => {
    const systemMessages = [
        { title: 'System Maintenance', message: 'Scheduled maintenance will occur tonight at 2:00 AM' },
        { title: 'New Feature Available', message: 'Check out the new health tracking dashboard' },
        { title: 'Data Backup Complete', message: 'All health records have been successfully backed up' },
        { title: 'Security Update', message: 'Please update your password for enhanced security' }
    ]
    
    const randomMessage = systemMessages[Math.floor(Math.random() * systemMessages.length)]
    addNotification(createSystemNotification(randomMessage.title, randomMessage.message))
}

// Toast methods
const showSuccessToast = () => {
    const messages = [
        { title: 'Health Exam Complete', message: 'Successfully completed health examination for John Doe' },
        { title: 'Data Saved', message: 'Student health record has been updated' },
        { title: 'Report Generated', message: 'Monthly health report is ready for download' }
    ]
    
    const randomMessage = messages[Math.floor(Math.random() * messages.length)]
    showSuccess(randomMessage.title, randomMessage.message)
}

const showErrorToast = () => {
    const errors = [
        { title: 'Connection Error', message: 'Unable to connect to the health database' },
        { title: 'Validation Failed', message: 'Please check the required health examination fields' },
        { title: 'Permission Denied', message: 'You do not have access to modify this health record' }
    ]
    
    const randomError = errors[Math.floor(Math.random() * errors.length)]
    showError(randomError.title, randomError.message)
}

const showWarningToast = () => {
    const warnings = [
        { title: 'Incomplete Data', message: 'Some health examination fields are missing' },
        { title: 'Overdue Checkup', message: '5 students have overdue health checkups' },
        { title: 'Low Stock Alert', message: 'Medical supplies are running low' }
    ]
    
    const randomWarning = warnings[Math.floor(Math.random() * warnings.length)]
    showWarning(randomWarning.title, randomWarning.message)
}

const showInfoToast = () => {
    const infos = [
        { title: 'Reminder', message: 'Health screening week starts next Monday' },
        { title: 'New Guidelines', message: 'Updated WHO health guidelines are now available' },
        { title: 'Training Session', message: 'Health staff training scheduled for Friday' }
    ]
    
    const randomInfo = infos[Math.floor(Math.random() * infos.length)]
    showInfo(randomInfo.title, randomInfo.message)
}
</script>
