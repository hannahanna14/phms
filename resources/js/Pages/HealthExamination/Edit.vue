<template>
    <Head title="Edit Health Examination" />
    <div class="health-examination-form">
        <div class="white-container">
            <h2 class="text-lg font-semibold mb-4">Edit Student Health Examination</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Vital Signs -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group">
                        <label>Temperature (°C) <span class="text-red-500">*</span></label>
                        <InputNumber v-model="form.temperature" :minFractionDigits="1" :maxFractionDigits="1" class="w-full" required />
                        <small class="text-red-500" v-if="errors.temperature">{{ errors.temperature }}</small>
                    </div>
                    <div class="form-group">
                        <label>Heart Rate (bpm) <span class="text-red-500">*</span></label>
                        <InputNumber v-model="form.heart_rate" class="w-full" required />
                        <small class="text-red-500" v-if="errors.heart_rate">{{ errors.heart_rate }}</small>
                    </div>
                    <div class="form-group">
                        <label>Height (cm) <span class="text-red-500">*</span></label>
                        <InputNumber v-model="form.height" :minFractionDigits="1" :maxFractionDigits="1" class="w-full" required />
                        <small class="text-red-500" v-if="errors.height">{{ errors.height }}</small>
                    </div>
                    <div class="form-group">
                        <label>Weight (kg) <span class="text-red-500">*</span></label>
                        <InputNumber v-model="form.weight" :minFractionDigits="1" :maxFractionDigits="1" class="w-full" required />
                        <small class="text-red-500" v-if="errors.weight">{{ errors.weight }}</small>
                    </div>
                </div>

                <!-- Nutritional Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Nutritional Status(BMI)</label>
                        <Select v-model="form.nutritional_status_bmi" :options="bmiOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.nutritional_status_bmi">{{ errors.nutritional_status_bmi }}</small>
                    </div>
                    <div class="form-group">
                        <label>Nutritional Status(Height for Age)</label>
                        <Select v-model="form.nutritional_status_height" :options="heightOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.nutritional_status_height">{{ errors.nutritional_status_height }}</small>
                    </div>
                </div>

                <!-- Screenings -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Vision Screening</label>
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
                        <label>Auditory Screening</label>
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
                        <label>Skin</label>
                        <Select v-model="form.skin" :options="skinOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.skin">{{ errors.skin }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.skin === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.skin_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Scalp</label>
                        <Select v-model="form.scalp" :options="scalpOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.scalp">{{ errors.scalp }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.scalp === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.scalp_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Eye</label>
                        <Select v-model="form.eye" :options="eyeOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.eye">{{ errors.eye }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.eye === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.eye_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                            />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Ear</label>
                        <Select v-model="form.ear" :options="earOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.ear">{{ errors.ear }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.ear === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.ear_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nose</label>
                        <Select v-model="form.nose" :options="noseOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.nose">{{ errors.nose }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.nose === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.nose_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mouth</label>
                        <Select v-model="form.mouth" :options="mouthOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.mouth">{{ errors.mouth }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.mouth === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.mouth_specify" 
                                placeholder="Please specify..." 
                                class="w-full" 
                            />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group">
                        <label>Lungs</label>
                        <Select v-model="form.lungs" :options="lungsOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.lungs">{{ errors.lungs }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.lungs === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.lungs_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Heart</label>
                        <Select v-model="form.heart" :options="heartOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.heart">{{ errors.heart }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.heart === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.heart_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Abdomen</label>
                        <Select v-model="form.abdomen" :options="abdomenOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.abdomen">{{ errors.abdomen }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.abdomen === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.abdomen_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deformities</label>
                        <Select v-model="form.deformities" :options="deformitiesOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.deformities">{{ errors.deformities }}</small>
                        <!-- Additional text box for "Others (specify)" -->
                        <div v-if="form.deformities === 'Others (specify)'" class="mt-2">
                            <InputText 
                                v-model="form.deformities_specify" 
                                placeholder="Please specify..." 
                                class="w-full text-sm" 
                            />
                        </div>
                    </div>
                </div>

                <!-- Immunization & Benefits Section -->
                <div class="border rounded-lg p-4">
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


                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" class="p-button-secondary" @click="$inertia.visit(`/pupil-health/health-examination/${student.id}?grade=${selectedGrade.replace('Grade ', '')}`)" />
                    <Button type="submit" label="Update" :loading="form.processing" />
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Button from 'primevue/button'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import Select from 'primevue/select'
import Checkbox from 'primevue/checkbox'

const props = defineProps({
    student: {
        type: Object,
        required: true
    },
    healthExamination: {
        type: Object,
        required: true
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


const form = useForm({
    temperature: parseFloat(props.healthExamination.temperature) || null,
    heart_rate: props.healthExamination.heart_rate ? parseInt(props.healthExamination.heart_rate.toString().replace(/[^\d]/g, '')) || null : null,
    height: parseFloat(props.healthExamination.height) || null,
    weight: parseFloat(props.healthExamination.weight) || null,
    nutritional_status_bmi: props.healthExamination.nutritional_status_bmi || '',
    nutritional_status_height: props.healthExamination.nutritional_status_height || '',
    vision_screening: props.healthExamination.vision_screening || '',
    vision_screening_specify: props.healthExamination.vision_screening_specify || '',
    auditory_screening: props.healthExamination.auditory_screening || '',
    auditory_screening_specify: props.healthExamination.auditory_screening_specify || '',
    skin: props.healthExamination.skin || '',
    skin_specify: props.healthExamination.skin_specify || '',
    scalp: props.healthExamination.scalp || '',
    scalp_specify: props.healthExamination.scalp_specify || '',
    eye: props.healthExamination.eye || '',
    eye_specify: props.healthExamination.eye_specify || '',
    ear: props.healthExamination.ear || '',
    ear_specify: props.healthExamination.ear_specify || '',
    nose: props.healthExamination.nose || '',
    nose_specify: props.healthExamination.nose_specify || '',
    mouth: props.healthExamination.mouth || '',
    mouth_specify: props.healthExamination.mouth_specify || '',
    neck: props.healthExamination.neck || '',
    throat: props.healthExamination.throat || '',
    lungs: props.healthExamination.lungs || props.healthExamination.lungs_heart || '',
    lungs_specify: props.healthExamination.lungs_specify || props.healthExamination.lungs_other_specify || '',
    heart: props.healthExamination.heart || props.healthExamination.lungs_heart || '',
    heart_specify: props.healthExamination.heart_specify || props.healthExamination.heart_other_specify || '',
    abdomen: props.healthExamination.abdomen || '',
    abdomen_specify: props.healthExamination.abdomen_specify || '',
    deformities: props.healthExamination.deformities || '',
    deformities_specify: props.healthExamination.deformities_specify || '',
    // Checkbox fields for UI (convert from backend values)
    iron_supplementation_check: props.healthExamination.iron_supplementation === 'Yes',
    deworming_check: props.healthExamination.deworming_status === 'dewormed',
    sbfp_beneficiary: props.healthExamination.sbfp_beneficiary || false,
    four_ps_beneficiary: props.healthExamination.four_ps_beneficiary || false,
    // Backend fields
    iron_supplementation: props.healthExamination.iron_supplementation,
    deworming_status: props.healthExamination.deworming_status,
    immunization: props.healthExamination.immunization,
    other_specify: props.healthExamination.other_specify,
    grade_level: props.selectedGrade || props.healthExamination.grade_level,
    school_year: props.healthExamination.school_year
})

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
    
    form.put(route('health-examination.update', props.healthExamination.id), {
        onSuccess: () => {
            errors.value = {} // Clear validation errors on success
        },
        onError: (serverErrors) => {
            // Merge server errors with client validation errors
            errors.value = { ...errors.value, ...serverErrors }
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
