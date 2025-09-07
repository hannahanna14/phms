<template>
    <MainLayout>
        <Head title="Create User" />
        <div class="create-user p-4">
            <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold mb-6">Create New User</h1>
                
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Username -->
                    <div class="field">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <InputText 
                            id="username"
                            v-model="form.username" 
                            class="w-full"
                            :class="{ 'p-invalid': errors.username }"
                        />
                        <small class="text-red-500" v-if="errors.username">{{ errors.username }}</small>
                    </div>

                    <!-- Full Name -->
                    <div class="field">
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <InputText 
                            id="full_name"
                            v-model="form.full_name" 
                            class="w-full"
                            :class="{ 'p-invalid': errors.full_name }"
                        />
                        <small class="text-red-500" v-if="errors.full_name">{{ errors.full_name }}</small>
                    </div>

                    <!-- Password -->
                    <div class="field">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <Password 
                            id="password"
                            v-model="form.password" 
                            class="w-full"
                            :class="{ 'p-invalid': errors.password }"
                            :feedback="false"
                            toggleMask
                        />
                        <small class="text-red-500" v-if="errors.password">{{ errors.password }}</small>
                    </div>

                    <!-- Role -->
                    <div class="field">
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <Select
                            id="role"
                            v-model="form.role"
                            :options="roles"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select a role"
                            class="w-full"
                            :class="{ 'p-invalid': errors.role }"
                        />
                        <small class="text-red-500" v-if="errors.role">{{ errors.role }}</small>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <Button 
                            type="submit" 
                            label="Create User" 
                            class="p-button-primary"
                            :loading="form.processing"
                        />
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Select from 'primevue/select'
import Button from 'primevue/button'

onMounted(() => {
    console.log('Create User component mounted')
})

const roles = [
    { label: 'Admin', value: 'admin' },
    { label: 'Nurse', value: 'nurse' },
    { label: 'Teacher', value: 'teacher' }
]

const form = useForm({
    username: '',
    full_name: '',
    password: '',
    role: ''
})

const submit = () => {
    console.log('Submitting form:', form.data())
    form.post(route('users.store'))
}
</script>

<style scoped>
.field {
    margin-bottom: 1.5rem;
}

:deep(.p-password-input) {
    width: 100%;
}

:deep(.p-dropdown) {
    width: 100%;
}
</style>
