<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ProjectLayout from '@/Layouts/ProjectLayout.vue';

const selectedUser = ref({});

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    columns: {
        type: Array,
        default: () => [],
    },
    members: {
        type: Object,
        required: true,
    },
    tasks: {
        type: Object,
        required: true,
    },
});
function requestSwap(taskId) {
    const userId = selectedUser.value[taskId];
    // Make an API call to request the swap
    // ...
}
</script>

<template>
    <ProjectLayout :project="project">
       
    </ProjectLayout>
</template>

<style>
.custom-table {
    max-width: 80%;
    border-collapse: separate;
}

.custom-table thead th {
    text-align: left;
    /* padding: 7px; */
    font-size: 14px;
    font-weight: bold;
    color: #6C5B7B;
    background-color: #C2B9CB;
    border: none;
    border-radius: 8px 8px 0 0;
}

.custom-table tbody td {
    background-color: #f9f9f9;
    /* padding: 7px; */
    font-size: 14px;
    border: none;
    border-radius: 8px;
    vertical-align: middle;
}

.custom-table tbody tr:hover td {
    background-color: #E0DCE5;
}

.btn-grad-outline {
    background-color: transparent;
    color: #6C5B7B;
    border: 1px solid #6C5B7B;
}

.btn-grad-outline:hover {
    background-color: #6C5B7B;
    color: #fff;
}
</style>
