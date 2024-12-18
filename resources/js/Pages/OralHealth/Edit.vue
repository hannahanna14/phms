<template>
    <Head title="Edit Oral Health Examination" />
    <div class="oral-health-examination-edit">
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-6">Edit Oral Health Examination for {{ student.full_name }}</h1>
                
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
                            Update Record
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
    },
    examination: {
        type: Object,
        required: true
    }
})

const form = useForm({
    index_dft: props.examination.index_dft,
    number_of_teeth_decayed: props.examination.number_of_teeth_decayed,
    number_of_teeth_filled: props.examination.number_of_teeth_filled,
    total_dft: props.examination.total_dft,
    for_extraction: props.examination.for_extraction,
    for_filling: props.examination.for_filling
})

const submit = () => {
    form.put(route('oral-health-examination.update', props.examination.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect back to student's oral health examination page
            $inertia.visit(route('oral-health-examination.show', props.student.id))
        }
    })
}
</script>

<style scoped>
.oral-health-examination-edit {
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
