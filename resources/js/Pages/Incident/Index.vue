<template>
    <Head title="Incident" />
    <div class="health-examination">
        <div class="main-view">
            <div class="search-container mb-4">
                <span class="p-input-icon-left w-full">
                    <i class="pi pi-search" />
                    <InputText 
                        v-model="searchQuery" 
                        placeholder="Search student..." 
                        class="search-input"
                    />
                </span>
            </div>
            
            <div class="student-table-container">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Student List</h2>
                </div>
                <table class="student-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="student in filteredStudents" 
                            :key="student.id"
                            class="hover:bg-gray-50"
                        >
                            <td>{{ student.full_name }}</td>
                            <td>{{ student.age }}</td>
                            <td>{{ student.sex }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'

const props = defineProps({
    students: {
        type: Array,
        default: () => []
    }
})

const searchQuery = ref('')

const filteredStudents = computed(() => {
    if (!searchQuery.value) return props.students
    
    return props.students.filter(student => 
        student.full_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})
</script>

<style scoped>
.health-examination {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

.search-container {
    margin-bottom: 20px;
}

.search-input {
    width: 100%;
    padding: 8px 8px 8px 35px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background-color: white;
}

.student-table-container {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.student-table {
    width: 100%;
    border-collapse: collapse;
}

.student-table th,
.student-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.student-table th {
    font-weight: 600;
    color: #374151;
}

.student-table tr:hover {
    background-color: #f9fafb;
}
</style>
