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
                    <Button label="Back" icon="pi pi-arrow-left" outlined severity="secondary" class="text-sm" />
                </Link>
            </div>

            <!-- Main Layout: Left and Right Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left Column: Student Details & Oral Health Records -->
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
                        <Select 
                            v-model="selectedGrade" 
                            :options="gradeLevels" 
                            placeholder="Select Grade"
                            class="w-32 text-sm"
                            @change="onGradeChange"
                        />
                    </div>

                    <!-- Oral Health Treatment Card -->
                    <div class="border rounded-lg bg-white shadow">
                        <div class="bg-blue-700 text-white p-2 flex justify-between items-center text-sm">
                            <span>Oral Health Treatment</span>
                            <Button 
                                icon="pi pi-plus" 
                                class="p-button-sm !bg-green-600 !text-white !border-green-600 hover:!bg-green-700" 
                                @click="$inertia.visit(`/pupil-health/oral-health-treatment/${student.id}/create`)" 
                            />
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
                                    <tr v-if="oralTreatmentRecords.length === 0">
                                        <td colspan="4" class="text-center py-2 text-gray-500">No records available</td>
                                    </tr>
                                    <tr v-for="treatment in oralTreatmentRecords" :key="treatment.id" class="border-b hover:bg-gray-50">
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

                <!-- Right Column: Oral Health Examination Details -->
                <div class="lg:col-span-3">
                    <div class="border rounded-lg bg-white shadow">
                        <div class="bg-blue-700 text-white p-3 text-sm flex justify-between items-center">
                            <span>Pupil Oral Health Examination</span>
                            <Button 
                                v-if="!currentExam"
                                label="Add Record" 
                                icon="pi pi-plus" 
                                class="p-button-sm !bg-green-600 !text-white !border-green-600 hover:!bg-green-700" 
                                @click="$inertia.visit(`/pupil-health/oral-health/${student.id}/create?grade=${selectedGrade.replace('Grade ', '')}`)"
                            />
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
    </div>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Select from 'primevue/select'
import axios from 'axios'

const props = defineProps({
    student: {
        type: Object,
        required: true
    },
    examinations: {
        type: Array,
        default: () => []
    }
})

const page = usePage()

const gradeLevels = ['Kinder 1', 'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];

// Initialize grade from URL parameter, session storage, or flash data
const initializeGrade = () => {
    const urlGrade = new URLSearchParams(window.location.search).get('grade');
    const flashGrade = page.props.flash?.grade;
    const sessionGrade = sessionStorage.getItem(`currentGrade_${props.student.id}`);
    const defaultGrade = 'Grade 6'; // Default to Grade 6 instead of student's current grade
    
    console.log('Grade initialization:', { urlGrade, flashGrade, sessionGrade, defaultGrade, studentGrade: props.student.grade_level });
    
    if (urlGrade) {
        const gradeValue = `Grade ${urlGrade}`;
        sessionStorage.setItem(`currentGrade_${props.student.id}`, gradeValue);
        return gradeValue;
    } else if (flashGrade) {
        const gradeValue = `Grade ${flashGrade}`;
        sessionStorage.setItem(`currentGrade_${props.student.id}`, gradeValue);
        return gradeValue;
    } else if (sessionGrade) {
        return sessionGrade;
    } else {
        return defaultGrade;
    }
};

const selectedGrade = ref(initializeGrade());

// Debug logging
console.log('Initial selectedGrade value:', selectedGrade.value);
console.log('gradeLevels:', gradeLevels);
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
        oralTreatmentRecords.value = response.data;
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
        
        const curve = Math.sin((index / (upperNumbers.length - 1)) * Math.PI) * 8;
        tooth.style.left = `${index * 35 + 20}px`;
        tooth.style.top = `${15 - curve}px`;
        
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
        
        const curve = Math.sin((index / (lowerNumbers.length - 1)) * Math.PI) * 8;
        tooth.style.left = `${index * 35 + 20}px`;
        tooth.style.top = `${15 + curve}px`;
        
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
        
        const curve = Math.sin((index / (upperNumbers.length - 1)) * Math.PI) * 6;
        tooth.style.left = `${index * 35 + 120}px`;
        tooth.style.top = `${15 - curve}px`;
        
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
        
        const curve = Math.sin((index / (lowerNumbers.length - 1)) * Math.PI) * 6;
        tooth.style.left = `${index * 35 + 120}px`;
        tooth.style.top = `${15 + curve}px`;
        
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
            symbolBadge.textContent = symbols.length;
            symbolBadge.title = symbols.join(', ');
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
            
            tooltip.innerHTML = `
                <div style="text-align: center;">
                    <div style="font-weight: bold; margin-bottom: 2px;">${category} Tooth ${number}</div>
                    <div style="font-size: 11px; color: #ccc; margin-bottom: 2px;">${type}</div>
                    <div style="font-size: 11px; color: #ff6b9d; font-weight: bold;">${symbols.length > 0 ? symbols.join(', ') : 'No symbols'}</div>
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

onMounted(() => {
    fetchOralHealthByGrade(selectedGrade.value);
    fetchOralTreatmentRecords();
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
</script>

<style scoped>
.oral-health-examination {
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

/* Dental Chart Display Styles */
.dental-chart-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin: 0 auto;
    max-width: 800px;
}

.dental-chart.compact {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.teeth-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.teeth-row.compact {
    position: relative;
    width: 600px;
    height: 60px;
}

.teeth-row.primary.compact {
    width: 580px;
}

.arch-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #666;
    text-align: center;
}

.tooth {
    position: absolute;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(145deg, #f0f0f0, #e0e0e0);
    border: 2px solid #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tooth:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    z-index: 5;
}

.primary-tooth {
    background: linear-gradient(145deg, #ff9a9e, #fecfef);
    border: 2px solid #ff6b9d;
    width: 28px;
    height: 28px;
    font-size: 9px;
}

.primary-tooth:hover {
    box-shadow: 0 2px 8px rgba(255, 107, 157, 0.4);
}

.tooth.has-symbol {
    position: relative;
}

.tooth-symbol {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
    font-weight: bold;
    z-index: 5;
    pointer-events: none;
}

.chart-selector {
    display: flex;
    gap: 8px;
    background: #e9ecef;
    padding: 4px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.chart-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    color: #666;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.chart-btn:hover {
    background: rgba(255, 255, 255, 0.5);
}

.chart-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Tooltip Styles */
.tooltip {
    position: absolute !important;
    background: rgba(0, 0, 0, 0.9) !important;
    color: white !important;
    padding: 8px 12px !important;
    border-radius: 6px !important;
    font-size: 12px !important;
    white-space: nowrap !important;
    z-index: 9999 !important;
    opacity: 0 !important;
    transform: translateX(-50%) !important;
    transition: opacity 0.3s ease !important;
    pointer-events: none !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3) !important;
}

.tooltip.show {
    opacity: 1 !important;
}

.tooltip-content {
    text-align: center;
}

.tooth-number {
    font-weight: bold;
    margin-bottom: 2px;
}

.tooth-type {
    font-size: 11px;
    color: #ccc;
    margin-bottom: 2px;
}

.tooth-condition {
    font-size: 11px;
    color: #ff6b9d;
    font-weight: bold;
}

.white-container {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.text-gray-600 {
    color: #6b7280;
}

.border-b {
    border-bottom: 1px solid #e5e7eb;
}
</style>
