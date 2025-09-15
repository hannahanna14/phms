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
                            label="Export PDF"
                            icon="pi pi-download"
                            class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700"
                            @click="printReport"
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

                        <!-- Permanent Teeth Columns -->
                        <Column v-if="hasRangeSet('permanent_teeth_decayed') || hasSelectedStudents()" header="Perm. Decayed">
                            <template #body="{ data }">
                                <span>{{ data.permanent_teeth_decayed || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('permanent_teeth_filled') || hasSelectedStudents()" header="Perm. Filled">
                            <template #body="{ data }">
                                <span>{{ data.permanent_teeth_filled || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('permanent_for_extraction') || hasSelectedStudents()" header="Perm. For Extraction">
                            <template #body="{ data }">
                                <span>{{ data.permanent_for_extraction || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('permanent_for_filling') || hasSelectedStudents()" header="Perm. For Filling">
                            <template #body="{ data }">
                                <span>{{ data.permanent_for_filling || 'N/A' }}</span>
                            </template>
                        </Column>

                        <!-- Temporary Teeth Columns -->
                        <Column v-if="hasRangeSet('temporary_teeth_decayed') || hasSelectedStudents()" header="Temp. Decayed">
                            <template #body="{ data }">
                                <span>{{ data.temporary_teeth_decayed || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('temporary_teeth_filled') || hasSelectedStudents()" header="Temp. Filled">
                            <template #body="{ data }">
                                <span>{{ data.temporary_teeth_filled || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('temporary_for_extraction') || hasSelectedStudents()" header="Temp. For Extraction">
                            <template #body="{ data }">
                                <span>{{ data.temporary_for_extraction || 'N/A' }}</span>
                            </template>
                        </Column>

                        <Column v-if="hasRangeSet('temporary_for_filling') || hasSelectedStudents()" header="Temp. For Filling">
                            <template #body="{ data }">
                                <span>{{ data.temporary_for_filling || 'N/A' }}</span>
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
    maxValues: Object,
    selected_students: Array
});

// Check if a range was set for a field (not [0,0] or null)
const hasRangeSet = (fieldKey) => {
    if (!props.minValues && !props.maxValues) return false;
    
    const minVal = props.minValues?.[fieldKey];
    const maxVal = props.maxValues?.[fieldKey];
    
    // Show column if either min or max is set and not zero
    return (minVal !== null && minVal !== undefined && minVal > 0) || 
           (maxVal !== null && maxVal !== undefined && maxVal > 0);
};

// Check if specific students were selected
const hasSelectedStudents = () => {
    return props.selected_students && Array.isArray(props.selected_students) && props.selected_students.length > 0;
};

const goBack = () => {
    router.visit('/oral-health-report');
};

const printReport = () => {
    // Create a form and submit it to trigger PDF download using blade template
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/oral-health-report/export-pdf';
    form.target = '_blank';

    // Add CSRF token
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content || '';
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);

    // Add grade level
    const gradeInput = document.createElement('input');
    gradeInput.type = 'hidden';
    gradeInput.name = 'grade_level';
    gradeInput.value = props.grade_level.replace('Grade ', '');
    form.appendChild(gradeInput);

    // Add section if exists
    if (props.section) {
        const sectionInput = document.createElement('input');
        sectionInput.type = 'hidden';
        sectionInput.name = 'section';
        sectionInput.value = props.section;
        form.appendChild(sectionInput);
    }

    // Add fields
    props.fields.forEach(field => {
        const fieldInput = document.createElement('input');
        fieldInput.type = 'hidden';
        fieldInput.name = 'fields[]';
        fieldInput.value = field;
        form.appendChild(fieldInput);
    });

    // Add oral exam fields
    if (props.oral_exam_fields && props.oral_exam_fields.length > 0) {
        props.oral_exam_fields.forEach(field => {
            const fieldInput = document.createElement('input');
            fieldInput.type = 'hidden';
            fieldInput.name = 'oral_exam_fields[]';
            fieldInput.value = field;
            form.appendChild(fieldInput);
        });
    }

    // Add optional filters
    if (props.gender_filter) {
        const genderInput = document.createElement('input');
        genderInput.type = 'hidden';
        genderInput.name = 'gender_filter';
        genderInput.value = props.gender_filter;
        form.appendChild(genderInput);
    }

    if (props.min_age) {
        const minAgeInput = document.createElement('input');
        minAgeInput.type = 'hidden';
        minAgeInput.name = 'min_age';
        minAgeInput.value = props.min_age;
        form.appendChild(minAgeInput);
    }

    if (props.max_age) {
        const maxAgeInput = document.createElement('input');
        maxAgeInput.type = 'hidden';
        maxAgeInput.name = 'max_age';
        maxAgeInput.value = props.max_age;
        form.appendChild(maxAgeInput);
    }

    if (props.sort_by) {
        const sortInput = document.createElement('input');
        sortInput.type = 'hidden';
        sortInput.name = 'sort_by';
        sortInput.value = props.sort_by;
        form.appendChild(sortInput);
    }

    // Add min/max values
    if (props.minValues) {
        Object.keys(props.minValues).forEach(key => {
            const minInput = document.createElement('input');
            minInput.type = 'hidden';
            minInput.name = `minValues[${key}]`;
            minInput.value = props.minValues[key];
            form.appendChild(minInput);
        });
    }

    if (props.maxValues) {
        Object.keys(props.maxValues).forEach(key => {
            const maxInput = document.createElement('input');
            maxInput.type = 'hidden';
            maxInput.name = `maxValues[${key}]`;
            maxInput.value = props.maxValues[key];
            form.appendChild(maxInput);
        });
    }

    // Add selected students
    if (props.selected_students && props.selected_students.length > 0) {
        props.selected_students.forEach(student => {
            const studentInput = document.createElement('input');
            studentInput.type = 'hidden';
            studentInput.name = 'selected_students[]';
            studentInput.value = typeof student === 'object' ? student.id : student;
            form.appendChild(studentInput);
        });
    }

    // Append form to body and submit
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
};
</script>
