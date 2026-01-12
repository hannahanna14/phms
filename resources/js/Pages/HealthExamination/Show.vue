<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick } from 'vue'
import Button from 'primevue/button'
import Card from 'primevue/card'
// Import shared CRUD form styles
import '../../../css/pages/shared/CrudForm.css';
// Import page-specific styles
import '../../../css/pages/HealthExamination/Show.css';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import HealthTreatmentViewModal from '@/Components/Modals/HealthTreatmentViewModal.vue';
import GrowthChart from '@/components/GrowthChart.vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';

const { student, healthExamination, selectedGrade: propSelectedGrade, userRole } = usePage().props;
const currentSchoolYear = computed(() => usePage().props.currentSchoolYear || `${new Date().getFullYear()}-${new Date().getFullYear() + 1}`);

// Modal state
const showTreatmentModal = ref(false);
const selectedTreatment = ref(null);
const treatmentTimerStatus = ref(null);
const treatmentRemainingMinutes = ref(0);

console.log('Props received:', { student, healthExamination, propSelectedGrade });
console.log('Current URL:', window.location.href);
console.log('URL search params:', new URLSearchParams(window.location.search).get('grade'));

const gradeLevels = computed(() => {
    const standardGrades = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
    // Convert student grade to match format (e.g., "6" becomes "Grade 6")
    const studentGradeFormatted = isNaN(student.grade_level) ? student.grade_level : `Grade ${student.grade_level}`;
    return standardGrades.includes(studentGradeFormatted) ? standardGrades : [...standardGrades, studentGradeFormatted];
});

// Option 3: Smart default with session-based override
const getInitialGrade = () => {
    // Check URL parameter first (from redirects)
    const urlGrade = new URLSearchParams(window.location.search).get('grade');
    if (urlGrade) {
        console.log('Using grade from URL:', urlGrade);
        const formattedGrade = isNaN(urlGrade) ? urlGrade : `Grade ${urlGrade}`;
        // Store in session for this browser session only
        sessionStorage.setItem(`currentGrade_${student.id}`, formattedGrade);
        return formattedGrade;
    }

    // Check flash data from redirects
    const page = usePage();
    const flashGrade = page.props.flash?.grade;
    if (flashGrade) {
        console.log('Using grade from flash:', flashGrade);
        const formattedGrade = isNaN(flashGrade) ? flashGrade : `Grade ${flashGrade}`;
        sessionStorage.setItem(`currentGrade_${student.id}`, formattedGrade);
        return formattedGrade;
    }

    // Then check prop
    if (propSelectedGrade) {
        console.log('Using grade from prop:', propSelectedGrade);
        return isNaN(propSelectedGrade) ? propSelectedGrade : `Grade ${propSelectedGrade}`;
    }

    // Check session storage for current session context
    const sessionGrade = sessionStorage.getItem(`currentGrade_${student.id}`);
    if (sessionGrade) {
        console.log('Using session grade:', sessionGrade);
        return sessionGrade;
    }

    // Default to student's grade (fresh session)
    console.log('Using student default grade:', student.grade_level);
    return isNaN(student.grade_level) ? student.grade_level : `Grade ${student.grade_level}`;
};

// Initialize with the correct grade immediately to prevent flickering
const initialGrade = getInitialGrade();
const selectedGrade = ref(initialGrade);

// Validate that selectedGrade is in gradeLevels array
const currentGradeLevels = gradeLevels.value;
if (!currentGradeLevels.includes(selectedGrade.value)) {
    console.warn('selectedGrade not found in gradeLevels, defaulting to student grade:', selectedGrade.value);
    selectedGrade.value = student.grade_level;
}

// Debug logging
console.log('Initial selectedGrade value:', selectedGrade.value);
console.log('gradeLevels:', currentGradeLevels);
console.log('Student grade level:', student.grade_level);
console.log('Is selectedGrade in gradeLevels?', currentGradeLevels.includes(selectedGrade.value));

onMounted(async () => {
    console.log('onMounted - Initial grade set to:', selectedGrade.value);

    // Ensure the component is fully rendered before setting the grade
    await nextTick();

    // Force re-validation of selectedGrade
    if (!currentGradeLevels.includes(selectedGrade.value)) {
        console.log('Forcing selectedGrade to student grade level:', student.grade_level);
        selectedGrade.value = student.grade_level;
    }

    // Initial load with current grade
    await fetchRecordByGrade(selectedGrade.value);
    await fetchTreatmentRecords();
    await fetchAllHealthExaminations();

    // Hide skeleton after data loads
    isLoading.value = false;
});
const healthRecords = ref([]);
const recordDetails = ref({});
const currentRecord = ref(null);
const treatmentRecords = ref([]);
const allHealthExaminations = ref([]);
const isLoading = ref(true);

const fetchRecordByGrade = async (gradeLevel) => {
    try {
        // Convert display format back to database format (e.g., "Grade 6" becomes "6")
        const dbGradeLevel = gradeLevel.startsWith('Grade ') ? gradeLevel.replace('Grade ', '') : gradeLevel;

        console.log('Fetching health exam for grade:', dbGradeLevel);

        const response = await axios.get(`/api/health-examination/student/${student.id}`, {
            params: {
                grade_level: dbGradeLevel
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        console.log('API Response:', response.data);

        // Check if response contains actual health examination data or just a message
        if (response.data && response.data.message) {
            console.log('No record found for this grade level');
            currentRecord.value = null;
        } else {
            console.log('Record found:', response.data);
            currentRecord.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching record details:', error);
        currentRecord.value = null;
    }
};

const fetchTreatmentRecords = async () => {
    try {
        const response = await axios.get(`/api/health-treatment/student/${student.id}`, {
            params: {
                grade: selectedGrade.value
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        treatmentRecords.value = response.data.data || [];
        console.log('Treatment records fetched for grade:', selectedGrade.value, treatmentRecords.value);
    } catch (error) {
        console.error('Error fetching treatment records:', error);
        treatmentRecords.value = [];
    }
};

const fetchAllHealthExaminations = async () => {
    try {
        const response = await axios.get(`/api/health-examination/student/${student.id}/all`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        allHealthExaminations.value = response.data || [];
        console.log('All health examinations fetched:', allHealthExaminations.value);
    } catch (error) {
        console.error('Error fetching all health examinations:', error);
        allHealthExaminations.value = [];
    }
};

const onGradeChange = async () => {
    console.log('Grade changed to:', selectedGrade.value);
    sessionStorage.setItem(`currentGrade_${student.id}`, selectedGrade.value);

    // Show skeleton loaders when switching grades
    isLoading.value = true;

    try {
        await fetchRecordByGrade(selectedGrade.value);
        await fetchTreatmentRecords();
    } finally {
        // Hide skeleton loaders after data loads
        isLoading.value = false;
    }
};

const printHealthExaminationPdf = () => {
    const url = `/health-examination-pdf/${student.id}`;
    window.open(url, '_blank');
};

const editTreatment = (treatment) => {
    window.location.href = `/health-treatment/${treatment.id}/edit`;
};

const viewTreatment = async (treatment) => {
    try {
        // Fetch full treatment details with timer status
        const response = await fetch(`/api/health-treatment/timer-status/${treatment.id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            selectedTreatment.value = treatment;
            treatmentTimerStatus.value = data.timer_status;
            treatmentRemainingMinutes.value = data.remaining_minutes || 0;
            showTreatmentModal.value = true;
        } else {
            console.error('Failed to fetch treatment details');
            // Fallback to basic modal without timer info
            selectedTreatment.value = treatment;
            treatmentTimerStatus.value = null;
            treatmentRemainingMinutes.value = 0;
            showTreatmentModal.value = true;
        }
    } catch (error) {
        console.error('Error fetching treatment details:', error);
        // Fallback to basic modal
        selectedTreatment.value = treatment;
        treatmentTimerStatus.value = null;
        treatmentRemainingMinutes.value = 0;
        showTreatmentModal.value = true;
    }
};

const closeTreatmentModal = () => {
    showTreatmentModal.value = false;
    selectedTreatment.value = null;
    treatmentTimerStatus.value = null;
    treatmentRemainingMinutes.value = 0;
};

const refreshExaminationData = async () => {
    try {
        const response = await fetch(`/api/health-examination/${student.id}/data?grade=${selectedGrade.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            // Update the examinations data
            examinations.value = data.examinations || [];
            console.log('Examination data refreshed');
        }
    } catch (error) {
        console.error('Error refreshing examination data:', error);
    }
};

const editTreatmentFromModal = (treatment) => {
    closeTreatmentModal();
    window.location.href = `/health-treatment/${treatment.id}/edit`;
};


// Check if we should refresh data on page load
onMounted(() => {
    const returnData = sessionStorage.getItem('returnToHealthExam');
    if (returnData) {
        const data = JSON.parse(returnData);
        if (data.shouldRefresh && data.studentId === student.id) {
            // Clear the flag
            sessionStorage.removeItem('returnToHealthExam');
            // Refresh the data after a short delay
            setTimeout(() => {
                refreshExaminationData();
            }, 500);
        }
    }
});

// Remove watch to prevent page reloads - only use @change handler

</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Enhanced Header -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 p-8 mb-8 backdrop-blur-sm">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="pi pi-file text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent mb-2">Health Examination Report</h1>
                            <p class="text-slate-600 font-medium">Comprehensive health assessment and medical records</p>
                        </div>
                    </div>
                    <Link href="/pupil-health" class="no-underline">
                        <Button
                            label="Back to List"
                            icon="pi pi-arrow-left"
                            class="p-button-outlined p-button-lg shadow-sm hover:shadow-md transition-all duration-300"
                            style="border: 2px solid #e5e7eb; color: #374151; font-weight: 600; border-radius: 12px;"
                        />
                    </Link>
                </div>
            </div>

            <!-- Main Layout: Left and Right Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left Column: Student Details & Treatment -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Student Details Card -->
                    <SkeletonLoader
                        v-if="isLoading"
                        type="card"
                        :lines="8"
                        class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden backdrop-blur-sm"
                    />

                    <div v-else class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden backdrop-blur-sm">
                        <div class="bg-gradient-to-r from-emerald-500 via-blue-500 to-indigo-600 p-5">
                            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                <i class="pi pi-user text-white/90"></i>
                                Pupil Details
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4 text-sm">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Name</div>
                                        <div class="font-bold text-slate-900 text-lg">{{ student.full_name }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">LRN</div>
                                        <div class="font-mono text-slate-900 text-lg font-bold">{{ student.lrn || '12345678901' }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Grade</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.grade_level }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">School Year</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.school_year || '2024-2025' }}</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mt-6">
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Age</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.age }} years</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Sex</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.sex }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                        <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Section</div>
                                        <div class="text-slate-900 text-lg font-bold">{{ student.section || 'Not Assigned' }}</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200/50">
                                            <div class="text-slate-600 font-semibold mb-2 text-sm uppercase tracking-wide">Status</div>
                                            <div>
                                                <span v-if="student.is_active && student.school_year === currentSchoolYear" class="text-green-600 text-lg font-bold">Active</span>
                                                <span v-else class="text-red-600 text-lg font-bold">Inactive</span>
                                                <div class="text-xs text-gray-500 mt-1">Last Year: {{ student.school_year || 'N/A' }}</div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grade Selection and Print PDF Button -->
                    <SkeletonLoader
                        v-if="isLoading"
                        type="form"
                        :fields="2"
                        class="flex gap-2"
                    />

                    <div v-else class="flex gap-2">
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
                            class="text-sm"
                            @click="printHealthExaminationPdf"
                        />
                    </div>

                    <!-- Health Treatment Card -->
                    <SkeletonLoader
                        v-if="isLoading"
                        type="card"
                        :lines="6"
                        class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden backdrop-blur-sm"
                    />

                    <div v-else class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden backdrop-blur-sm">
                        <div class="bg-gradient-to-r from-emerald-500 via-green-500 to-teal-600 p-5 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <i class="pi pi-heart text-white"></i>
                                </div>
                                <h3 class="text-xl font-bold text-white">Health Treatments</h3>
                            </div>
                            <Button
                                v-if="userRole === 'nurse'"
                                label="Add Treatment"
                                class="!bg-green-600 !text-white !border-green-600 hover:!bg-green-700 text-xs"
                                @click="$inertia.visit(`/pupil-health/health-treatment/${student.id}/create?grade=${encodeURIComponent(selectedGrade)}`)"
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
                                    <tr v-if="treatmentRecords.length === 0">
                                        <td colspan="5" class="text-center py-2 text-gray-500">No records available</td>
                                    </tr>
                                    <tr v-for="treatment in treatmentRecords" :key="treatment.id" class="border-b hover:bg-gray-50">
                                        <td class="py-2" style="max-width: 150px; word-break: break-all;">
                                            <div class="overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2;" v-tooltip.top="treatment.title">
                                                {{ treatment.title }}
                                            </div>
                                        </td>
                                        <td class="py-2" style="max-width: 200px; word-break: break-all;">
                                            <div class="overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2;" v-tooltip.top="treatment.chief_complaint">
                                                {{ treatment.chief_complaint }}
                                            </div>
                                        </td>
                                        <td class="py-2" style="max-width: 200px; word-break: break-all;">
                                            <div class="overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2;" v-tooltip.top="treatment.treatment">
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
                                                    @click="editTreatment(treatment)"
                                                    class="!p-1 !text-xs"
                                                    v-tooltip.top="'Edit Treatment'"
                                                />
                                                <Button
                                                    label="View"
                                                    icon="pi pi-eye"
                                                    size="small"
                                                    severity="info"
                                                    outlined
                                                    @click="viewTreatment(treatment)"
                                                    class="!p-1 !text-xs"
                                                    v-tooltip.top="'View Treatment'"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Health Examination (spans 3 columns) -->
                <div class="lg:col-span-3">
                    <!-- Skeleton Loader -->
                    <SkeletonLoader
                        v-if="isLoading"
                        type="card"
                        :lines="10"
                        class="mb-6"
                    />

                    <div v-else class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden backdrop-blur-sm">
                        <div class="bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-600 p-6 flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                    <i class="pi pi-heart text-white text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white mb-1">Health Examination</h2>
                                    <p class="text-purple-100 font-medium">Medical assessment results</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    v-if="!currentRecord && userRole === 'nurse'"
                                    label="Add Record"
                                    icon="pi pi-plus"
                                    class="p-button-sm !bg-green-600 !text-white !border-green-600 hover:!bg-green-700"
                                    @click="$inertia.visit(`/pupil-health/health-examination/${student.id}/create?grade=${selectedGrade.replace('Grade ', '')}`)"
                                />
                                <Button
                                    v-if="currentRecord && userRole === 'nurse'"
                                    label="Edit Record"
                                    icon="pi pi-pencil"
                                    class="p-button-sm !bg-orange-600 !text-white !border-orange-600 hover:!bg-orange-700"
                                    @click="$inertia.visit(`/pupil-health/health-examination/${currentRecord.id}/edit?grade=${selectedGrade.replace('Grade ', '')}`)"
                                />
                            </div>
                        </div>
                        <div class="p-6 text-center text-gray-500" v-if="!currentRecord">
                            <p>No health examination records found for {{ selectedGrade }}. Click the "Add Record" button to create one.</p>
                        </div>
                        <div class="p-6" v-else>
                            <!-- Physical Measurements Section -->
                            <div class="border rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold text-center mb-4">Physical Measurements</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="flex justify-between">
                                        <span>Height:</span>
                                        <span class="font-medium text-blue-600">{{ currentRecord.height ? `${currentRecord.height} cm` : 'Not recorded' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Weight:</span>
                                        <span class="font-medium text-blue-600">{{ currentRecord.weight ? `${currentRecord.weight} kg` : 'Not recorded' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Temperature:</span>
                                        <span class="font-medium">{{ currentRecord.temperature ? `${currentRecord.temperature}°C` : 'Not recorded' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Heart Rate:</span>
                                        <span class="font-medium">{{ currentRecord.heart_rate || 'Not recorded' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Physical Examination Section -->
                            <div class="border rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold text-center mb-4">Physical Examination</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="flex justify-between">
                                        <span>Skin:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.skin === 'Others (specify)' && currentRecord.skin_specify ? currentRecord.skin_specify : (currentRecord.skin || 'Not examined')">
                                            {{
                                                (currentRecord.skin === 'Others (specify)' && currentRecord.skin_specify
                                                    ? currentRecord.skin_specify
                                                    : (currentRecord.skin || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.skin === 'Others (specify)' && currentRecord.skin_specify
                                                    ? currentRecord.skin_specify
                                                    : (currentRecord.skin || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Scalp:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.scalp === 'Others (specify)' && currentRecord.scalp_specify ? currentRecord.scalp_specify : (currentRecord.scalp || 'Not examined')">
                                            {{
                                                (currentRecord.scalp === 'Others (specify)' && currentRecord.scalp_specify
                                                    ? currentRecord.scalp_specify
                                                    : (currentRecord.scalp || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.scalp === 'Others (specify)' && currentRecord.scalp_specify
                                                    ? currentRecord.scalp_specify
                                                    : (currentRecord.scalp || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Eyes:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.eye === 'Others (specify)' && currentRecord.eye_specify ? currentRecord.eye_specify : (currentRecord.eye || 'Not examined')">
                                            {{
                                                (currentRecord.eye === 'Others (specify)' && currentRecord.eye_specify
                                                    ? currentRecord.eye_specify
                                                    : (currentRecord.eye || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.eye === 'Others (specify)' && currentRecord.eye_specify
                                                    ? currentRecord.eye_specify
                                                    : (currentRecord.eye || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Ears:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.ear === 'Others (specify)' && currentRecord.ear_specify ? currentRecord.ear_specify : (currentRecord.ear || 'Not examined')">
                                            {{
                                                (currentRecord.ear === 'Others (specify)' && currentRecord.ear_specify
                                                    ? currentRecord.ear_specify
                                                    : (currentRecord.ear || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.ear === 'Others (specify)' && currentRecord.ear_specify
                                                    ? currentRecord.ear_specify
                                                    : (currentRecord.ear || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Nose:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.nose === 'Others (specify)' && currentRecord.nose_specify ? currentRecord.nose_specify : (currentRecord.nose || 'Not examined')">
                                            {{
                                                (currentRecord.nose === 'Others (specify)' && currentRecord.nose_specify
                                                    ? currentRecord.nose_specify
                                                    : (currentRecord.nose || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.nose === 'Others (specify)' && currentRecord.nose_specify
                                                    ? currentRecord.nose_specify
                                                    : (currentRecord.nose || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Mouth:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.mouth === 'Others (specify)' && currentRecord.mouth_specify ? currentRecord.mouth_specify : (currentRecord.mouth || 'Not examined')">
                                            {{
                                                (currentRecord.mouth === 'Others (specify)' && currentRecord.mouth_specify
                                                    ? currentRecord.mouth_specify
                                                    : (currentRecord.mouth || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.mouth === 'Others (specify)' && currentRecord.mouth_specify
                                                    ? currentRecord.mouth_specify
                                                    : (currentRecord.mouth || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Throat:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.throat === 'Others (specify)' && currentRecord.throat_specify ? currentRecord.throat_specify : (currentRecord.throat || 'Not examined')">
                                            {{
                                                (currentRecord.throat === 'Others (specify)' && currentRecord.throat_specify
                                                    ? currentRecord.throat_specify
                                                    : (currentRecord.throat || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.throat === 'Others (specify)' && currentRecord.throat_specify
                                                    ? currentRecord.throat_specify
                                                    : (currentRecord.throat || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Neck:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.neck === 'Others (specify)' && currentRecord.neck_specify ? currentRecord.neck_specify : (currentRecord.neck || 'Not examined')">
                                            {{
                                                (currentRecord.neck === 'Others (specify)' && currentRecord.neck_specify
                                                    ? currentRecord.neck_specify
                                                    : (currentRecord.neck || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.neck === 'Others (specify)' && currentRecord.neck_specify
                                                    ? currentRecord.neck_specify
                                                    : (currentRecord.neck || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Lungs:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.lungs === 'Others (specify)' && currentRecord.lungs_specify ? currentRecord.lungs_specify : currentRecord.lungs === 'Other specify' && currentRecord.lungs_other_specify ? currentRecord.lungs_other_specify : (currentRecord.lungs || currentRecord.lungs_heart || 'Not examined')">
                                            {{
                                                (currentRecord.lungs === 'Others (specify)' && currentRecord.lungs_specify
                                                    ? currentRecord.lungs_specify
                                                    : currentRecord.lungs === 'Other specify' && currentRecord.lungs_other_specify
                                                    ? currentRecord.lungs_other_specify
                                                    : (currentRecord.lungs || currentRecord.lungs_heart || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.lungs === 'Others (specify)' && currentRecord.lungs_specify
                                                    ? currentRecord.lungs_specify
                                                    : currentRecord.lungs === 'Other specify' && currentRecord.lungs_other_specify
                                                    ? currentRecord.lungs_other_specify
                                                    : (currentRecord.lungs || currentRecord.lungs_heart || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Heart:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.heart === 'Others (specify)' && currentRecord.heart_specify ? currentRecord.heart_specify : currentRecord.heart === 'Other specify' && currentRecord.heart_other_specify ? currentRecord.heart_other_specify : (currentRecord.heart || currentRecord.lungs_heart || 'Not examined')">
                                            {{
                                                (currentRecord.heart === 'Others (specify)' && currentRecord.heart_specify
                                                    ? currentRecord.heart_specify
                                                    : currentRecord.heart === 'Other specify' && currentRecord.heart_other_specify
                                                    ? currentRecord.heart_other_specify
                                                    : (currentRecord.heart || currentRecord.lungs_heart || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.heart === 'Others (specify)' && currentRecord.heart_specify
                                                    ? currentRecord.heart_specify
                                                    : currentRecord.heart === 'Other specify' && currentRecord.heart_other_specify
                                                    ? currentRecord.heart_other_specify
                                                    : (currentRecord.heart || currentRecord.lungs_heart || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Abdomen:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.abdomen === 'Others (specify)' && currentRecord.abdomen_specify ? currentRecord.abdomen_specify : (currentRecord.abdomen || 'Not examined')">
                                            {{
                                                (currentRecord.abdomen === 'Others (specify)' && currentRecord.abdomen_specify
                                                    ? currentRecord.abdomen_specify
                                                    : (currentRecord.abdomen || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.abdomen === 'Others (specify)' && currentRecord.abdomen_specify
                                                    ? currentRecord.abdomen_specify
                                                    : (currentRecord.abdomen || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Deformities:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.deformities === 'Others (specify)' && currentRecord.deformities_specify ? currentRecord.deformities_specify : (currentRecord.deformities || 'Not examined')">
                                            {{
                                                (currentRecord.deformities === 'Others (specify)' && currentRecord.deformities_specify
                                                    ? currentRecord.deformities_specify
                                                    : (currentRecord.deformities || 'Not examined')).substring(0, 15) +
                                                ((currentRecord.deformities === 'Others (specify)' && currentRecord.deformities_specify
                                                    ? currentRecord.deformities_specify
                                                    : (currentRecord.deformities || 'Not examined')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Nutritional Status Section -->
                            <div class="border rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold text-center mb-4">Nutritional Status</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <i class="pi pi-chart-bar text-blue-600"></i>
                                            <span>BMI Status:</span>
                                        </div>
                                        <Tag :value="currentRecord.nutritional_status_bmi || 'Not assessed'"
                                             :severity="currentRecord.nutritional_status_bmi === 'Normal' ? 'success' :
                                                       currentRecord.nutritional_status_bmi === 'Underweight' ? 'warning' : 'secondary'" />
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Height for Age:</span>
                                        <Tag :value="currentRecord.nutritional_status_height || 'Not assessed'"
                                             :severity="currentRecord.nutritional_status_height === 'Normal' ? 'success' : 'secondary'" />
                                    </div>
                                </div>
                            </div>

                            <!-- Screening Results Section -->
                            <div class="border rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold text-center mb-4">Screening Results</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="flex justify-between">
                                        <span>Vision Screening:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.vision_screening === 'Others (specify)' && currentRecord.vision_screening_specify ? currentRecord.vision_screening_specify : (currentRecord.vision_screening || 'Not tested')">
                                            {{
                                                (currentRecord.vision_screening === 'Others (specify)' && currentRecord.vision_screening_specify
                                                    ? currentRecord.vision_screening_specify
                                                    : (currentRecord.vision_screening || 'Not tested')).substring(0, 15) +
                                                ((currentRecord.vision_screening === 'Others (specify)' && currentRecord.vision_screening_specify
                                                    ? currentRecord.vision_screening_specify
                                                    : (currentRecord.vision_screening || 'Not tested')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Auditory Screening:</span>
                                        <span class="font-medium" v-tooltip.top="currentRecord.auditory_screening === 'Others (specify)' && currentRecord.auditory_screening_specify ? currentRecord.auditory_screening_specify : (currentRecord.auditory_screening || 'Not tested')">
                                            {{
                                                (currentRecord.auditory_screening === 'Others (specify)' && currentRecord.auditory_screening_specify
                                                    ? currentRecord.auditory_screening_specify
                                                    : (currentRecord.auditory_screening || 'Not tested')).substring(0, 15) +
                                                ((currentRecord.auditory_screening === 'Others (specify)' && currentRecord.auditory_screening_specify
                                                    ? currentRecord.auditory_screening_specify
                                                    : (currentRecord.auditory_screening || 'Not tested')).length > 15 ? '...' : '')
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Immunization & Benefits Section -->
                            <div class="border rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-center mb-4">Immunization & Benefits</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <i :class="currentRecord.immunization ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'" class="mr-2"></i>
                                        <span :title="currentRecord.immunization || 'Not recorded'">Immunization: {{ (currentRecord.immunization || 'Not recorded').substring(0, 15) }}{{ (currentRecord.immunization || 'Not recorded').length > 15 ? '...' : '' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i :class="currentRecord.iron_supplementation === 'Yes' ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'" class="mr-2"></i>
                                        <span>Iron Supplementation: {{ currentRecord.iron_supplementation || 'No' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i :class="currentRecord.deworming_status === 'dewormed' ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'" class="mr-2"></i>
                                        <span>Dewormed: {{ currentRecord.deworming_status === 'dewormed' ? 'Yes' : 'No' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i :class="currentRecord.sbfp_beneficiary ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'" class="mr-2"></i>
                                        <span>SBFP Beneficiary: {{ currentRecord.sbfp_beneficiary ? 'Yes' : 'No' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i :class="currentRecord.four_ps_beneficiary ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'" class="mr-2"></i>
                                        <span>4Ps Beneficiary: {{ currentRecord.four_ps_beneficiary ? 'Yes' : 'No' }}</span>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t">
                                    <h4 class="font-semibold mb-2">Examination Details</h4>
                                    <div class="flex justify-between text-sm">
                                        <span>Examination Date:</span>
                                        <span class="font-medium text-blue-600">{{ currentRecord.examination_date ? new Date(currentRecord.examination_date).toLocaleDateString() : 'Not recorded' }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Growth Chart Section -->
            <SkeletonLoader
                v-if="isLoading"
                type="card"
                :lines="8"
                class="mt-6 border rounded-lg bg-white shadow p-6"
            />

            <div v-else-if="allHealthExaminations.length > 0" class="mt-6">
                <div class="border rounded-lg bg-white shadow p-6">
                    <GrowthChart :health-examinations="allHealthExaminations" />
                </div>
            </div>
        </div>

        <!-- Health Treatment View Modal -->
        <HealthTreatmentViewModal
            :visible="showTreatmentModal"
            :treatment="selectedTreatment"
            :student="student"
            :timer-status="treatmentTimerStatus"
            :remaining-minutes="treatmentRemainingMinutes"
            @close="closeTreatmentModal"
            @edit="editTreatmentFromModal"
        />
    </div>
</template>
