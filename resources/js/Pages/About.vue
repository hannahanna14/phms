<script setup>
    defineProps({
        user: String
    })
</script>

<template>
    <div>
        <h1 class="text-3xl font-bold underline">
            About Page {{ user }}
        </h1>
    </div>
</template>