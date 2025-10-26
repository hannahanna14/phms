<template>
    <Head title="Create Oral Health Examination" />
    <div class="min-h-screen bg-gray-50 p-4">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white shadow rounded-lg p-6">
                <!-- Student Information Header -->
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Pupil Oral Health Examination</h1>
                    <p class="text-gray-600">Naawan Central School</p>
                </div>
                
                <!-- Student Details -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6 grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-semibold text-gray-700">Student:</span> {{ student.full_name }}
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Grade Level:</span> {{ gradeLevel }}
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">LRN:</span> {{ student.lrn || '10000000001' }}
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Section:</span> {{ student.section || 'Not Assigned' }}
                    </div>
                </div>

                <!-- Draft Restored Notification -->
                <div v-if="showDraftNotification" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="pi pi-info-circle text-blue-600 mr-2"></i>
                        <span class="text-blue-800 text-sm">
                            <strong>Draft restored:</strong> Your previous oral health examination data has been recovered.
                        </span>
                        <button @click="showDraftNotification = false" class="ml-auto text-blue-600 hover:text-blue-800">
                            <i class="pi pi-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Interactive Dental Chart -->
                <div class="mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="chart-selector">
                            <button
                                type="button"
                                :class="['chart-btn', { active: showPermanent }]"
                                @click="toggleChartType('permanent')"
                            >
                                Permanent Teeth
                            </button>
                            <button
                                type="button"
                                :class="['chart-btn', { active: !showPermanent }]"
                                @click="toggleChartType('primary')"
                            >
                                Temporary Teeth
                            </button>
                        </div>
                    </div>

                    <div class="dental-chart-container">
                        <div v-show="showPermanent" class="dental-chart compact">
                            <h3 class="text-center text-lg font-semibold mb-4">Permanent Teeth (32 teeth)</h3>
                            <div class="teeth-section">
                                <div class="arch-label">Upper Teeth</div>
                                <div id="upper-teeth" class="teeth-row upper compact"></div>
                                <div id="lower-teeth" class="teeth-row lower compact"></div>
                                <div class="arch-label">Lower Teeth</div>
                            </div>
                        </div>

                        <div v-show="!showPermanent" class="dental-chart compact">
                            <h3 class="text-center text-lg font-semibold mb-4">Temporary Teeth (20 teeth)</h3>
                            <div class="teeth-section">
                                <div class="arch-label">Upper Temporary Teeth</div>
                                <div id="upper-primary" class="teeth-row upper primary compact"></div>
                                <div id="lower-primary" class="teeth-row lower primary compact"></div>
                                <div class="arch-label">Lower Temporary Teeth</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Permanent Teeth Section -->
                    <div class="border rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-center mb-6">Permanent Teeth</h2>

                        <div class="grid grid-cols-3 gap-6">
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Index d.f.t. <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.permanent_index_dft"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="32"
                                    required
                                />
                                <small v-if="errors.permanent_index_dft" class="text-red-500">{{ errors.permanent_index_dft }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Teeth decayed <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.permanent_teeth_decayed"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="32"
                                    required
                                />
                                <small v-if="errors.permanent_teeth_decayed" class="text-red-500">{{ errors.permanent_teeth_decayed }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Teeth filled <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.permanent_teeth_filled"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="32"
                                    required
                                />
                                <small v-if="errors.permanent_teeth_filled" class="text-red-500">{{ errors.permanent_teeth_filled }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Teeth missing <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.permanent_teeth_missing"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="32"
                                    required
                                />
                                <small v-if="errors.permanent_teeth_missing" class="text-red-500">{{ errors.permanent_teeth_missing }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total d.f.t <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.permanent_total_dft"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="96"
                                    required
                                />
                                <small v-if="errors.permanent_total_dft" class="text-red-500">{{ errors.permanent_total_dft }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">For Extraction <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.permanent_for_extraction"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="32"
                                    required
                                />
                                <small v-if="errors.permanent_for_extraction" class="text-red-500">{{ errors.permanent_for_extraction }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">For Filling <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.permanent_for_filling"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="32"
                                    required
                                />
                                <small v-if="errors.permanent_for_filling" class="text-red-500">{{ errors.permanent_for_filling }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Temporary Teeth Section -->
                    <div class="border rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-center mb-6">Temporary Teeth</h2>

                        <div class="grid grid-cols-3 gap-6">
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Index d.f.t. <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.temporary_index_dft"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="20"
                                    required
                                />
                                <small v-if="errors.temporary_index_dft" class="text-red-500">{{ errors.temporary_index_dft }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Teeth decayed <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.temporary_teeth_decayed"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="20"
                                    required
                                />
                                <small v-if="errors.temporary_teeth_decayed" class="text-red-500">{{ errors.temporary_teeth_decayed }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Teeth filled <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.temporary_teeth_filled"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="20"
                                    required
                                />
                                <small v-if="errors.temporary_teeth_filled" class="text-red-500">{{ errors.temporary_teeth_filled }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total d.f.t <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.temporary_total_dft"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="60"
                                    required
                                />
                                <small v-if="errors.temporary_total_dft" class="text-red-500">{{ errors.temporary_total_dft }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">For Extraction <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.temporary_for_extraction"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="20"
                                    required
                                />
                                <small v-if="errors.temporary_for_extraction" class="text-red-500">{{ errors.temporary_for_extraction }}</small>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">For Filling <span class="text-red-500">*</span></label>
                                <InputText
                                    v-model="form.temporary_for_filling"
                                    type="number"
                                    class="w-full"
                                    placeholder="Enter value"
                                    min="0"
                                    max="20"
                                    required
                                />
                                <small v-if="errors.temporary_for_filling" class="text-red-500">{{ errors.temporary_for_filling }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Oral Health Conditions Section - SIMPLIFIED -->
                    <div class="border rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-center mb-6">Oral Health Conditions</h2>
                        <p class="text-center text-gray-600 mb-4">Select any conditions present for this examination:</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div v-for="condition in oralHealthConditions" :key="condition.key" class="border rounded p-3">
                                <label class="flex items-center space-x-2">
                                    <input 
                                        type="checkbox" 
                                        class="w-4 h-4"
                                        v-model="form.conditions[condition.key + '_present']"
                                    >
                                    <span class="text-sm font-medium">{{ condition.label }}</span>
                                </label>
                                <input 
                                    v-if="form.conditions[condition.key + '_present']"
                                    v-model="form.conditions[condition.key + '_date']"
                                    type="date" 
                                    class="mt-2 w-full text-xs border border-gray-300 rounded px-2 py-1"
                                    placeholder="Date detected"
                                >
                                <input 
                                    v-if="form.conditions[condition.key + '_present'] && condition.key === 'others_specify'"
                                    v-model="form.conditions[condition.key + '_specification']"
                                    type="text" 
                                    class="mt-2 w-full text-xs border border-gray-300 rounded px-2 py-1"
                                    placeholder="Please specify the condition..."
                                >
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8">
                        <Button
                            type="button"
                            @click="router.visit(`/pupil-health/oral-health/${student.id}?grade=${gradeLevel.value}`)"
                            severity="secondary"
                            label="Cancel"
                        />
                        <Button
                            type="submit"
                            label="Add"
                            :loading="form.processing"
                        />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Side Panel for Symbol Selection -->
    <div id="symbol-panel" class="side-panel">
        <div class="panel-header">
            <h3 id="selected-tooth-title">Select a tooth</h3>
            <button id="close-panel" class="close-btn" type="button">&times;</button>
        </div>

        <div class="panel-content">
            <div class="current-symbols-display" id="current-symbols-display">
                <strong>Current conditions:</strong> <span id="tooth-symbols">None</span>
            </div>

            <div class="symbol-categories">
                <div class="symbol-group">
                    <h4>Conditions</h4>
                    <div class="symbol-buttons">
                        <button class="symbol-btn" data-symbol="X" title="Carious tooth indicated for extraction" type="button">X</button>
                        <button class="symbol-btn" data-symbol="D" title="Carious tooth indicated for filling" type="button">D</button>
                        <button class="symbol-btn" data-symbol="RF" title="Root fragment" type="button">RF</button>
                        <button class="symbol-btn" data-symbol="M" title="Missing tooth" type="button">M</button>
                    </div>
                </div>

                <div class="symbol-group">
                    <h4>Treatments</h4>
                    <div class="symbol-buttons">
                        <button class="symbol-btn" data-symbol="F2" title="Permanent filled tooth with recurrence of decay" type="button">F2</button>
                        <button class="symbol-btn" data-symbol="√" title="Sound/erupted Permanent tooth" type="button">√</button>
                        <button class="symbol-btn" data-symbol="PFS" title="Pit and Fissure Sealant" type="button">PFS</button>
                        <button class="symbol-btn" data-symbol="JC" title="Jacket Crown" type="button">JC</button>
                    </div>
                </div>

                <div class="symbol-group">
                    <h4>Prosthetics</h4>
                    <div class="symbol-buttons">
                        <button class="symbol-btn" data-symbol="P" title="Pontic" type="button">P</button>
                        <button class="symbol-btn" data-symbol="RPD" title="Removable Partial Denture" type="button">RPD</button>
                        <button class="symbol-btn" data-symbol="FB" title="Fixed Bridge" type="button">FB</button>
                        <button class="symbol-btn" data-symbol="CD" title="Complete Denture" type="button">CD</button>
                    </div>
                </div>

                <div class="symbol-group">
                    <h4>Restorations</h4>
                    <div class="symbol-buttons">
                        <button class="symbol-btn" data-symbol="GI" title="Glass Ionomer" type="button">GI</button>
                        <button class="symbol-btn" data-symbol="CO" title="Composite" type="button">CO</button>
                        <button class="symbol-btn" data-symbol="AM" title="Amalgam" type="button">AM</button>
                    </div>
                </div>
            </div>

            <div class="panel-actions">
                <button id="clear-tooth-symbols" class="action-btn secondary" type="button">Clear Tooth</button>
                <button id="confirm-symbols" class="action-btn primary" type="button">Confirm</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import { computed, ref, onMounted, nextTick } from 'vue'
import { useFormPersistence } from '@/composables/useFormPersistence'

const props = defineProps({
    student: {
        type: Object,
        required: true
    },
    errors: {
        type: Object,
        default: () => ({})
    }
})

const page = usePage()
const gradeLevel = computed(() => {
    return new URLSearchParams(window.location.search).get('grade') || props.student.grade_level
})

// Dental chart state
const showPermanent = ref(true)
let selectedTooth = null
let toothSymbols = {}

// Oral Health Conditions data
const oralHealthConditions = [
    { key: 'gingivitis', label: 'Gingivitis' },
    { key: 'periodontal_disease', label: 'Periodontal Disease' },
    { key: 'malocclusion', label: 'Malocclusion' },
    { key: 'supernumerary_teeth', label: 'Supernumerary teeth' },
    { key: 'retained_deciduous_teeth', label: 'Retained deciduous teeth' },
    { key: 'decubital_ulcer', label: 'Decubital ulcer' },
    { key: 'calculus', label: 'Calculus' },
    { key: 'cleft_lip_palate', label: 'Cleft lip / palate' },
    { key: 'root_fragment', label: 'Root fragment' },
    { key: 'fluorosis', label: 'Fluorosis' },
    { key: 'others_specify', label: 'Others, specify' }
]

// Show only current grade being examined - define this first
const getCurrentGradeRange = (currentGrade) => {
    const gradeNum = parseInt(currentGrade) || 0

    // Map grade numbers to their corresponding range keys and simple labels
    const gradeMapping = {
        0: { key: 'kinder', label: 'Grade Level' },
        1: { key: 'grade_1_7', label: 'Grade Level' },
        2: { key: 'grade_2_8', label: 'Grade Level' },
        3: { key: 'grade_3_9', label: 'Grade Level' },
        4: { key: 'grade_4_10', label: 'Grade Level' },
        5: { key: 'grade_5_11', label: 'Grade Level' },
        6: { key: 'grade_6_12', label: 'Grade Level' }
    }

    // Handle Kinder cases
    if (currentGrade === 'K' || currentGrade === 'Kinder' || currentGrade === 'Kinder 2' || gradeNum === 0) {
        return [gradeMapping[0]]
    }

    // Return only the current grade range
    return gradeMapping[gradeNum] ? [gradeMapping[gradeNum]] : [gradeMapping[0]]
}

const relevantGradeRanges = computed(() => getCurrentGradeRange(gradeLevel.value))

// Initialize conditions structure for current grade only
const initializeConditions = () => {
    const currentGradeRanges = getCurrentGradeRange(gradeLevel.value)
    const conditions = {}
    
    oralHealthConditions.forEach(condition => {
        conditions[condition.key] = {}
        // Only initialize for the current grade range
        currentGradeRanges.forEach(gradeRange => {
            conditions[condition.key][gradeRange.key] = {
                present: false,
                date: '',
                specification: '' // For "Others, specify" text input
            }
        })
    })
    return conditions
}

const form = useForm({
    student_id: props.student.id,
    grade_level: gradeLevel.value,
    school_year: props.student.school_year || '2024-2025',
    examination_date: new Date().toISOString().split('T')[0],
    original_grade: gradeLevel.value,
    permanent_index_dft: 0,
    permanent_teeth_decayed: 0,
    permanent_teeth_filled: 0,
    permanent_teeth_missing: 0,
    permanent_total_dft: 0,
    permanent_for_extraction: 0,
    permanent_for_filling: 0,
    temporary_index_dft: 0,
    temporary_teeth_decayed: 0,
    temporary_teeth_filled: 0,
    temporary_total_dft: 0,
    temporary_for_extraction: 0,
    temporary_for_filling: 0,
    tooth_symbols: {},
    conditions: {}
})

// Set up form persistence
const {
    showDraftNotification,
    initializeForm,
    setupAutoSave,
    onSubmitSuccess,
    onCancel,
    hasUnsavedChanges
} = useFormPersistence(`oral_health_exam_create_${props.student.id}`, form, {
    excludeFields: ['student_id'], // Don't save student_id as it's always the same
    autoSave: true,
    showNotification: true
})

// Determine which teeth to show based on grade level
const shouldShowTemporaryTeeth = computed(() => {
    const grade = parseInt(gradeLevel.value) || 0
    return grade <= 3 // Show temporary teeth for Kinder and Grades 1-3
})

const toggleChartType = (type) => {
    showPermanent.value = type === 'permanent'
}

// Dental chart functionality
onMounted(() => {
    // Initialize conditions for the form
    initializeConditionsForForm()
    
    // Initialize form persistence
    initializeForm()
    setupAutoSave()

    nextTick(() => {
        initializeDentalChart()
    })
})

// Initialize conditions for form binding
const initializeConditionsForForm = () => {
    oralHealthConditions.forEach(condition => {
        form.conditions[condition.key + '_present'] = false
        form.conditions[condition.key + '_date'] = ''
        if (condition.key === 'others_specify') {
            form.conditions[condition.key + '_specification'] = ''
        }
    })
}

const initializeDentalChart = () => {
    createUpperTeeth()
    createLowerTeeth()
    createTemporaryTeeth()
    addToothSelectionFunctionality()
    addPanelFunctionality()

    // Set initial chart based on grade level
    if (shouldShowTemporaryTeeth.value) {
        showPermanent.value = false
    }
}

const createUpperTeeth = () => {
    const container = document.getElementById('upper-teeth')
    if (!container) return

    const toothNumbers = [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28]
    const toothTypes = ['Wisdom Tooth', 'Molar', 'Molar', 'Premolar', 'Premolar', 'Canine', 'Incisor', 'Incisor', 'Incisor', 'Incisor', 'Canine', 'Premolar', 'Premolar', 'Molar', 'Molar', 'Wisdom Tooth']

    container.innerHTML = ''
    for (let i = 0; i < toothNumbers.length; i++) {
        const tooth = document.createElement('div')
        tooth.className = 'tooth'
        tooth.textContent = toothNumbers[i]
        tooth.setAttribute('data-number', toothNumbers[i])
        tooth.setAttribute('data-type', toothTypes[i])

        const curve = Math.sin((i / (toothNumbers.length - 1)) * Math.PI) * 8
        tooth.style.left = `${i * 35 + 20}px`
        tooth.style.top = `${15 - curve}px`

        container.appendChild(tooth)
    }
}

const createLowerTeeth = () => {
    const container = document.getElementById('lower-teeth')
    if (!container) return

    const toothNumbers = [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38]
    const toothTypes = ['Wisdom Tooth', 'Molar', 'Molar', 'Premolar', 'Premolar', 'Canine', 'Incisor', 'Incisor', 'Incisor', 'Incisor', 'Canine', 'Premolar', 'Premolar', 'Molar', 'Molar', 'Wisdom Tooth']

    container.innerHTML = ''
    for (let i = 0; i < toothNumbers.length; i++) {
        const tooth = document.createElement('div')
        tooth.className = 'tooth'
        tooth.textContent = toothNumbers[i]
        tooth.setAttribute('data-number', toothNumbers[i])
        tooth.setAttribute('data-type', toothTypes[i])

        const curve = Math.sin((i / (toothNumbers.length - 1)) * Math.PI) * 8
        tooth.style.left = `${i * 35 + 20}px`
        tooth.style.top = `${15 + curve}px`

        container.appendChild(tooth)
    }
}

const createTemporaryTeeth = () => {
    const upperContainer = document.getElementById('upper-primary')
    const lowerContainer = document.getElementById('lower-primary')
    if (!upperContainer || !lowerContainer) return

    // Upper Temporary Teeth
    const upperNumbers = [55, 54, 53, 52, 51, 61, 62, 63, 64, 65]
    const upperTypes = ['Molar', 'Molar', 'Canine', 'Incisor', 'Incisor', 'Incisor', 'Incisor', 'Canine', 'Molar', 'Molar']

    upperContainer.innerHTML = ''
    for (let i = 0; i < upperNumbers.length; i++) {
        const tooth = document.createElement('div')
        tooth.className = 'tooth primary-tooth'
        tooth.textContent = upperNumbers[i]
        tooth.setAttribute('data-number', upperNumbers[i])
        tooth.setAttribute('data-type', upperTypes[i])
        tooth.setAttribute('data-category', 'Temporary')

        const curve = Math.sin((i / (upperNumbers.length - 1)) * Math.PI) * 6
        tooth.style.left = `${i * 55 + 30}px`
        tooth.style.top = `${15 - curve}px`

        upperContainer.appendChild(tooth)
    }

    // Lower Temporary Teeth
    const lowerNumbers = [85, 84, 83, 82, 81, 71, 72, 73, 74, 75]
    const lowerTypes = ['Molar', 'Molar', 'Canine', 'Incisor', 'Incisor', 'Incisor', 'Incisor', 'Canine', 'Molar', 'Molar']

    lowerContainer.innerHTML = ''
    for (let i = 0; i < lowerNumbers.length; i++) {
        const tooth = document.createElement('div')
        tooth.className = 'tooth primary-tooth'
        tooth.textContent = lowerNumbers[i]
        tooth.setAttribute('data-number', lowerNumbers[i])
        tooth.setAttribute('data-type', lowerTypes[i])
        tooth.setAttribute('data-category', 'Temporary')

        const curve = Math.sin((i / (lowerNumbers.length - 1)) * Math.PI) * 6
        tooth.style.left = `${i * 55 + 30}px`
        tooth.style.top = `${15 + curve}px`

        lowerContainer.appendChild(tooth)
    }
}

const addToothSelectionFunctionality = () => {
    const teeth = document.querySelectorAll('.tooth')

    teeth.forEach(tooth => {
        tooth.addEventListener('click', function() {
            if (selectedTooth) {
                selectedTooth.classList.remove('selected')
            }

            selectedTooth = this
            this.classList.add('selected')

            openSidePanel()
            updatePanelContent()
        })
    })
}

const openSidePanel = () => {
    const panel = document.getElementById('symbol-panel')
    if (panel) panel.classList.add('open')
}

const closeSidePanel = () => {
    const panel = document.getElementById('symbol-panel')
    if (panel) panel.classList.remove('open')

    if (selectedTooth) {
        selectedTooth.classList.remove('selected')
        selectedTooth = null
    }
}

const updatePanelContent = () => {
    if (!selectedTooth) return

    const toothNumber = selectedTooth.dataset.number
    const toothType = selectedTooth.dataset.type
    const category = selectedTooth.dataset.category || 'Permanent'

    const titleElement = document.getElementById('selected-tooth-title')
    if (titleElement) {
        titleElement.textContent = `${category} Tooth ${toothNumber} (${toothType})`
    }

    const currentSymbols = toothSymbols[toothNumber] || []
    const symbolsDisplay = document.getElementById('tooth-symbols')
    if (symbolsDisplay) {
        symbolsDisplay.textContent = currentSymbols.length > 0 ? currentSymbols.join(', ') : 'None'
    }

    const symbolBtns = document.querySelectorAll('.symbol-btn')
    symbolBtns.forEach(btn => {
        const symbol = btn.dataset.symbol
        if (currentSymbols.includes(symbol)) {
            btn.classList.add('selected')
        } else {
            btn.classList.remove('selected')
        }
    })
}

const addPanelFunctionality = () => {
    const closeBtn = document.getElementById('close-panel')
    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidePanel)
    }

    const symbolBtns = document.querySelectorAll('.symbol-btn')
    symbolBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!selectedTooth) return

            const symbol = this.dataset.symbol
            const toothNumber = selectedTooth.dataset.number

            if (!toothSymbols[toothNumber]) {
                toothSymbols[toothNumber] = []
            }

            const symbolIndex = toothSymbols[toothNumber].indexOf(symbol)
            if (symbolIndex > -1) {
                toothSymbols[toothNumber].splice(symbolIndex, 1)
                this.classList.remove('selected')
            } else {
                toothSymbols[toothNumber].push(symbol)
                this.classList.add('selected')
            }

            updateToothSymbolDisplay()
            updatePanelContent()
            updateFormData()
        })
    })

    const clearBtn = document.getElementById('clear-tooth-symbols')
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (!selectedTooth) return

            const toothNumber = selectedTooth.dataset.number
            toothSymbols[toothNumber] = []

            updateToothSymbolDisplay()
            updatePanelContent()
            updateFormData()
        })
    }

    const confirmBtn = document.getElementById('confirm-symbols')
    if (confirmBtn) {
        confirmBtn.addEventListener('click', closeSidePanel)
    }
}

const updateToothSymbolDisplay = () => {
    if (!selectedTooth) return

    const toothNumber = selectedTooth.dataset.number
    const symbols = toothSymbols[toothNumber] || []

    const existingSymbol = selectedTooth.querySelector('.tooth-symbol')
    if (existingSymbol) {
        existingSymbol.remove()
    }

    if (symbols.length > 0) {
        selectedTooth.classList.add('has-symbol')

        const symbolElement = document.createElement('div')
        symbolElement.className = 'tooth-symbol'
        symbolElement.textContent = symbols.length > 1 ? symbols.length : symbols[0]
        symbolElement.title = symbols.join(', ')

        selectedTooth.appendChild(symbolElement)
    } else {
        selectedTooth.classList.remove('has-symbol')
    }
}

const updateFormData = () => {
    form.tooth_symbols = { ...toothSymbols }
}

// Validation rules for oral health examination
const validateForm = () => {
    const errors = {}

    // Required permanent teeth fields
    if (form.permanent_index_dft === '' || form.permanent_index_dft === null) {
        errors.permanent_index_dft = 'Permanent Index D.F.T. is required'
    }
    if (form.permanent_teeth_decayed === '' || form.permanent_teeth_decayed === null) {
        errors.permanent_teeth_decayed = 'Permanent teeth decayed count is required'
    }
    if (form.permanent_teeth_filled === '' || form.permanent_teeth_filled === null) {
        errors.permanent_teeth_filled = 'Permanent teeth filled count is required'
    }
    if (form.permanent_teeth_missing === '' || form.permanent_teeth_missing === null) {
        errors.permanent_teeth_missing = 'Permanent teeth missing count is required'
    }
    if (form.permanent_total_dft === '' || form.permanent_total_dft === null) {
        errors.permanent_total_dft = 'Permanent total D.F.T. is required'
    }
    if (form.permanent_for_extraction === '' || form.permanent_for_extraction === null) {
        errors.permanent_for_extraction = 'Permanent teeth for extraction count is required'
    }
    if (form.permanent_for_filling === '' || form.permanent_for_filling === null) {
        errors.permanent_for_filling = 'Permanent teeth for filling count is required'
    }

    // Required temporary teeth fields
    if (form.temporary_index_dft === '' || form.temporary_index_dft === null) {
        errors.temporary_index_dft = 'Temporary Index d.f.t. is required'
    }
    if (form.temporary_teeth_decayed === '' || form.temporary_teeth_decayed === null) {
        errors.temporary_teeth_decayed = 'Temporary teeth decayed count is required'
    }
    if (form.temporary_teeth_filled === '' || form.temporary_teeth_filled === null) {
        errors.temporary_teeth_filled = 'Temporary teeth filled count is required'
    }
    if (form.temporary_total_dft === '' || form.temporary_total_dft === null) {
        errors.temporary_total_dft = 'Temporary total d.f.t. is required'
    }
    if (form.temporary_for_extraction === '' || form.temporary_for_extraction === null) {
        errors.temporary_for_extraction = 'Temporary teeth for extraction count is required'
    }
    if (form.temporary_for_filling === '' || form.temporary_for_filling === null) {
        errors.temporary_for_filling = 'Temporary teeth for filling count is required'
    }

    return errors
}

const errors = ref({})

const submit = () => {
    updateFormData()
    
    // Convert conditions to database format
    const dbConditions = {}
    oralHealthConditions.forEach(condition => {
        if (form.conditions[condition.key + '_present']) {
            let conditionValue = form.conditions[condition.key + '_date'] || new Date().toLocaleDateString('en-US', {month: '2-digit', day: '2-digit', year: '2-digit'})
            
            // For "others_specify", append the specification text
            if (condition.key === 'others_specify' && form.conditions[condition.key + '_specification']) {
                conditionValue += ` - ${form.conditions[condition.key + '_specification']}`
            }
            
            dbConditions[condition.key] = conditionValue
        }
    })
    
    // Update conditions in database format
    form.conditions = dbConditions

    // Validate form
    const validationErrors = validateForm()
    errors.value = validationErrors

    if (Object.keys(validationErrors).length > 0) {
        console.log('Form validation failed:', validationErrors)
        return
    }

    console.log('Submitting form:', form.data())

    form.post(route('oral-health-examination.store'), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Form submitted successfully')
            // Clear saved form data on successful submission
            onSubmitSuccess()
            errors.value = {} // Clear validation errors on success
            sessionStorage.setItem(`currentGrade_${props.student.id}`, `Grade ${gradeLevel.value}`)
        },
        onError: (serverErrors) => {
            console.error('Form submission errors:', serverErrors)
            // Merge server errors with client validation errors
            errors.value = { ...errors.value, ...serverErrors }
        }
    })
}

const cancel = () => {
    // Check if there are unsaved changes
    const hasChanges = hasUnsavedChanges()
    onCancel(hasChanges)
    window.history.back()
}
</script>

<style>
/* Compact Dental Chart Styles for PHMS Integration */
.dental-chart-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin: 0 auto;
    max-width: 800px;
}

.dental-chart.compact {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.teeth-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.teeth-row.compact {
    position: relative;
    width: 600px;
    height: 60px;
}

.teeth-row.primary.compact {
    width: 580px;
}

.arch-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #666;
    text-align: center;
}

.tooth {
    position: absolute;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(145deg, #f0f0f0, #e0e0e0);
    border: 2px solid #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #333;
}

.tooth:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    z-index: 5;
}

.primary-tooth {
    background: linear-gradient(145deg, #ff9a9e, #fecfef);
    border: 2px solid #ff6b9d;
    width: 28px;
    height: 28px;
    font-size: 9px;
}

.primary-tooth:hover {
    box-shadow: 0 4px 12px rgba(255, 107, 157, 0.4);
}

.tooth.selected {
    border: 3px solid #ff4757 !important;
    box-shadow: 0 0 15px rgba(255, 71, 87, 0.5) !important;
    z-index: 10;
}

.tooth.has-symbol {
    position: relative;
}

.tooth-symbol {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: bold;
    z-index: 10;
    pointer-events: none;
}

/* Chart Selector */
.chart-selector {
    display: flex;
    gap: 8px;
    background: #e9ecef;
    padding: 4px;
    border-radius: 8px;
}

.chart-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    color: #666;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.chart-btn:hover {
    background: rgba(255, 255, 255, 0.5);
}

.chart-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Side Panel Styles */
.side-panel {
    position: fixed;
    top: 60px;
    right: -350px;
    width: 350px;
    height: calc(100vh - 60px);
    background: white;
    box-shadow: -5px 0 20px rgba(0,0,0,0.1);
    transition: right 0.3s ease;
    z-index: 1000;
    overflow-y: auto;
}

.side-panel.open {
    right: 0;
}

.panel-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.panel-header h3 {
    margin: 0;
    font-size: 1.1rem;
}

.close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.3s ease;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.panel-content {
    padding: 16px;
}

.current-symbols-display {
    background: #f8f9ff;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #667eea;
    font-size: 0.9rem;
}

.symbol-categories {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.symbol-group h4 {
    color: #333;
    margin: 0 0 8px 0;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 4px;
}

.symbol-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
}

.symbol-btn {
    padding: 8px 6px;
    border: 2px solid #e0e0e0;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    font-weight: bold;
    font-size: 0.8rem;
}

.symbol-btn:hover {
    border-color: #667eea;
    background: #f8f9ff;
    transform: translateY(-1px);
}

.symbol-btn.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.panel-actions {
    display: flex;
    gap: 8px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 2px solid #f0f0f0;
}

.action-btn {
    flex: 1;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.action-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.action-btn.secondary {
    background: #f8f9fa;
    color: #666;
    border: 2px solid #e0e0e0;
}

.action-btn.secondary:hover {
    background: #e9ecef;
    border-color: #ccc;
}

/* Form Styles */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: #374151;
}

</style>
