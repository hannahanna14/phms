<template>
    <div class="bg-[#f4f4f4] min-h-screen flex items-start justify-center p-4">
        <Card class="w-full max-w-3xl shadow-xl">
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
                        <Dropdown v-model="form.visionScreening" :options="visionOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Auditory Screening</label>
                        <Dropdown v-model="form.auditoryScreening" :options="auditoryOptions" class="w-full" />
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

                    <!-- CHECKBOXES -->
                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.ironSupplementation" :binary="true" />
                        <label>Iron Supplementation</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.dewormed" :binary="true" />
                        <label>Dewormed</label>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Immunization</label>
                        <InputText v-model="form.immunization" class="w-full" />
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.sbfpBeneficiary" :binary="true" />
                        <label>SBFP Beneficiary</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.fourPsBeneficiary" :binary="true" />
                        <label>4Ps Beneficiary</label>
                    </div>
                </div>
            </template>
            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button label="Cancel" class="p-button-secondary" />
                    <Button label="Add" class="p-button-primary" @click="submitForm" />
                </div>
            </template>
        </Card>
    </div>
</template>

<script>
import { ref, watch } from "vue";
import Card from "primevue/card";
import InputText from "primevue/inputtext";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import Checkbox from "primevue/checkbox";
import Button from "primevue/button";

export default {
    components: { Card, InputText, MultiSelect, Dropdown, Checkbox, Button },
    setup() {
        const form = ref({
            height: "",
            weight: "",
            nutritionalStatusBMI: "",
            nutritionalStatusHeight: "",
        });

        const heightError = ref("");
        const weightError = ref("");

        const validateHeight = () => {
            if (!form.value.height || form.value.height < 50 || form.value.height > 200) {
                heightError.value = "Height must be between 50 cm and 200 cm.";
            } else {
                heightError.value = "";
            }
        };

        const validateWeight = () => {
            if (!form.value.weight || form.value.weight < 10 || form.value.weight > 200) {
                weightError.value = "Weight must be between 10 kg and 200 kg.";
            } else {
                weightError.value = "";
            }
        };

        watch([() => form.value.height, () => form.value.weight], () => {
            validateHeight();
            validateWeight();

            const heightMeters = form.value.height / 100;
            if (heightMeters > 0 && form.value.weight > 0) {
                const bmi = (form.value.weight / (heightMeters ** 2)).toFixed(2);
                form.value.nutritionalStatusBMI = bmi < 18.5 ? "Underweight" : bmi < 24.9 ? "Normal" : bmi < 29.9 ? "Overweight" : "Obese";
            } else {
                form.value.nutritionalStatusBMI = "";
            }
            form.value.nutritionalStatusHeight = form.value.height < 120 ? "Short Stature" : "Normal";
        });

        return { form, heightError, weightError, validateHeight, validateWeight };
    }
};
</script>
