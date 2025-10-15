<template>
    <Head title="| School Settings" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-cog mr-2 text-blue-600"></i>
                    School Settings
                </h1>
            </div>

            <!-- Settings Form -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="bg-blue-600 text-white p-4 rounded-t-lg">
                    <h2 class="text-lg font-medium">School Information</h2>
                    <p class="text-blue-100 text-sm">Configure your school's basic information and branding</p>
                </div>

                <form @submit.prevent="updateSettings" class="p-6 space-y-6">
                    <!-- School Information Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Name</label>
                                <InputText 
                                    v-model="form.school_name" 
                                    placeholder="Enter school name"
                                    class="w-full"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Address</label>
                                <Textarea 
                                    v-model="form.school_address" 
                                    placeholder="Enter complete school address"
                                    rows="3"
                                    class="w-full"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <InputText 
                                    v-model="form.school_phone" 
                                    placeholder="Enter phone number"
                                    class="w-full"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <InputText 
                                    v-model="form.school_email" 
                                    type="email"
                                    placeholder="Enter email address"
                                    class="w-full"
                                />
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Personnel & Branding</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Principal Name</label>
                                <InputText 
                                    v-model="form.principal_name" 
                                    placeholder="Enter principal's name"
                                    class="w-full"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Logo</label>
                                <div class="space-y-3">
                                    <div v-if="currentLogo" class="flex items-center gap-3">
                                        <img :src="currentLogo" alt="Current Logo" class="w-16 h-16 object-contain border rounded" />
                                        <span class="text-sm text-gray-600">Current logo</span>
                                    </div>
                                    <input 
                                        type="file" 
                                        ref="logoInput"
                                        @change="handleLogoUpload"
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    />
                                    <p class="text-xs text-gray-500">Upload PNG, JPG, or SVG. Recommended size: 200x200px</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 pt-6 border-t">
                        <Button 
                            type="button"
                            label="Reset to Defaults" 
                            icon="pi pi-refresh"
                            outlined 
                            severity="secondary"
                            @click="resetToDefaults"
                        />
                        <Button 
                            type="submit"
                            label="Save Settings" 
                            icon="pi pi-save"
                            :loading="loading"
                            class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                        />
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'

const props = defineProps({
    settings: Object
})

const loading = ref(false)
const logoInput = ref(null)
const currentLogo = ref(props.settings?.school_logo_url || null)

// Initialize form with current settings or defaults
const form = useForm({
    school_name: props.settings?.school_name || '',
    school_address: props.settings?.school_address || '',
    school_phone: props.settings?.school_phone || '',
    school_email: props.settings?.school_email || '',
    principal_name: props.settings?.principal_name || '',
    school_logo: null // Will be set when file is uploaded
})

const handleLogoUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.school_logo = file
        
        // Create preview URL
        const reader = new FileReader()
        reader.onload = (e) => {
            currentLogo.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const updateSettings = () => {
    loading.value = true
    
    // Use POST route for file uploads
    if (form.school_logo) {
        // Has file upload, use POST route
        form.post(route('settings.store'), {
            forceFormData: true,
            onSuccess: () => {
                loading.value = false
            },
            onError: () => {
                loading.value = false
            }
        })
    } else {
        // No file upload, use PUT route
        form.put(route('settings.update'), {
            onSuccess: () => {
                loading.value = false
            },
            onError: () => {
                loading.value = false
            }
        })
    }
}

const resetToDefaults = () => {
    form.reset()
    currentLogo.value = null
    if (logoInput.value) {
        logoInput.value.value = ''
    }
}
</script>

<style scoped>
/* Additional styling if needed */
</style>
