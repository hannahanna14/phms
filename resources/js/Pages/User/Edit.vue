<template>
    <Head title="Edit User" />
    <div class="health-examination">
        <div class="main-view">
            <div class="card">
                <h2 class="text-2xl font-bold mb-4">Edit User</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="form-group">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <InputText 
                            id="username"
                            v-model="form.username"
                            class="mt-1 block w-full"
                            :class="{ 'p-invalid': errors.username }"
                        />
                        <small class="text-red-500" v-if="errors.username">{{ errors.username }}</small>
                    </div>

                    <div class="form-group">
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <InputText 
                            id="full_name"
                            v-model="form.full_name"
                            class="mt-1 block w-full"
                            :class="{ 'p-invalid': errors.full_name }"
                        />
                        <small class="text-red-500" v-if="errors.full_name">{{ errors.full_name }}</small>
                    </div>

                    <div class="form-group">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <InputText 
                            id="email"
                            type="email"
                            v-model="form.email"
                            class="mt-1 block w-full"
                            :class="{ 'p-invalid': errors.email }"
                        />
                        <small class="text-red-500" v-if="errors.email">{{ errors.email }}</small>
                    </div>

                    <div class="form-group">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password (leave empty to keep current)</label>
                        <Password 
                            id="password"
                            v-model="form.password"
                            class="mt-1 block w-full"
                            :class="{ 'p-invalid': errors.password }"
                            toggleMask
                        />
                        <small class="text-red-500" v-if="errors.password">{{ errors.password }}</small>
                    </div>

                    <div class="form-group">
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <Select
                            id="role"
                            v-model="form.role"
                            :options="roles"
                            class="mt-1 block w-full"
                            :class="{ 'p-invalid': errors.role }"
                        />
                        <small class="text-red-500" v-if="errors.role">{{ errors.role }}</small>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <Button
                            type="button"
                            label="Cancel"
                            class="p-button-secondary"
                            @click="$inertia.visit(route('users.index'))"
                        />
                        <Button
                            type="submit"
                            label="Update User"
                            class="p-button-primary"
                            :loading="form.processing"
                        />
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Select from 'primevue/select'

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const roles = ['admin', 'nurse', 'teacher']

const form = useForm({
    username: props.user.username,
    full_name: props.user.full_name,
    email: props.user.email,
    password: '',
    role: props.user.role
})

const submit = () => {
    form.put(route('users.update', props.user.id))
}
</script>

<style scoped>
.health-examination {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

.card {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 1rem;
}

:deep(.p-password-input) {
    width: 100%;
}

:deep(.p-dropdown) {
    width: 100%;
}
</style>
