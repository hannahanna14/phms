<template>
    <Head title="| Incident Management" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-exclamation-triangle mr-2 text-red-600"></i>
                    Incident Management
                </h1>
                <Button 
                    label="Back to Dashboard" 
                    icon="pi pi-arrow-left" 
                    outlined 
                    severity="secondary" 
                    @click="$inertia.visit('/pupil-health')"
                />
            </div>

            <!-- Search -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex gap-2">
                    <span class="p-input-icon-left flex-1">
                        <i class="pi pi-search" />
                        <InputText 
                            v-model="searchQuery" 
                            placeholder="Search by student name..." 
                            class="w-full"
                        />
                    </span>
                    <Button 
                        label="Search"
                        icon="pi pi-search" 
                        severity="secondary"
                    />
                </div>
            </div>

            <!-- Students with Incidents -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Students with Incidents</h2>
                </div>
                
                <!-- Skeleton Loader -->
                <SkeletonLoader 
                    v-if="isLoading" 
                    type="list" 
                    :items="8"
                    class="p-6"
                />

                <div v-else-if="filteredStudents.length === 0" class="p-8 text-center text-gray-500">
                    No students found.
                </div>
                
                <div v-else class="divide-y divide-gray-200">
                    <div 
                        v-for="student in filteredStudents" 
                        :key="student.id"
                        class="p-4 hover:bg-gray-50 cursor-pointer"
                        @click="viewStudentIncidents(student)"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ student.full_name }}</h3>
                                <p class="text-sm text-gray-500">Grade {{ student.grade_level }} • Age {{ student.age }} • {{ student.sex }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <Tag 
                                    :value="`${student.incidents_count || 0} Incidents`" 
                                    severity="info"
                                />
                                <i class="pi pi-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import SkeletonLoader from '@/Components/SkeletonLoader.vue'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css'
// Import page-specific styles
import '../../../css/pages/Incident/Index.css'

const props = defineProps({
    students: {
        type: Array,
        default: () => []
    },
    userRole: {
        type: String,
        default: 'admin'
    }
})

const searchQuery = ref('')
const isLoading = ref(true)

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 500);
});

const filteredStudents = computed(() => {
    if (!searchQuery.value) return props.students
    
    return props.students.filter(student => 
        student.full_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

const viewStudentIncidents = (student) => {
    window.location.href = `/pupil-health/incident/${student.id}?grade=${student.grade_level}`
}
</script>
