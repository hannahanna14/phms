<template>
    <div class="print-container">
        <div class="print-header">
            <h1>{{ reportTitle }}</h1>
            <div class="report-info">
                <p><strong>Grade Level:</strong> {{ gradeLevel }}</p>
                <p><strong>School Year:</strong> {{ schoolYear }}</p>
                <p><strong>Generated:</strong> {{ new Date().toLocaleDateString() }}</p>
                <p><strong>Total Students:</strong> {{ reportData.length }}</p>
            </div>
        </div>

        <div class="print-content">
            <table class="report-table">
                <thead>
                    <tr>
                        <th v-if="selectedFields.includes('name')">Name</th>
                        <th v-if="selectedFields.includes('lrn')">LRN</th>
                        <th v-if="selectedFields.includes('grade_level')">Grade</th>
                        <th v-if="selectedFields.includes('section')">Section</th>
                        <th v-if="selectedFields.includes('gender')">Gender</th>
                        <th v-if="selectedFields.includes('age')">Age</th>
                        <th v-if="selectedFields.includes('birthdate')">Birthdate</th>
                        <th v-if="includeHealthExam">Health Exam</th>
                        <th v-if="includeHealthTreatment">Treatments</th>
                        <th v-if="includeOralHealth">Oral Health</th>
                        <th v-if="includeIncidents">Incidents</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="student in reportData" :key="student.name">
                        <td v-if="selectedFields.includes('name')">{{ student.name }}</td>
                        <td v-if="selectedFields.includes('lrn')">{{ student.lrn }}</td>
                        <td v-if="selectedFields.includes('grade_level')">{{ student.grade_level }}</td>
                        <td v-if="selectedFields.includes('section')">{{ student.section || 'N/A' }}</td>
                        <td v-if="selectedFields.includes('gender')">{{ student.gender }}</td>
                        <td v-if="selectedFields.includes('age')">{{ student.age }}</td>
                        <td v-if="selectedFields.includes('birthdate')">{{ formatDate(student.birthdate) }}</td>
                        <td v-if="includeHealthExam">{{ student.health_exam ? 'Yes' : 'No' }}</td>
                        <td v-if="includeHealthTreatment">{{ student.health_treatments?.length || 0 }}</td>
                        <td v-if="includeOralHealth">{{ student.oral_treatments?.length || 0 }}</td>
                        <td v-if="includeIncidents">{{ student.incidents?.length || 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    reportData: {
        type: Array,
        required: true
    },
    gradeLevel: {
        type: String,
        required: true
    },
    schoolYear: {
        type: String,
        required: true
    },
    selectedFields: {
        type: Array,
        required: true
    },
    includeHealthExam: {
        type: Boolean,
        default: false
    },
    includeHealthTreatment: {
        type: Boolean,
        default: false
    },
    includeOralHealth: {
        type: Boolean,
        default: false
    },
    includeIncidents: {
        type: Boolean,
        default: false
    }
});

const reportTitle = computed(() => {
    return `Health Report - ${props.gradeLevel} (${props.schoolYear})`;
});

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString();
};
</script>

<style scoped>
@media print {
    .print-container {
        width: 100%;
        margin: 0;
        padding: 20px;
    }
}

.print-container {
    font-family: Arial, sans-serif;
    max-width: 100%;
    margin: 0 auto;
    padding: 20px;
}

.print-header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
    padding-bottom: 20px;
}

.print-header h1 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #333;
}

.report-info {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 15px;
}

.report-info p {
    margin: 0;
    font-size: 14px;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.report-table th,
.report-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    font-size: 12px;
}

.report-table th {
    background-color: #f5f5f5;
    font-weight: bold;
    text-align: center;
}

.report-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.report-table tbody tr:hover {
    background-color: #f0f0f0;
}
</style>
