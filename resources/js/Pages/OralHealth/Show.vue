<template>
    <Head title="Oral Health Examination" />
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header with Back Button -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-file-medical mr-2 text-indigo-600"></i>
                    Oral Health Examination Report
                </h1>
                <Link href="/pupil-health" class="no-underline">
                    <Button 
                        label="Back" 
                        icon="pi pi-arrow-left" 
                        class="p-button-outlined text-sm" 
                        style="border: 1px solid #64748b; color: #64748b; font-weight: 500; transition: all 0.2s ease;"
                    />
                </Link>
            </div>

            <!-- Main Layout: Left and Right Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left Column: Student Details & Oral Health Records -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Student Details Card -->
                    <div class="border rounded-lg bg-white shadow p-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Pupil Details</h2>
                        <div class="space-y-2 text-sm">
                            <div><strong>Name:</strong> {{ student.full_name }}</div>
                            <div><strong>LRN:</strong> {{ student.lrn || '12345678901' }}</div>
                            <div><strong>Grade Level:</strong> {{ student.grade_level }}</div>
                            <div><strong>School Year:</strong> {{ student.school_year || '2024-2025' }}</div>
                        </div>
                        
                        
                        <h3 class="text-md font-semibold text-gray-700 mt-4 mb-2">Personal Info</h3>
                        <div class="space-y-2 text-sm">
                            <div><strong>Age:</strong> {{ student.age }} years</div>
                            <div><strong>Sex:</strong> {{ student.sex }}</div>
                            <div><strong>Section:</strong> {{ student.section || 'Not Assigned' }}</div>
                            <div><strong>Status:</strong> <span class="text-green-600 font-semibold">Active</span></div>
                        </div>
                    </div>

                    <!-- Grade Selection and Print PDF -->
                    <div class="flex gap-2">
                        <Select 
                            v-model="selectedGrade" 
                            :options="gradeLevels" 
                            placeholder="Select Grade"
                            class="w-32 text-sm"
                            @change="onGradeChange"
                        />
                        <Button 
                            v-if="userRole === 'nurse'"
                            label="Print PDF" 
                            icon="pi pi-print" 
                            severity="info"
                            @click="printPDF"
                            class="text-sm"
                        />
                    </div>

                    <!-- Oral Health Treatment Card -->
                    <div class="border rounded-lg bg-white shadow">
                        <div class="bg-blue-700 text-white p-2 flex justify-between items-center text-sm">
                            <span>Oral Health Treatment</span>
                            <Button 
                                v-if="userRole === 'nurse'"
                                label="Add Treatment"
                                class="!bg-green-600 !text-white !border-green-600 hover:!bg-green-700 text-xs" 
                                @click="$inertia.visit(`/pupil-health/oral-health-treatment/${student.id}/create?grade=${encodeURIComponent(selectedGrade)}`)" 
                            />
                        </div>
                        <div class="p-3">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-1">Title</th>
                                        <th class="text-left py-1">Chief Complaint</th>
                                        <th class="text-left py-1">Treatment</th>
                                        <th class="text-left py-1">Date</th>
                                        <th class="text-left py-1">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="oralTreatmentRecords.length === 0">
                                        <td colspan="5" class="text-center py-2 text-gray-500">No records available</td>
                                    </tr>
                                    <tr v-for="treatment in oralTreatmentRecords" :key="treatment.id" class="border-b hover:bg-gray-50">
                                        <td class="py-2" style="max-width: 150px; word-break: break-all;">
                                            <div class="overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2;" :title="treatment.title">
                                                {{ treatment.title }}
                                            </div>
                                        </td>
                                        <td class="py-2" style="max-width: 200px; word-break: break-all;">
                                            <div class="overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2;" :title="treatment.chief_complaint">
                                                {{ treatment.chief_complaint }}
                                            </div>
                                        </td>
                                        <td class="py-2" style="max-width: 200px; word-break: break-all;">
                                            <div class="overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2;" :title="treatment.treatment">
                                                {{ treatment.treatment }}
                                            </div>
                                        </td>
                                        <td class="py-2 whitespace-nowrap">{{ new Date(treatment.date).toLocaleDateString() }}</td>
                                        <td class="py-2">
                                            <div class="flex gap-1">
                                                <Button 
                                                    v-if="treatment.can_edit"
                                                    icon="pi pi-pencil" 
                                                    size="small"
                                                    severity="info"
                                                    @click="editOralTreatment(treatment)"
                                                    class="!p-1 !text-xs"
                                                    title="Edit Treatment"
                                                />
                                                <Button 
                                                    label="View"
                                                    icon="pi pi-eye" 
                                                    size="small"
                                                    severity="info"
                                                    outlined
                                                    @click="viewOralTreatment(treatment)"
                                                    class="!p-1 !text-xs"
                                                    title="View Treatment"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Oral Health Examination Details -->
                <div class="lg:col-span-3">
                    <div class="border rounded-lg bg-white shadow">
                        <div class="bg-blue-700 text-white p-3 text-sm flex justify-between items-center">
                            <span>Pupil Oral Health Examination</span>
                            <div class="flex gap-2">
                                <Button 
                                    v-if="!currentExam && userRole === 'nurse'"
                                    label="Add Record" 
                                    icon="pi pi-plus" 
                                    class="p-button-sm !bg-green-600 !text-white !border-green-600 hover:!bg-green-700" 
                                    @click="$inertia.visit(`/pupil-health/oral-health/${student.id}/create?grade=${selectedGrade.replace('Grade ', '')}`)"
                                />
                                <Button 
                                    v-if="currentExam && userRole === 'nurse'"
                                    label="Edit Record" 
                                    icon="pi pi-pencil" 
                                    class="p-button-sm !bg-orange-600 !text-white !border-orange-600 hover:!bg-orange-700" 
                                    @click="$inertia.visit(`/pupil-health/oral-health/${currentExam.id}/edit?grade=${selectedGrade.replace('Grade ', '')}`)"
                                />
                            </div>
                        </div>
                        <div class="p-6">
                            <div v-if="oralHealthRecords.length === 0" class="text-center py-8 text-gray-500">
                                No oral health examination records found. Click the + button to add a new record.
                            </div>
                            <div v-else-if="currentExam" class="space-y-6">
                                <!-- Permanent Teeth Section -->
                                <div class="border rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-center mb-4">Permanent Teeth</h3>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium">Index d.f.t.:</span>
                                            <Tag :value="currentExam.permanent_index_dft || 0" severity="info" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">Teeth Decayed:</span>
                                            <Tag :value="currentExam.permanent_teeth_decayed || 0" severity="danger" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">Teeth Filled:</span>
                                            <Tag :value="currentExam.permanent_teeth_filled || 0" severity="success" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">Total d.f.t:</span>
                                            <Tag :value="currentExam.permanent_total_dft || 0" severity="warning" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">For Extraction:</span>
                                            <Tag :value="currentExam.permanent_for_extraction || 0" severity="danger" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">For Filling:</span>
                                            <Tag :value="currentExam.permanent_for_filling || 0" severity="warning" class="ml-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Temporary Teeth Section -->
                                <div class="border rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-center mb-4">Temporary Teeth</h3>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium">Index d.f.t.:</span>
                                            <Tag :value="currentExam.temporary_index_dft || 0" severity="info" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">Teeth Decayed:</span>
                                            <Tag :value="currentExam.temporary_teeth_decayed || 0" severity="danger" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">Teeth Filled:</span>
                                            <Tag :value="currentExam.temporary_teeth_filled || 0" severity="success" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">Total d.f.t:</span>
                                            <Tag :value="currentExam.temporary_total_dft || 0" severity="warning" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">For Extraction:</span>
                                            <Tag :value="currentExam.temporary_for_extraction || 0" severity="danger" class="ml-2" />
                                        </div>
                                        <div>
                                            <span class="font-medium">For Filling:</span>
                                            <Tag :value="currentExam.temporary_for_filling || 0" severity="warning" class="ml-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Oral Health Conditions Section -->
                                <div class="border rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-center mb-4">Oral Health Conditions</h3>
                                    <div v-if="currentExam.conditions && Object.keys(currentExam.conditions).length > 0" class="grid grid-cols-2 gap-3 text-sm">
                                        <div v-for="(date, condition) in currentExam.conditions" :key="condition" class="flex items-center justify-between p-2 bg-red-50 rounded border-l-4 border-red-400">
                                            <div class="flex items-center">
                                                <i class="pi pi-check-circle text-red-500 mr-2"></i>
                                                <span class="font-medium capitalize">{{ formatConditionName(condition) }}</span>
                                            </div>
                                            <Tag :value="date" severity="danger" class="text-xs" />
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-4 text-gray-500 text-sm">
                                        <i class="pi pi-shield-check text-green-500 text-2xl mb-2"></i>
                                        <div>No oral health conditions detected</div>
                                    </div>
                                </div>

                                <!-- Interactive Dental Chart Display -->
                                <div v-if="currentExam.tooth_symbols && Object.keys(currentExam.tooth_symbols).length > 0" class="border rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-center mb-4">Dental Chart</h3>
                                    <div class="dental-chart-container">
                                        <div class="dental-chart compact">
                                            <div class="chart-selector">
                                                <button 
                                                    :class="['chart-btn', { active: chartType === 'permanent' }]"
                                                    @click="chartType = 'permanent'"
                                                >
                                                    Permanent Teeth
                                                </button>
                                                <button 
                                                    :class="['chart-btn', { active: chartType === 'temporary' }]"
                                                    @click="chartType = 'temporary'"
                                                >
                                                    Temporary Teeth
                                                </button>
                                            </div>
                                            
                                            <div class="teeth-section">
                                                <div class="arch-label">Upper Teeth</div>
                                                <div :class="['teeth-row compact', { 'primary': chartType === 'temporary' }]" id="upper-teeth-display"></div>
                                                
                                                <div class="arch-label">Lower Teeth</div>
                                                <div :class="['teeth-row compact', { 'primary': chartType === 'temporary' }]" id="lower-teeth-display"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                Select a record to view details.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Oral Health Treatment View Modal -->
        <OralHealthTreatmentViewModal
            :visible="showOralTreatmentModal"
            :treatment="selectedOralTreatment"
            :student="student"
            :timer-status="oralTreatmentTimerStatus"
            :remaining-minutes="oralTreatmentRemainingMinutes"
            @close="closeOralTreatmentModal"
            @edit="editOralTreatmentFromModal"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import OralHealthTreatmentViewModal from '@/Components/Modals/OralHealthTreatmentViewModal.vue';
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css';
// Import page-specific styles
import '../../../css/pages/OralHealth/Show.css';

const props = defineProps({
    student: {
        type: Object,
        required: true
    },
    examinations: {
        type: Array,
        default: () => []
    },
    userRole: {
        type: String,
        default: 'admin'
    }
})

// Modal state
const showOralTreatmentModal = ref(false);
const selectedOralTreatment = ref(null);
const oralTreatmentTimerStatus = ref(null);
const oralTreatmentRemainingMinutes = ref(0);

const printPDF = () => {
    const url = `/oral-health-examination/${props.student.id}/pdf`;
    window.open(url, '_blank');
};

const editOralTreatment = (treatment) => {
    window.location.href = `/oral-health-treatment/${treatment.id}/edit`;
};

const viewOralTreatment = async (treatment) => {
    try {
        // Fetch full treatment details with timer status
        const response = await fetch(`/api/oral-health-treatment/timer-status/${treatment.id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            selectedOralTreatment.value = treatment;
            oralTreatmentTimerStatus.value = data.timer_status;
            oralTreatmentRemainingMinutes.value = data.remaining_minutes || 0;
            showOralTreatmentModal.value = true;
        } else {
            console.error('Failed to fetch oral treatment details');
            // Fallback to basic modal without timer info
            selectedOralTreatment.value = treatment;
            oralTreatmentTimerStatus.value = null;
            oralTreatmentRemainingMinutes.value = 0;
            showOralTreatmentModal.value = true;
        }
    } catch (error) {
        console.error('Error fetching oral treatment details:', error);
        // Fallback to basic modal
        selectedOralTreatment.value = treatment;
        oralTreatmentTimerStatus.value = null;
        oralTreatmentRemainingMinutes.value = 0;
        showOralTreatmentModal.value = true;
    }
};

const closeOralTreatmentModal = () => {
    showOralTreatmentModal.value = false;
    selectedOralTreatment.value = null;
    oralTreatmentTimerStatus.value = null;
    oralTreatmentRemainingMinutes.value = 0;
};

const refreshOralHealthData = async () => {
    try {
        const response = await fetch(`/api/oral-health-examination/${student.id}/data?grade=${selectedGrade.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            // Update the examinations data
            examinations.value = data.examinations || [];
            console.log('Oral health data refreshed');
        }
    } catch (error) {
        console.error('Error refreshing oral health data:', error);
    }
};

const editOralTreatmentFromModal = (treatment) => {
    closeOralTreatmentModal();
    window.location.href = `/oral-health-treatment/${treatment.id}/edit`;
};

import axios from 'axios'

const page = usePage()

const gradeLevels = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];

// Check if we should refresh data on page load
onMounted(() => {
    const returnData = sessionStorage.getItem('returnToOralHealth');
    if (returnData) {
        const data = JSON.parse(returnData);
        if (data.shouldRefresh && data.studentId === student.id) {
            // Clear the flag
            sessionStorage.removeItem('returnToOralHealth');
            // Refresh the data after a short delay
            setTimeout(() => {
                refreshOralHealthData();
            }, 500);
        }
    }
});

// Initialize grade from URL parameter, session storage, or flash data
const initializeGrade = () => {
    const urlGrade = new URLSearchParams(window.location.search).get('grade');
    const flashGrade = page.props.flash?.grade;
    const sessionGrade = sessionStorage.getItem(`currentGrade_${props.student.id}`);
    // Default to student's actual grade level
    const defaultGrade = props.student.grade_level;
    
    console.log('Grade initialization:', { urlGrade, flashGrade, sessionGrade, defaultGrade, studentGrade: props.student.grade_level });
    
    if (urlGrade) {
        // Handle both "K-2" and numeric grades
        const gradeValue = urlGrade === 'K-2' ? 'Kinder 2' : `Grade ${urlGrade}`;
        sessionStorage.setItem(`currentGrade_${props.student.id}`, gradeValue);
        return gradeValue;
    } else if (flashGrade) {
        // Handle both "K-2" and numeric grades
        const gradeValue = flashGrade === 'K-2' ? 'Kinder 2' : `Grade ${flashGrade}`;
        sessionStorage.setItem(`currentGrade_${props.student.id}`, gradeValue);
        return gradeValue;
    } else if (sessionGrade) {
        return sessionGrade;
    } else {
        return defaultGrade;
    }
};

const selectedGrade = ref(initializeGrade());

// Validate that selectedGrade is in gradeLevels array
if (!gradeLevels.includes(selectedGrade.value)) {
    console.warn('selectedGrade not found in gradeLevels, defaulting to student grade:', selectedGrade.value);
    selectedGrade.value = props.student.grade_level;
}

// Debug logging
console.log('Initial selectedGrade value:', selectedGrade.value);
console.log('gradeLevels:', gradeLevels);
console.log('Student grade level:', props.student.grade_level);
console.log('Is selectedGrade in gradeLevels?', gradeLevels.includes(selectedGrade.value));
const currentExam = ref(null);
const oralHealthRecords = ref([]);
const oralTreatmentRecords = ref([]);
const chartType = ref('permanent');

const fetchOralHealthByGrade = async (gradeLevel) => {
    try {
        const dbGradeLevel = gradeLevel.startsWith('Grade ') ? gradeLevel.replace('Grade ', '') : gradeLevel;
        
        const response = await axios.get(`/api/oral-health-examination/student/${props.student.id}`, {
            params: {
                grade_level: dbGradeLevel
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (response.data && response.data.message) {
            currentExam.value = null;
            oralHealthRecords.value = [];
        } else {
            currentExam.value = response.data;
            oralHealthRecords.value = [response.data];
        }
    } catch (error) {
        console.error('Error fetching oral health data:', error);
        currentExam.value = null;
        oralHealthRecords.value = [];
    }
};

const fetchOralTreatmentRecords = async () => {
    try {
        const response = await axios.get(`/api/oral-health-treatment/student/${props.student.id}?grade=${selectedGrade.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        // Handle the response format that matches the controller's return structure
        oralTreatmentRecords.value = response.data.data || response.data || [];
    } catch (error) {
        console.error('Error fetching oral treatment records:', error);
        oralTreatmentRecords.value = [];
    }
};

const onGradeChange = () => {
    console.log('Grade changed to:', selectedGrade.value);
    sessionStorage.setItem(`currentGrade_${props.student.id}`, selectedGrade.value);
    fetchOralHealthByGrade(selectedGrade.value);
    fetchOralTreatmentRecords();
};

// Dental chart display functions
const createDisplayTeeth = (type) => {
    const upperContainer = document.getElementById('upper-teeth-display');
    const lowerContainer = document.getElementById('lower-teeth-display');
    
    if (!upperContainer || !lowerContainer) return;
    
    upperContainer.innerHTML = '';
    lowerContainer.innerHTML = '';
    
    if (type === 'permanent') {
        createPermanentTeethDisplay(upperContainer, lowerContainer);
    } else {
        createTemporaryTeethDisplay(upperContainer, lowerContainer);
    }
    
    addSymbolsToTeeth();
};

const createPermanentTeethDisplay = (upperContainer, lowerContainer) => {
    // Upper teeth: 18-11, 21-28
    const upperNumbers = [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28];
    const upperTypes = ['Wisdom', 'Molar', 'Molar', 'Premolar', 'Premolar', 'Canine', 'Incisor', 'Incisor', 'Incisor', 'Incisor', 'Canine', 'Premolar', 'Premolar', 'Molar', 'Molar', 'Wisdom'];
    
    upperNumbers.forEach((number, index) => {
        const tooth = document.createElement('div');
        tooth.className = 'tooth';
        tooth.textContent = number;
        tooth.setAttribute('data-number', number);
        tooth.setAttribute('data-type', upperTypes[index]);
        
        // Better spacing calculation - center the teeth properly
        const spacing = 35; // Further reduced spacing to fit all teeth
        const startOffset = 85; // Increased for better centering
        const curve = Math.sin((index / (upperNumbers.length - 1)) * Math.PI) * 12; // Increased curve
        
        tooth.style.left = `${index * spacing + startOffset}px`;
        tooth.style.top = `${20 - curve}px`; // Adjusted base position
        
        upperContainer.appendChild(tooth);
    });
    
    // Lower teeth: 48-31, 41-38
    const lowerNumbers = [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38];
    const lowerTypes = ['Wisdom', 'Molar', 'Molar', 'Premolar', 'Premolar', 'Canine', 'Incisor', 'Incisor', 'Incisor', 'Incisor', 'Canine', 'Premolar', 'Premolar', 'Molar', 'Molar', 'Wisdom'];
    
    lowerNumbers.forEach((number, index) => {
        const tooth = document.createElement('div');
        tooth.className = 'tooth';
        tooth.textContent = number;
        tooth.setAttribute('data-number', number);
        tooth.setAttribute('data-type', lowerTypes[index]);
        
        // Better spacing calculation - same as upper teeth
        const spacing = 35;
        const startOffset = 85;
        const curve = Math.sin((index / (lowerNumbers.length - 1)) * Math.PI) * 12;
        
        tooth.style.left = `${index * spacing + startOffset}px`;
        tooth.style.top = `${20 + curve}px`; // Adjusted base position
        
        lowerContainer.appendChild(tooth);
    });
};

const createTemporaryTeethDisplay = (upperContainer, lowerContainer) => {
    // Upper temporary teeth: 55-51, 61-65
    const upperNumbers = [55, 54, 53, 52, 51, 61, 62, 63, 64, 65];
    const upperTypes = ['Molar', 'Molar', 'Canine', 'Incisor', 'Incisor', 'Incisor', 'Incisor', 'Canine', 'Molar', 'Molar'];
    
    upperNumbers.forEach((number, index) => {
        const tooth = document.createElement('div');
        tooth.className = 'tooth primary-tooth';
        tooth.textContent = number;
        tooth.setAttribute('data-number', number);
        tooth.setAttribute('data-type', upperTypes[index]);
        tooth.setAttribute('data-category', 'Temporary');
        
        // Better spacing for temporary teeth - centered positioning
        const spacing = 42; // Proper spacing for temporary teeth
        const startOffset = 140; // Better centering for temporary teeth
        const curve = Math.sin((index / (upperNumbers.length - 1)) * Math.PI) * 8;
        
        tooth.style.left = `${index * spacing + startOffset}px`;
        tooth.style.top = `${20 - curve}px`;
        
        upperContainer.appendChild(tooth);
    });
    
    // Lower temporary teeth: 85-81, 71-75
    const lowerNumbers = [85, 84, 83, 82, 81, 71, 72, 73, 74, 75];
    const lowerTypes = ['Molar', 'Molar', 'Canine', 'Incisor', 'Incisor', 'Incisor', 'Incisor', 'Canine', 'Molar', 'Molar'];
    
    lowerNumbers.forEach((number, index) => {
        const tooth = document.createElement('div');
        tooth.className = 'tooth primary-tooth';
        tooth.textContent = number;
        tooth.setAttribute('data-number', number);
        tooth.setAttribute('data-type', lowerTypes[index]);
        tooth.setAttribute('data-category', 'Temporary');
        
        // Better spacing for temporary teeth - same as upper
        const spacing = 42;
        const startOffset = 140;
        const curve = Math.sin((index / (lowerNumbers.length - 1)) * Math.PI) * 8;
        
        tooth.style.left = `${index * spacing + startOffset}px`;
        tooth.style.top = `${20 + curve}px`;
        
        lowerContainer.appendChild(tooth);
    });
};

const addSymbolsToTeeth = () => {
    if (!currentExam.value?.tooth_symbols) return;
    
    const teeth = document.querySelectorAll('.tooth');
    teeth.forEach(tooth => {
        const toothNumber = tooth.getAttribute('data-number');
        const symbols = currentExam.value.tooth_symbols[toothNumber];
        
        if (symbols && symbols.length > 0) {
            tooth.classList.add('has-symbol');
            
            // Remove existing symbol badges
            const existingBadge = tooth.querySelector('.tooth-symbol');
            if (existingBadge) {
                existingBadge.remove();
            }
            
            // Add symbol badge
            const symbolBadge = document.createElement('div');
            symbolBadge.className = 'tooth-symbol';
            
            if (symbols.length === 1) {
                // Single condition - show the original symbol
                symbolBadge.textContent = symbols[0];
            } else if (symbols.length > 1) {
                // Multiple conditions - show count as number
                symbolBadge.textContent = symbols.length;
                symbolBadge.classList.add('multiple-conditions');
            }
            
            // Store original symbols for tooltip
            symbolBadge.setAttribute('data-symbols', symbols.join(', '));
            tooth.appendChild(symbolBadge);
        }
    });
    
    // Add tooltip functionality
    addTooltipFunctionality();
};

const addTooltipFunctionality = () => {
    const teeth = document.querySelectorAll('.tooth');
    
    teeth.forEach(tooth => {
        // Remove existing event listeners
        if (tooth._mouseEnterHandler) {
            tooth.removeEventListener('mouseenter', tooth._mouseEnterHandler);
        }
        if (tooth._mouseLeaveHandler) {
            tooth.removeEventListener('mouseleave', tooth._mouseLeaveHandler);
        }
        
        const mouseEnterHandler = function(e) {
            // Prevent event bubbling from child elements
            e.stopPropagation();
            
            const number = this.dataset.number;
            const type = this.dataset.type;
            const category = this.dataset.category || 'Permanent';
            
            // Debug logging
            console.log('Tooth hover:', { number, currentExam: currentExam.value, toothSymbols: currentExam.value?.tooth_symbols });
            
            const symbols = currentExam.value?.tooth_symbols?.[number] || [];
            
            // Symbol descriptions
            const symbolDescriptions = {
                'X': 'Carious tooth indicated for extraction',
                'D': 'Carious tooth indicated for filling',
                'F': 'Filled',
                'F2': 'Permanent filled tooth with recurrence of decay',
                'M': 'Missing tooth',
                'RF': 'Root Fragment',
                'E': 'Extraction needed',
                'C': 'Caries',
                'R': 'Root canal',
                'I': 'Impacted',
                '√': 'Sound/erupted Permanent tooth',
                'PFS': 'Pit and Fissure Sealant',
                'JC': 'Jacket Crown',
                'P': 'Pontic',
                'RPD': 'Removable Partial Denture',
                'FB': 'Fixed Bridge',
                'CD': 'Complete Denture',
                'GI': 'Glass Ionomer',
                'CO': 'Composite',
                'AM': 'Amalgam'
            };
            
            // Remove any existing tooltips
            const existingTooltips = document.querySelectorAll('.tooltip');
            existingTooltips.forEach(t => t.remove());
            
            // Create tooltip with inline styles for better visibility
            const tooltip = document.createElement('div');
            tooltip.style.cssText = `
                position: fixed !important;
                background: rgba(0, 0, 0, 0.9) !important;
                color: white !important;
                padding: 8px 12px !important;
                border-radius: 6px !important;
                font-size: 12px !important;
                white-space: nowrap !important;
                z-index: 99999 !important;
                pointer-events: none !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3) !important;
                transform: translateX(-50%) !important;
                opacity: 1 !important;
            `;
            
            const conditionsText = symbols.length > 0 
                ? symbols.map(symbol => symbolDescriptions[symbol] || symbol).join(', ')
                : 'No conditions';
            
            tooltip.innerHTML = `
                <div style="text-align: center;">
                    <div style="font-weight: bold; margin-bottom: 2px;">${category} Tooth ${number}</div>
                    <div style="font-size: 11px; color: #ccc; margin-bottom: 2px;">${type}</div>
                    <div style="font-size: 11px; color: #ff6b9d; font-weight: bold;">${conditionsText}</div>
                </div>
            `;
            
            document.body.appendChild(tooltip);
            
            // Position tooltip using fixed positioning
            const rect = this.getBoundingClientRect();
            tooltip.style.left = (rect.left + (rect.width / 2)) + 'px';
            tooltip.style.top = (rect.top - 50) + 'px';
            
            this.tooltip = tooltip;
        };
        
        const mouseLeaveHandler = function(e) {
            // Only remove tooltip if we're actually leaving the tooth element
            if (!this.contains(e.relatedTarget)) {
                if (this.tooltip) {
                    this.tooltip.remove();
                    this.tooltip = null;
                }
            }
        };
        
        // Store handlers for cleanup
        tooth._mouseEnterHandler = mouseEnterHandler;
        tooth._mouseLeaveHandler = mouseLeaveHandler;
        
        tooth.addEventListener('mouseenter', mouseEnterHandler);
        tooth.addEventListener('mouseleave', mouseLeaveHandler);
    });
};

// Watch for chart type changes
watch(chartType, (newType) => {
    createDisplayTeeth(newType);
});

// Watch for current exam changes
watch(currentExam, (newExam) => {
    if (newExam && newExam.tooth_symbols) {
        setTimeout(() => {
            createDisplayTeeth(chartType.value);
        }, 100);
    }
});

// Watch for chart type changes
watch(chartType, (newType) => {
    if (currentExam.value && currentExam.value.tooth_symbols) {
        setTimeout(() => {
            createDisplayTeeth(newType);
        }, 50);
    }
});

onMounted(async () => {
    console.log('onMounted - selectedGrade.value:', selectedGrade.value);
    console.log('onMounted - gradeLevels includes selectedGrade?', gradeLevels.includes(selectedGrade.value));
    
    // Ensure the component is fully rendered before setting the grade
    await nextTick();
    
    // Force re-validation of selectedGrade
    if (!gradeLevels.includes(selectedGrade.value)) {
        console.log('Forcing selectedGrade to student grade level:', props.student.grade_level);
        selectedGrade.value = props.student.grade_level;
    }
    
    fetchOralHealthByGrade(selectedGrade.value);
    fetchOralTreatmentRecords();
    
    // Initialize chart after component mounts
    setTimeout(() => {
        if (currentExam.value && currentExam.value.tooth_symbols) {
            createDisplayTeeth(chartType.value);
        }
    }, 200);
});

watch(selectedGrade, (newGrade) => {
    if (newGrade) {
        fetchOralHealthByGrade(newGrade);
        fetchOralTreatmentRecords();
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

// Format condition names for display
const formatConditionName = (condition) => {
    const conditionNames = {
        'gingivitis': 'Gingivitis',
        'periodontal_disease': 'Periodontal Disease',
        'malocclusion': 'Malocclusion',
        'supernumerary_teeth': 'Supernumerary Teeth',
        'retained_deciduous_teeth': 'Retained Deciduous Teeth',
        'decubital_ulcer': 'Decubital Ulcer',
        'calculus': 'Calculus',
        'cleft_lip_palate': 'Cleft Lip/Palate',
        'root_fragment': 'Root Fragment',
        'fluorosis': 'Fluorosis',
        'others_specify': 'Others (Specify)'
    };
    
    return conditionNames[condition] || condition.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}
</script>
