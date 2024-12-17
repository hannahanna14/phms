<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import NaawanLogo from '../../assets/logo.png';

const form = useForm({
    email: '',
    password: ''
});

const showPassword = ref(false);

const submit = () => {
    form.post(route("login"), {
        onError: () => form.reset('password')
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg w-[400px]">
            <!-- School Logo -->
            <div class="flex justify-center mb-6">
                <img :src="NaawanLogo" 
                     alt="MSU Naawan Logo" 
                     class="w-32">
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Username Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input 
                        type="text"
                        v-model="form.email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Username"
                    >
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Password"
                        >
                        <button 
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500"
                        >
                            <i class="pi" :class="showPassword ? 'pi-eye-slash' : 'pi-eye'"></i>
                        </button>
                    </div>
                    <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-[#1C1B7E] text-white py-2 px-4 rounded-md hover:bg-[#15155F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    :disabled="form.processing"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>
.bg-gray-100 {
    background-color: #F3F4F6;
}
</style>
