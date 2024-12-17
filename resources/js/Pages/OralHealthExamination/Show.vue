<template>
    <Head :title="`${student.full_name}'s Oral Health Examinations`" />
    <div class="p-4">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">{{ student.full_name }}'s Oral Health Records</h1>
            <Button icon="pi pi-plus" @click="$inertia.visit(route('oral-health-examination.create', student.id))" />
        </div>

        <div v-if="examinations.length === 0" class="text-gray-500">
            No oral health examinations recorded yet.
        </div>

        <div v-else class="space-y-4">
            <div v-for="exam in examinations" :key="exam.id" class="bg-white p-4 rounded-lg shadow">
                <div class="flex justify-between items-start">
                    <span>Oral Health Examination</span>
                    <span>{{ new Date(exam.examination_date).toLocaleDateString() }}</span>
                    <div class="flex gap-2">
                        <Button icon="pi pi-pencil" class="p-button-text p-button-sm" 
                            @click="$inertia.visit(route('oral-health-examination.edit', exam.id))" />
                        <Button icon="pi pi-trash" class="p-button-text p-button-sm p-button-danger" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div>
                        <div class="text-sm text-gray-600">Index d.f.t.</div>
                        <div>{{ exam.index_dft }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Teeth Decayed</div>
                        <div>{{ exam.number_of_teeth_decayed }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Teeth Filled</div>
                        <div>{{ exam.number_of_teeth_filled }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div>
                        <div class="text-sm text-gray-600">Total d.f.t</div>
                        <div>{{ exam.total_dft }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">For Extraction</div>
                        <div>{{ exam.for_extraction }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">For Filling</div>
                        <div>{{ exam.for_filling }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import Button from 'primevue/button'

defineProps({
    student: {
        type: Object,
        required: true
    },
    examinations: {
        type: Array,
        required: true,
        default: () => []
    }
})
</script>
