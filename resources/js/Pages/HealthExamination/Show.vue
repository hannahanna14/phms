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
                <h2 class="text-lg font-semibold">Health Examination Records</h2>
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
                        <Button 
                            icon="pi pi-pencil" 
                            class="p-button-text p-button-sm"
                            @click="$inertia.visit(route('health-examination.edit', exam.id))" 
                        />
                        <Button 
                            icon="pi pi-trash" 
                            class="p-button-text p-button-sm p-button-danger"
                            @click="confirmDelete(exam)" 
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Dialog v-model:visible="deleteDialog" modal header="Confirm Deletion" :style="{ width: '350px' }">
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem; color: var(--red-500)" />
            <span>Are you sure you want to delete this health examination record?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteDialog = false" />
            <Button label="Yes" icon="pi pi-check" class="p-button-danger" @click="deleteExamination" :loading="deleting" />
        </template>
    </Dialog>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'

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

const deleteDialog = ref(false)
const deleting = ref(false)
const examToDelete = ref(null)

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const confirmDelete = (exam) => {
    examToDelete.value = exam
    deleteDialog.value = true
}

const deleteExamination = () => {
    deleting.value = true
    router.delete(route('health-examination.destroy', examToDelete.value.id), {
        onSuccess: () => {
            deleteDialog.value = false
            deleting.value = false
            examToDelete.value = null
        },
        onError: () => {
            deleting.value = false
        }
    })
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

.confirmation-content {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 1rem 0;
}
</style>