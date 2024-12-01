<script setup>
    
</script>

<template>
    <Head :title="`| ${$page.component}`"/>
    <div>
       {{ $page.props.user }}
    </div>
</template>