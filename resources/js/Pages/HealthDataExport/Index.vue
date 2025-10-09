<template>
    <Head title="Health Data Export" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-download mr-2 text-green-600"></i>
                    Health Data Export
                </h1>
                <Link href="/dashboard" class="no-underline">
                    <Button label="Back to Dashboard" icon="pi pi-arrow-left" outlined severity="secondary" class="text-sm" />
                </Link>
            </div>

            <!-- Export Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Health Examinations Export -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="bg-blue-600 text-white p-4 rounded-t-lg">
                        <h2 class="text-lg font-medium flex items-center">
                            <i class="pi pi-heart mr-2"></i>
                            Health Examinations
                        </h2>
                        <p class="text-blue-100 text-sm">Export all student health examination records</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Date Range -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                    <DatePicker 
                                        v-model="healthExamFilters.date_from" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select start date"
                                        class="w-full"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                    <DatePicker 
                                        v-model="healthExamFilters.date_to" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select end date"
                                        class="w-full"
                                    />
                                </div>
                            </div>

                            <!-- Grade Level -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                                <Select 
                                    v-model="healthExamFilters.grade_level"
                                    :options="gradeOptions"
                                    placeholder="All Grades"
                                    class="w-full"
                                />
                            </div>

                            <!-- Include Personal Info -->
                            <div class="flex items-center">
                                <Checkbox 
                                    v-model="healthExamFilters.include_personal_info" 
                                    inputId="health_personal_info"
                                    :binary="true"
                                />
                                <label for="health_personal_info" class="ml-2 text-sm text-gray-700">
                                    Include personal information (name, LRN, etc.)
                                </label>
                            </div>

                            <!-- Export Buttons -->
                            <div class="flex gap-2 pt-2">
                                <Button 
                                    label="Export CSV" 
                                    icon="pi pi-file"
                                    @click="exportHealthExaminations('csv')"
                                    :loading="loading.healthExam"
                                    class="!bg-green-600 !border-green-600 hover:!bg-green-700 w-full"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Oral Health Examinations Export -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="bg-purple-600 text-white p-4 rounded-t-lg">
                        <h2 class="text-lg font-medium flex items-center">
                            <i class="pi pi-heart mr-2"></i>
                            Oral Health Examinations
                        </h2>
                        <p class="text-purple-100 text-sm">Export all student oral health examination records</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Date Range -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                    <DatePicker 
                                        v-model="oralHealthExamFilters.date_from" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select start date"
                                        class="w-full"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                    <DatePicker 
                                        v-model="oralHealthExamFilters.date_to" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select end date"
                                        class="w-full"
                                    />
                                </div>
                            </div>

                            <!-- Grade Level -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                                <Select 
                                    v-model="oralHealthExamFilters.grade_level"
                                    :options="gradeOptions"
                                    placeholder="All Grades"
                                    class="w-full"
                                />
                            </div>

                            <!-- Include Personal Info -->
                            <div class="flex items-center">
                                <Checkbox 
                                    v-model="oralHealthExamFilters.include_personal_info" 
                                    inputId="oral_personal_info"
                                    :binary="true"
                                />
                                <label for="oral_personal_info" class="ml-2 text-sm text-gray-700">
                                    Include personal information (name, LRN, etc.)
                                </label>
                            </div>

                            <!-- Export Buttons -->
                            <div class="flex gap-2 pt-2">
                                <Button 
                                    label="Export CSV" 
                                    icon="pi pi-file"
                                    @click="exportOralHealthExaminations('csv')"
                                    :loading="loading.oralHealthExam"
                                    class="!bg-purple-600 !border-purple-600 hover:!bg-purple-700 w-full"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Treatments Export -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="bg-orange-600 text-white p-4 rounded-t-lg">
                        <h2 class="text-lg font-medium flex items-center">
                            <i class="pi pi-plus-circle mr-2"></i>
                            Health Treatments
                        </h2>
                        <p class="text-orange-100 text-sm">Export all student health treatment records</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Date Range -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                    <DatePicker 
                                        v-model="healthTreatmentFilters.date_from" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select start date"
                                        class="w-full"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                    <DatePicker 
                                        v-model="healthTreatmentFilters.date_to" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select end date"
                                        class="w-full"
                                    />
                                </div>
                            </div>

                            <!-- Grade Level -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                                <Select 
                                    v-model="healthTreatmentFilters.grade_level"
                                    :options="gradeOptions"
                                    placeholder="All Grades"
                                    class="w-full"
                                />
                            </div>

                            <!-- Export Buttons -->
                            <div class="flex gap-2 pt-4">
                                <Button 
                                    label="Export CSV" 
                                    icon="pi pi-file"
                                    @click="exportHealthTreatments('csv')"
                                    :loading="loading.healthTreatment"
                                    class="!bg-orange-600 !border-orange-600 hover:!bg-orange-700 w-full"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Oral Health Treatments Export -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="bg-teal-600 text-white p-4 rounded-t-lg">
                        <h2 class="text-lg font-medium flex items-center">
                            <i class="pi pi-plus-circle mr-2"></i>
                            Oral Health Treatments
                        </h2>
                        <p class="text-teal-100 text-sm">Export all student oral health treatment records</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Date Range -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                    <DatePicker 
                                        v-model="oralHealthTreatmentFilters.date_from" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select start date"
                                        class="w-full"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                    <DatePicker 
                                        v-model="oralHealthTreatmentFilters.date_to" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select end date"
                                        class="w-full"
                                    />
                                </div>
                            </div>

                            <!-- Grade Level -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                                <Select 
                                    v-model="oralHealthTreatmentFilters.grade_level"
                                    :options="gradeOptions"
                                    placeholder="All Grades"
                                    class="w-full"
                                />
                            </div>

                            <!-- Export Buttons -->
                            <div class="flex gap-2 pt-4">
                                <Button 
                                    label="Export CSV" 
                                    icon="pi pi-file"
                                    @click="exportOralHealthTreatments('csv')"
                                    :loading="loading.oralHealthTreatment"
                                    class="!bg-teal-600 !border-teal-600 hover:!bg-teal-700 w-full"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Incidents Export -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="bg-red-600 text-white p-4 rounded-t-lg">
                        <h2 class="text-lg font-medium flex items-center">
                            <i class="pi pi-exclamation-triangle mr-2"></i>
                            Incidents
                        </h2>
                        <p class="text-red-100 text-sm">Export all student incident records</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Date Range -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                    <DatePicker 
                                        v-model="incidentFilters.date_from" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select start date"
                                        class="w-full"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                    <DatePicker 
                                        v-model="incidentFilters.date_to" 
                                        dateFormat="yy-mm-dd"
                                        placeholder="Select end date"
                                        class="w-full"
                                    />
                                </div>
                            </div>

                            <!-- Grade Level -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                                <Select 
                                    v-model="incidentFilters.grade_level"
                                    :options="gradeOptions"
                                    placeholder="All Grades"
                                    class="w-full"
                                />
                            </div>

                            <!-- Export Buttons -->
                            <div class="flex gap-2 pt-4">
                                <Button 
                                    label="Export CSV" 
                                    icon="pi pi-file"
                                    @click="exportIncidents('csv')"
                                    :loading="loading.incident"
                                    class="!bg-red-600 !border-red-600 hover:!bg-red-700 w-full"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export All Data -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="bg-gray-800 text-white p-4 rounded-t-lg">
                        <h2 class="text-lg font-medium flex items-center">
                            <i class="pi pi-database mr-2"></i>
                            Export All Data
                        </h2>
                        <p class="text-gray-300 text-sm">Export all health data in one comprehensive file</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="text-center text-gray-600 text-sm">
                                This will export all health examinations, treatments, and incidents in separate CSV files.
                            </div>

                            <!-- Export All Button -->
                            <div class="pt-2">
                                <Button 
                                    label="Export All Data (CSV)" 
                                    icon="pi pi-download"
                                    @click="exportAllData()"
                                    :loading="loading.all"
                                    class="!bg-gray-800 !border-gray-800 hover:!bg-gray-900 w-full"
                                />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import Button from 'primevue/button'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'
import Checkbox from 'primevue/checkbox'

const props = defineProps({
    schoolSettings: Object
})

// Grade options
const gradeOptions = [
    'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'
]

// Loading states
const loading = ref({
    healthExam: false,
    oralHealthExam: false,
    healthTreatment: false,
    oralHealthTreatment: false,
    incident: false,
    all: false
})

// Filter states
const healthExamFilters = ref({
    date_from: null,
    date_to: null,
    grade_level: null,
    include_personal_info: true
})

const oralHealthExamFilters = ref({
    date_from: null,
    date_to: null,
    grade_level: null,
    include_personal_info: true
})

const healthTreatmentFilters = ref({
    date_from: null,
    date_to: null,
    grade_level: null
})

const oralHealthTreatmentFilters = ref({
    date_from: null,
    date_to: null,
    grade_level: null
})

const incidentFilters = ref({
    date_from: null,
    date_to: null,
    grade_level: null
})

// Export functions
const exportHealthExaminations = async (format) => {
    loading.value.healthExam = true
    try {
        const params = new URLSearchParams({
            format,
            ...prepareFilters(healthExamFilters.value)
        })
        
        // Direct navigation to trigger download
        window.location.href = `/health-data-export/health-examinations?${params}`
        
        // Show success message after a short delay
        setTimeout(() => {
            alert(`✅ Health Examinations export started! Check your Downloads folder for the ${format.toUpperCase()} file.`)
        }, 2000)
        
    } catch (error) {
        console.error('Export failed:', error)
        alert('❌ Export failed. Please try again.')
    } finally {
        setTimeout(() => {
            loading.value.healthExam = false
        }, 2000)
    }
}

const exportOralHealthExaminations = async (format) => {
    loading.value.oralHealthExam = true
    try {
        const params = new URLSearchParams({
            format,
            ...prepareFilters(oralHealthExamFilters.value)
        })
        
        window.open(`/health-data-export/oral-health-examinations?${params}`, '_blank')
    } catch (error) {
        console.error('Export failed:', error)
    } finally {
        loading.value.oralHealthExam = false
    }
}

const exportHealthTreatments = async (format) => {
    loading.value.healthTreatment = true
    try {
        const params = new URLSearchParams({
            format,
            ...prepareFilters(healthTreatmentFilters.value)
        })
        
        window.open(`/health-data-export/health-treatments?${params}`, '_blank')
    } catch (error) {
        console.error('Export failed:', error)
    } finally {
        loading.value.healthTreatment = false
    }
}

const exportOralHealthTreatments = async (format) => {
    loading.value.oralHealthTreatment = true
    try {
        const params = new URLSearchParams({
            format,
            ...prepareFilters(oralHealthTreatmentFilters.value)
        })
        
        window.open(`/health-data-export/oral-health-treatments?${params}`, '_blank')
    } catch (error) {
        console.error('Export failed:', error)
    } finally {
        loading.value.oralHealthTreatment = false
    }
}

const exportIncidents = async (format) => {
    loading.value.incident = true
    try {
        const params = new URLSearchParams({
            format,
            ...prepareFilters(incidentFilters.value)
        })
        
        window.open(`/health-data-export/incidents?${params}`, '_blank')
    } catch (error) {
        console.error('Export failed:', error)
    } finally {
        loading.value.incident = false
    }
}

const exportAllData = async () => {
    loading.value.all = true
    try {
        // Export all data types as separate CSV files
        await exportHealthExaminations('csv')
        await exportOralHealthExaminations('csv')
        await exportHealthTreatments('csv')
        await exportOralHealthTreatments('csv')
        await exportIncidents('csv')
        
        alert('✅ All data exports started! Check your Downloads folder for the CSV files.')
    } catch (error) {
        console.error('Export failed:', error)
        alert('❌ Export failed. Please try again.')
    } finally {
        loading.value.all = false
    }
}

// Helper function to prepare filters for URL
const prepareFilters = (filters) => {
    const prepared = {}
    
    Object.keys(filters).forEach(key => {
        if (filters[key] !== null && filters[key] !== undefined) {
            if (filters[key] instanceof Date) {
                prepared[key] = filters[key].toISOString().split('T')[0]
            } else if (typeof filters[key] === 'boolean') {
                prepared[key] = filters[key] ? '1' : '0'
            } else {
                prepared[key] = filters[key]
            }
        }
    })
    
    return prepared
}
</script>

<style scoped>
/* Additional styling if needed */
</style>
