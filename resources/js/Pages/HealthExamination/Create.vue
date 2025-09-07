<template>
    <div class="bg-[#f4f4f4] min-h-screen flex items-start justify-center p-4">
        <Card class="w-full max-w-5xl shadow-xl">
            <template #title>
                <div class="text-center">
                    <h2 class="text-lg font-bold">Pupil Health Examination</h2>
                    <p class="text-sm text-gray-500">Naawan Central School</p>
                    <hr class="my-2" />
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-2 gap-3">
                    <!-- HEIGHT FIELD -->
                    <div>
                        <label class="text-sm font-semibold">Height (cm)</label>
                        <InputText
                            v-model="form.height"
                            class="w-full"
                            :class="{ 'p-invalid border-2 border-red-500': heightError }"
                            @input="validateHeight"
                            type="number"
                            min="50"
                            max="200"
                        />
                        <small v-if="heightError" class="text-red-500">{{ heightError }}</small>
                    </div>

                    <!-- WEIGHT FIELD -->
                    <div>
                        <label class="text-sm font-semibold">Weight (kg)</label>
                        <InputText
                            v-model="form.weight"
                            class="w-full"
                            :class="{ 'p-invalid border-2 border-red-500': weightError }"
                            @input="validateWeight"
                            type="number"
                            min="10"
                            max="200"
                        />
                        <small v-if="weightError" class="text-red-500">{{ weightError }}</small>
                    </div>


                    <!-- TEMPERATURE FIELD -->
                    <div>
                        <label class="text-sm font-semibold">Temperature/BP</label>
                        <InputText v-model="form.temperature" class="w-full" type="number" />
                    </div>

                    <!-- HEART RATE FIELD -->
                    <div>
                        <label class="text-sm font-semibold">Heart Rate</label>
                        <InputText v-model="form.heartRate" class="w-full" type="number" />
                    </div>

                    <!-- NUTRITIONAL STATUS FIELDS -->
                    <div>
                        <label class="text-sm font-semibold">Nutritional Status (BMI)</label>
                        <InputText v-model="form.nutritionalStatusBMI" class="w-full" disabled />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Nutritional Status (Height for Age)</label>
                        <InputText v-model="form.nutritionalStatusHeight" class="w-full" disabled />
                    </div>

                    <!-- DROPDOWNS -->
                    <div>
                        <label class="text-sm font-semibold">Vision Screening</label>
                        <Select v-model="form.visionScreening" :options="visionOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Auditory Screening</label>
                        <Select v-model="form.auditoryScreening" :options="auditoryOptions" class="w-full" />
                    </div>

                    <!-- MULTISELECT FIELDS -->
                    <div>
                        <label class="text-sm font-semibold">Skin/Scalp</label>
                        <MultiSelect v-model="form.skinScalp" :options="scalpOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Eyes/Ears/Nose</label>
                        <MultiSelect v-model="form.eyesEarsNose" :options="eyesEarsNoseOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Mouth/Throat/Neck</label>
                        <MultiSelect v-model="form.mouthThroatNeck" :options="mouthThroatNeckOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Lungs/Heart</label>
                        <MultiSelect v-model="form.lungsHeart" :options="lungsHeartOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Abdomen</label>
                        <MultiSelect v-model="form.abdomen" :options="abdomenOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Deformities</label>
                        <MultiSelect v-model="form.deformities" :options="deformitiesOptions" class="w-full" />
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Immunization</label>
                        <InputText v-model="form.immunization" class="w-full" />
                    </div>

                    <!-- SINGLE "OTHER'S, SPECIFY" FIELD -->
                    <div>
                        <label class="text-sm font-semibold">Other's, specify</label>
                        <InputText v-model="form.otherSpecify" class="w-full mt-1" placeholder="Specify other conditions" />
                    </div>

                    <!-- CHECKBOXES -->
                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.iron_supplementation" :binary="true" />
                        <label>Iron Supplementation</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.deworming_status" :binary="true" />
                        <label>Dewormed</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.sbfp_beneficiary" :binary="true" />
                        <label>SBFP Beneficiary</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.four_ps_beneficiary" :binary="true" />
                        <label>4Ps Beneficiary</label>
                    </div>

                </div>
            </template>
            <template #footer>
                <hr class="my-2" />
                <div class="flex justify-end gap-2">
                    <Button label="Cancel"  @click="$inertia.visit(`/pupil-health/health-examination/${student.id}?grade=${gradeLevel.value}`)"
                            severity="secondary" class="p-button-secondary" />
                    <Button label="Add" class="p-button-primary" @click="submitForm" />
                </div>
            </template>
        </Card>
    </div>
</template>

<script>
import { ref, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Card from "primevue/card";
import InputText from "primevue/inputtext";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";
import Checkbox from "primevue/checkbox";
import Button from "primevue/button";

export default {
    components: { Card, InputText, MultiSelect, Select, Checkbox, Button },
    setup() {
        const form = useForm({
            height: "",
            weight: "",
            nutritional_status_bmi: "",
            nutritional_status_height: "",
            vision_screening: null,
            auditory_screening: null,
            skin: "",
            scalp: "",
            eye: "",
            ear: "",
            nose: "",
            mouth: "",
            throat: "",
            neck: "",
            lungs_heart: "",
            abdomen: "",
            deformities: "",
            deworming_status: false,
            iron_supplementation: false,
            sbfp_beneficiary: false,
            four_ps_beneficiary: false,
            temperature: "",
            heart_rate: "",
            remarks: "",
            immunization: "",
            other_specify: "",
            student_id: usePage().props.student.id,
            grade_level: usePage().props.grade_level || '6',
            examination_date: new Date().toISOString().split('T')[0],
            school_year: `${new Date().getFullYear()}-${new Date().getFullYear() + 1}`,
            // Frontend display fields (not sent to backend)
            skinScalp: [],
            eyesEarsNose: [],
            mouthThroatNeck: [],
            lungsHeart: [],
            nutritionalStatusBMI: "",
            nutritionalStatusHeight: "",
            heartRate: "",
            otherSpecify: ""
        });

        const visionOptions = ["Passed", "Failed"];
        const auditoryOptions = ["Passed", "Failed"];
        const scalpOptions = ["Presence of Lice", "Redness of Skin", "White Spots", "Flaky Skin", "Impetigo/Boil", "Hematoma", "Bruises/Injuries", "Itchiness", "Skin Lesions", "Acne/Pimple"];
        const eyesEarsNoseOptions = ["Normal", "Stye", "Eye Redness", "Ocular Misalignment", "Pale Conjunctiva", "Ear Discharge", "Impacted Cerumen", "Mucus Discharge", "Nose Bleeding (Epistaxis)", "Eye Discharge", "Matted Eyelashes"];
        const mouthThroatNeckOptions = ["Normal", "Enlarged Tonsil", "Presence of Lesions", "Inflamed Pharynx", "Enlarged Lymph Nodes", "Others"];
        const lungsHeartOptions = ["Normal", "Rales", "Wheeze", "Murmur", "Irregular Heart Rate", "Others"];
        const abdomenOptions = ["Normal", "Distended", "Abdominal Pain", "Tenderness", "Dysmenorrhea", "Others"];
        const deformitiesOptions = ["Acquired", "Congenital"];

        const heightError = ref("");
        const weightError = ref("");

        const validateHeight = () => {
            if (!form.height || form.height < 50 || form.height > 200) {
                heightError.value = "Height must be between 50 cm and 200 cm.";
            } else {
                heightError.value = "";
            }
        };

        const validateWeight = () => {
            if (!form.weight || form.weight < 10 || form.weight > 200) {
                weightError.value = "Weight must be between 10 kg and 200 kg.";
            } else {
                weightError.value = "";
            }
        };

        watch([() => form.height, () => form.weight], () => {
            validateHeight();
            validateWeight();

            const heightMeters = form.height / 100;
            if (heightMeters > 0 && form.weight > 0) {
                const bmi = (form.weight / (heightMeters ** 2)).toFixed(2);
                const bmiStatus = bmi < 18.5 ? "Underweight" : bmi < 24.9 ? "Normal" : bmi < 29.9 ? "Overweight" : "Obese";
                form.nutritional_status_bmi = bmiStatus;
                form.nutritionalStatusBMI = bmiStatus; // For display
            } else {
                form.nutritional_status_bmi = "";
                form.nutritionalStatusBMI = "";
            }
            const heightStatus = form.height < 120 ? "Short Stature" : "Normal";
            form.nutritional_status_height = heightStatus;
            form.nutritionalStatusHeight = heightStatus; // For display
        });

        const page = usePage();
        const studentId = page.props.student?.id;
        const gradeLevel = page.props.grade_level;

        console.log('Student ID:', studentId, 'Grade Level:', gradeLevel, 'Student:', page.props.student);

        const submitForm = () => {
            // Convert multiselect arrays to individual fields
            if (form.skinScalp && Array.isArray(form.skinScalp)) {
                form.skin = form.skinScalp.filter(item => ['Presence of Lice', 'Redness of Skin', 'White Spots', 'Flaky Skin', 'Impetigo/Boil', 'Hematoma', 'Bruises/Injuries', 'Itchiness', 'Skin Lesions', 'Acne/Pimple'].includes(item)).join(', ');
                form.scalp = form.skinScalp.filter(item => ['Presence of Lice', 'Redness of Skin', 'White Spots', 'Flaky Skin'].includes(item)).join(', ');
            }

            if (form.eyesEarsNose && Array.isArray(form.eyesEarsNose)) {
                form.eye = form.eyesEarsNose.filter(item => ['Stye', 'Eye Redness', 'Ocular Misalignment', 'Pale Conjunctiva', 'Eye Discharge', 'Matted Eyelashes'].includes(item)).join(', ');
                form.ear = form.eyesEarsNose.filter(item => ['Ear Discharge', 'Impacted Cerumen'].includes(item)).join(', ');
                form.nose = form.eyesEarsNose.filter(item => ['Mucus Discharge', 'Nose Bleeding (Epistaxis)'].includes(item)).join(', ');
            }

            if (form.mouthThroatNeck && Array.isArray(form.mouthThroatNeck)) {
                form.mouth = form.mouthThroatNeck.filter(item => ['Enlarged Tonsil', 'Presence of Lesions'].includes(item)).join(', ');
                form.throat = form.mouthThroatNeck.filter(item => ['Inflamed Pharynx'].includes(item)).join(', ');
                form.neck = form.mouthThroatNeck.filter(item => ['Enlarged Lymph Nodes'].includes(item)).join(', ');
            }

            if (form.lungsHeart && Array.isArray(form.lungsHeart)) {
                form.lungs_heart = form.lungsHeart.join(', ');
            }

            if (form.abdomen && Array.isArray(form.abdomen)) {
                form.abdomen = form.abdomen.join(', ');
            }

            if (form.deformities && Array.isArray(form.deformities)) {
                form.deformities = form.deformities.join(', ');
            }

            // Map frontend field names to backend field names
            form.heart_rate = form.heartRate || form.heart_rate;
            form.other_specify = form.otherSpecify || form.other_specify;
            form.nutritional_status_bmi = form.nutritionalStatusBMI || form.nutritional_status_bmi;
            form.nutritional_status_height = form.nutritionalStatusHeight || form.nutritional_status_height;

            // Convert boolean checkboxes to Yes/No strings
            form.deworming_status = form.deworming_status ? 'Yes' : 'No';
            form.iron_supplementation = form.iron_supplementation ? 'Yes' : 'No';

            console.log('Submitting form data:', form);

            // Submit the form data using the correct route name
            form.post(route('health-examination.store'), {
                onSuccess: () => {
                    // The server will handle the redirect with the success message
                    console.log('Form submitted successfully, waiting for redirect...');
                },
                onError: (errors) => {
                    console.error('Form submission errors:', errors);
                },
                preserveScroll: true,
                preserveState: true
            });
        };

        return {
            form,
            visionOptions,
            auditoryOptions,
            scalpOptions,
            eyesEarsNoseOptions,
            mouthThroatNeckOptions,
            lungsHeartOptions,
            abdomenOptions,
            deformitiesOptions,
            submitForm,
            heightError,
            weightError,
            validateHeight,
            validateWeight
        };
    }
};
</script>
