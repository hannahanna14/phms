<template>
    <Head title="| Error Logs & Activity Monitor" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-list mr-2 text-red-600"></i>
                    Error Logs & Activity Monitor
                </h1>
            </div>

            <!-- Log Type Tabs -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6">
                        <button
                            @click="switchLogType('activity')"
                            :class="[
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                logType === 'activity' 
                                    ? 'border-blue-500 text-blue-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            <i class="pi pi-users mr-2"></i>
                            Activity Logs
                        </button>
                        <button
                            @click="switchLogType('laravel')"
                            :class="[
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                logType === 'laravel' 
                                    ? 'border-red-500 text-red-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            <i class="pi pi-exclamation-triangle mr-2"></i>
                            System Errors
                        </button>
                    </nav>
                </div>

                <!-- Search and Filters -->
                <div class="p-4 bg-gray-50 border-b">
                    <div class="flex gap-3 items-end flex-wrap">
                        <!-- Search -->
                        <div style="width: 300px;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <InputText 
                                v-model="filters.search" 
                                placeholder="Search logs..."
                                class="w-full"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <Button 
                            label="Search"
                            icon="pi pi-search" 
                            @click="applyFilters"
                            class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                        />
                        <div class="flex-grow"></div>

                        <!-- Date From -->
                        <div style="width: 180px;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <DatePicker 
                                v-model="filters.date_from" 
                                dateFormat="yy-mm-dd"
                                placeholder="Select start date"
                                class="w-full"
                            />
                        </div>

                        <!-- Date To -->
                        <div style="width: 180px;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <DatePicker 
                                v-model="filters.date_to" 
                                dateFormat="yy-mm-dd"
                                placeholder="Select end date"
                                class="w-full"
                            />
                        </div>

                        <!-- Log Level (for Laravel logs) -->
                        <div v-if="logType === 'laravel'" style="width: 180px;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Log Level</label>
                            <Select 
                                v-model="filters.level"
                                :options="logLevels"
                                placeholder="All Levels"
                                class="w-full"
                            />
                        </div>

                        <!-- Filter Actions -->
                        <Button 
                            label="Apply" 
                            icon="pi pi-filter"
                            @click="applyFilters"
                            severity="success"
                        />
                        <Button 
                            label="Clear" 
                            icon="pi pi-times"
                            @click="clearFilters"
                            outlined
                            severity="secondary"
                        />
                    </div>
                </div>

                <!-- Actions Bar -->
                <div class="p-4 bg-white border-b flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            Total: {{ logs.total || logs.length || 0 }} entries
                        </span>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-600">Per page:</label>
                            <Select 
                                v-model="perPage"
                                :options="[25, 50, 100, 200]"
                                @change="applyFilters"
                                class="w-20"
                            />
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <Button 
                            label="Download" 
                            icon="pi pi-download"
                            @click="downloadLogs"
                            :loading="downloading"
                            outlined
                            severity="secondary"
                            size="small"
                        />
                        <Button 
                            label="Clear Logs" 
                            icon="pi pi-trash"
                            @click="confirmClearLogs"
                            :loading="clearing"
                            severity="danger"
                            outlined
                            size="small"
                        />
                        <Button 
                            label="Refresh" 
                            icon="pi pi-refresh"
                            @click="refreshLogs"
                            :loading="loading"
                            size="small"
                        />
                    </div>
                </div>
            </div>

            <!-- Activity Logs Table -->
            <div v-if="logType === 'activity'" class="bg-white rounded-lg shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Timestamp
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    IP Address
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="log in (logs.data || logs)" :key="log.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDateTime(log.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-xs font-medium text-white">
                                                    {{ getUserInitials(log.causer) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ log.causer?.full_name || 'System' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ log.causer?.role || 'system' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ log.properties?.ip_address || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="flex items-center space-x-2">
                                        <span :class="getEventBadgeClass(log.event)" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ log.event }}
                                        </span>
                                        <span class="truncate" :title="log.description">
                                            {{ log.description }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <Button 
                                        icon="pi pi-eye" 
                                        @click="viewLogDetails(log)"
                                        text
                                        rounded
                                        severity="secondary"
                                        size="small"
                                        v-tooltip.left="'View Details'"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="logs.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                    <Paginator 
                        :rows="parseInt(logs.per_page)" 
                        :totalRecords="parseInt(logs.total)" 
                        :first="(parseInt(logs.current_page) - 1) * parseInt(logs.per_page)"
                        @page="onPageChange"
                    />
                </div>
            </div>

            <!-- Laravel Error Logs Table -->
            <div v-else-if="logType === 'laravel'" class="bg-white rounded-lg shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Timestamp
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Message
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(log, index) in (logs.data || logs)" :key="index" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ log.timestamp }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="flex items-center space-x-2">
                                        <span :class="getLogLevelClass(log.level)" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ log.level }}
                                        </span>
                                        <span class="truncate" :title="log.message">
                                            {{ log.message }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <Button 
                                        icon="pi pi-eye"
                                        @click="viewLogDetails(log)"
                                        text
                                        rounded
                                        severity="secondary"
                                        size="small"
                                        v-tooltip.left="'View Details'"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- Empty State -->
            <div v-if="!loading && (!(logs.data || logs) || (logs.data || logs).length === 0)" class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="pi pi-info-circle text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No logs found</h3>
                <p class="text-gray-500">No {{ logType }} logs match your current filters.</p>
            </div>
        </div>

        <!-- Log Details Dialog -->
        <Dialog v-model:visible="showLogDetails" modal header="Log Details" class="w-full max-w-4xl">
            <div v-if="selectedLog" class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ selectedLog.full_content }}</pre>
                </div>
            </div>
        </Dialog>

        <!-- Clear Logs Confirmation -->
        <Dialog v-model:visible="showClearConfirm" modal header="Clear Logs" class="w-96">
            <div class="space-y-4">
                <p class="text-gray-700">
                    Are you sure you want to clear all {{ logType }} logs? This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-2">
                    <Button label="Cancel" @click="showClearConfirm = false" outlined />
                    <Button label="Clear Logs" @click="clearLogs" severity="danger" :loading="clearing" />
                </div>
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'
import Dialog from 'primevue/dialog'
import Paginator from 'primevue/paginator'

const props = defineProps({
    logs: Object,
    logType: String,
    filters: Object
})

// Reactive data
const loading = ref(false)
const downloading = ref(false)
const clearing = ref(false)
const showLogDetails = ref(false)
const showClearConfirm = ref(false)
const selectedLog = ref(null)
const perPage = ref(50)

// Initialize logs data
const logs = ref(props.logs || { data: [], total: 0, per_page: 50, current_page: 1, last_page: 1 })

// Filters
const filters = ref({
    search: props.filters?.search || '',
    date_from: props.filters?.date_from ? new Date(props.filters.date_from) : null,
    date_to: props.filters?.date_to ? new Date(props.filters.date_to) : null,
    level: props.filters?.level || null
})

// Log type
const logType = ref(props.logType || 'activity')

// Log levels for Laravel logs
const logLevels = ['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']

// Methods
const switchLogType = (type) => {
    logType.value = type
    applyFilters()
}

const applyFilters = () => {
    loading.value = true
    
    const params = {
        type: logType.value,
        per_page: perPage.value,
        search: filters.value.search || undefined,
        date_from: filters.value.date_from ? filters.value.date_from.toISOString().split('T')[0] : undefined,
        date_to: filters.value.date_to ? filters.value.date_to.toISOString().split('T')[0] : undefined,
        level: filters.value.level || undefined
    }
    
    // Remove undefined values
    Object.keys(params).forEach(key => params[key] === undefined && delete params[key])
    
    router.get('/error-logs', params, {
        preserveState: true,
        onSuccess: (page) => {
            logs.value = page.props.logs
        },
        onFinish: () => loading.value = false
    })
}

const clearFilters = () => {
    filters.value = {
        search: '',
        date_from: null,
        date_to: null,
        level: null
    }
    applyFilters()
}

const refreshLogs = () => {
    applyFilters()
}

const downloadLogs = async () => {
    downloading.value = true
    try {
        const params = new URLSearchParams({
            type: logType.value,
            search: filters.value.search || '',
            date_from: filters.value.date_from ? filters.value.date_from.toISOString().split('T')[0] : '',
            date_to: filters.value.date_to ? filters.value.date_to.toISOString().split('T')[0] : '',
            level: filters.value.level || ''
        })
        
        // Remove empty parameters
        Array.from(params.keys()).forEach(key => {
            if (!params.get(key)) params.delete(key)
        })
        
        window.location.href = `/error-logs/download?${params.toString()}`
    } catch (error) {
        console.error('Download failed:', error)
    } finally {
        setTimeout(() => {
            downloading.value = false
        }, 1000)
    }
}

const confirmClearLogs = () => {
    showClearConfirm.value = true
}

const clearLogs = async () => {
    clearing.value = true
    try {
        await fetch(`/error-logs/clear?type=${logType.value}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        showClearConfirm.value = false
        refreshLogs() // Refresh logs
    } catch (error) {
        console.error('Clear failed:', error)
    } finally {
        clearing.value = false
    }
}

const viewLogDetails = (log) => {
    selectedLog.value = log
    showLogDetails.value = true
}

const onPageChange = (event) => {
    const params = new URLSearchParams(window.location.search)
    params.set('page', event.page + 1)
    router.get(`/error-logs?${params.toString()}`, {}, { preserveState: true })
}

// Utility functions
const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString()
}

const getUserInitials = (user) => {
    if (!user || !user.full_name) return 'S'
    return user.full_name.split(' ').map(n => n[0]).join('').toUpperCase()
}

const getEventBadgeClass = (event) => {
    const classes = {
        'created': 'bg-green-100 text-green-800',
        'updated': 'bg-blue-100 text-blue-800',
        'deleted': 'bg-red-100 text-red-800',
        'login': 'bg-green-100 text-green-800',
        'logout': 'bg-gray-100 text-gray-800'
    }
    return classes[event] || 'bg-gray-100 text-gray-800'
}

const getLogLevelClass = (level) => {
    const classes = {
        'ERROR': 'bg-red-100 text-red-800',
        'CRITICAL': 'bg-red-100 text-red-800',
        'WARNING': 'bg-yellow-100 text-yellow-800',
        'INFO': 'bg-blue-100 text-blue-800',
        'DEBUG': 'bg-gray-100 text-gray-800'
    }
    return classes[level] || 'bg-gray-100 text-gray-800'
}

const getAuthEventClass = (description) => {
    if (description.includes('logged in')) {
        return 'bg-green-100 text-green-800'
    } else if (description.includes('logged out')) {
        return 'bg-blue-100 text-blue-800'
    } else if (description.includes('login attempt')) {
        return 'bg-red-100 text-red-800'
    }
    return 'bg-gray-100 text-gray-800'
}

const getSubjectName = (subjectType) => {
    if (!subjectType) return 'N/A'
    return subjectType.split('\\').pop()
}
</script>

<style scoped>
/* Additional styling if needed */
</style>
