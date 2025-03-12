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
                        <FloatLabel>
                            <InputText
                                v-model="form.height"
                                class="w-full"
                                :class="{ 'p-invalid border-2 border-red-500': heightError && form.height !== '' }"
                                @input="validateHeight"
                                type="number"
                            />
                            <label>Height (cm)</label>
                            <small v-if="heightError && form.height !== ''" class="text-red-500">{{ heightError }}</small>
                        </FloatLabel>
                    </div>

                    <!-- WEIGHT FIELD -->
                    <div>
                        <FloatLabel>
                            <InputText
                                v-model="form.weight"
                                class="w-full"
                                type="number"
                            />
                            <label>Weight (kg)</label>
                        </FloatLabel>
                    </div>

                    <!-- TEMPERATURE FIELD -->
                    <div>
                        <FloatLabel>
                            <InputText
                                v-model="form.temperature"
                                class="w-full"
                                type="number"
                            />
                            <label>Temperature/BP</label>
                        </FloatLabel>
                    </div>

                    <!-- HEART RATE FIELD -->
                    <div>
                        <FloatLabel>
                            <InputText
                                v-model="form.heartRate"
                                class="w-full"
                                type="number"
                            />
                            <label>Heart Rate</label>
                        </FloatLabel>
                    </div>

                    <!-- NUTRITIONAL STATUS FIELDS -->
                    <div>
                        <FloatLabel>
                            <InputText v-model="form.nutritionalStatusBMI" class="w-full" />
                            <label>Nutritional Status (BMI)</label>
                        </FloatLabel>
                    </div>
                    <div>
                        <FloatLabel>
                            <InputText v-model="form.nutritionalStatusHeight" class="w-full" />
                            <label>Nutritional Status (Height for Age)</label>
                        </FloatLabel>
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
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import Checkbox from "primevue/checkbox";
import Button from "primevue/button";

export default {
    components: { Card, FloatLabel, InputText, MultiSelect, Dropdown, Checkbox, Button },
    setup() {
        const form = ref({
            height: "",
            weight: "",
            temperature: "",
            heartRate: "",
            nutritionalStatusBMI: "",
            nutritionalStatusHeight: "",
            visionScreening: "",
            auditoryScreening: "",
            skinScalp: [],
            eyesEarsNose: [],
            mouthThroatNeck: [],
            lungsHeart: [],
            abdomen: [],
            deformities: [],
            ironSupplementation: false,
            dewormed: false,
            immunization: "",
            sbfpBeneficiary: false,
            fourPsBeneficiary: false,
        });

        const heightError = ref("");

        const validateHeight = () => {
            const heightValue = parseFloat(form.value.height);

            if (form.value.height === "") {
                heightError.value = ""; // Remove error when field is empty
            } else if (isNaN(heightValue) || heightValue < 100 || heightValue > 300) {
                heightError.value = "Please enter a valid height (100-300 cm).";
            } else {
                heightError.value = "";
            }
        };

        watch(() => form.value.height, validateHeight);

        return { form, heightError, validateHeight };
    },
};
</script>

<style>
.p-invalid {
    border-color: red !important;
    box-shadow: 0 0 0 1px red !important;
}
</style>
