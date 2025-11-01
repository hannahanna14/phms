<template>
    <Head title="| Messages" />
    <div class="messages-page">
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Header -->
            <div class="border-b border-gray-200 p-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
                    <Button
                        @click="$inertia.visit(route('messages.create'))"
                        icon="pi pi-plus"
                        label="New Message"
                        class="p-button-primary"
                    />
                </div>

                <!-- Tabs -->
                <div class="mt-4">
                    <TabView v-model:activeIndex="activeTabIndex" @tab-change="onTabChange">
                        <TabPanel>
                            <template #header>
                                <div class="flex items-center space-x-2">
                                    <i class="pi pi-inbox"></i>
                                    <span>Inbox</span>
                                    <Badge v-if="unreadCount > 0" :value="unreadCount" severity="danger" />
                                </div>
                            </template>
                        </TabPanel>
                        <TabPanel>
                            <template #header>
                                <div class="flex items-center space-x-2">
                                    <i class="pi pi-send"></i>
                                    <span>Sent</span>
                                </div>
                            </template>
                        </TabPanel>
                        <TabPanel>
                            <template #header>
                                <div class="flex items-center space-x-2">
                                    <i class="pi pi-eye-slash"></i>
                                    <span>Unread</span>
                                    <Badge v-if="unreadCount > 0" :value="unreadCount" severity="warning" />
                                </div>
                            </template>
                        </TabPanel>
                        <TabPanel>
                            <template #header>
                                <div class="flex items-center space-x-2">
                                    <i class="pi pi-exclamation-triangle"></i>
                                    <span>Urgent</span>
                                </div>
                            </template>
                        </TabPanel>
                    </TabView>
                </div>
            </div>

            <!-- Messages List -->
            <div class="p-6">
                <div v-if="messages.data.length === 0" class="text-center py-12">
                    <i class="pi pi-inbox text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No messages</h3>
                    <p class="text-gray-500">{{ getEmptyMessage() }}</p>
                </div>

                <div v-else class="space-y-2">
                    <div
                        v-for="message in messages.data"
                        :key="message.id"
                        @click="viewMessage(message.id)"
                        class="message-item p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                        :class="{
                            'border-blue-200 bg-blue-50': !message.is_read && currentTab === 'inbox',
                            'border-gray-200': message.is_read || currentTab !== 'inbox'
                        }"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3 mb-2">
                                    <!-- Avatar -->
                                    <Avatar
                                        :label="getInitials(currentTab === 'sent' ? message.receiver?.full_name : message.sender?.full_name)"
                                        class="bg-blue-500 text-white"
                                        shape="circle"
                                        size="small"
                                    />
                                    
                                    <!-- Sender/Receiver Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-900 truncate">
                                                {{ currentTab === 'sent' ? message.receiver?.full_name || 'Broadcast' : message.sender?.full_name }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ currentTab === 'sent' ? message.receiver?.role : message.sender?.role }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 truncate">{{ message.subject }}</div>
                                    </div>
                                </div>

                                <!-- Message Preview -->
                                <div class="text-sm text-gray-600 line-clamp-2 mb-2">
                                    {{ message.content }}
                                </div>

                                <!-- Related Student -->
                                <div v-if="message.related_student" class="flex items-center space-x-2 text-xs text-blue-600 mb-2">
                                    <i class="pi pi-user"></i>
                                    <span>{{ message.related_student.full_name }} ({{ message.related_student.grade_level }})</span>
                                    <span v-if="message.related_module" class="text-gray-500">• {{ formatModule(message.related_module) }}</span>
                                </div>
                            </div>

                            <!-- Message Meta -->
                            <div class="flex flex-col items-end space-y-2 ml-4">
                                <div class="text-xs text-gray-500">{{ formatTime(message.created_at) }}</div>
                                
                                <!-- Priority Badge -->
                                <Tag
                                    v-if="message.priority !== 'normal'"
                                    :value="message.priority"
                                    :severity="getPrioritySeverity(message.priority)"
                                    class="text-xs"
                                />

                                <!-- Type Badge -->
                                <Tag
                                    v-if="message.type !== 'personal'"
                                    :value="message.type"
                                    :severity="getTypeSeverity(message.type)"
                                    class="text-xs"
                                />

                                <!-- Unread Indicator -->
                                <div
                                    v-if="!message.is_read && currentTab === 'inbox'"
                                    class="w-2 h-2 bg-blue-500 rounded-full"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="messages.data.length > 0" class="mt-6 flex justify-center">
                    <Paginator
                        :rows="messages.per_page"
                        :totalRecords="messages.total"
                        :first="(messages.current_page - 1) * messages.per_page"
                        @page="onPageChange"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Button from 'primevue/button'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css'
// Import page-specific styles
import '../../../css/pages/Messages/Index.css'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import Badge from 'primevue/badge'
import Avatar from 'primevue/avatar'
import Tag from 'primevue/tag'
import Paginator from 'primevue/paginator'

const props = defineProps({
    messages: Object,
    currentTab: String,
    unreadCount: Number
})

// Tab management
const tabs = ['inbox', 'sent', 'unread', 'urgent']
const activeTabIndex = ref(tabs.indexOf(props.currentTab))

// Helper functions
const getInitials = (name) => {
    if (!name) return '?'
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const formatTime = (datetime) => {
    const date = new Date(datetime)
    const now = new Date()
    const diffInHours = (now - date) / (1000 * 60 * 60)
    
    if (diffInHours < 24) {
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
    } else if (diffInHours < 168) { // 7 days
        return date.toLocaleDateString('en-US', { weekday: 'short' })
    } else {
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    }
}

const formatModule = (module) => {
    const modules = {
        'health_exam': 'Health Exam',
        'treatment': 'Treatment',
        'oral_health': 'Oral Health',
        'incident': 'Incident'
    }
    return modules[module] || module
}

const getPrioritySeverity = (priority) => {
    const severities = {
        'low': 'secondary',
        'normal': 'info',
        'high': 'warning',
        'urgent': 'danger'
    }
    return severities[priority] || 'info'
}

const getTypeSeverity = (type) => {
    const severities = {
        'personal': 'info',
        'broadcast': 'success',
        'system': 'secondary',
        'urgent': 'danger'
    }
    return severities[type] || 'info'
}

const getEmptyMessage = () => {
    const messages = {
        'inbox': 'You have no messages in your inbox.',
        'sent': 'You haven\'t sent any messages yet.',
        'unread': 'You have no unread messages.',
        'urgent': 'You have no urgent messages.'
    }
    return messages[props.currentTab] || 'No messages found.'
}

// Actions
const onTabChange = (event) => {
    const tab = tabs[event.index]
    router.visit(route('messages.index', { tab }), {
        preserveState: true,
        preserveScroll: true
    })
}

const viewMessage = (messageId) => {
    router.visit(route('messages.show', messageId))
}

const onPageChange = (event) => {
    const page = (event.first / event.rows) + 1
    router.visit(route('messages.index', { 
        tab: props.currentTab, 
        page 
    }), {
        preserveState: true,
        preserveScroll: true
    })
}
</script>
