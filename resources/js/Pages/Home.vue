<script setup>
import { ref } from 'vue';
// import { Form } from '@primevue/forms';
// import Dialog from 'primevue/dialog';
const visible = ref(false);
</script>

<template>
    <Head :title="`| ${$page.component}`"/>
    
    <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex flex-col">
      <div class="p-4 font-bold text-lg border-b border-gray-700">Admin Panel</div>
      <nav class="flex-1">
        <ul>
          <li class="p-4 hover:bg-gray-700">
            <a href="#" class="block">Dashboard</a>
          </li>
          <li class="p-4 hover:bg-gray-700">
            <a href="#" class="block">Users</a>
          </li>
          <li class="p-4 hover:bg-gray-700">
            <a href="#" class="block">Settings</a>
          </li>
        </ul>
      </nav>
      <div class="p-4 border-t border-gray-700">Logout</div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
      <header class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <Button label="Small" icon="pi pi-check" size="small" />
        <Button label="Normal" icon="pi pi-check" />
        <Button label="Large" icon="pi pi-check" size="large" />
      </header>

      <section>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Card 1 -->
          <div class="bg-white shadow rounded p-6">
            <h2 class="text-lg font-semibold mb-2">Users</h2>
            <p class="text-gray-600">120 Active Users</p>
          </div>
          <!-- Card 2 -->
          <div class="bg-white shadow rounded p-6">
            <h2 class="text-lg font-semibold mb-2">Revenue</h2>
            <p class="text-gray-600">$15,000 This Month</p>
          </div>
          <!-- Card 3 -->
          <div class="bg-white shadow rounded p-6">
            <h2 class="text-lg font-semibold mb-2">Support Tickets</h2>
            <p class="text-gray-600">23 Open Tickets</p>
          </div>
        </div>
      </section>
    </main>
  </div>

</template>