<template>
    <Head title="Create Health Examination" />
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Enhanced Header -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 mb-8 backdrop-blur-sm">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <div class="w-14 h-14 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="pi pi-plus text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent mb-2">Create Health Examination</h1>
                            <p class="text-slate-600 font-medium">Add new medical assessment for {{ student.full_name }}</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-slate-600 font-semibold">Student:</span>
                                <div class="font-bold text-slate-900">{{ student.full_name }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Grade:</span>
                                <div class="font-bold text-slate-900">{{ student.grade_level }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">LRN:</span>
                                <div class="font-mono font-bold text-slate-900">{{ student.lrn || 'Not Available' }}</div>
                            </div>
                            <div>
                                <span class="text-slate-600 font-semibold">Section:</span>
                                <div class="font-bold text-slate-900">{{ student.section || 'Not Assigned' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 backdrop-blur-sm">

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
                            placeholder="e.g., 36.5"
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
                            placeholder="e.g., 80"
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
                            placeholder="e.g., 120.5"
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
                            placeholder="e.g., 25.0"
                        />
                        <small class="text-gray-500">Range: 10-150 kg</small>
                        <small class="text-red-500" v-if="errors.weight">{{ errors.weight }}</small>
                    </div>
                </div>

                <!-- Nutritional Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Nutritional Status(BMI) <span class="text-red-500">*</span></label>
                        <div class="mb-2">
                            <div class="text-sm text-gray-600">
                                Calculated BMI:
                                <span class="font-semibold text-blue-600">{{ calculatedBMI || 'Enter height & weight' }}</span>
                                <span v-if="calculatedBMI" class="text-xs text-gray-500">kg/m²</span>
                            </div>
                        </div>
                        <Select
                            v-model="form.nutritional_status_bmi"
                            :options="bmiOptions"
                            class="w-full"
                            placeholder="Auto-calculated from height & weight"
                            :disabled="true"
                            readonly
                        />
                        <small class="text-gray-500 text-xs">Automatically calculated when height and weight are entered</small>
                        <small class="text-red-500" v-if="errors.nutritional_status_bmi">{{ errors.nutritional_status_bmi }}</small>
                    </div>
                    <div class="form-group">
                        <label>Nutritional Status(Height for Age) <span class="text-red-500">*</span></label>
                        <div class="mb-2">
                            <div class="text-sm text-gray-600">
                                Height Assessment:
                                <span class="font-semibold text-green-600">{{ calculatedHeightStatus || 'Enter height' }}</span>
                            </div>
                        </div>
                        <Select
                            v-model="form.nutritional_status_height"
                            :options="heightOptions"
                            class="w-full"
                            placeholder="Auto-calculated from height & grade"
                            :disabled="true"
                            readonly
                        />
                        <small class="text-gray-500 text-xs">Automatically calculated based on height and grade level</small>
                        <small class="text-red-500" v-if="errors.nutritional_status_height">{{ errors.nutritional_status_height }}</small>
                    </div>
                </div>

                <!-- Screenings -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Vision Screening <span class="text-red-500">*</span></label>
                        <Select v-model="form.vision_screening" :options="screeningOptions" class="w-full" placeholder="Select vision result" />
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
                        <Select v-model="form.auditory_screening" :options="screeningOptions" class="w-full" placeholder="Select auditory result" />
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
                        <Select v-model="form.skin" :options="skinOptions" class="w-full" placeholder="Select skin condition" />
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
                        <Select v-model="form.scalp" :options="scalpOptions" class="w-full" placeholder="Select scalp condition" />
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
                        <Select v-model="form.eye" :options="eyeOptions" class="w-full" placeholder="Select eye condition" />
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
                        <Select v-model="form.ear" :options="earOptions" class="w-full" placeholder="Select ear condition" />
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
                        <Select v-model="form.nose" :options="noseOptions" class="w-full" placeholder="Select nose condition" />
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
                        <Select v-model="form.mouth" :options="mouthOptions" class="w-full" placeholder="Select mouth condition" />
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
                        <Select v-model="form.throat" :options="['Normal', 'Inflamed', 'Enlarged tonsils', 'Others (specify)']" class="w-full" placeholder="Select throat condition" />
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
                        <Select v-model="form.neck" :options="['Normal', 'Lymph nodes enlarged', 'Stiff neck', 'Others (specify)']" class="w-full" placeholder="Select neck condition" />
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
                        <Select v-model="form.lungs" :options="lungsOptions" class="w-full" placeholder="Select lungs condition" />
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
                        <Select v-model="form.heart" :options="heartOptions" class="w-full" placeholder="Select heart condition" />
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
                        <Select v-model="form.abdomen" :options="abdomenOptions" class="w-full" placeholder="Select abdomen condition" />
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
                        <Select v-model="form.deformities" :options="deformitiesOptions" class="w-full" placeholder="Select deformities status" />
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
                            <label for="iron_supplementation_check">Iron Supplementation</label>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="form.iron_supplementation_check" inputId="iron_supplementation_check" :binary="true" />
                                <span class="text-sm">Yes</span>
                            </div>
                            <small class="text-red-500" v-if="errors.iron_supplementation">{{ errors.iron_supplementation }}</small>
                        </div>
                        <div class="form-group">
                            <label for="deworming_check">Dewormed</label>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="form.deworming_check" inputId="deworming_check" :binary="true" />
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
                            <label for="sbfp_beneficiary">SBFP Beneficiary</label>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="form.sbfp_beneficiary" inputId="sbfp_beneficiary" :binary="true" />
                                <span class="text-sm">Yes</span>
                            </div>
                            <small class="text-red-500" v-if="errors.sbfp_beneficiary">{{ errors.sbfp_beneficiary }}</small>
                        </div>
                        <div class="form-group">
                            <label for="four_ps_beneficiary">4Ps Beneficiary</label>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="form.four_ps_beneficiary" inputId="four_ps_beneficiary" :binary="true" />
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
    </div>

    <!-- Confirmation Dialog -->
    <ConfirmDialog></ConfirmDialog>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Select from 'primevue/select'
import Checkbox from 'primevue/checkbox'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from 'primevue/useconfirm'
import { useToastStore } from '@/Stores/toastStore'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css'
// Import page-specific styles
import '../../../css/pages/HealthExamination/Create.css'

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

// Toast store and confirm dialog
const { showSuccess } = useToastStore()
const confirm = useConfirm()

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

// Computed BMI calculation
const calculatedBMI = computed(() => {
    const height = parseFloat(form.height);
    const weight = parseFloat(form.weight);

    if (height > 0 && weight > 0) {
        const bmi = weight / Math.pow(height / 100, 2);
        return bmi.toFixed(1);
    }
    return null;
});

// Computed BMI status based on WHO standards for school-aged children
const calculatedBMIStatus = computed(() => {
    const bmi = parseFloat(calculatedBMI.value);

    if (!bmi || isNaN(bmi)) return null;

    // WHO BMI-for-age classifications for 5-19 years
    if (bmi < 18.5) {
        return bmi < 16 ? 'Severely Wasted' : 'Wasted';
    } else if (bmi >= 18.5 && bmi < 25) {
        return 'Normal';
    } else if (bmi >= 25 && bmi < 30) {
        return 'Overweight';
    } else {
        return 'Obese';
    }
});

// Computed height-for-age status (simplified - would need actual growth charts for precision)
const calculatedHeightStatus = computed(() => {
    const height = parseFloat(form.height);
    const gradeLevel = props.student.grade_level;

    if (!height || !gradeLevel) return null;

    // Simplified height expectations by grade level (in cm)
    // These are approximate values - in a real system, you'd use WHO growth charts
    const heightExpectations = {
        'Kinder 2': 110,
        'Grade 1': 115,
        'Grade 2': 120,
        'Grade 3': 125,
        'Grade 4': 130,
        'Grade 5': 135,
        'Grade 6': 140
    };

    const expectedHeight = heightExpectations[gradeLevel];
    if (!expectedHeight) return 'Normal (≥-2 SD)'; // Default if grade not found

    const difference = height - expectedHeight;

    if (difference >= -2) {
        return 'Normal (≥-2 SD)';
    } else if (difference >= -3) {
        return 'Mild Stunting (-2 to -3 SD)';
    } else {
        return 'Severe Stunting (<-3 SD)';
    }
});

// Auto-update nutritional status fields when height/weight change
watch([() => form.height, () => form.weight], () => {
    if (calculatedBMIStatus.value) {
        form.nutritional_status_bmi = calculatedBMIStatus.value;
    }
});

watch(() => form.height, () => {
    if (calculatedHeightStatus.value) {
        form.nutritional_status_height = calculatedHeightStatus.value;
    }
});

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
    // Validate form first
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

    // Show confirmation dialog
    confirm.require({
        message: 'Are you sure you want to create this health examination record?',
        header: 'Confirm Creation',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-text p-button-secondary',
        acceptClass: 'p-button-primary',
        acceptLabel: 'Yes, Create',
        rejectLabel: 'Cancel',
        accept: () => {
            performCreate()
        }
    })
}

const performCreate = () => {
    // Convert checkbox values to backend format
    form.iron_supplementation = form.iron_supplementation_check ? 'Yes' : 'No'
    form.deworming_status = form.deworming_check ? 'dewormed' : 'not_dewormed'

    console.log('Submitting form with data:', form.data())

    form.post(route('health-examination.store'), {
        onSuccess: (response) => {
            console.log('Form submitted successfully:', response)
            errors.value = {} // Clear validation errors on success
            showSuccess('Health Examination Created', 'The health examination record has been created successfully!')
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
