<template>
    <Head title="Message Details" />
    <div class="show-message-page">
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Message Details</h1>
                <div class="flex space-x-2">
                    <Button
                        @click="replyToMessage"
                        icon="pi pi-reply"
                        label="Reply"
                        class="p-button-primary"
                        v-if="message.sender_id !== $page.props.auth.user.id"
                    />
                    <Button
                        @click="deleteMessage"
                        icon="pi pi-trash"
                        label="Delete"
                        class="p-button-danger p-button-outlined"
                    />
                    <Button
                        @click="$inertia.visit(route('messages.index'))"
                        icon="pi pi-arrow-left"
                        label="Back"
                        class="p-button-outlined"
                        style="border: 1px solid #64748b; color: #64748b; font-weight: 500; transition: all 0.2s ease;"
                    />
                </div>
            </div>

            <!-- Message Header -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4">
                        <Avatar
                            :label="getInitials(message.sender?.full_name)"
                            class="bg-blue-500 text-white"
                            shape="circle"
                            size="large"
                        />
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-semibold text-gray-900">{{ message.sender?.full_name }}</span>
                                <span class="text-sm text-gray-500">({{ message.sender?.role }})</span>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                To: {{ message.receiver?.full_name || 'All Users (Broadcast)' }}
                                <span v-if="message.receiver?.role" class="text-gray-500">({{ message.receiver.role }})</span>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ formatDateTime(message.created_at) }}
                                <span v-if="message.is_read && message.read_at" class="ml-2">
                                    • Read {{ formatDateTime(message.read_at) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end space-y-2">
                        <Tag
                            v-if="message.priority !== 'normal'"
                            :value="message.priority"
                            :severity="getPrioritySeverity(message.priority)"
                        />
                        <Tag
                            v-if="message.type !== 'personal'"
                            :value="message.type"
                            :severity="getTypeSeverity(message.type)"
                        />
                    </div>
                </div>
            </div>

            <!-- Subject -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ message.subject }}</h2>
            </div>

            <!-- Related Student Info -->
            <div v-if="message.related_student" class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-900 mb-2">Related Student Information</h3>
                <div class="flex items-center space-x-3">
                    <Avatar
                        icon="pi pi-user"
                        class="bg-blue-500 text-white"
                        shape="circle"
                    />
                    <div>
                        <div class="font-medium text-blue-900">{{ message.related_student.full_name }}</div>
                        <div class="text-sm text-blue-700">
                            {{ message.related_student.grade_level }} - {{ message.related_student.section }}
                        </div>
                        <div v-if="message.related_module" class="text-sm text-blue-600 mt-1">
                            Module: {{ formatModule(message.related_module) }}
                            <span v-if="message.related_record_id" class="ml-2">
                                (Record #{{ message.related_record_id }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Content -->
            <div class="bg-white border rounded-lg p-6 mb-6">
                <div class="prose max-w-none">
                    <div class="whitespace-pre-wrap text-gray-800 leading-relaxed">{{ message.content }}</div>
                </div>
            </div>

            <!-- Attachments (if any) -->
            <div v-if="message.attachments && message.attachments.length > 0" class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-900 mb-3">Attachments</h3>
                <div class="space-y-2">
                    <div
                        v-for="(attachment, index) in message.attachments"
                        :key="index"
                        class="flex items-center space-x-3 p-2 bg-white rounded border"
                    >
                        <i class="pi pi-file text-gray-500"></i>
                        <span class="text-sm text-gray-700">{{ attachment.name }}</span>
                        <Button
                            icon="pi pi-download"
                            class="p-button-text p-button-sm"
                            @click="downloadAttachment(attachment)"
                        />
                    </div>
                </div>
            </div>

            <!-- Message Actions -->
            <div class="flex justify-between items-center pt-6 border-t">
                <div class="text-sm text-gray-500">
                    Message ID: {{ message.id }}
                </div>
                <div class="flex space-x-2">
                    <Button
                        v-if="message.sender_id !== $page.props.auth.user.id"
                        @click="replyToMessage"
                        icon="pi pi-reply"
                        label="Reply"
                        class="p-button-primary"
                    />
                    <Button
                        @click="forwardMessage"
                        icon="pi pi-share-alt"
                        label="Forward"
                        class="p-button-secondary"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router, usePage } from '@inertiajs/vue3'
import Button from 'primevue/button'
import Avatar from 'primevue/avatar'
import Tag from 'primevue/tag'

const props = defineProps({
    message: Object
})

const page = usePage()

// Helper functions
const getInitials = (name) => {
    if (!name) return '?'
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatModule = (module) => {
    const modules = {
        'health_exam': 'Health Examination',
        'treatment': 'Treatment',
        'oral_health': 'Oral Health',
        'incident': 'Incident Report'
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

// Actions
const replyToMessage = () => {
    const replySubject = props.message.subject.startsWith('Re: ') 
        ? props.message.subject 
        : `Re: ${props.message.subject}`
    
    router.visit(route('messages.create', {
        to: props.message.sender_id,
        subject: replySubject,
        student_id: props.message.related_student_id,
        module: props.message.related_module,
        record_id: props.message.related_record_id
    }))
}

const forwardMessage = () => {
    const forwardSubject = props.message.subject.startsWith('Fwd: ') 
        ? props.message.subject 
        : `Fwd: ${props.message.subject}`
    
    router.visit(route('messages.create', {
        subject: forwardSubject
    }))
}

const deleteMessage = () => {
    if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
        router.delete(route('messages.destroy', props.message.id))
    }
}

const downloadAttachment = (attachment) => {
    // Implementation for downloading attachments
    console.log('Download attachment:', attachment)
}
</script>

<style scoped>
.show-message-page {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

.prose {
    max-width: none;
}
</style>
