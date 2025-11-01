<template>
    <Head title="New Message" />
    <div class="create-message-page">
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">New Message</h1>
                <Button
                    @click="$inertia.visit(route('messages.index'))"
                    icon="pi pi-arrow-left"
                    label="Back to Messages"
                    class="p-button-text"
                />
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Recipient -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Recipient <span class="text-red-500">*</span>
                        </label>
                        <Dropdown
                            v-model="form.receiver_id"
                            :options="users"
                            optionLabel="full_name"
                            optionValue="id"
                            placeholder="Select recipient"
                            class="w-full"
                            :class="{ 'p-invalid': errors.receiver_id }"
                        >
                            <template #option="slotProps">
                                <div class="flex items-center space-x-2">
                                    <Avatar
                                        :label="getInitials(slotProps.option.full_name)"
                                        class="bg-blue-500 text-white"
                                        shape="circle"
                                        size="small"
                                    />
                                    <div>
                                        <div class="font-medium">{{ slotProps.option.full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ slotProps.option.role }}</div>
                                    </div>
                                </div>
                            </template>
                        </Dropdown>
                        <small v-if="errors.receiver_id" class="text-red-500">{{ errors.receiver_id }}</small>
                        <div class="mt-2">
                            <Checkbox v-model="isBroadcast" inputId="broadcast" binary />
                            <label for="broadcast" class="ml-2 text-sm text-gray-600">Send as broadcast message (to all users)</label>
                        </div>
                    </div>

                    <!-- Message Type & Priority -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <Dropdown
                                v-model="form.type"
                                :options="typeOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Select type"
                                class="w-full"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <Dropdown
                                v-model="form.priority"
                                :options="priorityOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Select priority"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <InputText
                        v-model="form.subject"
                        placeholder="Enter message subject"
                        class="w-full"
                        :class="{ 'p-invalid': errors.subject }"
                    />
                    <small v-if="errors.subject" class="text-red-500">{{ errors.subject }}</small>
                </div>

                <!-- Related Student (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Related Student (Optional)</label>
                    <Dropdown
                        v-model="form.related_student_id"
                        :options="students"
                        optionLabel="display_name"
                        optionValue="id"
                        placeholder="Select student (optional)"
                        class="w-full"
                        showClear
                    >
                        <template #option="slotProps">
                            <div>
                                <div class="font-medium">{{ slotProps.option.full_name }}</div>
                                <div class="text-sm text-gray-500">{{ slotProps.option.grade_level }} - {{ slotProps.option.section }}</div>
                            </div>
                        </template>
                    </Dropdown>
                </div>

                <!-- Module Context (Optional) -->
                <div v-if="form.related_student_id" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Related Module</label>
                        <Dropdown
                            v-model="form.related_module"
                            :options="moduleOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select module"
                            class="w-full"
                            showClear
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Record ID</label>
                        <InputNumber
                            v-model="form.related_record_id"
                            placeholder="Enter record ID"
                            class="w-full"
                        />
                    </div>
                </div>

                <!-- Message Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <Textarea
                        v-model="form.content"
                        placeholder="Enter your message..."
                        rows="8"
                        class="w-full"
                        :class="{ 'p-invalid': errors.content }"
                    />
                    <small v-if="errors.content" class="text-red-500">{{ errors.content }}</small>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <Button
                        @click="router.visit('/messages')"
                        label="Cancel"
                        class="p-button-text"
                        type="button"
                    />
                    <Button
                        type="submit"
                        label="Send Message"
                        icon="pi pi-send"
                        class="p-button-primary"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Dropdown from 'primevue/dropdown'
import Checkbox from 'primevue/checkbox'
import Avatar from 'primevue/avatar'
import InputNumber from 'primevue/inputnumber'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css'
// Import page-specific styles
import '../../../css/pages/Messages/Create.css'

const props = defineProps({
    users: Array,
    students: Array,
    preData: Object
})

// Form validation errors
const errors = ref({})
const isBroadcast = ref(false)

// Form data
const form = useForm({
    receiver_id: props.preData?.receiver_id || null,
    subject: props.preData?.subject || '',
    content: '',
    type: 'personal',
    priority: 'normal',
    related_student_id: props.preData?.related_student_id || null,
    related_module: props.preData?.related_module || null,
    related_record_id: props.preData?.related_record_id || null,
    attachments: []
})

// Watch broadcast checkbox
watch(isBroadcast, (newValue) => {
    if (newValue) {
        form.receiver_id = null
        form.type = 'broadcast'
    } else {
        form.type = 'personal'
    }
})

// Options
const typeOptions = [
    { label: 'Personal', value: 'personal' },
    { label: 'Broadcast', value: 'broadcast' },
    { label: 'System', value: 'system' },
    { label: 'Urgent', value: 'urgent' }
]

const priorityOptions = [
    { label: 'Low', value: 'low' },
    { label: 'Normal', value: 'normal' },
    { label: 'High', value: 'high' },
    { label: 'Urgent', value: 'urgent' }
]

const moduleOptions = [
    { label: 'Health Examination', value: 'health_exam' },
    { label: 'Treatment', value: 'treatment' },
    { label: 'Oral Health', value: 'oral_health' },
    { label: 'Incident Report', value: 'incident' }
]

// Computed properties
const studentsWithDisplayName = computed(() => {
    return props.students.map(student => ({
        ...student,
        display_name: `${student.full_name} (${student.grade_level} - ${student.section})`
    }))
})

// Helper functions
const getInitials = (name) => {
    if (!name) return '?'
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

// Form submission
const submit = () => {
    // Client-side validation
    errors.value = {}
    
    if (!form.subject.trim()) {
        errors.value.subject = 'Subject is required'
    }
    
    if (!form.content.trim()) {
        errors.value.content = 'Message content is required'
    }
    
    if (!isBroadcast.value && !form.receiver_id) {
        errors.value.receiver_id = 'Please select a recipient or choose broadcast'
    }
    
    if (Object.keys(errors.value).length > 0) {
        return
    }
    
    form.post(route('messages.store'), {
        onError: (formErrors) => {
            errors.value = formErrors
        }
    })
}
</script>
