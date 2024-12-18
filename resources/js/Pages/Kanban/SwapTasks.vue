<template>
    <ProjectLayout :project="{ id: projectId, background: background }">
        <header class="bg-light-purple p-4 rounded" style="background-color: #fff; border: solid 1px #6C5B7B;">
            <h5 style="text-align: center; font-weight: bold; background: linear-gradient(to right, #6C5B7B, #C2B9CB); color: #fff; max-width: 300px; padding: 5px; border-radius: 20px;">
                Swap Tasks
            </h5>
        </header>
        <div class="row g-4 mt-3">
            <div class="col-md-12">
                <div class="p-4 rounded" style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                    <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Tasks</h3>
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Task</th>
                                <th>Description</th>
                                <!-- <th>Assigned To</th> -->
                                <th>Swap With</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index) in tasks" :key="task.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ task.title }}</td>
                                <td>{{ task.description }}</td>
                                <!-- <td>{{ task.userRole?.users?.name || 'Unassigned' }}</td> -->
                                <td>
                                    <select v-model="selectedUser[task.id]" class="form-select">
                                        <option v-for="member in members" :key="member.id" :value="member.id">
                                            {{ member.users.name }} ({{ member.roles.name }})
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <button @click="requestSwap(task.id)" class="btn btn-grad-outline">Request Swap</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </ProjectLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ProjectLayout from '@/Layouts/ProjectLayout.vue';

const { tasks, members, projectId, background } = usePage().props;
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

<style scoped>
.custom-table {
    width: 100%;
    border-collapse: separate;
}

.custom-table thead th {
    text-align: left;
    padding: 7px;
    font-size: 14px;
    font-weight: bold;
    color: #6C5B7B;
    background-color: #C2B9CB;
    border: none;
    border-radius: 8px 8px 0 0;
}

.custom-table tbody td {
    background-color: #f9f9f9;
    padding: 7px;
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
