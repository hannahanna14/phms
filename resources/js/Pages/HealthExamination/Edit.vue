<template>
    <Head title="Edit Health Examination" />
    <div class="health-examination-form">
        <div class="white-container">
            <h2 class="text-lg font-semibold mb-4">Edit Student Health Examination</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Vital Signs -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group">
                        <label>Temperature (°C)</label>
                        <InputNumber v-model="form.temperature" :minFractionDigits="1" :maxFractionDigits="1" class="w-full" />
                        <small class="text-red-500" v-if="errors.temperature">{{ errors.temperature }}</small>
                    </div>
                    <div class="form-group">
                        <label>Heart Rate (bpm)</label>
                        <InputNumber v-model="form.heart_rate" class="w-full" />
                        <small class="text-red-500" v-if="errors.heart_rate">{{ errors.heart_rate }}</small>
                    </div>
                    <div class="form-group">
                        <label>Height (cm)</label>
                        <InputNumber v-model="form.height" :minFractionDigits="1" :maxFractionDigits="1" class="w-full" />
                        <small class="text-red-500" v-if="errors.height">{{ errors.height }}</small>
                    </div>
                    <div class="form-group">
                        <label>Weight (kg)</label>
                        <InputNumber v-model="form.weight" :minFractionDigits="1" :maxFractionDigits="1" class="w-full" />
                        <small class="text-red-500" v-if="errors.weight">{{ errors.weight }}</small>
                    </div>
                </div>

                <!-- Nutritional Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Nutritional Status(BMI)</label>
                        <Dropdown v-model="form.nutritional_status_bmi" :options="bmiOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.nutritional_status_bmi">{{ errors.nutritional_status_bmi }}</small>
                    </div>
                    <div class="form-group">
                        <label>Nutritional Status(Height for Age)</label>
                        <Dropdown v-model="form.nutritional_status_height" :options="heightOptions" class="w-full" />
                        <small class="text-red-500" v-if="errors.nutritional_status_height">{{ errors.nutritional_status_height }}</small>
                    </div>
                </div>

                <!-- Screenings -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Vision Screening</label>
                        <Dropdown v-model="form.vision_screening" :options="['Passed', 'Failed']" class="w-full" />
                        <small class="text-red-500" v-if="errors.vision_screening">{{ errors.vision_screening }}</small>
                    </div>
                    <div class="form-group">
                        <label>Auditory Screening</label>
                        <Dropdown v-model="form.auditory_screening" :options="['Passed', 'Failed']" class="w-full" />
                        <small class="text-red-500" v-if="errors.auditory_screening">{{ errors.auditory_screening }}</small>
                    </div>
                </div>

                <!-- Physical Assessment -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Skin</label>
                        <Dropdown v-model="form.skin" :options="['Normal', 'Redness of Skin', 'White Spots', 'Flaky Skin']" class="w-full" />
                        <small class="text-red-500" v-if="errors.skin">{{ errors.skin }}</small>
                    </div>
                    <div class="form-group">
                        <label>Scalp</label>
                        <Dropdown v-model="form.scalp" :options="['Normal', 'Presence of Lice']" class="w-full" />
                        <small class="text-red-500" v-if="errors.scalp">{{ errors.scalp }}</small>
                    </div>
                    <div class="form-group">
                        <label>Eye</label>
                        <Dropdown v-model="form.eye" :options="['Normal', 'Eye Redness', 'Pale Conjunctiva']" class="w-full" />
                        <small class="text-red-500" v-if="errors.eye">{{ errors.eye }}</small>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Ear</label>
                        <Dropdown v-model="form.ear" :options="['Normal', 'Ear discharge']" class="w-full" />
                        <small class="text-red-500" v-if="errors.ear">{{ errors.ear }}</small>
                    </div>
                    <div class="form-group">
                        <label>Nose</label>
                        <Dropdown v-model="form.nose" :options="['Normal', 'Mucus discharge', 'Nose Bleeding']" class="w-full" />
                        <small class="text-red-500" v-if="errors.nose">{{ errors.nose }}</small>
                    </div>
                    <div class="form-group">
                        <label>Mouth</label>
                        <Dropdown v-model="form.mouth" :options="['Normal', 'Enlarged tonsil', 'Inflamed pharynx']" class="w-full" />
                        <small class="text-red-500" v-if="errors.mouth">{{ errors.mouth }}</small>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Lungs/Heart</label>
                        <Dropdown v-model="form.lungs_heart" :options="['Normal', 'Rales', 'Wheeze', 'Murmur', 'Irregular heart rate']" class="w-full" />
                        <small class="text-red-500" v-if="errors.lungs_heart">{{ errors.lungs_heart }}</small>
                    </div>
                    <div class="form-group">
                        <label>Abdomen</label>
                        <Dropdown v-model="form.abdomen" :options="['Normal', 'Distended', 'Tenderness']" class="w-full" />
                        <small class="text-red-500" v-if="errors.abdomen">{{ errors.abdomen }}</small>
                    </div>
                    <div class="form-group">
                        <label>Deformities</label>
                        <Dropdown v-model="form.deformities" :options="['None', 'Acquired', 'Congenital']" class="w-full" />
                        <small class="text-red-500" v-if="errors.deformities">{{ errors.deformities }}</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <InputText v-model="form.remarks" class="w-full" />
                    <small class="text-red-500" v-if="errors.remarks">{{ errors.remarks }}</small>
                </div>

                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" class="p-button-secondary" @click="$inertia.visit(route('health-examination.show', student.id))" />
                    <Button type="submit" label="Update" :loading="form.processing" />
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Button from 'primevue/button'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'

const props = defineProps({
    student: {
        type: Object,
        required: true
    },
    examination: {
        type: Object,
        required: true
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
    'Severely Stunted',
    'Stunted',
    'Normal',
    'Tall'
]

const form = useForm({
    temperature: props.examination.temperature,
    heart_rate: props.examination.heart_rate,
    height: props.examination.height,
    weight: props.examination.weight,
    nutritional_status_bmi: props.examination.nutritional_status_bmi,
    nutritional_status_height: props.examination.nutritional_status_height,
    vision_screening: props.examination.vision_screening,
    auditory_screening: props.examination.auditory_screening,
    skin: props.examination.skin,
    scalp: props.examination.scalp,
    eye: props.examination.eye,
    ear: props.examination.ear,
    nose: props.examination.nose,
    mouth: props.examination.mouth,
    lungs_heart: props.examination.lungs_heart,
    abdomen: props.examination.abdomen,
    deformities: props.examination.deformities,
    remarks: props.examination.remarks
})

const submit = () => {
    form.put(route('health-examination.update', props.examination.id))
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
