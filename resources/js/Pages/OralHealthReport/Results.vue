<template>
    <Head title="Oral Health Report Results" />

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Oral Health Report Results</h1>
                        <p class="text-gray-600 mt-1">{{ grade_level }} {{ section ? '- Section ' + section : '' }}</p>
                    </div>
                    <div class="flex gap-3">
                        <Button
                            label="Back to Report"
                            icon="pi pi-arrow-left"
                            @click="goBack"
                            severity="secondary"
                        />
                        <Button
                            label="Print Report"
                            icon="pi pi-print"
                            @click="printReport"
                            class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                        />
                    </div>
                </div>
            </div>

            <!-- Report Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ reportData.length }}</div>
                    <div class="text-sm text-gray-600">Total Students</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-green-600">{{ grade_level }}</div>
                    <div class="text-sm text-gray-600">Grade Level</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ section || 'All' }}</div>
                    <div class="text-sm text-gray-600">Section</div>
                </div>
            </div>

            <!-- Report Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <DataTable
                    :value="reportData"
                    :paginator="true"
                    :rows="25"
                    :rowsPerPageOptions="[10, 25, 50, 100]"
                    stripedRows
                    :scrollable="true"
                    scrollHeight="600px"
                >
                        <!-- Student information columns (always shown) -->
                        <Column field="name" header="Name" sortable frozen />
                        <Column field="lrn" header="LRN" sortable />
                        <Column field="grade_level" header="Grade" sortable />
                        <Column field="section" header="Section" sortable />
                        <Column field="gender" header="Gender" sortable />
                        <Column field="age" header="Age" sortable />
                        <Column field="birthdate" header="Birthdate" sortable />

                        <!-- Oral examination field columns -->
                        <Column v-if="oral_exam_fields.includes('decayed_teeth')" header="Decayed Teeth">
                            <template #body="{ data }">
                                <span>{{ data.oral_health?.permanent_teeth_decayed || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="oral_exam_fields.includes('missing_teeth')" header="Missing Teeth">
                            <template #body="{ data }">
                                <span>{{ data.oral_health?.permanent_for_extraction || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="oral_exam_fields.includes('filled_teeth')" header="Filled Teeth">
                            <template #body="{ data }">
                                <span>{{ data.oral_health?.permanent_teeth_filled || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="oral_exam_fields.includes('total_dmft')" header="Total DMFT">
                            <template #body="{ data }">
                                <span>{{ data.oral_health?.permanent_total_dft || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="oral_exam_fields.includes('sealant')" header="Sealant">
                            <template #body="{ data }">
                                <span>{{ data.oral_health?.permanent_index_dft || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="oral_exam_fields.includes('fluoride_application')" header="Fluoride Application">
                            <template #body="{ data }">
                                <span>{{ data.oral_health?.temporary_total_dft || 'N/A' }}</span>
                            </template>
                        </Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const props = defineProps({
    reportData: Array,
    grade_level: String,
    section: String,
    fields: Array,
    oral_exam_fields: Array,
    gender_filter: String,
    min_age: Number,
    max_age: Number,
    sort_by: String,
    minValues: Object,
    maxValues: Object
});

const goBack = () => {
    router.visit('/oral-health-report');
};

const printReport = () => {
    // Create form data with current report parameters
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/oral-health-report/export-pdf';
    form.target = '_blank';

    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
    }

    // Add all the report parameters
    const params = {
        grade_level: props.grade_level,
        section: props.section,
        fields: props.fields,
        oral_exam_fields: props.oral_exam_fields,
        gender_filter: props.gender_filter,
        min_age: props.min_age,
        max_age: props.max_age,
        sort_by: props.sort_by,
        minValues: props.minValues,
        maxValues: props.maxValues
    };

    Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
            if (Array.isArray(params[key])) {
                params[key].forEach(value => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `${key}[]`;
                    input.value = value;
                    form.appendChild(input);
                });
            } else if (typeof params[key] === 'object') {
                Object.keys(params[key]).forEach(subKey => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `${key}[${subKey}]`;
                    input.value = params[key][subKey];
                    form.appendChild(input);
                });
            } else {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = params[key];
                form.appendChild(input);
            }
        }
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
};
</script>
