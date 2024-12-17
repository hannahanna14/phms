<template>
    <Head title="Add Oral Health Examination" />
    <div class="oral-examination-form">
        <div class="white-container">
            <h2 class="text-lg font-semibold mb-4">Student Oral Health Examination</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Temporary Teeth -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="field">
                        <label for="index_dft">Index d.f.t.</label>
                        <InputNumber id="index_dft" v-model="form.index_dft" />
                    </div>
                    <div class="field">
                        <label for="number_of_teeth_decayed">Number of Teeth decayed</label>
                        <InputNumber id="number_of_teeth_decayed" v-model="form.number_of_teeth_decayed" />
                    </div>
                    <div class="field">
                        <label for="number_of_teeth_filled">Number of Teeth filled</label>
                        <InputNumber id="number_of_teeth_filled" v-model="form.number_of_teeth_filled" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="field">
                        <label for="total_dft">Total d.f.t</label>
                        <InputNumber id="total_dft" v-model="form.total_dft" />
                    </div>
                    <div class="field">
                        <label for="for_extraction">For Extraction</label>
                        <InputNumber id="for_extraction" v-model="form.for_extraction" />
                    </div>
                    <div class="field">
                        <label for="for_filling">For Filling</label>
                        <InputNumber id="for_filling" v-model="form.for_filling" />
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <Button type="button" label="Cancel" class="p-button-secondary" @click="$inertia.visit(route('oral-health-examination.show', student.id))" />
                    <Button type="submit" label="Add" />
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Button from 'primevue/button'
import InputNumber from 'primevue/inputnumber'

const props = defineProps({
    student: {
        type: Object,
        required: true
    }
})

const form = useForm({
    student_id: props.student.id,
    index_dft: null,
    number_of_teeth_decayed: null,
    number_of_teeth_filled: null,
    total_dft: null,
    for_extraction: null,
    for_filling: null
})

const submit = () => {
    form.post(route('oral-health-examination.store'), {
        preserveScroll: true
    })
}
</script>

<style scoped>
.oral-examination-form {
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

.field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.field label {
    font-size: 0.875rem;
    color: #666;
}
</style>
