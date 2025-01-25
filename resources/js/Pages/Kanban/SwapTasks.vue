<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ProjectLayout from '@/Layouts/ProjectLayout.vue';
import axios from 'axios';

const selectedUser = ref({});
const receivedRequests = ref([]);
const sentRequests = ref([]);
const userTasks = ref([]);

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

function fetchRequests() {
    // Fetch received and sent requests from the API
    // ...
}

function fetchUserTasks() {
    axios.get('/swap-tasks')
        .then(response => {
            userTasks.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching user tasks:', error);
        });
}

function respondToRequest(requestId, status) {
    // Make an API call to respond to the request (accept/reject)
    // ...
}

onMounted(() => {
    fetchRequests();
    fetchUserTasks();
});
</script>

<template>
    <ProjectLayout :project="project">
        <div class="container">
            <header class="mb-4">
                <h5
                    style="text-align: center;font-weight: bold;background: linear-gradient(to right,  #6C5B7B, #C2B9CB); color:#fff; max-width:100px; padding: 5px; border-radius: 20px;">
                    Swap Tasks</h5>
            </header>
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="p-4 rounded" style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Request to Swap Task</h3>
                        <div v-for="task in tasks" :key="task.id" class="mb-3">
                            <label>{{ task.title }}</label>
                            <select v-model="selectedUser[task.id]" class="form-select">
                                <option v-for="member in props.members" :key="member.id" :value="member.id">{{ member.users.name }} - {{ member.roles.name }}</option>
                            </select>
                            <button @click="requestSwap(task.id)" class="btn btn-grad-outline mt-2">Request Swap</button>
                        </div>
                    </div>
                </div>
               
            </div>
            <div class="row g-4 mt-4">
                <div class="col-md-6">
                    <div class="p-4 rounded" style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Received Requests</h3>
                        <div v-if="receivedRequests.length === 0">No request</div>
                        <div v-else>
                            <div v-for="request in receivedRequests" :key="request.id" class="card mb-3" style="background-color: #C2B9CB; border: none;">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="card-title" style="color: #2A4965; font-weight: bold;">{{ request.task.title }}</p>
                                        <p class="card-text text-muted">from {{ request.old_user.name }}</p>
                                    </div>
                                    <div>
                                        <button @click="respondToRequest(request.id, 'accept')" class="btn btn-grad-outline me-2">Accept</button>
                                        <button @click="respondToRequest(request.id, 'reject')" class="btn btn-delete">Reject</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 rounded" style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Sent Requests</h3>
                        <div v-if="sentRequests.length === 0">No request</div>
                        <div v-else>
                            <div v-for="request in sentRequests" :key="request.id" class="card mb-3" style="background-color: #C2B9CB; border: none;">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="card-title" style="color: #2A4965; font-weight: bold;">{{ request.task.title }}</p>
                                        <p class="card-text text-muted">to {{ request.new_user ? request.new_user.name : 'Pending' }}</p>
                                        <p class="card-text text-muted">Status: {{ request.status }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

.card {
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.btn i {
    margin-right: 5px;
    vertical-align: middle;
}

.btn-grad {
    background-color: #fff;
    color: #6C5B7B;
    border-radius: 30px;
}

.create-btn {
    border: solid 1px #6c5b7b;
    color: #6c5b7b;
}

.create-btn:hover {
    background-color: #6c5b7b;
    color: #fff;
}

.btn-grad:hover {
    background-color: #6C5B7B;
    color: #fff;
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

.btn-delete {
    color: rgb(175, 100, 100);
    border: 1px solid rgb(175, 100, 100)
}

.btn-delete:hover {
    color: rgb(255, 255, 255);
    background-color: rgb(175, 100, 100);
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination button {
    background-color: #6C5B7B;
    color: white;
    border: none;
    padding: 7px;
    margin: 0 5px;
    cursor: pointer;
    border-radius: 5px;
}

.pagination button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
</style>
