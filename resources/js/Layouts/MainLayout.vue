<template>
    <div class="main-container">
        <div class="layout-wrapper">
            <!-- Sidebar -->
            <aside
                :class="[
                    'sidebar',
                    isSidebarOpen ? 'sidebar-open' : 'sidebar-closed'
                ]"
            >
                <div class="card sidebar-card">
                    <TieredMenu
                        :model="sideBarItems"
                        class="sidebar-menu"
                    >
                        <template #start>
                            <div class="sidebar-logo-container">
                                <img
                                    :src="logoSrc"
                                    alt="MedPort Logo"
                                    class="sidebar-logo"
                                />
                                <span class="sidebar-brand">MedPort</span>
                            </div>
                        </template>
                        <template #item="{ item, props, hasSubmenu }">
                            <Link
                                v-if="item.route"
                                :href="item.route"
                                :class="[
                                    'menu-item-link',
                                    isActiveRoute(item.route) ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                                v-bind="props.action"
                            >
                                <span :class="[
                                    item.icon,
                                    isActiveRoute(item.route) ? 'menu-item-icon-active' : 'menu-item-icon-inactive'
                                ]" />
                                <span class="ml-2">{{ item.label }}</span>
                            </Link>
                            <button
                                v-else-if="item.command"
                                @click="item.command"
                                class="menu-item-button"
                                v-bind="props.action"
                            >
                                <span :class="[item.icon, 'menu-item-icon-inactive']" />
                                <span class="ml-2">{{ item.label }}</span>
                            </button>
                        </template>
                    </TieredMenu>
                </div>
            </aside>

            <!-- Main Content -->
            <main
                :class="[
                    'main-content',
                    isSidebarOpen ? 'main-content-shifted' : 'main-content-full'
                ]"
            >

                <header class="main-header">
                    <Menubar :model="menuBarItems" class="header-menubar md:w-100 !border-l-0 !rounded-none">
                        <template #start>
                            <button
                                @click="toggleSidebar"
                                class="sidebar-toggle-btn"
                            >
                                <i class="pi pi-bars sidebar-toggle-icon"></i>
                            </button>
                            <Link href="/" class="header-brand-link">
                                MedPort
                            </Link>
                        </template>
                        <template #end>
                            <div class="header-end-container">
                                <!-- Viewer Only Badge for Teachers -->
                                <div v-if="user.role === 'teacher'" class="viewer-badge-container">
                                    <span class="viewer-badge">
                                        👁️ Viewer Only
                                    </span>
                                </div>

                                <!-- Notification Dropdown -->
                                <NotificationDropdown
                                    :notifications="filteredNotifications"
                                    @mark-as-read="markNotificationAsRead"
                                    @mark-all-as-read="markAllNotificationsAsRead"
                                    @delete-notification="deleteNotification"
                                    @clear-all-notifications="handleClearAllNotifications"
                                    @view-all-notifications="handleViewAllNotifications"
                                />

                                <div class="user-info-container">
                                    <Avatar
                                        image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
                                        class="user-avatar"
                                        shape="circle"
                                    />
                                    <span class="user-details">
                                        <span class="user-name">{{ user.full_name }}</span>
                                        <span class="user-role">{{ user.role }}</span>
                                    </span>
                                </div>
                            </div>
                        </template>
                    </Menubar>
                </header>

                <div class="content-wrapper">
                    <slot></slot>
                </div>
            </main>
        </div>

        <!-- Toast Notifications -->
        <ToastNotification
            :toasts="toasts"
            @remove-toast="removeToast"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import TieredMenu from 'primevue/tieredmenu'
import Menubar from 'primevue/menubar'
import Avatar from 'primevue/avatar'
import Button from 'primevue/button'
import Menu from 'primevue/menu'
import Badge from 'primevue/badge'
import ToastNotification from '@/Components/ToastNotification.vue'
// Import the logo
import logoSrc from '@/assets/logo.png'
// Import component styles
import '../../css/layouts/MainLayout.css'

// Import notification components
import NotificationDropdown from '@/Components/NotificationDropdown.vue'
import { useNotificationStore } from '@/Stores/notificationStore.js'
import { useToastStore } from '@/Stores/toastStore.js'
import { integrateHealthTreatmentNotifications, integrateOralHealthTreatmentNotifications, integrateIncidentNotifications } from '@/Utils/notificationIntegration.js'

const page = usePage()

// Function to check if a route is currently active
const isActiveRoute = (route) => {
    const currentUrl = page.url

    // Handle exact matches
    if (route === '/' && currentUrl === '/') {
        return true
    }

    // Handle other routes - check if current URL starts with the route
    if (route !== '/' && currentUrl.startsWith(route)) {
        return true
    }

    return false
}

// Health alerts data - you can move this to a composable later
const urgentAlertsCount = computed(() => {
    // This should come from your dashboard data or a separate API call
    const dashboardData = page.props.dashboardData || {}
    const healthAlerts = dashboardData.healthAlerts || []
    return healthAlerts.filter(alert => alert.severity === 'danger').length
})

const user = computed(() => {
    const authUser = page.props.auth?.user
    return {
        full_name: authUser?.full_name || authUser?.name || 'Guest',
        role: authUser?.role || 'Not Logged In'
    }
})

// Notification store
const {
    notifications,
    unreadCount,
    initializeNotifications,
    markAsRead,
    markAllAsRead,
    addNotification,
    removeNotification,
    clearAllNotifications,
    clearNotificationsForRole,
    getFilteredNotifications
} = useNotificationStore()

// Computed property for role-based filtered notifications
const filteredNotifications = computed(() => {
    const userRole = page.props.auth?.user?.role || 'teacher'

    // Use the notification store's proper filtering method
    return getFilteredNotifications(userRole)
})

// Notification methods
const markNotificationAsRead = (notificationId) => {
    markAsRead(notificationId)
}


const markAllNotificationsAsRead = () => {
    const userId = page.props.auth?.user?.id
    markAllAsRead(userId)
}

const deleteNotification = (notificationId) => {
    const userId = page.props.auth?.user?.id
    removeNotification(notificationId, userId)
}

const handleClearAllNotifications = () => {
    clearAllNotifications()
}

const handleViewAllNotifications = () => {
    // Navigate to a dedicated notifications page
    router.visit('/notifications')
}

// Toast store
const {
    toasts,
    removeToast,
    showSuccess,
    showInfo,
    showHealthExamComplete,
    showTreatmentScheduled,
    showReportGenerated
} = useToastStore()

// Production notification functions will be added here as needed

const menuBarItems = ref([])

const isSidebarOpen = ref(true)

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    console.log('Sidebar toggled:', isSidebarOpen.value)
}

const userMenuItems = ref([
    // Profile functionality removed - not needed for health management system
]);

const userMenu = ref(null);

const toggleUserMenu = (event) => {
    userMenu.value.toggle(event);
};

const logout = () => {
    // Use Inertia router with optimized settings for fast logout
    router.post('/logout', {}, {
        preserveState: false,
        preserveScroll: false,
        replace: true,
        onStart: () => {
            // Clear storage immediately when logout starts
            try {
                localStorage.clear();
                sessionStorage.clear();
            } catch (e) {
                // Ignore errors
            }
        },
        onError: () => {
            // If there's any error, force redirect to login
            window.location.href = '/login';
        }
    });
}

const sideBarItems = computed(() => {
    const userRole = page.props.auth?.user?.role || 'teacher'

    const baseItems = [
        {
            separator: true
        },
        {
            label: 'Dashboard',
            icon: 'pi pi-home',
            route: '/'
        },
        {
            label: 'Pupil Health',
            icon: 'pi pi-heart',
            route: '/pupil-health'
        }
    ]

    // Add Health Report and Oral Health Report for admin and nurse only
    if (userRole === 'admin' || userRole === 'nurse') {
        baseItems.push({
            label: 'Health Report',
            icon: 'pi pi-chart-bar',
            route: '/health-report'
        })
        baseItems.push({
            label: 'Oral Health Report',
            icon: 'pi pi-file-check',
            route: '/oral-health-report'
        })
    }

    baseItems.push({
        separator: true
    })
    baseItems.push({
        label: 'Calendar',
        icon: 'pi pi-calendar',
        route: '/schedule-calendar'
    })
    baseItems.push({
        label: 'Consultation Messages',
        icon: 'pi pi-comments',
        route: '/consultation'
    })

    // Add Settings for admin users only
    if (userRole === 'admin') {
        baseItems.push({
            separator: true
        })
        //.push({
          //  label: 'Pupil Management',
          //  icon: 'pi pi-users',
          //  route: '/student-management'
       // })
        baseItems.push({
            label: 'Export Data',
            icon: 'pi pi-download',
            route: '/health-data-export'
        })
        baseItems.push({
            label: 'Logs',
            icon: 'pi pi-list',
            route: '/error-logs'
        })
    }

    // Add logout at the end
    baseItems.push({
        separator: true
    })
    baseItems.push({
        label: 'Logout',
        icon: 'pi pi-sign-out',
        command: logout
    })

    return baseItems
})

// Helper functions for creating notifications
const createUnrecordedStudentNotification = (studentName, missingType) => {
    return {
        id: Date.now() + Math.random(),
        type: 'warning',
        title: 'Missing Health Record',
        message: `${studentName} is missing ${missingType} record`,
        timestamp: new Date().toISOString(),
        actions: [
            {
                label: 'View Student',
                action: () => {
                    // Navigate to student's health page
                    router.visit(`/pupil-health/student/${studentName}`)
                }
            }
        ]
    }
}

const createBatchUnrecordedNotification = (count, recordType) => {
    return {
        id: Date.now() + Math.random(),
        type: 'info',
        title: 'Multiple Missing Records',
        message: `${count} students are missing ${recordType} records`,
        timestamp: new Date().toISOString(),
        actions: [
            {
                label: 'View Report',
                action: () => {
                    // Navigate to health report page
                    router.visit('/health-report')
                }
            }
        ]
    }
}

// Check for unrecorded students
const checkUnrecordedStudents = async () => {
    try {
        const response = await fetch('/api/notifications/check-unrecorded')
        const data = await response.json()

        if (data.notifications && data.notifications.length > 0) {
            const userId = page.props.auth?.user?.id
            data.notifications.forEach(notification => {
                if (notification.type === 'unrecorded_student') {
                    addNotification(createUnrecordedStudentNotification(
                        notification.student_name,
                        notification.missing_type
                    ), userId)
                } else if (notification.type === 'batch_unrecorded') {
                    addNotification(createBatchUnrecordedNotification(
                        notification.count,
                        notification.record_type
                    ), userId)
                }
            })
        }
    } catch (error) {
        console.error('Error checking unrecorded students:', error)
    }
}

// Check for schedule notifications
const checkScheduleNotifications = async () => {
    try {
        const response = await fetch('/api/notifications/check-schedules')
        const data = await response.json()

        if (data.notifications && data.notifications.length > 0) {
            const userId = page.props.auth?.user?.id
            data.notifications.forEach(notification => {
                addNotification(createScheduleNotification(notification), userId)
            })
        }
    } catch (error) {
        console.error('Error checking schedule notifications:', error)
    }
}

// Helper function for creating schedule notifications
const createScheduleNotification = (scheduleData) => {
    return {
        id: scheduleData.id,
        type: 'schedule',
        title: scheduleData.title,
        message: scheduleData.message,
        timestamp: new Date().toISOString(),
        priority: scheduleData.priority || 'medium',
        schedule_id: scheduleData.schedule_id,
        actions: [
            {
                label: 'View Schedule',
                action: () => {
                    router.visit(`/schedule-calendar/show/${scheduleData.schedule_id}`)
                }
            }
        ]
    }
}

// Global timer monitoring system
const globalTimerInterval = ref(null)

const startGlobalTimerMonitoring = () => {
    console.log('🌍 Starting global timer monitoring...')

    // Check all active timers every minute
    globalTimerInterval.value = setInterval(async () => {
        try {
            console.log('🔍 Checking all active timers...')

            // Check Health Treatments
            const healthResponse = await fetch('/api/notifications/check-timers')
            if (healthResponse.ok) {
                const healthData = await healthResponse.json()

                if (healthData.treatments && healthData.treatments.length > 0) {
                    const healthIntegration = integrateHealthTreatmentNotifications()

                    healthData.treatments.forEach(treatment => {
                        const remainingMinutes = treatment.remaining_minutes

                        // 30-minute warning
                        if (remainingMinutes <= 30 && remainingMinutes > 29 && !treatment.thirty_min_notified) {
                            console.log(`🔔 30-minute warning for Health Treatment: ${treatment.title}`)
                            healthIntegration.handleTimerCheck(30, treatment.student, treatment)
                        }

                        // 15-minute warning
                        if (remainingMinutes <= 15 && remainingMinutes > 14 && !treatment.fifteen_min_notified) {
                            console.log(`🚨 15-minute warning for Health Treatment: ${treatment.title}`)
                            healthIntegration.handleTimerCheck(15, treatment.student, treatment)
                        }

                        // Expired
                        if (remainingMinutes <= 0 && !treatment.expired_notified) {
                            console.log(`⏰ Timer expired for Health Treatment: ${treatment.title}`)
                            healthIntegration.handleStatusChange('expired', treatment.student, treatment)
                        }
                    })
                }
            }

        } catch (error) {
            console.error('Error in global timer monitoring:', error)
        }
    }, 60000) // Check every minute
}

const stopGlobalTimerMonitoring = () => {
    if (globalTimerInterval.value) {
        console.log('🛑 Stopping global timer monitoring')
        clearInterval(globalTimerInterval.value)
        globalTimerInterval.value = null
    }
}

// Initialize notifications when component mounts
onMounted(() => {
    const userId = page.props.auth?.user?.id
    initializeNotifications(userId)


    // Clear cached notifications based on current user role
    const userRole = page.props.auth?.user?.role
    if (userRole) {
        clearNotificationsForRole(userRole)
    }

    // Check for unrecorded students on load
    checkUnrecordedStudents()

    // Check for schedule notifications on load
    checkScheduleNotifications()

    // Check for unrecorded students every 10 minutes
    setInterval(checkUnrecordedStudents, 600000)

    // Check for schedule notifications every 5 minutes
    setInterval(checkScheduleNotifications, 300000)

    // Start global timer monitoring
    startGlobalTimerMonitoring()
})

// Stop monitoring when component unmounts
onUnmounted(() => {
    stopGlobalTimerMonitoring()
})
</script>
