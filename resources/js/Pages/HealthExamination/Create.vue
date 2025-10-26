<template>
    <Head title="Create Health Examination" />
    <div class="health-examination-form">
        <div class="white-container">
            <!-- Student Information Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Pupil Health Examination</h1>
                <p class="text-gray-600">Naawan Central School</p>
            </div>
            
            <!-- Student Details -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6 grid grid-cols-2 gap-4">
                <div>
                    <span class="font-semibold text-gray-700">Student:</span> {{ student.full_name }}
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Grade Level:</span> {{ student.grade_level }}
                </div>
                <div>
                    <span class="font-semibold text-gray-700">LRN:</span> {{ student.lrn || '10000000001' }}
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Section:</span> {{ student.section || 'Not Assigned' }}
                </div>
            </div>
            
            <h2 class="text-lg font-semibold mb-4">Create Student Health Examination</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Vital Signs -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group">
                        <label>Temperature (°C) <span class="text-red-500">*</span></label>
                        <InputText 
                            v-model="form.temperature" 
                            :class="['w-full', { 'p-invalid': errors.temperature }]" 
                            type="number" 
                            step="0.1" 
                            min="35" 
                            max="42" 
                        />
                        <small class="text-gray-500">Normal range: 35°C - 42°C</small>
                        <small class="text-red-500" v-if="errors.temperature">{{ errors.temperature }}</small>
                    </div>
                    <div class="form-group">
                        <label>Heart Rate (bpm) <span class="text-red-500">*</span></label>
                        <InputText 
                            v-model="form.heart_rate" 
                            :class="['w-full', { 'p-invalid': errors.heart_rate }]" 
                            type="number" 
                            min="40" 
                            max="200" 
                        />
                        <small class="text-gray-500">Normal range: 40-200 bpm</small>
                        <small class="text-red-500" v-if="errors.heart_rate">{{ errors.heart_rate }}</small>
                    </div>
                    <div class="form-group">
                        <label>Height (cm) <span class="text-red-500">*</span></label>
                        <InputText 
                            v-model="form.height" 
                            :class="['w-full', { 'p-invalid': errors.height }]" 
                            type="number" 
                            step="0.1" 
                            min="50" 
                            max="200" 
                        />
                        <small class="text-gray-500">Range: 50-200 cm</small>
                        <small class="text-red-500" v-if="errors.height">{{ errors.height }}</small>
                    </div>
                    <div class="form-group">
                        <label>Weight (kg) <span class="text-red-500">*</span></label>
                        <InputText 
                            v-model="form.weight" 
                            :class="['w-full', { 'p-invalid': errors.weight }]" 
                            type="number" 
                            step="0.1" 
                            min="10" 
                            max="150" 
                        />
                        <small class="text-gray-500">Range: 10-150 kg</small>
                        <small class="text-red-500" v-if="errors.weight">{{ errors.weight }}</small>
                    </div>
                </div>

                <!-- Nutritional Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Nutritional Status(BMI) <span class="text-red-500">*</span></label>
                        <Select v-model="form.nutritional_status_bmi" :options="bmiOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.nutritional_status_bmi">{{ errors.nutritional_status_bmi }}</small>
                    </div>
                    <div class="form-group">
                        <label>Nutritional Status(Height for Age) <span class="text-red-500">*</span></label>
                        <Select v-model="form.nutritional_status_height" :options="heightOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.nutritional_status_height">{{ errors.nutritional_status_height }}</small>
                    </div>
                </div>

                <!-- Screenings -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Vision Screening <span class="text-red-500">*</span></label>
                        <Select v-model="form.vision_screening" :options="screeningOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.vision_screening">{{ errors.vision_screening }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.vision_screening === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.vision_screening_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                                           />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Auditory Screening <span class="text-red-500">*</span></label>
                        <Select v-model="form.auditory_screening" :options="screeningOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.auditory_screening">{{ errors.auditory_screening }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.auditory_screening === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.auditory_screening_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                                           />
                        </div>
                    </div>
                </div>

                <!-- Physical Assessment -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Skin <span class="text-red-500">*</span></label>
                        <Select v-model="form.skin" :options="skinOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.skin">{{ errors.skin }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.skin === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.skin_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Scalp <span class="text-red-500">*</span></label>
                        <Select v-model="form.scalp" :options="scalpOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.scalp">{{ errors.scalp }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.scalp === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.scalp_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Eye <span class="text-red-500">*</span></label>
                        <Select v-model="form.eye" :options="eyeOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.eye">{{ errors.eye }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.eye === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.eye_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Ear <span class="text-red-500">*</span></label>
                        <Select v-model="form.ear" :options="earOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.ear">{{ errors.ear }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.ear === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.ear_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nose <span class="text-red-500">*</span></label>
                        <Select v-model="form.nose" :options="noseOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.nose">{{ errors.nose }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.nose === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.nose_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mouth <span class="text-red-500">*</span></label>
                        <Select v-model="form.mouth" :options="mouthOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.mouth">{{ errors.mouth }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.mouth === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.mouth_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Throat</label>
                        <Select v-model="form.throat" :options="['Normal', 'Inflamed', 'Enlarged tonsils', 'Others (specify)']" class="w-full" />
                        <small class="text-red-500" v-if="errors.throat">{{ errors.throat }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.throat === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.throat_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Neck</label>
                        <Select v-model="form.neck" :options="['Normal', 'Lymph nodes enlarged', 'Stiff neck', 'Others (specify)']" class="w-full" />
                        <small class="text-red-500" v-if="errors.neck">{{ errors.neck }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.neck === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.neck_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group">
                        <label>Lungs <span class="text-red-500">*</span></label>
                        <Select v-model="form.lungs" :options="lungsOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.lungs">{{ errors.lungs }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.lungs === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.lungs_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Heart <span class="text-red-500">*</span></label>
                        <Select v-model="form.heart" :options="heartOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.heart">{{ errors.heart }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.heart === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.heart_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Abdomen <span class="text-red-500">*</span></label>
                        <Select v-model="form.abdomen" :options="abdomenOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.abdomen">{{ errors.abdomen }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.abdomen === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.abdomen_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deformities <span class="text-red-500">*</span></label>
                        <Select v-model="form.deformities" :options="deformitiesOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.deformities">{{ errors.deformities }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.deformities === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.deformities_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                                maxlength="15"
                                                           />
                        </div>
                    </div>
                </div>

                <!-- Immunization & Benefits Section -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-md font-semibold mb-4 text-blue-800">Immunization & Benefits</h3>
                    <div class="grid grid-cols-5 gap-4">
                        <div class="form-group">
                            <label>Iron Supplementation</label>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="form.iron_supplementation_check" :binary="true" />
                                <span class="text-sm">Yes</span>
                            </div>
                            <small class="text-red-500" v-if="errors.iron_supplementation">{{ errors.iron_supplementation }}</small>
                        </div>
                        <div class="form-group">
                            <label>Dewormed</label>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="form.deworming_check" :binary="true" />
                                <span class="text-sm">Yes</span>
                            </div>
                            <small class="text-red-500" v-if="errors.deworming_status">{{ errors.deworming_status }}</small>
                        </div>
                        <div class="form-group">
                            <label>Immunization (Specify what kind)</label>
                            <InputText v-model="form.immunization" class="w-full" placeholder="e.g., Measles, Polio, etc." maxlength="15" />
                            <small class="text-red-500" v-if="errors.immunization">{{ errors.immunization }}</small>
                        </div>
                        <div class="form-group">
                            <label>SBFP Beneficiary</label>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="form.sbfp_beneficiary" :binary="true" />
                                <span class="text-sm">Yes</span>
                            </div>
                            <small class="text-red-500" v-if="errors.sbfp_beneficiary">{{ errors.sbfp_beneficiary }}</small>
                        </div>
                        <div class="form-group">
                            <label>4Ps Beneficiary</label>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="form.four_ps_beneficiary" :binary="true" />
                                <span class="text-sm">Yes</span>
                            </div>
                            <small class="text-red-500" v-if="errors.four_ps_beneficiary">{{ errors.four_ps_beneficiary }}</small>
                        </div>
                    </div>
                </div>


                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" class="p-button-secondary" @click="$inertia.visit(`/pupil-health/health-examination/${student.id}?grade=${props.grade_level || props.selectedGrade?.replace('Grade ', '') || '6'}`)" />
                    <Button type="submit" label="Create" :loading="form.processing" />
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Select from 'primevue/select'
import Checkbox from 'primevue/checkbox'

const props = defineProps({
    student: {
        type: Object,
        required: true
    },
    selectedGrade: {
        type: String,
        default: ''
    },
    grade_level: {  // Add this prop that comes from backend
        type: String,
        default: ''
    },
    errors: {
        type: Object,
        default: () => ({})
    }
})

// Debug grade level
console.log('Create form - selectedGrade prop:', props.selectedGrade)
console.log('Create form - grade_level prop:', props.grade_level)
console.log('Create form - student:', props.student)

// Form options
const bmiOptions = [
    'Severely Wasted',
    'Wasted',
    'Normal',
    'Overweight',
    'Obese'
]

const heightOptions = [
    'Normal (≥-2 SD)',
    'Mild Stunting (-2 to -3 SD)',
    'Severe Stunting (<-3 SD)'
]

const screeningOptions = ['Normal', 'Abnormal', 'Others (specify)']

const skinOptions = ['Normal', 'Redness of Skin', 'White Spots', 'Flaky Skin', 'Others (specify)']
const scalpOptions = ['Normal', 'Presence of Lice', 'Others (specify)']
const eyeOptions = ['Normal', 'Eye Redness', 'Pale Conjunctiva', 'Others (specify)']
const earOptions = ['Normal', 'Ear discharge', 'Others (specify)']
const noseOptions = ['Normal', 'Mucus discharge', 'Nose Bleeding', 'Others (specify)']
const mouthOptions = ['Normal', 'Enlarged tonsil', 'Inflamed pharynx', 'Others (specify)']
const lungsOptions = ['Normal', 'Rales', 'Wheeze', 'Others (specify)']
const heartOptions = ['Normal', 'Murmur', 'Irregular heart rate', 'Others (specify)']
const abdomenOptions = ['Normal', 'Distended', 'Tenderness', 'Others (specify)']
const deformitiesOptions = ['None', 'Acquired', 'Congenital', 'Others (specify)']

const yesNoOptions = ['Yes', 'No']
const dewormingOptions = ['dewormed', 'not_dewormed']

// Form setup
const form = useForm({
    temperature: '',
    heart_rate: '',
    height: '',
    weight: '',
    nutritional_status_bmi: '',
    nutritional_status_height: '',
    vision_screening: '',
    vision_screening_specify: '',
    auditory_screening: '',
    auditory_screening_specify: '',
    skin: '',
    skin_specify: '',
    scalp: '',
    scalp_specify: '',
    eye: '',
    eye_specify: '',
    ear: '',
    ear_specify: '',
    nose: '',
    nose_specify: '',
    mouth: '',
    mouth_specify: '',
    throat: '',
    throat_specify: '',
    neck: '',
    neck_specify: '',
    lungs: '',
    lungs_specify: '',
    heart: '',
    heart_specify: '',
    abdomen: '',
    abdomen_specify: '',
    deformities: '',
    deformities_specify: '',
    // Checkbox fields for UI
    iron_supplementation_check: false,
    deworming_check: false,
    sbfp_beneficiary: false,
    four_ps_beneficiary: false,
    // Backend fields (will be set from checkboxes)
    iron_supplementation: '',
    deworming_status: '',
    immunization: '',
    other_specify: '',
    student_id: props.student.id,
    grade_level: props.grade_level || props.selectedGrade?.replace('Grade ', '') || '6',
    school_year: `${new Date().getFullYear()}-${new Date().getFullYear() + 1}`,
    examination_date: new Date().toISOString().split('T')[0]
})


// Validation rules
const validateForm = () => {
    const errors = {}
    
    // Temperature validation (35°C - 42°C)
    if (!form.temperature) {
        errors.temperature = 'Temperature is required'
    } else {
        const temp = parseFloat(form.temperature)
        if (temp < 35 || temp > 42) {
            errors.temperature = 'Temperature must be between 35°C and 42°C'
        }
    }
    
    // Heart rate validation (40-200 bpm for children)
    if (!form.heart_rate) {
        errors.heart_rate = 'Heart rate is required'
    } else {
        const hr = parseInt(form.heart_rate)
        if (hr < 40 || hr > 200) {
            errors.heart_rate = 'Heart rate must be between 40 and 200 bpm'
        }
    }
    
    // Height validation (50-200 cm for school children)
    if (!form.height) {
        errors.height = 'Height is required'
    } else {
        const height = parseFloat(form.height)
        if (height < 50 || height > 200) {
            errors.height = 'Height must be between 50 and 200 cm'
        }
    }
    
    // Weight validation (10-150 kg for school children)
    if (!form.weight) {
        errors.weight = 'Weight is required'
    } else {
        const weight = parseFloat(form.weight)
        if (weight < 10 || weight > 150) {
            errors.weight = 'Weight must be between 10 and 150 kg'
        }
    }
    
    // Required nutritional status
    if (!form.nutritional_status_bmi) errors.nutritional_status_bmi = 'BMI nutritional status is required'
    if (!form.nutritional_status_height) errors.nutritional_status_height = 'Height for age nutritional status is required'
    
    // Required screenings
    if (!form.vision_screening) errors.vision_screening = 'Vision screening is required'
    if (!form.auditory_screening) errors.auditory_screening = 'Auditory screening is required'
    
    // Required physical examination fields
    if (!form.skin) errors.skin = 'Skin examination is required'
    if (!form.scalp) errors.scalp = 'Scalp examination is required'
    if (!form.eye) errors.eye = 'Eye examination is required'
    if (!form.ear) errors.ear = 'Ear examination is required'
    if (!form.nose) errors.nose = 'Nose examination is required'
    if (!form.mouth) errors.mouth = 'Mouth examination is required'
    if (!form.lungs) errors.lungs = 'Lungs examination is required'
    if (!form.heart) errors.heart = 'Heart examination is required'
    if (!form.abdomen) errors.abdomen = 'Abdomen examination is required'
    if (!form.deformities) errors.deformities = 'Deformities examination is required'
    
    return errors
}

const errors = ref({})

const submit = () => {
    // Validate form
    const validationErrors = validateForm()
    errors.value = validationErrors
    
    if (Object.keys(validationErrors).length > 0) {
        console.log('Form validation failed:', validationErrors)
        // Scroll to the first error field
        const firstErrorField = Object.keys(validationErrors)[0]
        const errorElement = document.querySelector(`[name="${firstErrorField}"]`) || 
                            document.querySelector(`input[type="number"]`) ||
                            document.querySelector('.text-red-500')
        if (errorElement) {
            errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }
        return
    }
    
    // Convert checkbox values to backend format
    form.iron_supplementation = form.iron_supplementation_check ? 'Yes' : 'No'
    form.deworming_status = form.deworming_check ? 'dewormed' : 'not_dewormed'
    
    console.log('Submitting form with data:', form.data())
    
    form.post(route('health-examination.store'), {
        onSuccess: (response) => {
            console.log('Form submitted successfully:', response)
            errors.value = {} // Clear validation errors on success
        },
        onError: (serverErrors) => {
            console.error('Form submission errors:', serverErrors)
            // Merge server errors with client validation errors
            errors.value = { ...errors.value, ...serverErrors }
        },
        onFinish: () => {
            console.log('Form submission finished')
        }
    })
}
</script>

<style scoped>
.health-examination-form {
    padding: 20px;
    background-color: #f5f7f9;
    min-height: 100vh;
}

.white-container {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    max-width: 1200px;
    margin: 0 auto;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: #374151;
}

:deep(.p-inputtext) {
    width: 100%;
}

:deep(.p-dropdown) {
    width: 100%;
}

:deep(.p-button) {
    min-width: 100px;
}

/* PrimeVue invalid styling */
:deep(.p-invalid .p-inputtext),
:deep(.p-invalid.p-inputtext) {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}

:deep(.p-invalid .p-inputtext:focus),
:deep(.p-invalid.p-inputtext:focus) {
    outline: 2px solid #ef4444 !important;
    box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.25) !important;
}
</style>
