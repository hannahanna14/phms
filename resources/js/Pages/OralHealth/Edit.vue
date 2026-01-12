<template>
    <Head title="Edit Oral Health Examination" />
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 p-6">
        <div class="max-w-6xl mx-auto">
            <!-- Enhanced Header -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 mb-8 backdrop-blur-sm">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <div class="w-14 h-14 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="pi pi-pencil text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent mb-2">Edit Oral Health Examination</h1>
                            <p class="text-slate-600 font-medium">Update dental assessment for {{ student?.full_name || 'Loading...' }}</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-slate-600 font-semibold">Student:</span>
                                <div class="font-bold text-slate-900">{{ student?.full_name || 'Loading...' }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Grade:</span>
                                <div class="font-bold text-slate-900">{{ selectedGrade }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">LRN:</span>
                                <div class="font-mono font-bold text-slate-900">{{ student?.lrn || 'Not Available' }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Section:</span>
                                <div class="font-bold text-slate-900">{{ student?.section || 'Not Assigned' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 backdrop-blur-sm">

                <!-- Loading/Error State -->
                <div v-if="!oralHealthExamination?.id" class="text-center py-8">
                    <div class="text-gray-500">
                        <i class="pi pi-spinner pi-spin text-2xl mb-4"></i>
                        <p>Loading examination data...</p>
                    </div>
                </div>

                <!-- Draft Restored Notification -->
                <div v-if="showDraftNotification" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="pi pi-info-circle text-blue-600 mr-2"></i>
                        <span class="text-blue-800 text-sm">
                            <strong>Draft restored:</strong> Your previous changes have been recovered.
                        </span>
                        <button @click="showDraftNotification = false" class="ml-auto text-blue-600 hover:text-blue-800">
                            <i class="pi pi-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Interactive Dental Chart -->
                <div v-if="oralHealthExamination?.id" class="mb-8">
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
                                <div id="edit-upper-teeth" class="teeth-row upper compact"></div>
                                <div id="edit-lower-teeth" class="teeth-row lower compact"></div>
                                <div class="arch-label">Lower Teeth</div>
                            </div>
                        </div>

                        <div v-show="!showPermanent" class="dental-chart compact">
                            <h3 class="text-center text-lg font-semibold mb-4">Temporary Teeth (20 teeth)</h3>
                            <div class="teeth-section">
                                <div class="arch-label">Upper Temporary Teeth</div>
                                <div id="edit-upper-primary" class="teeth-row upper primary compact"></div>
                                <div id="edit-lower-primary" class="teeth-row lower primary compact"></div>
                                <div class="arch-label">Lower Temporary Teeth</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form v-if="oralHealthExamination?.id || true" @submit.prevent="submit" class="space-y-6">
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
                                <small v-if="errors?.permanent_index_dft" class="text-red-500">{{ errors.permanent_index_dft }}</small>
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
                                <small v-if="errors?.permanent_teeth_decayed" class="text-red-500">{{ errors.permanent_teeth_decayed }}</small>
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
                                <small v-if="errors?.permanent_teeth_filled" class="text-red-500">{{ errors.permanent_teeth_filled }}</small>
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
                                <small v-if="errors?.permanent_teeth_missing" class="text-red-500">{{ errors.permanent_teeth_missing }}</small>
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
                                <small v-if="errors?.permanent_total_dft" class="text-red-500">{{ errors.permanent_total_dft }}</small>
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
                                <small v-if="errors?.permanent_for_extraction" class="text-red-500">{{ errors.permanent_for_extraction }}</small>
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
                                <small v-if="errors?.permanent_for_filling" class="text-red-500">{{ errors.permanent_for_filling }}</small>
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
                                <small v-if="errors?.temporary_index_dft" class="text-red-500">{{ errors.temporary_index_dft }}</small>
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
                                <small v-if="errors?.temporary_teeth_decayed" class="text-red-500">{{ errors.temporary_teeth_decayed }}</small>
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
                                <small v-if="errors?.temporary_teeth_filled" class="text-red-500">{{ errors.temporary_teeth_filled }}</small>
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
                                <small v-if="errors?.temporary_total_dft" class="text-red-500">{{ errors.temporary_total_dft }}</small>
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
                                <small v-if="errors?.temporary_for_extraction" class="text-red-500">{{ errors.temporary_for_extraction }}</small>
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
                                <small v-if="errors?.temporary_for_filling" class="text-red-500">{{ errors.temporary_for_filling }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- TEST SECTION -->

                    <!-- Oral Health Conditions Section - SIMPLIFIED -->
                    <div class="border rounded-lg p-6 mt-6">
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

                    <div class="flex justify-end gap-4 mt-6">
                        <Button
                            type="button"
                            label="Cancel"
                            severity="secondary"
                            @click="cancel"
                        />
                        <Button
                            type="submit"
                            label="Update"
                            :loading="form.processing"
                        />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Side Panel for Symbol Selection -->
    <div id="edit-symbol-panel" class="side-panel">
        <div class="panel-header">
            <h3 id="edit-selected-tooth-title">Select a tooth</h3>
            <button id="edit-close-panel" class="close-btn" type="button">&times;</button>
        </div>

        <div class="panel-content">
            <div class="current-symbols-display" id="edit-current-symbols-display">
                <strong>Current conditions:</strong> <span id="edit-tooth-symbols">None</span>
            </div>

            <div class="symbol-categories">
                <div class="symbol-group">
                    <h4>Conditions</h4>
                    <div class="symbol-buttons">
                        <button class="symbol-btn" data-symbol="X" v-tooltip.top="'Carious tooth indicated for extraction'" type="button">X</button>
                        <button class="symbol-btn" data-symbol="D" v-tooltip.top="'Carious tooth indicated for filling'" type="button">D</button>
                        <button class="symbol-btn" data-symbol="RF" v-tooltip.top="'Root fragment'" type="button">RF</button>
                        <button class="symbol-btn" data-symbol="M" v-tooltip.top="'Missing tooth'" type="button">M</button>
                    </div>
                </div>

                <div class="symbol-group">
                    <h4>Treatments</h4>
                    <div class="symbol-buttons">
                        <button class="symbol-btn" data-symbol="F2" v-tooltip.top="'Permanent filled tooth with recurrence of decay'" type="button">F2</button>
                        <button class="symbol-btn" data-symbol="√" v-tooltip.top="'Sound/erupted Permanent tooth'" type="button">√</button>
                        <button class="symbol-btn" data-symbol="PFS" v-tooltip.top="'Pit and Fissure Sealant'" type="button">PFS</button>
                        <button class="symbol-btn" data-symbol="JC" v-tooltip.top="'Jacket Crown'" type="button">JC</button>
                    </div>
                </div>

                <div class="symbol-group">
                    <h4>Prosthetics</h4>
                    <div class="symbol-buttons">
                        <button class="symbol-btn" data-symbol="P" v-tooltip.top="'Pontic'" type="button">P</button>
                        <button class="symbol-btn" data-symbol="RPD" v-tooltip.top="'Removable Partial Denture'" type="button">RPD</button>
                        <button class="symbol-btn" data-symbol="FB" v-tooltip.top="'Fixed Bridge'" type="button">FB</button>
                        <button class="symbol-btn" data-symbol="CD" v-tooltip.top="'Complete Denture'" type="button">CD</button>
                    </div>
                </div>

                <div class="symbol-group">
                    <h4>Restorations</h4>
                    <div class="symbol-buttons">
                        <button class="symbol-btn" data-symbol="GI" v-tooltip.top="'Glass Ionomer'" type="button">GI</button>
                        <button class="symbol-btn" data-symbol="CO" v-tooltip.top="'Composite'" type="button">CO</button>
                        <button class="symbol-btn" data-symbol="AM" v-tooltip.top="'Amalgam'" type="button">AM</button>
                    </div>
                </div>
            </div>

            <div class="panel-actions">
                <button id="edit-clear-tooth-symbols" class="action-btn secondary" type="button">Clear Tooth</button>
                <button id="edit-confirm-symbols" class="action-btn primary" type="button">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Dialog -->
    <ConfirmDialog />
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from 'primevue/useconfirm'
import { useFormPersistence } from '@/composables/useFormPersistence'
import { computed, ref, onMounted, nextTick } from 'vue'
import { useToastStore } from '@/Stores/toastStore'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css'
// Import page-specific styles
import '../../../css/pages/OralHealth/Edit.css'
// Import dental chart styles
import '../../../css/pages/OralHealth/DentalChart.css'

// Toast store
const confirm = useConfirm()
const { showSuccess, showError } = useToastStore()

const props = defineProps({
    student: {
        type: Object,
        default: () => ({})
    },
    oralHealthExamination: {
        type: Object,
        default: () => ({})
    },
    selectedGrade: {
        type: String,
        default: ''
    },
    errors: {
        type: Object,
        default: () => ({})
    }
})

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

// Grade level logic - define this first
const getCurrentGradeRange = (grade) => {
    const gradeRanges = {
        'Kinder 2': [{ key: 'kinder', label: 'Kinder' }],
        'Grade 1': [{ key: 'grade_1_7', label: 'Grade 1/7' }],
        'Grade 2': [{ key: 'grade_2_8', label: 'Grade 2/8' }],
        'Grade 3': [{ key: 'grade_3_9', label: 'Grade 3/9' }],
        'Grade 4': [{ key: 'grade_4_10', label: 'Grade 4/10' }],
        'Grade 5': [{ key: 'grade_5_11', label: 'Grade 5/11' }],
        'Grade 6': [{ key: 'grade_6_12', label: 'Grade 6/12' }]
    }

    return gradeRanges[grade] || [{ key: 'grade_1_7', label: 'Grade 1/7' }]
}

// Initialize conditions structure for current grade only
const initializeConditions = () => {
    const currentGrade = props.selectedGrade || props.oralHealthExamination?.grade_level || '';
    const currentGradeRanges = getCurrentGradeRange(currentGrade)
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
    examination_date: props.oralHealthExamination?.examination_date ?
        new Date(props.oralHealthExamination.examination_date).toISOString().split('T')[0] :
        new Date().toISOString().split('T')[0],
    permanent_index_dft: props.oralHealthExamination?.permanent_index_dft || 0,
    permanent_teeth_decayed: props.oralHealthExamination?.permanent_teeth_decayed || 0,
    permanent_teeth_filled: props.oralHealthExamination?.permanent_teeth_filled || 0,
    permanent_teeth_missing: props.oralHealthExamination?.permanent_teeth_missing || 0,
    permanent_total_dft: props.oralHealthExamination?.permanent_total_dft || 0,
    permanent_for_extraction: props.oralHealthExamination?.permanent_for_extraction || 0,
    permanent_for_filling: props.oralHealthExamination?.permanent_for_filling || 0,
    temporary_index_dft: props.oralHealthExamination?.temporary_index_dft || 0,
    temporary_teeth_decayed: props.oralHealthExamination?.temporary_teeth_decayed || 0,
    temporary_teeth_filled: props.oralHealthExamination?.temporary_teeth_filled || 0,
    temporary_teeth_missing: props.oralHealthExamination?.temporary_teeth_missing || 0,
    temporary_total_dft: props.oralHealthExamination?.temporary_total_dft || 0,
    temporary_for_extraction: props.oralHealthExamination?.temporary_for_extraction || 0,
    temporary_for_filling: props.oralHealthExamination?.temporary_for_filling || 0,
    grade_level: props.selectedGrade || props.oralHealthExamination?.grade_level || '',
    school_year: props.oralHealthExamination?.school_year || '2024-2025',
    tooth_symbols: props.oralHealthExamination?.tooth_symbols || {},
    conditions: {}
})

// Dental chart state
const showPermanent = ref(true)
let selectedTooth = null
let toothSymbols = {}

// Grade level logic
const gradeLevel = computed(() => props.selectedGrade || props.oralHealthExamination?.grade_level || '')

// const relevantGradeRanges = computed(() => getCurrentGradeRange(gradeLevel.value))


const toggleChartType = (type) => {
    showPermanent.value = type === 'permanent'
}

// Set up form persistence
const {
    showDraftNotification,
    initializeForm,
    setupAutoSave,
    onSubmitSuccess,
    onCancel,
    hasUnsavedChanges
} = useFormPersistence(`oral_health_exam_edit_${props.oralHealthExamination?.id || 'temp'}`, form, {
    excludeFields: [], // Save all form fields for edit
    autoSave: true,
    showNotification: true
})

// Submit function
const submitForm = () => {
    // Convert conditions back to database format
    const dbConditions = {}
    oralHealthConditions.forEach(condition => {
        if (form.conditions[condition.key + '_present']) {
            dbConditions[condition.key] = form.conditions[condition.key + '_date'] || new Date().toLocaleDateString('en-US', {month: '2-digit', day: '2-digit', year: '2-digit'})
        }
    })

    console.log('Submitting form with data:', {
        formData: form.data(),
        toothSymbols: toothSymbols,
        conditions: dbConditions
    })

    // Validate required fields
    if (!form.grade_level) {
        showError('Grade Level Required', 'Grade level is required')
        return
    }

    if (!form.school_year) {
        showError('School Year Required', 'School year is required')
        return
    }

    // Update tooth symbols from the dental chart
    form.tooth_symbols = toothSymbols

    // Update conditions in database format
    form.conditions = dbConditions

    form.put(`/pupil-health/oral-health/${props.oralHealthExamination.id}`, {
        onSuccess: (response) => {
            console.log('Form submission successful', response)
            showSuccess('Success', 'Oral health examination record updated successfully!')
            onSubmitSuccess()
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors)
            console.log('Form data that failed:', form.data())
            console.log('Validation errors:', Object.keys(errors))

            // Log each validation error in detail
            Object.keys(errors).forEach(field => {
                console.error(`Validation error for ${field}:`, errors[field])
            })

            // Show user-friendly error message with specific errors
            const errorMessages = Object.keys(errors).map(field => `${field}: ${errors[field]}`).join('\n')
            showError('Form Submission Failed', `Form submission failed with validation errors:\n\n${errorMessages}`)
        },
        onFinish: () => {
            console.log('Form submission finished')
        }
    })
}

// Debug logging removed to prevent errors

// Initialize conditions data
const initializeConditionsData = () => {
    // Initialize all conditions as false
    oralHealthConditions.forEach(condition => {
        form.conditions[condition.key + '_present'] = false
        form.conditions[condition.key + '_date'] = ''
        if (condition.key === 'others_specify') {
            form.conditions[condition.key + '_specification'] = ''
        }
    })

    // Load existing conditions if they exist
    if (props.oralHealthExamination?.conditions) {
        const existingConditions = typeof props.oralHealthExamination.conditions === 'string'
            ? JSON.parse(props.oralHealthExamination.conditions)
            : props.oralHealthExamination.conditions

        // Handle seeded format: {"gingivitis": "10/06/24"}
        if (existingConditions && typeof existingConditions === 'object') {
            Object.keys(existingConditions).forEach(conditionKey => {
                form.conditions[conditionKey + '_present'] = true

                let conditionValue = existingConditions[conditionKey]

                // For "others_specify", check if it contains specification text
                if (conditionKey === 'others_specify' && conditionValue.includes(' - ')) {
                    const parts = conditionValue.split(' - ')
                    form.conditions[conditionKey + '_date'] = parts[0]
                    form.conditions[conditionKey + '_specification'] = parts[1]
                } else {
                    form.conditions[conditionKey + '_date'] = conditionValue
                }
            })
        }
    }
}

// Initialize form persistence and dental chart
onMounted(() => {
    initializeForm()
    setupAutoSave()
    initializeConditionsData()

    nextTick(() => {
        initializeDentalChart()
    })
})

const initializeDentalChart = () => {
    createUpperTeeth()
    createLowerTeeth()
    createTemporaryTeeth()
    addToothSelectionFunctionality()
    addPanelFunctionality()

    // Load existing tooth symbols if available
    if (props.oralHealthExamination?.tooth_symbols) {
        toothSymbols = { ...props.oralHealthExamination.tooth_symbols }

        // Ensure all tooth symbols are arrays
        Object.keys(toothSymbols).forEach(toothNum => {
            if (!Array.isArray(toothSymbols[toothNum])) {
                toothSymbols[toothNum] = []
            }
        })

        renderToothSymbols()
    }
}

// Dental chart creation functions (matching create form exactly)
const createUpperTeeth = () => {
    const container = document.getElementById('edit-upper-teeth')
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
        tooth.dataset.toothNumber = toothNumbers[i]

        const curve = Math.sin((i / (toothNumbers.length - 1)) * Math.PI) * 8
        tooth.style.left = `${i * 35 + 20}px`
        tooth.style.top = `${15 - curve}px`

        container.appendChild(tooth)
    }
}

const createLowerTeeth = () => {
    const container = document.getElementById('edit-lower-teeth')
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
        tooth.dataset.toothNumber = toothNumbers[i]

        const curve = Math.sin((i / (toothNumbers.length - 1)) * Math.PI) * 8
        tooth.style.left = `${i * 35 + 20}px`
        tooth.style.top = `${15 + curve}px`

        container.appendChild(tooth)
    }
}

const createTemporaryTeeth = () => {
    const upperContainer = document.getElementById('edit-upper-primary')
    const lowerContainer = document.getElementById('edit-lower-primary')
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
        tooth.dataset.toothNumber = upperNumbers[i]

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
        tooth.dataset.toothNumber = lowerNumbers[i]

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
    const panel = document.getElementById('edit-symbol-panel')
    if (panel) panel.classList.add('open')
}

const closeSidePanel = () => {
    const panel = document.getElementById('edit-symbol-panel')
    if (panel) panel.classList.remove('open')

    if (selectedTooth) {
        selectedTooth.classList.remove('selected')
        selectedTooth = null
    }
}

const updatePanelContent = () => {
    if (!selectedTooth) return

    const toothNumber = selectedTooth.dataset.number || selectedTooth.dataset.toothNumber
    const toothType = selectedTooth.dataset.type
    const category = selectedTooth.dataset.category || 'Permanent'

    const titleElement = document.getElementById('edit-selected-tooth-title')
    if (titleElement) {
        titleElement.textContent = `${category} Tooth ${toothNumber} (${toothType})`
    }

    const currentSymbols = toothSymbols[toothNumber] || []

    // Ensure currentSymbols is an array
    const symbolsArray = Array.isArray(currentSymbols) ? currentSymbols : []

    const symbolsDisplay = document.getElementById('edit-tooth-symbols')
    if (symbolsDisplay) {
        symbolsDisplay.textContent = symbolsArray.length > 0 ? symbolsArray.join(', ') : 'None'
    }

    const symbolBtns = document.querySelectorAll('.symbol-btn')
    symbolBtns.forEach(btn => {
        const symbol = btn.dataset.symbol
        if (symbolsArray.includes(symbol)) {
            btn.classList.add('selected')
        } else {
            btn.classList.remove('selected')
        }
    })
}

const addPanelFunctionality = () => {
    const closeBtn = document.getElementById('edit-close-panel')
    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidePanel)
    }

    // Add confirm button functionality
    const confirmBtn = document.getElementById('edit-confirm-symbols')
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            if (selectedTooth) {
                closeSidePanel()
            }
        })
    }

    // Add clear button functionality
    const clearBtn = document.getElementById('edit-clear-tooth-symbols')
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (!selectedTooth) return

            const toothNumber = selectedTooth.dataset.number || selectedTooth.dataset.toothNumber

            // Clear all symbols for this tooth
            if (toothSymbols[toothNumber]) {
                toothSymbols[toothNumber] = []
            }

            // Update visual display
            renderToothSymbols()
            updatePanelContent()

            // Update form data
            form.tooth_symbols = { ...toothSymbols }
        })
    }

    const symbolBtns = document.querySelectorAll('.symbol-btn')
    symbolBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!selectedTooth) return

            const toothNumber = selectedTooth.dataset.number || selectedTooth.dataset.toothNumber
            const symbol = this.dataset.symbol

            if (!toothSymbols[toothNumber]) {
                toothSymbols[toothNumber] = []
            }

            if (toothSymbols[toothNumber].includes(symbol)) {
                // Remove symbol
                toothSymbols[toothNumber] = toothSymbols[toothNumber].filter(s => s !== symbol)
                this.classList.remove('selected')
            } else {
                // Add symbol
                toothSymbols[toothNumber].push(symbol)
                this.classList.add('selected')
            }

            // Update visual display
            renderToothSymbols()
            updatePanelContent()

            // Update form data
            form.tooth_symbols = { ...toothSymbols }
        })
    })
}

const renderToothSymbols = () => {
    // Clear all existing symbols first
    document.querySelectorAll('.tooth').forEach(tooth => {
        tooth.classList.remove('has-symbol')
        const existingSymbol = tooth.querySelector('.tooth-symbol')
        if (existingSymbol) {
            existingSymbol.remove()
        }
    })

    // Render existing tooth symbols on the chart
    Object.keys(toothSymbols).forEach(toothNum => {
        const toothElement = document.querySelector(`[data-tooth-number="${toothNum}"], [data-number="${toothNum}"]`)

        // Ensure toothSymbols[toothNum] is an array
        if (!Array.isArray(toothSymbols[toothNum])) {
            toothSymbols[toothNum] = []
        }

        if (toothElement && toothSymbols[toothNum] && toothSymbols[toothNum].length > 0) {
            toothElement.classList.add('has-symbol')

            // Create symbol display - show count instead of symbol codes
            const symbolElement = document.createElement('div')
            symbolElement.className = 'tooth-symbol'
            symbolElement.textContent = toothSymbols[toothNum].length
            toothElement.appendChild(symbolElement)
        }
    })
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
    // Show confirmation dialog
    confirm.require({
        message: 'Are you sure you want to update this oral health examination record?',
        header: 'Confirm Update',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Update Record',
            severity: 'success'
        },
        accept: () => {
            // Proceed with form submission
            submitForm()
        }
    })
}

const cancel = () => {
    // Navigate directly without form persistence dialog
    window.location.href = `/pupil-health/oral-health/${props.student.id}?grade=${props.selectedGrade.replace('Grade ', '')}`
}
</script>
