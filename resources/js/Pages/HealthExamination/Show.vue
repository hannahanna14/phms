<template>
    <Head title="Student Information" />
    <div class="health-examination">
        <!-- Student Information Box -->
        <div class="white-container mb-4">
            <h2 class="text-lg font-semibold mb-4">Student Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Full Name</label>
                    <div>{{ student.full_name }}</div>
                </div>
                
                <div class="info-item">
                    <label>Sex</label>
                    <div>{{ student.sex }}</div>
                </div>
                
                <div class="info-item">
                    <label>Age</label>
                    <div>{{ student.age }}</div>
                </div>
            </div>
        </div>

        <!-- Records Box -->
        <div class="white-container">
            <div class="flex justify-between items-center mb-4">
                <label>Name</label>
                <label>Date</label>
                <Button icon="pi pi-plus" class="p-button-text" @click="$inertia.visit(route('health-examination.create', student.id))" />
            </div>

            <div v-if="!examinations || examinations.length === 0" class="text-center py-8 text-gray-500">
                No records found
            </div>
            <div v-else>
                <div v-for="exam in examinations" :key="exam.id" class="flex justify-between items-center py-2 border-b">
                    <span>Health Examination</span>
                    <span>{{ formatDate(exam.examination_date) }}</span>
                    <div class="flex gap-2">
                        <Button icon="pi pi-pencil" class="p-button-text p-button-sm" />
                        <Button icon="pi pi-trash" class="p-button-text p-button-sm p-button-danger" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import Button from 'primevue/button'

defineProps({
    student: {
        type: Object,
        required: true
    },
    examinations: {
        type: Array,
        required: true,
        default: () => []
    }
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}
</script>

<style scoped>
.health-examination {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

.white-container {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    padding: 16px;
    background-color: white;
    border-radius: 8px;
    border: 1px solid #eee;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-item label {
    font-size: 0.875rem;
    color: #666;
}

.info-item div {
    font-size: 1rem;
    color: #333;
}

label {
    font-size: 0.875rem;
    color: #666;
}

.p-button-sm {
    padding: 0.25rem !important;
}
</style>