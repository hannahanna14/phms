<template>
    <Head title="Oral Health Examination" />
    <div class="oral-health-examination">
        <!-- Student Information Box -->
        <div class="white-container mb-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">{{ student.full_name }}</h1>
                    <div class="text-gray-600">
                        Age: {{ student.age }} | Sex: {{ student.sex }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Oral Health Examination Records -->
        <div class="white-container">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Oral Health Examination Records</h2>
                <button 
                    @click="$inertia.visit(route('oral-health-examination.create', student.id))" 
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"
                >
                    Add New Record
                </button>
            </div>

            <div v-if="!examinations || examinations.length === 0" class="text-center py-8 text-gray-500">
                No oral health examination records found.
            </div>
            <div v-else>
                <div 
                    v-for="exam in examinations" 
                    :key="exam.id" 
                    class="flex justify-between items-center py-4 border-b"
                >
                    <div class="flex-grow">
                        <div class="font-semibold">Oral Health Examination</div>
                        <div class="text-sm text-gray-500">
                            {{ formatDate(exam.created_at) }}
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button 
                            @click="$inertia.visit(route('oral-health-examination.edit', exam.id))"
                            class="text-blue-500 hover:text-blue-700 mr-2"
                        >
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
    student: {
        type: Object,
        required: true
    },
    examinations: {
        type: Array,
        default: () => []
    }
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}
</script>

<style scoped>
.oral-health-examination {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

.white-container {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.text-gray-600 {
    color: #6b7280;
}

.border-b {
    border-bottom: 1px solid #e5e7eb;
}
</style>
