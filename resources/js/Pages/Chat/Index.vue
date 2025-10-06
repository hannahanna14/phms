<template>
    <Head title="Chat" />
    <div class="chat-container">
        <div class="chat-layout">
            <!-- Sidebar - Conversations List -->
            <div class="chat-sidebar">
                <div class="sidebar-header">
                    <h2 class="text-lg font-semibold text-gray-900">Chats</h2>
                    <Button
                        @click="showNewChatDialog = true"
                        icon="pi pi-plus"
                        class="p-button-text p-button-sm"
                        v-tooltip="'New Chat'"
                    />
                </div>

                <!-- Search -->
                <div class="p-3 border-b">
                    <InputText
                        v-model="searchQuery"
                        placeholder="Search conversations..."
                        class="w-full"
                        icon="pi pi-search"
                    />
                </div>

                <!-- Conversations List -->
                <div class="conversations-list">
                    <div
                        v-for="conversation in filteredConversations"
                        :key="conversation.id"
                        @click="selectConversation(conversation.id)"
                        class="conversation-item"
                        :class="{ 'active': selectedConversation?.id === conversation.id }"
                    >
                        <div class="flex items-center space-x-3 p-3">
                            <div class="relative">
                                <Avatar
                                    :label="getConversationInitials(conversation)"
                                    class="bg-blue-500 text-white"
                                    shape="circle"
                                />
                                <div
                                    v-if="conversation.unread_count > 0"
                                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                                >
                                    {{ conversation.unread_count > 9 ? '9+' : conversation.unread_count }}
                                </div>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-medium text-gray-900 truncate">{{ conversation.title }}</h3>
                                    <span class="text-xs text-gray-500">
                                        {{ conversation.latest_message?.formatted_time }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 truncate">
                                    <span v-if="conversation.latest_message?.sender_name" class="font-medium">
                                        {{ conversation.latest_message.sender_name }}:
                                    </span>
                                    {{ conversation.latest_message?.content || 'No messages yet' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="conversations.length === 0" class="p-6 text-center text-gray-500">
                        <i class="pi pi-comments text-3xl mb-3 block"></i>
                        <p>No conversations yet</p>
                        <p class="text-sm">Start a new chat to begin messaging</p>
                    </div>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="chat-main">
                <div v-if="!selectedConversation" class="chat-welcome">
                    <div class="text-center">
                        <i class="pi pi-comments text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">Welcome to PHMS Chat</h3>
                        <p class="text-gray-600 mb-6">Select a conversation to start messaging or create a new chat</p>
                        <Button
                            @click="showNewChatDialog = true"
                            icon="pi pi-plus"
                            label="Start New Chat"
                            class="p-button-primary"
                        />
                    </div>
                </div>

                <div v-else class="chat-active">
                    <!-- Chat Header -->
                    <div class="chat-header">
                        <div class="flex items-center space-x-3">
                            <Avatar
                                :label="getConversationInitials(selectedConversation)"
                                class="bg-blue-500 text-white"
                                shape="circle"
                            />
                            <div>
                                <h3 class="font-medium text-gray-900">{{ selectedConversation.title }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ selectedConversation.participants?.length }} participant{{ selectedConversation.participants?.length > 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Button
                                icon="pi pi-refresh"
                                class="p-button-text p-button-sm"
                                @click="refreshMessages"
                                v-tooltip="'Refresh'"
                            />
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div ref="messagesContainer" class="messages-container">
                        <div v-if="messages.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-comment text-3xl mb-3 block"></i>
                            <p>No messages yet</p>
                            <p class="text-sm">Send the first message to start the conversation</p>
                        </div>

                        <div v-else class="messages-list">
                            <div
                                v-for="message in messages"
                                :key="message.id"
                                class="message-wrapper"
                                :class="{ 'own-message': message.is_own }"
                            >
                                <div class="message-bubble" :class="{ 'own': message.is_own }">
                                    <div class="message-content">{{ message.content }}</div>
                                    <div class="message-time">{{ message.formatted_time }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="message-input-area">
                        <form @submit.prevent="sendMessage" class="flex items-end space-x-3">
                            <div class="flex-1">
                                <Textarea
                                    v-model="newMessage"
                                    placeholder="Type a message..."
                                    rows="1"
                                    autoResize
                                    class="w-full"
                                    @keydown.enter.exact.prevent="sendMessage"
                                    @keydown.enter.shift.exact="newMessage += '\n'"
                                />
                            </div>
                            <Button
                                type="submit"
                                icon="pi pi-send"
                                class="p-button-primary"
                                :disabled="!newMessage.trim() || sending"
                                :loading="sending"
                            />
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Chat Dialog -->
        <Dialog
            v-model:visible="showNewChatDialog"
            modal
            header="Start New Chat"
            class="w-96"
        >
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                    <Dropdown
                        v-model="selectedUserId"
                        :options="users"
                        optionLabel="full_name"
                        optionValue="id"
                        placeholder="Choose a user to chat with"
                        class="w-full"
                    >
                        <template #option="slotProps">
                            <div class="flex items-center space-x-2">
                                <Avatar
                                    :label="getInitials(slotProps.option.full_name)"
                                    class="bg-gray-500 text-white"
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
                </div>
            </div>
            
            <template #footer>
                <div class="flex justify-end space-x-2">
                    <Button
                        label="Cancel"
                        class="p-button-text"
                        @click="showNewChatDialog = false"
                    />
                    <Button
                        label="Start Chat"
                        class="p-button-primary"
                        @click="startNewChat"
                        :disabled="!selectedUserId"
                    />
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Avatar from 'primevue/avatar'
import Dialog from 'primevue/dialog'
import Dropdown from 'primevue/dropdown'

const props = defineProps({
    conversations: Array,
    selectedConversation: Object,
    messages: Array,
    users: Array
})

// Reactive data
const searchQuery = ref('')
const newMessage = ref('')
const sending = ref(false)
const showNewChatDialog = ref(false)
const selectedUserId = ref(null)
const messagesContainer = ref(null)

// Computed properties
const filteredConversations = computed(() => {
    if (!searchQuery.value) return props.conversations
    
    return props.conversations.filter(conversation =>
        conversation.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

// Helper functions
const getInitials = (name) => {
    if (!name) return '?'
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const getConversationInitials = (conversation) => {
    return getInitials(conversation.title)
}

// Actions
const selectConversation = (conversationId) => {
    router.visit(route('chat.index', { conversation: conversationId }), {
        preserveState: true,
        preserveScroll: false
    })
}

const sendMessage = async () => {
    if (!newMessage.value.trim() || sending.value) return
    
    sending.value = true
    
    try {
        const response = await fetch(route('chat.send'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                conversation_id: props.selectedConversation.id,
                content: newMessage.value.trim()
            })
        })
        
        if (response.ok) {
            const data = await response.json()
            // Add message to local state
            props.messages.push(data.message)
            newMessage.value = ''
            
            // Scroll to bottom
            await nextTick()
            scrollToBottom()
            
            // Refresh conversation list to update last message
            setTimeout(() => {
                router.reload({ only: ['conversations'] })
            }, 100)
        }
    } catch (error) {
        console.error('Error sending message:', error)
    } finally {
        sending.value = false
    }
}

const startNewChat = () => {
    if (!selectedUserId.value) return
    
    router.post(route('chat.start'), {
        user_id: selectedUserId.value
    })
    
    showNewChatDialog.value = false
    selectedUserId.value = null
}

const refreshMessages = () => {
    router.reload({ only: ['messages'] })
}

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
}

// Lifecycle
onMounted(() => {
    scrollToBottom()
})
</script>

<style scoped>
.chat-container {
    height: calc(100vh - 80px);
    background: #f5f7f9;
}

.chat-layout {
    display: flex;
    height: 100%;
    background: white;
    border-radius: 8px;
    margin: 20px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.chat-sidebar {
    width: 320px;
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: between;
    align-items: center;
}

.conversations-list {
    flex: 1;
    overflow-y: auto;
}

.conversation-item {
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s;
}

.conversation-item:hover {
    background-color: #f9fafb;
}

.conversation-item.active {
    background-color: #eff6ff;
    border-right: 3px solid #3b82f6;
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.chat-welcome {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.chat-active {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.chat-header {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    background: #f9fafb;
}

.messages-list {
    display: flex;
    flex-direction: column;
    space-y: 0.5rem;
}

.message-wrapper {
    display: flex;
    margin-bottom: 0.5rem;
}

.message-wrapper.own-message {
    justify-content: flex-end;
}

.message-bubble {
    max-width: 70%;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    background: white;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.message-bubble.own {
    background: #3b82f6;
    color: white;
}

.message-content {
    word-wrap: break-word;
    white-space: pre-wrap;
}

.message-time {
    font-size: 0.75rem;
    opacity: 0.7;
    margin-top: 0.25rem;
}

.message-input-area {
    padding: 1rem;
    border-top: 1px solid #e5e7eb;
    background: white;
}

:deep(.p-textarea) {
    resize: none;
    min-height: 40px;
    max-height: 120px;
}
</style>
