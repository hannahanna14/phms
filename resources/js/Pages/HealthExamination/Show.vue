<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import Select from 'primevue/select';

const { student, healthExamination, selectedGrade: propSelectedGrade } = usePage().props;

console.log('Props received:', { student, healthExamination, propSelectedGrade });
console.log('Current URL:', window.location.href);
console.log('URL search params:', new URLSearchParams(window.location.search).get('grade'));

const gradeLevels = computed(() => {
    const standardGrades = ['Kinder 1', 'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
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

onMounted(async () => {
    console.log('Initial grade set to:', selectedGrade.value);
    
    // Initial load with current grade
    await fetchRecordByGrade(selectedGrade.value);
    await fetchTreatmentRecords();
});
const healthRecords = ref([]);
const recordDetails = ref({});
const currentRecord = ref(null);
const treatmentRecords = ref([]);

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
        const response = await axios.get(`/api/health-treatment/student/${student.id}?grade=${selectedGrade.value}`, {
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

const changeGrade = (grade) => {
    selectedGrade.value = grade;
    console.log('Grade changed to:', grade);
    sessionStorage.setItem(`currentGrade_${student.id}`, grade);
    fetchRecordByGrade(grade);
    fetchTreatmentRecords();
};

// Remove watch to prevent page reloads - only use @change handler

</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header with Back Button -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="pi pi-file-medical mr-2 text-indigo-600"></i>
                    Health Examination Report
                </h1>
                <Link href="/pupil-health" class="no-underline">
                    <Button label="Back" icon="pi pi-arrow-left" outlined severity="secondary" class="text-sm" />
                </Link>
            </div>

            <!-- Main Layout: Left and Right Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left Column: Student Details & Treatment -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Student Details Card -->
                    <div class="border rounded-lg bg-white shadow p-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Student Details</h2>
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

                    <!-- Grade Selection -->
                    <div class="flex gap-2">
                        <button 
                            v-for="grade in gradeLevels" 
                            :key="grade"
                            @click="changeGrade(grade)"
                            :class="['px-3 py-1 text-xs rounded', selectedGrade === grade ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300']"
                        >
                            {{ grade }}
                        </button>
                    </div>

                    <!-- Health Treatment Card -->
                    <div class="border rounded-lg bg-white shadow">
                        <div class="bg-blue-700 text-white p-2 flex justify-between items-center text-sm">
                            <span>Health Treatment</span>
                            <Button icon="pi pi-plus" class="p-button-text text-white text-xs" @click="$inertia.visit(`/pupil-health/health-treatment/${student.id}/create`)" />
                        </div>
                        <div class="p-3">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-1">Chief Complaint</th>
                                        <th class="text-left py-1">Treatment</th>
                                        <th class="text-left py-1">Status</th>
                                        <th class="text-left py-1">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="treatmentRecords.length === 0">
                                        <td colspan="4" class="text-center py-2 text-gray-500">No records available</td>
                                    </tr>
                                    <tr v-for="treatment in treatmentRecords" :key="treatment.id" class="border-b hover:bg-gray-50">
                                        <td class="py-2">{{ treatment.chief_complaint }}</td>
                                        <td class="py-2">{{ treatment.treatment }}</td>
                                        <td class="py-2">
                                            <Tag 
                                                :value="treatment.status"
                                                :severity="treatment.status === 'completed' ? 'success' : 
                                                         treatment.status === 'in_progress' ? 'warning' :
                                                         treatment.status === 'cancelled' ? 'danger' : 'secondary'"
                                                class="text-xs"
                                            />
                                        </td>
                                        <td class="py-2">{{ new Date(treatment.date).toLocaleDateString() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Health Examination (spans 3 columns) -->
                <div class="lg:col-span-3">
                    <div class="border rounded-lg bg-white shadow">
                        <div class="bg-blue-700 text-white p-3 text-sm flex justify-between items-center">
                            <span>Pupil Health Examination</span>
                            <Button 
                                v-if="!currentRecord"
                                label="Add Record" 
                                icon="pi pi-plus" 
                                class="p-button-sm !bg-green-600 !text-white !border-green-600 hover:!bg-green-700" 
                                @click="$inertia.visit(`/pupil-health/health-examination/${student.id}/create?grade=${selectedGrade.replace('Grade ', '')}`)"
                            />
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
                                        <span>Temperature/BP:</span>
                                        <span class="font-medium">{{ currentRecord.temperature_bp || 'Not recorded' }}</span>
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
                                        <span>Skin/Scalp:</span>
                                        <span class="font-medium">{{ currentRecord.skin || 'Not examined' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Eyes/Ears/Nose:</span>
                                        <span class="font-medium">{{ currentRecord.eye || 'Not examined' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Mouth/Throat/Neck:</span>
                                        <span class="font-medium">{{ currentRecord.mouth || 'Not examined' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Lungs/Heart:</span>
                                        <span class="font-medium">{{ currentRecord.lungs_heart || 'Not examined' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Abdomen:</span>
                                        <span class="font-medium">{{ currentRecord.abdomen || 'Not examined' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Deformities:</span>
                                        <span class="font-medium">{{ currentRecord.deformities || 'Not examined' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Nutritional Status Section -->
                            <div class="border rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold text-center mb-4">Nutritional Status</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="flex justify-between">
                                        <span>BMI Status:</span>
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
                                        <span class="font-medium">{{ currentRecord.vision_screening || 'Not tested' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Auditory Screening:</span>
                                        <span class="font-medium">{{ currentRecord.auditory_screening || 'Not tested' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Immunization & Benefits Section -->
                            <div class="border rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-center mb-4">Immunization & Benefits</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <i :class="currentRecord.iron_supplementation === 'Yes' ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'" class="mr-2"></i>
                                        <span>Iron Supplementation: {{ currentRecord.iron_supplementation || 'No' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i :class="currentRecord.deworming_status === 'Yes' ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'" class="mr-2"></i>
                                        <span>Dewormed: {{ currentRecord.deworming_status || 'No' }}</span>
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
        </div>
    </div>
</template>

<style scoped>
/* Additional compact styling can be added here if needed */
</style>
