<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import NaawanLogo from '../../assets/logo.png';

const form = useForm({
    username: '',
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
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600">
        <!-- Backdrop blur card -->
        <div class="backdrop-blur-md bg-white/20 p-8 rounded-2xl shadow-2xl border border-white/30 w-[400px]">
            <!-- School Logo -->
            <div class="flex justify-center mb-6">
                <img :src="NaawanLogo" 
                     alt="MSU Naawan Logo" 
                     class="w-32 drop-shadow-lg">
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Username Field -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Username</label>
                    <input 
                        type="text"
                        v-model="form.username"
                        :class="[
                            'w-full px-4 py-3 rounded-xl bg-white/90 backdrop-blur-sm border-2 transition-all duration-200 placeholder-gray-500',
                            'focus:outline-none focus:ring-2 focus:ring-white/50 focus:bg-white',
                            form.errors.username ? 'border-red-400 bg-red-50/90' : 'border-white/30 hover:border-white/50'
                        ]"
                        placeholder="Enter your username"
                    >
                    <div v-if="form.errors.username" class="text-red-100 text-sm mt-2 bg-red-500/20 p-2 rounded-lg backdrop-blur-sm">
                        <i class="pi pi-exclamation-triangle mr-2"></i>
                        Oops! Please check your username and try again.
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Password</label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            :class="[
                                'w-full px-4 py-3 pr-12 rounded-xl bg-white/90 backdrop-blur-sm border-2 transition-all duration-200 placeholder-gray-500',
                                'focus:outline-none focus:ring-2 focus:ring-white/50 focus:bg-white',
                                form.errors.password ? 'border-red-400 bg-red-50/90' : 'border-white/30 hover:border-white/50'
                            ]"
                            placeholder="Enter your password"
                        >
                        <button 
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-600 hover:text-gray-800 transition-colors"
                        >
                            <i class="pi" :class="showPassword ? 'pi-eye-slash' : 'pi-eye'"></i>
                        </button>
                    </div>
                    <div v-if="form.errors.password" class="text-red-100 text-sm mt-2 bg-red-500/20 p-2 rounded-lg backdrop-blur-sm">
                        <i class="pi pi-exclamation-triangle mr-2"></i>
                        Hmm, that doesn't look right. Please try again.
                    </div>
                </div>

                <!-- General Error Message -->
                <div v-if="form.errors.username || form.errors.password" class="text-red-100 text-sm bg-red-500/20 p-3 rounded-lg backdrop-blur-sm border border-red-400/30">
                    <i class="pi pi-times-circle mr-2"></i>
                    Unable to sign you in. Please double-check your credentials and try again.
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-xl font-medium shadow-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-white/50 transform hover:scale-[1.02] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="flex items-center justify-center">
                        <i class="pi pi-spinner pi-spin mr-2"></i>
                        Logging in...
                    </span>
                    <span v-else>Log In</span>
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Custom backdrop blur support for older browsers */
.backdrop-blur-md {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

/* Smooth animations */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced shadow for glass effect */
.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
}
</style>
