<script setup>
// Import component styles
import '../../css/components/TextInput.css'

const model = defineModel({
    type: null,
    required: true
})

defineProps({
    name: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: "text"
    },
    message: String
})
</script>

<template>
    <div class="text-input-container">
        <label class="text-input-label">{{ name }}</label>
        <input 
            :type="type" 
            v-model="model" 
            :class="[
                'text-input-field',
                { 'text-input-error': message }
            ]"
        >
        <small class="text-input-error-message" v-if="message">{{ message }}</small>
    </div>
</template>
