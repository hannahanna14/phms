<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'primevue/chart'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

// Reactive variables for student statistics
const totalStudents = ref(0)
const femaleStudents = ref(0)
const maleStudents = ref(0)

// Reactive variables for deworming and iron supplement data
const dewormingData = ref({ dewormed: 0, notDewormed: 0 })
const ironSupplementData = ref({ positive: 0, negative: 0 })

// Chart data for deworming
const dewormingChartData = ref({
    labels: ['Dewormed', 'Not Dewormed'],
    datasets: [{
        data: [0, 0],
        backgroundColor: ['#36A2EB', '#FF6384'],
    }]
})

const dewormingChartOptions = ref({
    plugins: {
        title: {
            display: true,
            text: 'Deworming Status'
        }
    }
})

// Chart data for iron supplement
const ironSupplementChartData = ref({
    labels: ['Positive', 'Negative'],
    datasets: [{
        data: [0, 0],
        backgroundColor: ['#4BC0C0', '#FF9F40'],
    }]
})

const ironSupplementChartOptions = ref({
    plugins: {
        title: {
            display: true,
            text: 'Iron Supplementation'
        }
    }
})

// Nutritional Status BMI Chart Data
const nutritionalStatusBMIChartData = ref({
    labels: [],
    datasets: [{
        label: 'Nutritional Status (BMI)',
        data: [],
        backgroundColor: '#42A5F5'
    }]
})

// Nutritional Status Height Chart Data
const nutritionalStatusHeightChartData = ref({
    labels: [],
    datasets: [{
        label: 'Nutritional Status (Height)',
        data: [],
        backgroundColor: '#66BB6A'
    }]
})

// Update data when component mounts
onMounted(() => {
    const dashboardData = page.props.dashboardData || {}

    // Update student statistics
    totalStudents.value = dashboardData.totalStudents || 0
    femaleStudents.value = dashboardData.femaleStudents || 0
    maleStudents.value = dashboardData.maleStudents || 0

    // Update deworming data
    dewormingData.value = dashboardData.deworming || { dewormed: 0, notDewormed: 0 }
    dewormingChartData.value.datasets[0].data = [
        dewormingData.value.dewormed, 
        dewormingData.value.notDewormed
    ]

    // Update iron supplementation data
    ironSupplementData.value = dashboardData.ironSupplement || { positive: 0, negative: 0 }
    ironSupplementChartData.value.datasets[0].data = [
        ironSupplementData.value.positive, 
        ironSupplementData.value.negative
    ]

    // Update Nutritional Status BMI Chart
    if (dashboardData.nutritionalStatusBMI) {
        nutritionalStatusBMIChartData.value.labels = dashboardData.nutritionalStatusBMI.map(item => item.nutritional_status_bmi)
        nutritionalStatusBMIChartData.value.datasets[0].data = dashboardData.nutritionalStatusBMI.map(item => item.count)
    }

    // Update Nutritional Status Height Chart
    if (dashboardData.nutritionalStatusHeight) {
        nutritionalStatusHeightChartData.value.labels = dashboardData.nutritionalStatusHeight.map(item => item.nutritional_status_height)
        nutritionalStatusHeightChartData.value.datasets[0].data = dashboardData.nutritionalStatusHeight.map(item => item.count)
    }
})

const schoolYear = ref('2023-2024')
const schoolYears = ref(['2023-2024', '2022-2023', '2021-2022'])
</script>

<template>
    <div>
        <!-- Student Statistics Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-2">Total Number of Pupils</h2>
                <p class="text-2xl font-bold text-blue-600">{{ totalStudents }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-2">Female Pupils</h2>
                <p class="text-2xl font-bold text-pink-600">{{ femaleStudents }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-2">Male Pupils</h2>
                <p class="text-2xl font-bold text-blue-600">{{ maleStudents }}</p>
            </div>
        </div>

        <!-- Data Visualization Section -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <!-- Deworming Status Chart -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Deworming Status</h2>
                <Chart type="doughnut" :data="dewormingChartData" :options="dewormingChartOptions" />
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <p>Dewormed Pupils: <span class="font-bold">{{ dewormingData.dewormed }}</span></p>
                    </div>
                    <div>
                        <p>Not Dewormed Pupils: <span class="font-bold">{{ dewormingData.notDewormed }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Iron Supplementation Chart -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Iron Supplementation</h2>
                <Chart type="doughnut" :data="ironSupplementChartData" :options="ironSupplementChartOptions" />
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <p>Positive Pupils: <span class="font-bold">{{ ironSupplementData.positive }}</span></p>
                    </div>
                    <div>
                        <p>Negative Pupils: <span class="font-bold">{{ ironSupplementData.negative }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nutritional Status Charts -->
        <div class="grid grid-cols-2 gap-6">
            <!-- Nutritional Status (BMI) Chart -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Nutritional Status (BMI)</h2>
                <Chart 
                    type="bar" 
                    :data="nutritionalStatusBMIChartData" 
                />
            </div>

            <!-- Nutritional Status (Height) Chart -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Nutritional Status (Height)</h2>
                <Chart 
                    type="bar" 
                    :data="nutritionalStatusHeightChartData" 
                />
            </div>
        </div>
    </div>
</template>