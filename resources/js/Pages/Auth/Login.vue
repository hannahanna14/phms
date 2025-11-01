<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import NaawanLogo from '../../assets/logo.png';
// Import component styles
import '../../../css/pages/auth/Login.css';

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
    <div class="login-container">
        <!-- Backdrop blur card -->
        <div class="login-card">
            <!-- School Logo -->
            <div class="login-logo-container">
                <img :src="NaawanLogo" 
                     alt="MSU Naawan Logo" 
                     class="login-logo">
            </div>

            <form @submit.prevent="submit" class="login-form">
                <!-- Username Field -->
                <div class="form-field">
                    <label class="form-label">Username</label>
                    <input 
                        type="text"
                        v-model="form.username"
                        :class="[
                            'form-input',
                            form.errors.username ? 'form-input-error' : 'form-input-default'
                        ]"
                        placeholder="Enter your username"
                    >
                    <div v-if="form.errors.username" class="error-message">
                        <i class="pi pi-exclamation-triangle mr-2"></i>
                        Oops! Please check your username and try again.
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-field">
                    <label class="form-label">Password</label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            :class="[
                                'form-input-password',
                                form.errors.password ? 'form-input-error' : 'form-input-default'
                            ]"
                            placeholder="Enter your password"
                        >
                        <button 
                            type="button"
                            @click="showPassword = !showPassword"
                            class="password-toggle-btn"
                        >
                            <i class="pi" :class="showPassword ? 'pi-eye-slash' : 'pi-eye'"></i>
                        </button>
                    </div>
                    <div v-if="form.errors.password" class="error-message">
                        <i class="pi pi-exclamation-triangle mr-2"></i>
                        Hmm, that doesn't look right. Please try again.
                    </div>
                </div>

                <!-- General Error Message -->
                <div v-if="form.errors.username || form.errors.password" class="error-message-general">
                    <i class="pi pi-times-circle mr-2"></i>
                    Unable to sign you in. Please double-check your credentials and try again.
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="login-button"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="login-button-loading">
                        <i class="pi pi-spinner pi-spin mr-2"></i>
                        Logging in...
                    </span>
                    <span v-else>Log In</span>
                </button>
            </form>
        </div>
    </div>
</template>
