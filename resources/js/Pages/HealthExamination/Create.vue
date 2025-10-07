<template>
    <Head title="Create Health Examination" />
    <div class="health-examination-form">
        <div class="white-container">
            <h2 class="text-lg font-semibold mb-4">Create Student Health Examination</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Vital Signs -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group">
                        <label>Temperature (°C) <span class="text-red-500">*</span></label>
                        <InputText v-model="form.temperature" class="w-full" type="number" step="0.1" required />
                        <small class="text-red-500" v-if="errors.temperature">{{ errors.temperature }}</small>
                    </div>
                    <div class="form-group">
                        <label>Heart Rate (bpm) <span class="text-red-500">*</span></label>
                        <InputText v-model="form.heart_rate" class="w-full" type="number" required />
                        <small class="text-red-500" v-if="errors.heart_rate">{{ errors.heart_rate }}</small>
                    </div>
                    <div class="form-group">
                        <label>Height (cm) <span class="text-red-500">*</span></label>
                        <InputText v-model="form.height" class="w-full" type="number" step="0.1" @input="calculateBMI" required />
                        <small class="text-red-500" v-if="errors.height">{{ errors.height }}</small>
                    </div>
                    <div class="form-group">
                        <label>Weight (kg) <span class="text-red-500">*</span></label>
                        <InputText v-model="form.weight" class="w-full" type="number" step="0.1" @input="calculateBMI" required />
                        <small class="text-red-500" v-if="errors.weight">{{ errors.weight }}</small>
                    </div>
                </div>

                <!-- Nutritional Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Nutritional Status(BMI) <span class="text-red-500">*</span></label>
                        <Select v-model="form.nutritional_status_bmi" :options="bmiOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.nutritional_status_bmi">{{ errors.nutritional_status_bmi }}</small>
                    </div>
                    <div class="form-group">
                        <label>Nutritional Status(Height for Age) <span class="text-red-500">*</span></label>
                        <Select v-model="form.nutritional_status_height" :options="heightOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.nutritional_status_height">{{ errors.nutritional_status_height }}</small>
                    </div>
                </div>

                <!-- Screenings -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Vision Screening <span class="text-red-500">*</span></label>
                        <Select v-model="form.vision_screening" :options="screeningOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.vision_screening">{{ errors.vision_screening }}</small>
                    </div>
                    <div class="form-group">
                        <label>Auditory Screening <span class="text-red-500">*</span></label>
                        <Select v-model="form.auditory_screening" :options="screeningOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.auditory_screening">{{ errors.auditory_screening }}</small>
                    </div>
                </div>

                <!-- Physical Assessment -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Skin <span class="text-red-500">*</span></label>
                        <Select v-model="form.skin" :options="skinOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.skin">{{ errors.skin }}</small>
                    </div>
                    <div class="form-group">
                        <label>Scalp <span class="text-red-500">*</span></label>
                        <Select v-model="form.scalp" :options="scalpOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.scalp">{{ errors.scalp }}</small>
                    </div>
                    <div class="form-group">
                        <label>Eye <span class="text-red-500">*</span></label>
                        <Select v-model="form.eye" :options="eyeOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.eye">{{ errors.eye }}</small>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Ear <span class="text-red-500">*</span></label>
                        <Select v-model="form.ear" :options="earOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.ear">{{ errors.ear }}</small>
                    </div>
                    <div class="form-group">
                        <label>Nose <span class="text-red-500">*</span></label>
                        <Select v-model="form.nose" :options="noseOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.nose">{{ errors.nose }}</small>
                    </div>
                    <div class="form-group">
                        <label>Mouth <span class="text-red-500">*</span></label>
                        <Select v-model="form.mouth" :options="mouthOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.mouth">{{ errors.mouth }}</small>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group">
                        <label>Lungs <span class="text-red-500">*</span></label>
                        <Select v-model="form.lungs" :options="lungsOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.lungs">{{ errors.lungs }}</small>
                        <!-- Additional text box for "Other specify" -->
                        <div v-if="form.lungs === 'Other specify'" class="mt-2">
                            <InputText 
                                v-model="form.lungs_other_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Heart <span class="text-red-500">*</span></label>
                        <Select v-model="form.heart" :options="heartOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.heart">{{ errors.heart }}</small>
                        <!-- Additional text box for "Other specify" -->
                        <div v-if="form.heart === 'Other specify'" class="mt-2">
                            <InputText 
                                v-model="form.heart_other_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Abdomen <span class="text-red-500">*</span></label>
                        <Select v-model="form.abdomen" :options="abdomenOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.abdomen">{{ errors.abdomen }}</small>
                    </div>
                    <div class="form-group">
                        <label>Deformities <span class="text-red-500">*</span></label>
                        <Select v-model="form.deformities" :options="deformitiesOptions" class="w-full" required />
                        <small class="text-red-500" v-if="errors.deformities">{{ errors.deformities }}</small>
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
                            <InputText v-model="form.immunization" class="w-full" placeholder="e.g., Measles, Polio, etc." />
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

                <div class="form-group">
                    <label>Remarks</label>
                    <InputText v-model="form.remarks" class="w-full" />
                    <small class="text-red-500" v-if="errors.remarks">{{ errors.remarks }}</small>
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
    'Normal (18.5-24.9)',
    'Underweight (16.0-18.4)', 
    'Severely Underweight (<16.0)',
    'Overweight (25.0-29.9)',
    'Obese (≥30.0)'
]

const heightOptions = [
    'Normal (≥-2 SD)',
    'Mild Stunting (-2 to -3 SD)',
    'Severe Stunting (<-3 SD)'
]

const screeningOptions = ['Normal', 'Abnormal']

const skinOptions = ['Normal', 'Redness of Skin', 'White Spots', 'Flaky Skin']
const scalpOptions = ['Normal', 'Presence of Lice']
const eyeOptions = ['Normal', 'Eye Redness', 'Pale Conjunctiva']
const earOptions = ['Normal', 'Ear discharge']
const noseOptions = ['Normal', 'Mucus discharge', 'Nose Bleeding']
const mouthOptions = ['Normal', 'Enlarged tonsil', 'Inflamed pharynx']
const lungsOptions = ['Normal', 'Rales', 'Wheeze', 'Other specify']
const heartOptions = ['Normal', 'Murmur', 'Irregular heart rate', 'Other specify']
const abdomenOptions = ['Normal', 'Distended', 'Tenderness']
const deformitiesOptions = ['None', 'Acquired', 'Congenital']

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
    auditory_screening: '',
    skin: '',
    scalp: '',
    eye: '',
    ear: '',
    nose: '',
    mouth: '',
    lungs: '',
    lungs_other_specify: '',
    heart: '',
    heart_other_specify: '',
    abdomen: '',
    deformities: '',
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
    remarks: '',
    student_id: props.student.id,
    grade_level: props.grade_level || props.selectedGrade?.replace('Grade ', '') || '6',
    school_year: `${new Date().getFullYear()}-${new Date().getFullYear() + 1}`,
    examination_date: new Date().toISOString().split('T')[0]
})

// BMI calculation
const calculateBMI = () => {
    const height = parseFloat(form.height)
    const weight = parseFloat(form.weight)
    
    if (height && weight && height > 0 && weight > 0) {
        const heightInMeters = height / 100
        const bmi = weight / (heightInMeters * heightInMeters)
        
        if (bmi < 16.0) {
            form.nutritional_status_bmi = 'Severely Underweight (<16.0)'
        } else if (bmi < 18.5) {
            form.nutritional_status_bmi = 'Underweight (16.0-18.4)'
        } else if (bmi < 25.0) {
            form.nutritional_status_bmi = 'Normal (18.5-24.9)'
        } else if (bmi < 30.0) {
            form.nutritional_status_bmi = 'Overweight (25.0-29.9)'
        } else {
            form.nutritional_status_bmi = 'Obese (≥30.0)'
        }
    }
}

// Validation rules
const validateForm = () => {
    const errors = {}
    
    // Required vital signs
    if (!form.temperature) errors.temperature = 'Temperature is required'
    if (!form.heart_rate) errors.heart_rate = 'Heart rate is required'
    if (!form.height) errors.height = 'Height is required'
    if (!form.weight) errors.weight = 'Weight is required'
    
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
</style>
