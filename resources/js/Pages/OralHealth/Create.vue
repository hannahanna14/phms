<template>
    <Head title="Create Oral Health Examination" />
    <div class="oral-health-examination-create">
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-6">Oral Health Examination for {{ student.full_name }}</h1>
                
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Index DFT</label>
                            <input 
                                type="number" 
                                v-model="form.index_dft" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                placeholder="Enter Index DFT"
                            />
                            <small v-if="errors.index_dft" class="text-red-500">{{ errors.index_dft }}</small>
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Number of Teeth Decayed</label>
                            <input 
                                type="number" 
                                v-model="form.number_of_teeth_decayed" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                placeholder="Enter Decayed Teeth"
                            />
                            <small v-if="errors.number_of_teeth_decayed" class="text-red-500">{{ errors.number_of_teeth_decayed }}</small>
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Number of Teeth Filled</label>
                            <input 
                                type="number" 
                                v-model="form.number_of_teeth_filled" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                placeholder="Enter Filled Teeth"
                            />
                            <small v-if="errors.number_of_teeth_filled" class="text-red-500">{{ errors.number_of_teeth_filled }}</small>
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">Total DFT</label>
                            <input 
                                type="number" 
                                v-model="form.total_dft" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                placeholder="Enter Total DFT"
                            />
                            <small v-if="errors.total_dft" class="text-red-500">{{ errors.total_dft }}</small>
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">For Extraction</label>
                            <input 
                                type="number" 
                                v-model="form.for_extraction" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                placeholder="Enter Teeth for Extraction"
                            />
                            <small v-if="errors.for_extraction" class="text-red-500">{{ errors.for_extraction }}</small>
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700">For Filling</label>
                            <input 
                                type="number" 
                                v-model="form.for_filling" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                placeholder="Enter Teeth for Filling"
                            />
                            <small v-if="errors.for_filling" class="text-red-500">{{ errors.for_filling }}</small>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button 
                            type="button" 
                            @click="$inertia.visit(route('oral-health-examination.show', student.id))"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Save Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'

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
        preserveScroll: true,
        onSuccess: () => {
            // Redirect back to student's oral health examination page
            $inertia.visit(route('oral-health-examination.show', props.student.id))
        }
    })
}
</script>

<style scoped>
.oral-health-examination-create {
    background-color: #f5f7f9;
    min-height: 100vh;
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
</style>
