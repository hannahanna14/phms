<template>
  <div class="growth-chart-container">
    <div class="chart-header mb-4">
      <h3 class="text-lg font-semibold text-gray-800">Growth Chart</h3>
      <div class="flex gap-2 mt-2">
        <Button
          label="Height"
          @click="activeChart = 'height'"
          :severity="activeChart === 'height' ? 'info' : 'secondary'"
          :outlined="activeChart !== 'height'"
        />
        <Button
          label="Weight"
          @click="activeChart = 'weight'"
          :severity="activeChart === 'weight' ? 'success' : 'secondary'"
          :outlined="activeChart !== 'weight'"
        />
      </div>
    </div>

    <div class="chart-wrapper bg-white p-4 rounded-lg border shadow-sm">
      <Chart type="line" :data="currentChartData" :options="chartOptions" class="chart-canvas" />
    </div>

    <div v-if="latestData" class="stats-summary mt-4 grid grid-cols-2 gap-4">
      <div class="stat-card bg-blue-50 p-4 rounded-lg border border-blue-200">
        <div class="text-sm text-blue-600 font-medium">Latest Height</div>
        <div class="text-2xl font-bold text-blue-700">{{ latestData.height }} cm</div>
        <div class="text-xs text-gray-600 mt-1">{{ latestData.date }}</div>
      </div>
      <div class="stat-card bg-green-50 p-4 rounded-lg border border-green-200">
        <div class="text-sm text-green-600 font-medium">Latest Weight</div>
        <div class="text-2xl font-bold text-green-700">{{ latestData.weight }} kg</div>
        <div class="text-xs text-gray-600 mt-1">{{ latestData.date }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Chart from 'primevue/chart';
import Button from 'primevue/button';

const props = defineProps({
  healthExaminations: {
    type: Array,
    required: true,
    default: () => []
  }
});

const activeChart = ref('height');

// Process health examination data
const chartData = computed(() => {
  if (!props.healthExaminations || props.healthExaminations.length === 0) {
    return {
      labels: [],
      heights: [],
      weights: [],
      bmis: []
    };
  }

  // Sort by examination date
  const sorted = [...props.healthExaminations].sort((a, b) => 
    new Date(a.examination_date) - new Date(b.examination_date)
  );

  const labels = [];
  const heights = [];
  const weights = [];
  const bmis = [];

  sorted.forEach(exam => {
    if (exam.height && exam.weight) {
      // Use grade level as label
      const label = exam.grade_level || 'N/A';
      
      // Remove units and parse values
      const heightValue = parseFloat(String(exam.height).replace(/\s*(cm|centimeters?)/gi, '').trim()) || 0;
      const weightValue = parseFloat(String(exam.weight).replace(/\s*(kg|kilograms?)/gi, '').trim()) || 0;
      
      labels.push(label);
      heights.push(heightValue);
      weights.push(weightValue);
      
      // Calculate BMI
      const heightInMeters = heightValue / 100;
      const bmi = weightValue / (heightInMeters * heightInMeters);
      bmis.push(bmi.toFixed(1));
    }
  });

  return { labels, heights, weights, bmis };
});

// Latest data for summary cards
const latestData = computed(() => {
  if (!props.healthExaminations || props.healthExaminations.length === 0) {
    return null;
  }

  const sorted = [...props.healthExaminations].sort((a, b) => 
    new Date(b.examination_date) - new Date(a.examination_date)
  );

  const latest = sorted[0];
  if (!latest.height || !latest.weight) return null;

  // Remove units if they exist in the data
  const heightValue = String(latest.height).replace(/\s*(cm|centimeters?)/gi, '').trim();
  const weightValue = String(latest.weight).replace(/\s*(kg|kilograms?)/gi, '').trim();

  return {
    height: heightValue,
    weight: weightValue,
    date: new Date(latest.examination_date).toLocaleDateString('en-US', { 
      month: 'short', 
      day: 'numeric', 
      year: 'numeric' 
    })
  };
});

const getBMICategory = (bmi) => {
  const bmiValue = parseFloat(bmi);
  if (bmiValue < 18.5) return 'Underweight';
  if (bmiValue < 25) return 'Normal';
  if (bmiValue < 30) return 'Overweight';
  return 'Obese';
};

// Current chart data based on selected type
const currentChartData = computed(() => {
  const configs = {
    height: {
      label: 'Height (cm)',
      data: chartData.value.heights,
      borderColor: 'rgb(59, 130, 246)',
      backgroundColor: 'rgba(59, 130, 246, 0.2)'
    },
    weight: {
      label: 'Weight (kg)',
      data: chartData.value.weights,
      borderColor: 'rgb(34, 197, 94)',
      backgroundColor: 'rgba(34, 197, 94, 0.2)'
    }
  };

  const config = configs[activeChart.value];

  return {
    labels: chartData.value.labels,
    datasets: [{
      label: config.label,
      data: config.data,
      borderColor: config.borderColor,
      backgroundColor: config.backgroundColor,
      borderWidth: 4,
      tension: 0.4,
      pointRadius: 8,
      pointHoverRadius: 10,
      pointBackgroundColor: config.borderColor,
      pointBorderColor: '#fff',
      pointBorderWidth: 3
    }]
  };
});

// Chart options
const chartOptions = computed(() => {
  const yAxisLabel = activeChart.value === 'height' ? 'Height (cm)' : 'Weight (kg)';
  
  return {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          font: {
            size: 16,
            weight: 'bold'
          },
          padding: 15,
          boxWidth: 40,
          boxHeight: 15
        }
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        padding: 15,
        titleFont: {
          size: 16,
          weight: 'bold'
        },
        bodyFont: {
          size: 15
        }
      }
    },
    scales: {
      y: {
        beginAtZero: false,
        ticks: {
          font: {
            size: 14
          }
        },
        title: {
          display: true,
          text: yAxisLabel,
          font: {
            size: 16,
            weight: 'bold'
          }
        },
        grid: {
          color: 'rgba(0, 0, 0, 0.05)'
        }
      },
      x: {
        ticks: {
          font: {
            size: 14
          }
        },
        title: {
          display: true,
          text: 'Grade Level',
          font: {
            size: 16,
            weight: 'bold'
          }
        },
        grid: {
          display: false
        }
      }
    }
  };
});

</script>

<style scoped>
.growth-chart-container {
  width: 100%;
}

.chart-wrapper {
  position: relative;
  width: 100%;
  height: 500px;
  margin-bottom: 2rem;
  min-height: 500px;
}

.chart-canvas {
  width: 100% !important;
  height: 100% !important;
}

.chart-canvas :deep(canvas) {
  width: 100% !important;
  height: 100% !important;
}

.stat-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Mobile responsive */
@media (max-width: 640px) {
  .chart-wrapper {
    height: 350px;
    min-height: 350px;
  }
  
  .stats-summary {
    grid-template-columns: 1fr !important;
  }
}

@media (min-width: 641px) and (max-width: 1024px) {
  .chart-wrapper {
    height: 400px;
    min-height: 400px;
  }
}
</style>
