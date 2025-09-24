<script setup>
import { ref, onMounted, computed } from 'vue'
import Chart from 'primevue/chart'
import Button from 'primevue/button'
import Card from 'primevue/card'
import ProgressBar from 'primevue/progressbar'
import Badge from 'primevue/badge'
import Timeline from 'primevue/timeline'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()

// Reactive variables for student statistics
const totalStudents = ref(0)
const femaleStudents = ref(0)
const maleStudents = ref(0)

// Reactive variables for deworming and iron supplement data
const dewormingData = ref({ dewormed: 0, notDewormed: 0 })
const ironSupplementData = ref({ positive: 0, negative: 0 })

// Chart data for deworming
const dewormingChartData = ref({
    labels: ['Dewormed', 'Not Dewormed'],
    datasets: [{
        data: [0, 0],
        backgroundColor: ['#36A2EB', '#FF6384'],
    }]
})

const dewormingChartOptions = ref({
    plugins: {
        title: {
            display: true,
            text: 'Deworming Status'
        }
    }
})

// Chart data for iron supplement
const ironSupplementChartData = ref({
    labels: ['Positive', 'Negative'],
    datasets: [{
        data: [0, 0],
        backgroundColor: ['#4BC0C0', '#FF9F40'],
    }]
})

const ironSupplementChartOptions = ref({
    plugins: {
        title: {
            display: true,
            text: 'Iron Supplementation'
        }
    }
})

// Nutritional Status BMI Chart Data
const nutritionalStatusBMIChartData = ref({
    labels: [],
    datasets: [{
        label: 'Nutritional Status (BMI)',
        data: [],
        backgroundColor: '#42A5F5'
    }]
})

// Nutritional Status Height Chart Data
const nutritionalStatusHeightChartData = ref({
    labels: [],
    datasets: [{
        label: 'Nutritional Status (Height)',
        data: [],
        backgroundColor: '#66BB6A'
    }]
})

// Update data when component mounts
onMounted(() => {
    const dashboardData = page.props.dashboardData || {}

    // Update student statistics
    totalStudents.value = dashboardData.totalStudents || 0
    femaleStudents.value = dashboardData.femaleStudents || 0
    maleStudents.value = dashboardData.maleStudents || 0

    // Update deworming data
    dewormingData.value = dashboardData.deworming || { dewormed: 0, notDewormed: 0 }
    dewormingChartData.value.datasets[0].data = [
        dewormingData.value.dewormed, 
        dewormingData.value.notDewormed
    ]

    // Update iron supplementation data
    ironSupplementData.value = dashboardData.ironSupplement || { positive: 0, negative: 0 }
    ironSupplementChartData.value.datasets[0].data = [
        ironSupplementData.value.positive, 
        ironSupplementData.value.negative
    ]

    // Update Nutritional Status BMI Chart
    if (dashboardData.nutritionalStatusBMI) {
        nutritionalStatusBMIChartData.value.labels = dashboardData.nutritionalStatusBMI.map(item => item.nutritional_status_bmi)
        nutritionalStatusBMIChartData.value.datasets[0].data = dashboardData.nutritionalStatusBMI.map(item => item.count)
    }

    // Update Nutritional Status Height Chart
    if (dashboardData.nutritionalStatusHeight) {
        nutritionalStatusHeightChartData.value.labels = dashboardData.nutritionalStatusHeight.map(item => item.nutritional_status_height)
        nutritionalStatusHeightChartData.value.datasets[0].data = dashboardData.nutritionalStatusHeight.map(item => item.count)
    }

    // Update Health Alerts from database
    if (dashboardData.healthAlerts) {
        healthAlerts.value = dashboardData.healthAlerts.map(alert => ({
            id: alert.id,
            type: alert.severity || 'info', // 'danger', 'warning', 'info'
            icon: getAlertIcon(alert.type),
            title: alert.title,
            message: alert.message,
            count: alert.count || 0,
            color: getAlertColor(alert.severity)
        }))
    } else {
        // Generate health alerts based on existing data
        const alerts = []
        
        // Check for students without health examinations
        const studentsWithoutHealthExam = totalStudents.value - (dewormingData.value.dewormed + dewormingData.value.notDewormed)
        if (studentsWithoutHealthExam > 0) {
            alerts.push({
                id: 1,
                type: 'danger',
                icon: 'pi pi-heart',
                title: 'Health Check Overdue',
                message: `${studentsWithoutHealthExam} students need health examination`,
                count: studentsWithoutHealthExam,
                color: 'red'
            })
        }

        // Check deworming status
        if (dewormingData.value.notDewormed > 0) {
            alerts.push({
                id: 2,
                type: 'warning',
                icon: 'pi pi-exclamation-triangle',
                title: 'Deworming Required',
                message: `${dewormingData.value.notDewormed} students need deworming`,
                count: dewormingData.value.notDewormed,
                color: 'orange'
            })
        }

        healthAlerts.value = alerts
    }

    // Update Health Metrics from database
    if (dashboardData.healthMetrics) {
        healthMetrics.value = dashboardData.healthMetrics
    } else {
        // Calculate health metrics based on existing data
        const metrics = []
        
        if (totalStudents.value > 0) {
            // Deworming coverage
            const dewormingCoverage = Math.round((dewormingData.value.dewormed / totalStudents.value) * 100)
            metrics.push({
                label: 'Deworming Coverage',
                value: dewormingCoverage,
                target: 100,
                icon: 'pi pi-shield',
                color: dewormingCoverage >= 90 ? 'success' : dewormingCoverage >= 70 ? 'warning' : 'danger'
            })

            // Iron supplementation coverage
            const ironCoverage = Math.round((ironSupplementData.value.positive / totalStudents.value) * 100)
            metrics.push({
                label: 'Iron Supplementation',
                value: ironCoverage,
                target: 100,
                icon: 'pi pi-heart',
                color: ironCoverage >= 80 ? 'success' : ironCoverage >= 60 ? 'warning' : 'danger'
            })

            // Health records completion
            const healthRecordsCompletion = Math.round(((dewormingData.value.dewormed + dewormingData.value.notDewormed) / totalStudents.value) * 100)
            metrics.push({
                label: 'Health Records',
                value: healthRecordsCompletion,
                target: 100,
                icon: 'pi pi-file',
                color: healthRecordsCompletion >= 95 ? 'success' : healthRecordsCompletion >= 80 ? 'warning' : 'danger'
            })
        }

        healthMetrics.value = metrics
    }
})

// Helper functions for alert styling
const getAlertIcon = (type) => {
    switch (type) {
        case 'vaccination': return 'pi pi-shield'
        case 'health_check': return 'pi pi-heart'
        case 'dental': return 'pi pi-bookmark'
        case 'deworming': return 'pi pi-exclamation-triangle'
        default: return 'pi pi-info-circle'
    }
}

const getAlertColor = (severity) => {
    switch (severity) {
        case 'danger': return 'red'
        case 'warning': return 'orange'
        case 'info': return 'blue'
        default: return 'gray'
    }
}

const schoolYear = ref('2023-2024')
const schoolYears = ref(['2023-2024', '2022-2023', '2021-2022'])

// Health Alerts and Notifications (will be populated from database)
const healthAlerts = ref([])

// Health program progress (will be populated from database)
const healthMetrics = ref([])

// Recent Activities Timeline
const recentActivities = ref([
    {
        date: new Date(),
        icon: 'pi pi-user-plus',
        color: 'green',
        title: 'New Health Record Added',
        description: 'Health examination completed for John Smith (Grade 5)'
    },
    {
        date: new Date(Date.now() - 2 * 60 * 60 * 1000),
        icon: 'pi pi-file',
        color: 'blue',
        title: 'Health Report Generated',
        description: 'Monthly health report generated for Grade 4 students'
    },
    {
        date: new Date(Date.now() - 4 * 60 * 60 * 1000),
        icon: 'pi pi-heart',
        color: 'pink',
        title: 'Oral Health Screening',
        description: 'Dental examination completed for 12 students'
    },
    {
        date: new Date(Date.now() - 6 * 60 * 60 * 1000),
        icon: 'pi pi-shield',
        color: 'orange',
        title: 'Vaccination Updated',
        description: 'COVID-19 booster records updated for Grade 6'
    }
])


// Computed properties for dynamic content
const totalHealthRecords = computed(() => totalStudents.value)
const healthCompletionRate = computed(() => {
    if (totalStudents.value === 0) return 0
    return Math.round((dewormingData.value.dewormed / totalStudents.value) * 100)
})

const urgentAlertsCount = computed(() => {
    return healthAlerts.value.filter(alert => alert.type === 'danger').length
})

// Format time for timeline
const formatTimeAgo = (date) => {
    const now = new Date()
    const diff = now - date
    const hours = Math.floor(diff / (1000 * 60 * 60))
    const minutes = Math.floor(diff / (1000 * 60))
    
    if (hours > 0) return `${hours}h ago`
    if (minutes > 0) return `${minutes}m ago`
    return 'Just now'
}
</script>

<template>
    <div class="space-y-6">
        <!-- Header with Welcome Message and Quick Stats -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Health Dashboard</h1>
                    <p class="text-blue-100">Welcome to Naawan Central School Health Management System</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">{{ totalStudents }}</div>
                    <div class="text-blue-100">Total Students</div>
                </div>
            </div>
        </div>

        <!-- Health Alerts Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold flex items-center">
                    <i class="pi pi-bell mr-2 text-orange-500"></i>
                    Health Alerts
                    <Badge v-if="urgentAlertsCount > 0" :value="urgentAlertsCount" severity="danger" class="ml-2" />
                </h2>
            </div>
            <div v-if="healthAlerts.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div 
                    v-for="alert in healthAlerts" 
                    :key="alert.id"
                    class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                    :class="{
                        'border-orange-200 bg-orange-50': alert.type === 'warning',
                        'border-red-200 bg-red-50': alert.type === 'danger',
                        'border-blue-200 bg-blue-50': alert.type === 'info'
                    }"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                            <i :class="alert.icon" :style="`color: ${alert.color}`" class="text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ alert.title }}</h3>
                                <p class="text-sm text-gray-600">{{ alert.message }}</p>
                            </div>
                        </div>
                        <Badge :value="alert.count" :style="`background-color: ${alert.color}`" />
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
                <i class="pi pi-check-circle text-4xl text-green-500 mb-3"></i>
                <p class="text-lg font-medium">All Good!</p>
                <p class="text-sm">No health alerts at this time</p>
            </div>
        </div>

        <!-- Student Demographics -->
        <Card>
            <template #title>
                <div class="flex items-center">
                    <i class="pi pi-users mr-2 text-blue-500"></i>
                    Student Demographics
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ totalStudents }}</div>
                        <div class="text-sm text-gray-600">Total Students</div>
                    </div>
                    <div class="text-center p-4 bg-pink-50 rounded-lg">
                        <div class="text-2xl font-bold text-pink-600">{{ femaleStudents }}</div>
                        <div class="text-sm text-gray-600">Female</div>
                    </div>
                    <div class="text-center p-4 bg-indigo-50 rounded-lg">
                        <div class="text-2xl font-bold text-indigo-600">{{ maleStudents }}</div>
                        <div class="text-sm text-gray-600">Male</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ healthCompletionRate }}%</div>
                        <div class="text-sm text-gray-600">Health Records</div>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Health Metrics Progress -->
        <Card v-if="healthMetrics.length > 0">
            <template #title>
                <div class="flex items-center">
                    <i class="pi pi-chart-line mr-2 text-green-500"></i>
                    Health Program Progress
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="metric in healthMetrics" :key="metric.label" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i :class="metric.icon" class="mr-2 text-lg"></i>
                                <span class="font-medium">{{ metric.label }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ metric.value }}/{{ metric.target }}</span>
                        </div>
                        <ProgressBar 
                            :value="(metric.value / metric.target) * 100" 
                            :class="`progress-${metric.color}`"
                            :showValue="false"
                        />
                        <div class="text-sm text-gray-600">
                            {{ Math.round((metric.value / metric.target) * 100) }}% Complete
                        </div>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Charts and Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Health Status Charts -->
            <div class="space-y-6">
                <!-- Deworming Status Chart -->
                <Card>
                    <template #title>Deworming Status</template>
                    <template #content>
                        <Chart type="doughnut" :data="dewormingChartData" :options="dewormingChartOptions" class="h-64" />
                        <div class="grid grid-cols-2 gap-4 mt-4 text-center">
                            <div class="p-3 bg-blue-50 rounded">
                                <div class="text-lg font-bold text-blue-600">{{ dewormingData.dewormed }}</div>
                                <div class="text-sm text-gray-600">Dewormed</div>
                            </div>
                            <div class="p-3 bg-red-50 rounded">
                                <div class="text-lg font-bold text-red-600">{{ dewormingData.notDewormed }}</div>
                                <div class="text-sm text-gray-600">Not Dewormed</div>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Iron Supplementation Chart -->
                <Card>
                    <template #title>Iron Supplementation</template>
                    <template #content>
                        <Chart type="doughnut" :data="ironSupplementChartData" :options="ironSupplementChartOptions" class="h-64" />
                        <div class="grid grid-cols-2 gap-4 mt-4 text-center">
                            <div class="p-3 bg-teal-50 rounded">
                                <div class="text-lg font-bold text-teal-600">{{ ironSupplementData.positive }}</div>
                                <div class="text-sm text-gray-600">Positive</div>
                            </div>
                            <div class="p-3 bg-orange-50 rounded">
                                <div class="text-lg font-bold text-orange-600">{{ ironSupplementData.negative }}</div>
                                <div class="text-sm text-gray-600">Negative</div>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Recent Activities Timeline -->
            <Card>
                <template #title>
                    <div class="flex items-center">
                        <i class="pi pi-clock mr-2 text-purple-500"></i>
                        Recent Activities
                    </div>
                </template>
                <template #content>
                    <Timeline :value="recentActivities" class="w-full">
                        <template #marker="slotProps">
                            <div 
                                class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm"
                                :style="`background-color: ${slotProps.item.color}`"
                            >
                                <i :class="slotProps.item.icon"></i>
                            </div>
                        </template>
                        <template #content="slotProps">
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">{{ slotProps.item.title }}</h4>
                                <p class="text-sm text-gray-600 mb-1">{{ slotProps.item.description }}</p>
                                <small class="text-xs text-gray-400">{{ formatTimeAgo(slotProps.item.date) }}</small>
                            </div>
                        </template>
                    </Timeline>
                </template>
            </Card>
        </div>

        <!-- Nutritional Status Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Card>
                <template #title>Nutritional Status (BMI)</template>
                <template #content>
                    <Chart type="bar" :data="nutritionalStatusBMIChartData" />
                </template>
            </Card>

            <Card>
                <template #title>Nutritional Status (Height)</template>
                <template #content>
                    <Chart type="bar" :data="nutritionalStatusHeightChartData" />
                </template>
            </Card>
        </div>
    </div>
</template>