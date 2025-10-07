<template>
    <div class="bg-gray-100 h-screen">
        <div class="flex flex-col md:flex-row h-full">
            <!-- Sidebar -->
            <aside
                :class="[
                    'fixed top-0 left-0 h-full transition-all duration-300 z-[1100] bg-white shadow-lg',
                    isSidebarOpen ? 'w-60' : 'w-0 overflow-hidden'
                ]"
            >
                <div class="card flex justify-center h-full">
                    <TieredMenu
                        :model="sideBarItems"
                        class="w-full md:w-60 h-full flex-shrink-0 !rounded-none"
                    >
                        <template #start>
                            <div class="flex items-center w-full pt-20 px-2">
                                <img
                                    :src="logoSrc"
                                    alt="MedPort Logo"
                                    class="h-10 w-10 mr-2"
                                />
                                <span class="text-xl font-semibold">MedPort</span>
                            </div>
                        </template>
                        <template #item="{ item, props, hasSubmenu }">
                            <Link
                                v-if="item.route"
                                :href="item.route"
                                class="no-underline"
                                v-bind="props.action"
                            >
                                <span :class="item.icon" />
                                <span class="ml-2">{{ item.label }}</span>
                            </Link>
                            <button
                                v-else-if="item.command"
                                @click="item.command"
                                class="w-full text-left p-3 hover:bg-gray-100 flex items-center no-underline border-none bg-transparent cursor-pointer"
                                v-bind="props.action"
                            >
                                <span :class="item.icon" />
                                <span class="ml-2">{{ item.label }}</span>
                            </button>
                        </template>
                    </TieredMenu>
                </div>
            </aside>

            <!-- Main Content -->
            <main
                class="flex-1 transition-all duration-300 relative"
                :class="isSidebarOpen ? 'md:ml-60' : 'ml-0'"
            >

                <header class="fixed top-0 left-0 right-0 z-[1100]">
                    <Menubar :model="menuBarItems" class="w-full md:w-100 !border-l-0 !rounded-none">
                        <template #start>
                            <button
                                @click="toggleSidebar"
                                class="bg-transparent border-none p-2 mr-2 cursor-pointer hover:bg-gray-200 rounded"
                            >
                                <i class="pi pi-bars text-gray-700"></i>
                            </button>
                        </template>
                        <template #end>
                            <div class="flex items-center space-x-3">
                                <!-- Notification Dropdown -->
                                <NotificationDropdown 
                                    :notifications="notifications"
                                    @mark-as-read="markNotificationAsRead"
                                    @mark-all-as-read="markAllNotificationsAsRead"
                                />
                                
                                <button
                                    @click="toggleUserMenu"
                                    v-ripple
                                    class="relative overflow-hidden w-full border-0 bg-transparent flex items-start justify-center pl-4 hover:bg-surface-100 dark:hover:bg-surface-800 rounded-none cursor-pointer transition-colors duration-200"
                                >
                                    <Avatar
                                        image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
                                        class="mr-2"
                                        shape="circle"
                                    />
                                    <span class="inline-flex flex-col items-start">
                                        <span class="font-bold text-xs">{{ user.full_name }}</span>
                                        <span class="text-xs">{{ user.role }}</span>
                                    </span>
                                </button>
                                <Menu ref="userMenu" :model="userMenuItems" popup />
                            </div>
                        </template>
                    </Menubar>
                </header>

                <div class="md:mt-16 p-4">
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
import ToastNotification from '@/Components/ToastNotification.vue'
// Import the logo
import logoSrc from '@/assets/logo.png'

// Import notification components
import NotificationDropdown from '@/Components/NotificationDropdown.vue'
import { useNotificationStore } from '@/Stores/notificationStore.js'
import { useToastStore } from '@/Stores/toastStore.js'
import { integrateHealthTreatmentNotifications, integrateOralHealthTreatmentNotifications, integrateIncidentNotifications } from '@/Utils/notificationIntegration.js'

const page = usePage()

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
    initializeNotifications, 
    markAsRead, 
    markAllAsRead,
    addNotification,
    createHealthExamNotification,
    createTreatmentNotification,
    createReportNotification
} = useNotificationStore()

// Notification methods
const markNotificationAsRead = (notificationId) => {
    markAsRead(notificationId)
}

const markAllNotificationsAsRead = () => {
    markAllAsRead()
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

const logout = () => {
    router.post('/logout', {}, {
        onSuccess: () => {
            console.log('Logged out successfully');
        },
        onError: (errors) => {
            console.error('Logout failed', errors);
        }
    });
};

const userMenuItems = ref([
    {
        label: 'Profile',
        icon: 'pi pi-user',
        command: () => {
            // Navigate to profile page if needed
            // router.visit('/profile');
        }
    }
]);

const userMenu = ref(null);

const toggleUserMenu = (event) => {
    userMenu.value.toggle(event);
};

const sideBarItems = ref([
    {
        label: 'Dashboard',
        icon: 'pi pi-home',
        route: '/'
    },
    {
        label: 'Pupil Health',
        icon: 'pi pi-heart',
        route: '/pupil-health'
    },
    {
        label: 'Health Report',
        icon: 'pi pi-chart-bar',
        route: '/health-report'
    },
    {
        label: 'Oral Health Report',
        icon: 'pi pi-file-check',
        route: '/oral-health-report'
    },
    {
        separator: true
    },
    {
        label: 'Schedule Calendar',
        icon: 'pi pi-calendar',
        route: '/schedule-calendar'
    },
    {
        label: 'Chat',
        icon: 'pi pi-comments',
        route: '/chat'
    },
    {
        separator: true
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        command: logout
    }
])

// Check for unrecorded students notifications
const checkUnrecordedStudents = async () => {
    try {
        const response = await fetch('/api/notifications/check-unrecorded')
        const data = await response.json()
        
        if (data.notifications && data.notifications.length > 0) {
            data.notifications.forEach(notification => {
                if (notification.type === 'unrecorded_student') {
                    addNotification(createUnrecordedStudentNotification(
                        notification.student_name, 
                        notification.missing_type
                    ))
                } else if (notification.type === 'batch_unrecorded') {
                    addNotification(createBatchUnrecordedNotification(
                        notification.count,
                        notification.record_type
                    ))
                }
            })
        }
    } catch (error) {
        console.error('Error checking unrecorded students:', error)
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
    initializeNotifications()
    
    // Check for unrecorded students on load
    checkUnrecordedStudents()
    
    // Check for unrecorded students every 10 minutes
    setInterval(checkUnrecordedStudents, 600000)
    
    // Start global timer monitoring
    startGlobalTimerMonitoring()
})

// Stop monitoring when component unmounts
onUnmounted(() => {
    stopGlobalTimerMonitoring()
})
</script>

<style scoped>
.sidebar-closed {
    width: 0;
    overflow: hidden;
}
</style>
