<template>
    <Head title="Add Health Examination" />
    <div class="health-examination-form">
        <div class="white-container">
            <h2 class="text-lg font-semibold mb-4">Student Health Examination</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Vital Signs -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="form-group">
                        <label>Temperature/BP</label>
                        <input v-model="form.temperature" type="text" class="form-input" />
                    </div>
                    <div class="form-group">
                        <label>Heart Rate</label>
                        <input v-model="form.heart_rate" type="text" class="form-input" />
                    </div>
                    <div class="form-group">
                        <label>Height(cm)</label>
                        <input v-model="form.height" type="text" class="form-input" />
                    </div>
                    <div class="form-group">
                        <label>Weight(kg)</label>
                        <input v-model="form.weight" type="text" class="form-input" />
                    </div>
                </div>

                <!-- Nutritional Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Nutritional Status(BMI)</label>
                        <select v-model="form.nutritional_status_bmi" class="form-select">
                            <option value="Normal Weight">Normal Weight</option>
                            <option value="Wasted/Underweight">Wasted/Underweight</option>
                            <option value="Severly Wasted/Underwt">Severly Wasted/Underwt</option>
                            <option value="Overweight">Overweight</option>
                            <option value="Obese">Obese</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nutritional Status(Height for Age)</label>
                        <select v-model="form.nutritional_status_height" class="form-select">
                            <option value="Normal Height">Normal Height</option>
                            <option value="Stunted">Stunted</option>
                            <option value="Severly Stunted">Severly Stunted</option>
                            <option value="Tall">Tall</option>
                        </select>
                    </div>
                </div>

                <!-- Screenings -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label>Vision Screening</label>
                        <select v-model="form.vision_screening" class="form-select">
                            <option value="Passed">Passed</option>
                            <option value="Failed">Failed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Auditory Screening</label>
                        <select v-model="form.auditory_screening" class="form-select">
                            <option value="Passed">Passed</option>
                            <option value="Failed">Failed</option>
                        </select>
                    </div>
                </div>

                <!-- Physical Assessment -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Skin</label>
                        <select v-model="form.skin" class="form-select">
                            <option value="Normal">Normal</option>
                            <option value="Redness of Skin">Redness of Skin</option>
                            <option value="White Spots">White Spots</option>
                            <option value="Flaky Skin">Flaky Skin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Scalp</label>
                        <select v-model="form.scalp" class="form-select">
                            <option value="Normal">Normal</option>
                            <option value="Presence of Lice">Presence of Lice</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Eye</label>
                        <select v-model="form.eye" class="form-select">
                            <option value="Normal">Normal</option>
                            <option value="Eye Redness">Eye Redness</option>
                            <option value="Pale Conjunctiva">Pale Conjunctiva</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Ear</label>
                        <select v-model="form.ear" class="form-select">
                            <option value="Normal">Normal</option>
                            <option value="Ear discharge">Ear discharge</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nose</label>
                        <select v-model="form.nose" class="form-select">
                            <option value="Normal">Normal</option>
                            <option value="Mucus discharge">Mucus discharge</option>
                            <option value="Nose Bleeding">Nose Bleeding</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mouth</label>
                        <select v-model="form.mouth" class="form-select">
                            <option value="Normal">Normal</option>
                            <option value="Enlarged tonsil">Enlarged tonsil</option>
                            <option value="Inflamed pharynx">Inflamed pharynx</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="form-group">
                        <label>Lungs/Heart</label>
                        <select v-model="form.lungs_heart" class="form-select">
                            <option value="Normal">Normal</option>
                            <option value="Rales">Rales</option>
                            <option value="Wheeze">Wheeze</option>
                            <option value="Murmur">Murmur</option>
                            <option value="Irregular heart rate">Irregular heart rate</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Abdomen</label>
                        <select v-model="form.abdomen" class="form-select">
                            <option value="Normal">Normal</option>
                            <option value="Distended">Distended</option>
                            <option value="Tenderness">Tenderness</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deformities</label>
                        <select v-model="form.deformities" class="form-select">
                            <option value="None">None</option>
                            <option value="Acquired">Acquired</option>
                            <option value="Congenital">Congenital</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea v-model="form.remarks" class="form-textarea"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" class="p-button-secondary" @click="$inertia.visit(route('health-examination.show', student.id))" />
                    <Button type="submit" label="Add" />
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Button from 'primevue/button'

const props = defineProps({
    student: {
        type: Object,
        required: true
    }
})

const form = useForm({
    student_id: props.student.id,
    examination_date: new Date().toISOString().split('T')[0],
    temperature: '',
    heart_rate: '',
    height: '',
    weight: '',
    nutritional_status_bmi: 'Normal Weight',
    nutritional_status_height: 'Normal Height',
    vision_screening: 'Passed',
    auditory_screening: 'Passed',
    skin: 'Normal',
    scalp: 'Normal',
    eye: 'Normal',
    ear: 'Normal',
    nose: 'Normal',
    mouth: 'Normal',
    neck: 'Normal',
    throat: 'Normal',
    lungs_heart: 'Normal',
    abdomen: 'Normal',
    deformities: 'None',
    remarks: ''
})

const submit = () => {
    form.post(route('health-examination.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect back to student details
            $inertia.visit(route('health-examination.show', student.id))
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
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.form-group label {
    font-size: 0.875rem;
    color: #666;
}

.form-input,
.form-select,
.form-textarea {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
}

.form-textarea {
    min-height: 100px;
    resize: vertical;
}
</style>
