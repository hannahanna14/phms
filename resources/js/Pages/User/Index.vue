<template>
    <MainLayout>
        <Head title="Manage Users" />
        <div class="main-view px-6 mx-4">
            <div class="flex justify-between items-center mb-4">
                <div class="search-box flex-1 mr-2">
                    <span class="p-input-icon-left w-full">
                        <i class="pi pi-search" />
                        <InputText 
                            v-model="searchQuery" 
                            placeholder="Search user..." 
                            class="w-full"
                        />
                    </span>
                </div>
                <Link 
                    :href="route('users.create')"
                    class="p-button p-button-primary"
                    v-tooltip.bottom="'Add User'"
                >
                    <i class="pi pi-plus"></i>
                </Link>
            </div>
            
            <div class="user-table-container flex justify-center">
                <div class="flex justify-between items-center mb-4 w-full">
                    <h2 class="text-xl font-bold">Users List</h2>
                </div>
                <table class="user-table w-full md:w-3/4 lg:w-1/2">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="user in filteredUsers" 
                            :key="user.id"
                            class="hover:bg-gray-50"
                        >
                            <td>{{ user.username }}</td>
                            <td>{{ user.full_name }}</td>
                            <td>
                                <span :class="getRoleBadgeClass(user.role)">
                                    {{ user.role }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import InputText from 'primevue/inputtext'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css'
// Import page-specific styles
import '../../../css/pages/User/Index.css'

const props = defineProps({
    users: {
        type: Array,
        required: true,
        default: () => []
    }
})

const searchQuery = ref('')

const filteredUsers = computed(() => {
    if (!searchQuery.value) return props.users
    
    const query = searchQuery.value.toLowerCase()
    return props.users.filter(user => 
        user.username.toLowerCase().includes(query) ||
        user.full_name.toLowerCase().includes(query) ||
        user.role.toLowerCase().includes(query)
    )
})

const getRoleBadgeClass = (role) => {
    const baseClasses = 'px-2 py-1 rounded-full text-xs font-semibold'
    switch (role) {
        case 'admin':
            return `${baseClasses} bg-red-100 text-red-800`
        case 'nurse':
            return `${baseClasses} bg-blue-100 text-blue-800`
        case 'teacher':
            return `${baseClasses} bg-green-100 text-green-800`
        default:
            return `${baseClasses} bg-gray-100 text-gray-800`
    }
}
</script>
