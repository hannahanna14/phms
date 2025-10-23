<script setup>
import { ref, onMounted, computed } from 'vue'
import Chart from 'primevue/chart'
import Button from 'primevue/button'
import Card from 'primevue/card'
import ProgressBar from 'primevue/progressbar'
import Badge from 'primevue/badge'
import Timeline from 'primevue/timeline'
import Dropdown from 'primevue/dropdown'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()

// User role
const userRole = ref(page.props.userRole || 'teacher')

// Grade Level and Section filters
const selectedGradeLevel = ref(page.props.selectedGradeLevel || 'All')
const selectedSection = ref(page.props.selectedSection || 'All')
const availableGradeLevels = ref(page.props.gradeLevels || ['All', 'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'])
const availableSections = ref(page.props.sections || ['All', 'A', 'B', 'C'])

// Reactive variables for student statistics
const totalStudents = ref(0)
const femaleStudents = ref(0)
const maleStudents = ref(0)

// Health Examination Completion by Grade
const healthCompletionData = ref({
    labels: ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'],
    datasets: [{
        label: 'Completion Rate (%)',
        data: [0, 0, 0, 0, 0, 0, 0],
        borderColor: '#3B82F6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true
    }]
})

// Health Issues Distribution
const healthIssuesData = ref({
    labels: ['Dental Issues', 'BMI Concerns', 'Vision Problems', 'Incidents', 'Oral Health', 'Nutritional Issues'],
    datasets: [{
        label: 'Number of Students',
        data: [0, 0, 0, 0, 0, 0],
        backgroundColor: [
            '#EF4444', // Red for dental
            '#F59E0B', // Amber for BMI
            '#8B5CF6', // Purple for vision
            '#DC2626', // Dark red for incidents
            '#10B981', // Green for oral health
            '#3B82F6'  // Blue for nutritional
        ],
        borderWidth: 1
    }]
})

// Monthly Health Activity Trends
const monthlyActivityData = ref({
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [
        {
            label: 'Health Examinations',
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            backgroundColor: 'rgba(59, 130, 246, 0.6)',
            borderColor: '#3B82F6'
        },
        {
            label: 'Treatments',
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            backgroundColor: 'rgba(16, 185, 129, 0.6)',
            borderColor: '#10B981'
        },
        {
            label: 'Incidents',
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            backgroundColor: 'rgba(239, 68, 68, 0.6)',
            borderColor: '#EF4444'
        }
    ]
})

// Interactive Graph Selection
const selectedGraphType = ref('deworming')
const availableGraphs = ref([
    { value: 'deworming', label: 'Deworming Status', icon: 'pi pi-shield' },
    { value: 'bmi', label: 'BMI Distribution', icon: 'pi pi-chart-bar' },
    { value: 'nutritionalHeight', label: 'Height for Age', icon: 'pi pi-chart-line' },
    { value: 'ironSupplement', label: 'Iron Supplementation', icon: 'pi pi-heart' },
    { value: 'visionScreening', label: 'Vision Screening', icon: 'pi pi-eye' },
    { value: 'auditoryScreening', label: 'Auditory Screening', icon: 'pi pi-volume-up' },
    { value: 'skinExamination', label: 'Skin', icon: 'pi pi-user' },
    { value: 'scalpExamination', label: 'Scalp', icon: 'pi pi-user-plus' },
    { value: 'eyesExamination', label: 'Eyes', icon: 'pi pi-eye' },
    { value: 'earsExamination', label: 'Ears', icon: 'pi pi-volume-up' },
    { value: 'noseExamination', label: 'Nose', icon: 'pi pi-search-plus' },
    { value: 'mouthExamination', label: 'Mouth', icon: 'pi pi-comments' },
    { value: 'throatExamination', label: 'Throat', icon: 'pi pi-volume-down' },
    { value: 'neckExamination', label: 'Neck', icon: 'pi pi-stop-circle' },
    { value: 'lungsExamination', label: 'Lungs', icon: 'pi pi-refresh' },
    { value: 'heartExamination', label: 'Heart', icon: 'pi pi-heart-fill' },
    { value: 'abdomen', label: 'Abdomen', icon: 'pi pi-circle' },
    { value: 'deformities', label: 'Deformities', icon: 'pi pi-exclamation-triangle' }
])

// Dynamic chart data based on selection
const dynamicChartData = ref({
    labels: ['Dewormed', 'Not Dewormed'],
    datasets: [{
        data: [0, 0],
        backgroundColor: ['#36A2EB', '#FF6384'],
    }]
})

const dynamicChartOptions = ref({
    responsive: true,
    plugins: {
        title: {
            display: true,
            text: 'Deworming Status'
        },
        legend: {
            position: 'bottom'
        }
    }
})

// Chart type (doughnut, bar, etc.)
const currentChartType = ref('doughnut')

// Summary stats for selected metric
const summaryStats = ref([
    { label: 'Dewormed', value: 0, color: 'blue' },
    { label: 'Not Dewormed', value: 0, color: 'red' }
])

// Helper functions for dynamic chart colors
const generateColors = (count) => {
    const colors = ['#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#06B6D4', '#F97316', '#84CC16', '#EC4899']
    return Array.from({ length: count }, (_, i) => colors[i % colors.length])
}

const getColorForValue = (value) => {
    const normalValues = ['Normal', 'normal', 'Clear', 'clear', 'Healthy', 'healthy', 'Soft', 'soft', 'None', 'none']
    return normalValues.includes(value) ? 'green' : 'orange'
}

// Function to update chart based on selection
const updateChart = (graphType) => {
    selectedGraphType.value = graphType
    
    switch (graphType) {
        case 'deworming':
            dynamicChartData.value = {
                labels: ['Dewormed', 'Not Dewormed'],
                datasets: [{
                    data: [dashboardData.value.deworming?.dewormed || 0, dashboardData.value.deworming?.notDewormed || 0],
                    backgroundColor: ['#10B981', '#EF4444']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Deworming Status'
            currentChartType.value = 'doughnut'
            summaryStats.value = [
                { label: 'Dewormed', value: dashboardData.value.deworming?.dewormed || 0, color: 'green' },
                { label: 'Not Dewormed', value: dashboardData.value.deworming?.notDewormed || 0, color: 'red' }
            ]
            break
            
        case 'bmi':
            const bmiData = dashboardData.value.nutritionalStatusBMI || []
            
            // Always show all 5 BMI categories for better visualization
            const totalBMI = (dashboardData.value.nutritionalStatusBMI?.reduce((sum, item) => sum + item.count, 0)) || 0
            const normalBMI = dashboardData.value.nutritionalStatusBMI?.find(item => 
                item.nutritional_status_bmi === 'Normal' || 
                (item.nutritional_status_bmi && item.nutritional_status_bmi.includes('Normal'))
            )?.count || 0
            const underweightBMI = (dashboardData.value.nutritionalStatusBMI?.find(item => 
                item.nutritional_status_bmi === 'Underweight' || 
                (item.nutritional_status_bmi && item.nutritional_status_bmi.includes('Underweight'))
            )?.count || 0) + 
            (dashboardData.value.nutritionalStatusBMI?.find(item => 
                item.nutritional_status_bmi === 'Severely Underweight' || 
                (item.nutritional_status_bmi && item.nutritional_status_bmi.includes('Severely'))
            )?.count || 0)
            const overweightBMI = (dashboardData.value.nutritionalStatusBMI?.find(item => 
                item.nutritional_status_bmi === 'Overweight' || 
                (item.nutritional_status_bmi && item.nutritional_status_bmi.includes('Overweight'))
            )?.count || 0) + 
            (dashboardData.value.nutritionalStatusBMI?.find(item => 
                item.nutritional_status_bmi === 'Obese' || 
                (item.nutritional_status_bmi && item.nutritional_status_bmi.includes('Obese'))
            )?.count || 0)
            
            dynamicChartData.value = {
                labels: ['Normal (18.5-24.9)', 'Underweight (16.0-18.4)', 'Severely Underweight (<16.0)', 'Overweight (25.0-29.9)', 'Obese (≥30.0)'],
                datasets: [{
                    data: [normalBMI, underweightBMI, 0, overweightBMI, 0],
                    backgroundColor: generateColors(5)
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'BMI Distribution (WHO Standards)'
            currentChartType.value = 'doughnut'
            summaryStats.value = [
                { label: 'Normal BMI', value: normalBMI, color: 'green' },
                { label: 'Underweight', value: underweightBMI, color: 'orange' },
                { label: 'Overweight/Obese', value: overweightBMI, color: 'red' }
            ]
            break
            
        case 'nutritionalHeight':
            const heightData = dashboardData.value.nutritionalStatusHeight || []
            
            // Always show all 3 categories for better visualization
            const normalHeight = dashboardData.value.nutritionalStatusHeight?.find(item => 
                item.nutritional_status_height === 'Normal' || 
                (item.nutritional_status_height && item.nutritional_status_height.includes('Normal'))
            )?.count || 0
            const mildStunting = dashboardData.value.nutritionalStatusHeight?.find(item => 
                item.nutritional_status_height === 'Mild Stunting' || 
                (item.nutritional_status_height && item.nutritional_status_height.includes('Mild'))
            )?.count || 0
            const severeStunting = dashboardData.value.nutritionalStatusHeight?.find(item => 
                item.nutritional_status_height === 'Severe Stunting' || 
                (item.nutritional_status_height && item.nutritional_status_height.includes('Severe'))
            )?.count || 0
            
            dynamicChartData.value = {
                labels: ['Normal (≥-2 SD)', 'Mild Stunting (-2 to -3 SD)', 'Severe Stunting (<-3 SD)'],
                datasets: [{
                    data: [normalHeight, mildStunting, severeStunting],
                    backgroundColor: generateColors(3)
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Height for Age (WHO Growth Standards)'
            currentChartType.value = 'doughnut'
            summaryStats.value = [
                { label: 'Normal Height', value: normalHeight, color: 'green' },
                { label: 'Mild Stunting', value: mildStunting, color: 'orange' },
                { label: 'Severe Stunting', value: severeStunting, color: 'red' }
            ]
            break
            
        case 'ironSupplement':
            dynamicChartData.value = {
                labels: ['Positive', 'Negative'],
                datasets: [{
                    data: [dashboardData.value.ironSupplement?.positive || 0, dashboardData.value.ironSupplement?.negative || 0],
                    backgroundColor: ['#10B981', '#EF4444']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Iron Supplementation Status'
            currentChartType.value = 'doughnut'
            summaryStats.value = [
                { label: 'Positive', value: dashboardData.value.ironSupplement?.positive || 0, color: 'green' },
                { label: 'Negative', value: dashboardData.value.ironSupplement?.negative || 0, color: 'red' }
            ]
            break
            
        case 'abdomen':
            const abdomenData = dashboardData.value.examinations?.abdomen || {}
            const abdomenEntries = Object.entries(abdomenData)
            const totalAbdomen = abdomenEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalAbdomen > 0 ? abdomenEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalAbdomen > 0 ? abdomenEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalAbdomen > 0 ? generateColors(abdomenEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Abdomen Examination'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalAbdomen > 0 ? 
                abdomenEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'skinExamination':
            const skinData = dashboardData.value.examinations?.skin || {}
            const skinEntries = Object.entries(skinData)
            const totalSkin = skinEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalSkin > 0 ? skinEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalSkin > 0 ? skinEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalSkin > 0 ? generateColors(skinEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Skin Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalSkin > 0 ? 
                skinEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'scalpExamination':
            const scalpData = dashboardData.value.examinations?.scalp || {}
            const scalpEntries = Object.entries(scalpData)
            const totalScalp = scalpEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalScalp > 0 ? scalpEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalScalp > 0 ? scalpEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalScalp > 0 ? generateColors(scalpEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Scalp Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalScalp > 0 ? 
                scalpEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'eyesExamination':
            const eyesData = dashboardData.value.examinations?.eyes || {}
            const eyesEntries = Object.entries(eyesData)
            const totalEyes = eyesEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalEyes > 0 ? eyesEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalEyes > 0 ? eyesEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalEyes > 0 ? generateColors(eyesEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Eyes Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalEyes > 0 ? 
                eyesEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'earsExamination':
            const earsData = dashboardData.value.examinations?.ears || {}
            const earsEntries = Object.entries(earsData)
            const totalEars = earsEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalEars > 0 ? earsEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalEars > 0 ? earsEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalEars > 0 ? generateColors(earsEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Ears Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalEars > 0 ? 
                earsEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'noseExamination':
            const noseData = dashboardData.value.examinations?.nose || {}
            const noseEntries = Object.entries(noseData)
            const totalNose = noseEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalNose > 0 ? noseEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalNose > 0 ? noseEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalNose > 0 ? generateColors(noseEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Nose Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalNose > 0 ? 
                noseEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'mouthExamination':
            const mouthData = dashboardData.value.examinations?.mouth || {}
            const mouthEntries = Object.entries(mouthData)
            const totalMouth = mouthEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalMouth > 0 ? mouthEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalMouth > 0 ? mouthEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalMouth > 0 ? generateColors(mouthEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Mouth Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalMouth > 0 ? 
                mouthEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'throatExamination':
            const throatData = dashboardData.value.examinations?.throat || {}
            const throatEntries = Object.entries(throatData)
            const totalThroat = throatEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalThroat > 0 ? throatEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalThroat > 0 ? throatEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalThroat > 0 ? generateColors(throatEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Throat Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalThroat > 0 ? 
                throatEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'neckExamination':
            const neckData = dashboardData.value.examinations?.neck || {}
            const neckEntries = Object.entries(neckData)
            const totalNeck = neckEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalNeck > 0 ? neckEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalNeck > 0 ? neckEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalNeck > 0 ? generateColors(neckEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Neck Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalNeck > 0 ? 
                neckEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'lungsExamination':
            const lungsData = dashboardData.value.examinations?.lungs || {}
            const lungsEntries = Object.entries(lungsData)
            const totalLungs = lungsEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalLungs > 0 ? lungsEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalLungs > 0 ? lungsEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalLungs > 0 ? generateColors(lungsEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Lungs Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalLungs > 0 ? 
                lungsEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'heartExamination':
            const heartData = dashboardData.value.examinations?.heart || {}
            const heartEntries = Object.entries(heartData)
            const totalHeart = heartEntries.reduce((sum, [key, value]) => sum + value, 0)
            
            dynamicChartData.value = {
                labels: totalHeart > 0 ? heartEntries.map(([key, value]) => key) : ['No Data Available'],
                datasets: [{
                    data: totalHeart > 0 ? heartEntries.map(([key, value]) => value) : [1],
                    backgroundColor: totalHeart > 0 ? generateColors(heartEntries.length) : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Heart Examination Results'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalHeart > 0 ? 
                heartEntries.map(([key, value]) => ({ 
                    label: key, 
                    value: value, 
                    color: getColorForValue(key) 
                })) : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
            
        case 'deformities':
            const deformitiesNone = dashboardData.value.deformities?.none || 0
            const deformitiesCongenital = dashboardData.value.deformities?.congenital || 0
            const deformitiesAcquired = dashboardData.value.deformities?.acquired || 0
            const deformitiesOther = dashboardData.value.deformities?.other || 0
            const totalDeformities = deformitiesNone + deformitiesCongenital + deformitiesAcquired + deformitiesOther
            const hasDeformities = deformitiesCongenital + deformitiesAcquired + deformitiesOther
            
            dynamicChartData.value = {
                labels: totalDeformities > 0 ? ['None', 'Congenital', 'Acquired', 'Other'] : ['No Data Available'],
                datasets: [{
                    data: totalDeformities > 0 ? [deformitiesNone, deformitiesCongenital, deformitiesAcquired, deformitiesOther] : [1],
                    backgroundColor: totalDeformities > 0 ? ['#10B981', '#F59E0B', '#EF4444', '#DC2626'] : ['#E5E7EB']
                }]
            }
            dynamicChartOptions.value.plugins.title.text = 'Deformities Assessment'
            currentChartType.value = 'doughnut'
            summaryStats.value = totalDeformities > 0 ? [
                { label: 'No Deformities', value: deformitiesNone, color: 'green' },
                { label: 'Has Deformities', value: hasDeformities, color: 'red' }
            ] : [
                { label: 'No Data', value: 'Available', color: 'gray' }
            ]
            break
    }
}

const healthCompletionOptions = ref({
    responsive: true,
    plugins: {
        title: {
            display: true,
            text: 'Health Examination Completion by Grade Level'
        },
        legend: {
            display: false
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            max: 100,
            ticks: {
                callback: function(value) {
                    return value + '%'
                }
            }
        }
    }
})

const dashboardData = ref({})
const isLoading = ref(true)
const hasError = ref(false)

// Error handling wrapper
const safeUpdateChart = (graphType) => {
    try {
        // Reset error state when switching charts
        hasError.value = false
        updateChart(graphType)
    } catch (error) {
        console.error(`Error updating chart for ${graphType}:`, error)
        console.error('Dashboard data:', dashboardData.value)
        hasError.value = true
        
        // Set fallback data to prevent complete failure
        dynamicChartData.value = {
            labels: ['No Data Available'],
            datasets: [{
                data: [1],
                backgroundColor: ['#6B7280']
            }]
        }
        summaryStats.value = [
            { label: 'Error', value: 'Data unavailable', color: 'red' }
        ]
    }
}

// Handle filter changes
const onFilterChange = () => {
    console.log('Filters changed - Grade:', selectedGradeLevel.value, 'Section:', selectedSection.value)
    // Refresh dashboard data with new filters
    refreshDashboardData()
}

// Function to refresh dashboard data based on selected year
const refreshDashboardData = async () => {
    try {
        isLoading.value = true
        
        // Make API call to get filtered data
        const response = await fetch(`/api/dashboard-data?grade_level=${selectedGradeLevel.value}&section=${selectedSection.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        
        if (response.ok) {
            const data = await response.json()
            dashboardData.value = data.dashboardData || {}
            
            // Update statistics
            totalStudents.value = dashboardData.value.totalStudents || 0
            femaleStudents.value = dashboardData.value.femaleStudents || 0
            maleStudents.value = dashboardData.value.maleStudents || 0
            
            // Refresh current chart
            safeUpdateChart(selectedGraphType.value)
        }
    } catch (error) {
        console.error('Error refreshing dashboard data:', error)
        hasError.value = true
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    try {
        // Ensure page.props exists
        if (!page || !page.props) {
            console.warn('Page props not available, using default data')
            dashboardData.value = {}
        } else {
            dashboardData.value = page.props.dashboardData || {}
        }
        
        // Update student statistics
        totalStudents.value = dashboardData.value.totalStudents || 0
        femaleStudents.value = dashboardData.value.femaleStudents || 0
        maleStudents.value = dashboardData.value.maleStudents || 0

        // Update Health Examination Completion by Grade
        if (dashboardData.value.healthCompletionByGrade) {
            healthCompletionData.value.datasets[0].data = dashboardData.value.healthCompletionByGrade
        } else {
            // Sample data - replace with actual calculation
            healthCompletionData.value.datasets[0].data = [85, 92, 78, 88, 95, 82, 90]
        }

        // Update Health Issues Distribution
        if (dashboardData.value.healthIssues) {
            healthIssuesData.value.datasets[0].data = [
                dashboardData.value.healthIssues.dental || 12,
                dashboardData.value.healthIssues.bmi || 8,
                dashboardData.value.healthIssues.vision || 5,
                dashboardData.value.healthIssues.incidents || 3,
                dashboardData.value.oralHealth?.conditions?.length || 15,
                dashboardData.value.healthIssues.nutritional || 7
            ]
        } else {
            // Use actual oral health data or sample data
            const oralHealthConditionsCount = dashboardData.value.oralHealth?.conditions?.length || 15
            healthIssuesData.value.datasets[0].data = [12, 8, 5, 3, oralHealthConditionsCount, 7]
        }

        // Initialize with default chart (deworming)
        safeUpdateChart('deworming')
        
        isLoading.value = false
    } catch (error) {
        console.error('Error in onMounted:', error)
        hasError.value = true
        isLoading.value = false
    }

    // Update Health Alerts from database
    if (dashboardData.value.healthAlerts) {
        healthAlerts.value = dashboardData.value.healthAlerts.map(alert => ({
            id: alert.id,
            type: alert.severity || 'info', // 'danger', 'warning', 'info'
            icon: getAlertIcon(alert.type),
            title: alert.title,
            message: alert.message,
            count: alert.count || 0,
            color: getAlertColor(alert.severity)
        }))
    } else {
        // Generate health alerts based on existing data
        const alerts = []
        
        // Calculate deworming totals for reuse
        const dewormingTotal = (dashboardData.value.deworming?.dewormed || 0) + (dashboardData.value.deworming?.notDewormed || 0)
        
        // Check for students without health examinations
        const studentsWithoutHealthExam = totalStudents.value - dewormingTotal
        if (studentsWithoutHealthExam > 0) {
            alerts.push({
                id: 1,
                type: 'danger',
                icon: 'pi pi-heart',
                title: 'Health Check Overdue',
                message: `${studentsWithoutHealthExam} students need health examination`,
                count: studentsWithoutHealthExam,
                color: 'red'
            })
        }

        // Check deworming status
        const notDewormed = dashboardData.value.deworming?.notDewormed || 0
        if (notDewormed > 0) {
            alerts.push({
                id: 2,
                type: 'warning',
                icon: 'pi pi-exclamation-triangle',
                title: 'Deworming Required',
                message: `${notDewormed} students need deworming`,
                count: notDewormed,
                color: 'orange'
            })
        }

        healthAlerts.value = alerts
        
        // Update Health Metrics from database
        if (dashboardData.value.healthMetrics) {
            healthMetrics.value = dashboardData.value.healthMetrics
        } else {
            // Calculate health metrics based on existing data
            const metrics = []
            
            if (totalStudents.value > 0) {
                // Deworming coverage
                const dewormingCoverage = Math.round(((dashboardData.value.deworming?.dewormed || 0) / totalStudents.value) * 100)
                metrics.push({
                    label: 'Deworming Coverage',
                    value: dewormingCoverage,
                    target: 100,
                    icon: 'pi pi-shield',
                    color: dewormingCoverage >= 90 ? 'success' : dewormingCoverage >= 70 ? 'warning' : 'danger'
                })

                // Iron supplementation coverage
                const ironCoverage = Math.round(((dashboardData.value.ironSupplement?.positive || 0) / totalStudents.value) * 100)
                metrics.push({
                    label: 'Iron Supplementation',
                    value: ironCoverage,
                    target: 100,
                    icon: 'pi pi-heart',
                    color: ironCoverage >= 80 ? 'success' : ironCoverage >= 60 ? 'warning' : 'danger'
                })

                // Health records completion
                const healthRecordsCompletion = Math.round((dewormingTotal / totalStudents.value) * 100)
                metrics.push({
                    label: 'Health Records',
                    value: healthRecordsCompletion,
                    target: 100,
                    icon: 'pi pi-file',
                    color: healthRecordsCompletion >= 95 ? 'success' : healthRecordsCompletion >= 80 ? 'warning' : 'danger'
                })
            }

            healthMetrics.value = metrics
        }
    }
})

// Helper functions for alert styling
const getAlertIcon = (type) => {
    switch (type) {
        case 'vaccination': return 'pi pi-shield'
        case 'health_check': return 'pi pi-heart'
        case 'dental': return 'pi pi-bookmark'
        case 'deworming': return 'pi pi-exclamation-triangle'
        default: return 'pi pi-info-circle'
    }
}

const getAlertColor = (severity) => {
    switch (severity) {
        case 'danger': return 'red'
        case 'warning': return 'orange'
        case 'info': return 'blue'
        default: return 'gray'
    }
}

const schoolYear = ref('2023-2024')
const schoolYears = ref(['2023-2024', '2022-2023', '2021-2022'])

// Health Alerts and Notifications (will be populated from database)
const healthAlerts = ref([])

// Health program progress (will be populated from database)
const healthMetrics = ref([])

// Recent Activities Timeline
const recentActivities = ref([
    {
        date: new Date(),
        icon: 'pi pi-user-plus',
        color: 'green',
        title: 'New Health Record Added',
        description: 'Health examination completed for John Smith (Grade 5)'
    },
    {
        date: new Date(Date.now() - 2 * 60 * 60 * 1000),
        icon: 'pi pi-file',
        color: 'blue',
        title: 'Health Report Generated',
        description: 'Monthly health report generated for Grade 4 students'
    },
    {
        date: new Date(Date.now() - 4 * 60 * 60 * 1000),
        icon: 'pi pi-heart',
        color: 'pink',
        title: 'Oral Health Screening',
        description: 'Dental examination completed for 12 students'
    },
    {
        date: new Date(Date.now() - 6 * 60 * 60 * 1000),
        icon: 'pi pi-shield',
        color: 'orange',
        title: 'Vaccination Updated',
        description: 'COVID-19 booster records updated for Grade 6'
    }
])


// Computed properties for dynamic content
const totalHealthRecords = computed(() => totalStudents.value)
const healthCompletionRate = computed(() => {
    if (totalStudents.value === 0) return 0
    return Math.round((dewormingData.value.dewormed / totalStudents.value) * 100)
})

const urgentAlertsCount = computed(() => {
    return healthAlerts.value.filter(alert => alert.type === 'danger').length
})

// Format time for timeline
const handleAlertClick = (alert) => {
    switch (alert.type) {
        case 'danger':
        case 'warning':
            router.visit('/health-report')
            break
        case 'info':
            router.visit('/pupils')
            break
        default:
            router.visit('/')
    }
}
</script>

<template>
    <!-- Header with Welcome Message and Demographics -->
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 text-white rounded-lg p-6 mb-6 shadow-lg">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-2">Health Dashboard</h1>
                <p class="text-blue-100 text-sm mb-4">Welcome to Naawan Central School Health Management System</p>
                <!-- Merged Demographics -->
                <div class="grid grid-cols-3 gap-6">
                    <div class="text-center bg-white/10 rounded-lg p-3">
                        <div class="text-2xl font-bold">{{ totalStudents }}</div>
                        <div class="text-blue-100 text-sm">Total Students</div>
                    </div>
                    <div class="text-center bg-white/10 rounded-lg p-3">
                        <div class="text-2xl font-bold">{{ femaleStudents }}</div>
                        <div class="text-blue-100 text-sm">Female</div>
                    </div>
                    <div class="text-center bg-white/10 rounded-lg p-3">
                        <div class="text-2xl font-bold">{{ maleStudents }}</div>
                        <div class="text-blue-100 text-sm">Male</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Dashboard Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Side: Health Metric Selector -->
        <Card class="shadow-md xl:col-span-1">
            <template #title>
                <div class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-sliders-h mr-2 text-purple-500"></i>
                    Select Health Metric
                </div>
            </template>
            <template #content>
                <div v-if="isLoading" class="flex justify-center items-center p-8">
                    <i class="pi pi-spin pi-spinner text-2xl text-blue-500"></i>
                    <span class="ml-2 text-gray-600">Loading health metrics...</span>
                </div>
                <div v-else-if="hasError" class="text-center p-8">
                    <i class="pi pi-exclamation-triangle text-2xl text-red-500 mb-2"></i>
                    <p class="text-red-600">Error loading dashboard data. Please refresh the page.</p>
                </div>
                <div v-else class="max-h-96 overflow-y-auto space-y-2">
                    <button
                        v-for="graph in availableGraphs"
                        :key="graph.value"
                        @click="safeUpdateChart(graph.value)"
                        :class="[
                            'w-full flex items-center p-2 rounded-md border-2 transition-all duration-200 text-left text-sm font-medium',
                            selectedGraphType === graph.value 
                                ? 'border-blue-500 bg-blue-50 text-blue-700 shadow-sm' 
                                : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50'
                        ]"
                    >
                        <i :class="graph.icon" class="mr-2 text-sm"></i>
                        <span class="text-xs">{{ graph.label }}</span>
                    </button>
                </div>
            </template>
        </Card>

        <!-- Right Side: Dynamic Chart Display -->
        <div class="xl:col-span-2">
            <Card class="shadow-md">
                <template #title>
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="pi pi-chart-pie mr-2 text-green-500"></i>
                            {{ availableGraphs.find(g => g.value === selectedGraphType)?.label || 'Health Metric' }}
                        </div>
                        <!-- Grade Level and Section Filters (Hidden for Teachers) -->
                        <div v-if="userRole !== 'teacher'" class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <label class="text-sm text-gray-600">Grade:</label>
                                <Dropdown 
                                    v-model="selectedGradeLevel" 
                                    :options="availableGradeLevels" 
                                    @change="onFilterChange"
                                    class="w-32 text-sm"
                                    placeholder="Select Grade"
                                />
                            </div>
                            <div class="flex items-center space-x-2">
                                <label class="text-sm text-gray-600">Section:</label>
                                <Dropdown 
                                    v-model="selectedSection" 
                                    :options="availableSections" 
                                    @change="onFilterChange"
                                    class="w-24 text-sm"
                                    placeholder="Select Section"
                                />
                            </div>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Chart -->
                        <div class="flex flex-col items-center">
                            <Chart 
                                :type="currentChartType" 
                                :data="dynamicChartData" 
                                :options="dynamicChartOptions" 
                                class="h-80 w-full max-w-md" 
                            />
                        </div>
                        
                        <!-- Summary Stats -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-800 mb-4 text-lg">Summary Statistics</h4>
                            <div 
                                v-for="stat in summaryStats" 
                                :key="stat.label"
                                class="flex items-center justify-between p-4 rounded-lg border-l-4 bg-gray-50"
                                :class="{
                                    'border-l-green-500 bg-green-50': stat.color === 'green',
                                    'border-l-red-500 bg-red-50': stat.color === 'red',
                                    'border-l-orange-500 bg-orange-50': stat.color === 'orange',
                                    'border-l-blue-500 bg-blue-50': stat.color === 'blue'
                                }"
                            >
                                <div>
                                    <span class="font-medium text-gray-700 text-sm">{{ stat.label }}</span>
                                </div>
                                <span 
                                    class="text-2xl font-bold"
                                    :class="{
                                        'text-green-600': stat.color === 'green',
                                        'text-red-600': stat.color === 'red',
                                        'text-orange-600': stat.color === 'orange',
                                        'text-blue-600': stat.color === 'blue'
                                    }"
                                >
                                    {{ stat.value }}
                                </span>
                            </div>
                            
                            <!-- Additional Health Insights -->
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <h5 class="font-semibold text-blue-800 mb-2 flex items-center">
                                    <i class="pi pi-info-circle mr-2"></i>
                                    Quick Insights
                                </h5>
                                <p class="text-sm text-blue-700" v-if="selectedGraphType === 'deworming'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students are dewormed. 
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students need deworming.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'bmi'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0) + (summaryStats[2]?.value || 0)) * 100) }}% of students have normal BMI (WHO standards).
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium text-orange-600">{{ summaryStats[1]?.value }} students are underweight.</span>
                                    <span v-if="(summaryStats[2]?.value || 0) > 0" class="font-medium text-red-600">{{ summaryStats[2]?.value }} students are overweight/obese.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'nutritionalHeight'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0) + (summaryStats[2]?.value || 0)) * 100) }}% of students have normal height for age (WHO standards).
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium text-yellow-600">{{ summaryStats[1]?.value }} students have mild stunting.</span>
                                    <span v-if="(summaryStats[2]?.value || 0) > 0" class="font-medium text-red-600">{{ summaryStats[2]?.value }} students have severe stunting.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'ironSupplement'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% positive iron supplementation status.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have negative iron status.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'visionScreening'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal vision.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal vision.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'auditoryScreening'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal hearing.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal hearing.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'skinExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal skin examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal skin findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'scalpExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal scalp examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal scalp findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'eyesExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal eyes examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal eye findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'earsExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal ears examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal ear findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'noseExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal nose examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal nose findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'mouthExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal mouth examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal mouth findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'throatExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal throat examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal throat findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'neckExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal neck examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal neck findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'lungsExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal lungs examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal lung findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'heartExamination'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal heart examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal findings.</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'abdomen'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have normal abdomen examination.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have abnormal findings (including distended abdomen).</span>
                                </p>
                                <p class="text-sm text-blue-700" v-else-if="selectedGraphType === 'deformities'">
                                    {{ Math.round((summaryStats[0]?.value || 0) / ((summaryStats[0]?.value || 0) + (summaryStats[1]?.value || 0)) * 100) }}% of students have no deformities.
                                    <span v-if="(summaryStats[1]?.value || 0) > 0" class="font-medium">{{ summaryStats[1]?.value }} students have deformities (congenital or acquired).</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </div>
</template>