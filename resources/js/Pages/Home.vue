<script setup>
import { ref, onMounted } from 'vue';
import Chart from 'primevue/chart';

const chartData = ref({
    labels: ['Severely Wasted', 'Wasted', 'Normal', 'Overweight'],
    datasets: [
        {
            label: 'Number of Students',
            data: [25, 50, 300, 125],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',  // Red for Severely Wasted
                'rgba(255, 159, 64, 0.2)',  // Orange for Wasted
                'rgba(75, 192, 192, 0.2)',  // Green for Normal
                'rgba(54, 162, 235, 0.2)'   // Blue for Overweight
            ],
            borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)'
            ],
            borderWidth: 1
        }
    ]
});

const chartOptions = ref({
    indexAxis: 'y',  // This makes it a horizontal bar chart
    scales: {
        x: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Number of Students'
            }
        },
        y: {
            title: {
                display: true,
                text: 'Nutritional Status'
            }
        }
    },
    plugins: {
        legend: {
            display: false  // Hide legend since we only have one dataset
        },
        title: {
            display: true,
            text: 'Student Nutritional Status Distribution',
            font: {
                size: 16
            }
        }
    }
});

const visible = ref(false);
</script>

<template>
    <Head :title="`| ${$page.component}`"/>

    <div class="flex h-screen">
    <main class="flex-1 p-6">
      <header class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Dashboard</h1>
      </header>

      <section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
          <!-- Card 1 -->
          <div class="bg-white shadow rounded p-6">
            <h2 class="text-lg font-semibold mb-2">Number of Student(2024-2025)</h2>
            <p class="text-gray-600">500</p>
          </div>
          <!-- Card 2 -->
          <div class="bg-white shadow rounded p-6">
            <h2 class="text-lg font-semibold mb-2">Boys</h2>
            <p class="text-gray-600">250</p>
          </div>
          <!-- Card 3 -->
          <div class="bg-white shadow rounded p-6">
            <h2 class="text-lg font-semibold mb-2">Girls</h2>
            <p class="text-gray-600">250</p>
          </div>
        </div>

        <div class="bg-white shadow rounded p-6">
            <Chart type="bar" :data="chartData" :options="chartOptions" class="w-full" style="height: 400px;" />
        </div>

      </section>
    </main>
  </div>
</template>
