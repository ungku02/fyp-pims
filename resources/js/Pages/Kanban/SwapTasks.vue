<script setup>
import { ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ProjectLayout from '@/Layouts/ProjectLayout.vue';
import axios from 'axios';

const selectedUser = ref({});

watch(selectedUser, (newVal) => {
    console.log('Selected user updated:', newVal);
}, { deep: true });

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
    sentRequests: {
        type: Object,
        required: true,
    },
    receivedRequests: {
        type: Object,
        required: true,
    },
});

const successMessageVisible = ref(false);
const errorMessageVisible = ref(false);
const successVisible = ref(false);


const hasPendingRequest = (taskId) => {
    return props.sentRequests.some((request) => request.card_id === taskId && request.status === "pending");
};

function requestSwap(taskId) {
    const userId = selectedUser.value[taskId];
    if (!userId) {
        alert('Please select a user to swap with.');
        return;
    }

    console.log('Requesting swap:', taskId, userId);

    axios.post('/swap-tasks/request', { task_id: taskId, user_id: userId })
        .then(response => {
            console.log('Swap request sent:', response.data);
            successVisible.value = true;

            fetchRequests();
        })
        .catch(error => {
            console.error('Error requesting swap:', error);
        });
}

// function fetchRequests() {
//     axios.get('/swap-tasks/requests')
//         .then(response => {
//             receivedRequests.value = response.data.receivedRequests;
//             sentRequests.value = response.data.sentRequests;
//         })
//         .catch(error => {
//             console.error('Error fetching swap requests:', error);
//         });
// }

function fetchUserTasks() {
    axios.get('/swap-tasks/get')
        .then(response => {
            userTasks.value = response.data;
            props.receivedRequests.value = response.data.receivedRequests;
            props.sentRequests.value = response.data.sentRequests;
        })
        .catch(error => {
            console.error('Error fetching user tasks:', error);
        });
}

function respondToRequest(requestId, status) {
    axios.post(`/swap-tasks/respond/${requestId}`, { status })
        .then(response => {
            // Log response if needed
            console.log('Response received:', response);

            if (status === 'accept') {
                successMessageVisible.value = true;
                errorMessageVisible.value = false;
            } else {
                successMessageVisible.value = false;
                errorMessageVisible.value = true;
            }

            fetchUserTasks();
        })
        .catch(error => {
            successMessageVisible.value = false;
            errorMessageVisible.value = true;
        });
}


const getStatusBadgeClass = (status) => {
    switch (status) {
        case "pending":
            return "badge bg-warning text-dark"; // Kuning
        case "accept":
            return "badge bg-success"; // Hijau
        case "reject":
            return "badge bg-danger"; // Merah
        default:
            return "badge bg-secondary"; // Kelabu
    }
};

const closeAlert = (type) => {
    if (type === 'success') {
        successMessageVisible.value = false;
    } else if (type === 'error') {
        errorMessageVisible.value = false;
    }
    // Refresh the page
    location.reload();
};


onMounted(() => {
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
            <div class="row g-4 mb-4">
                <!-- Received Requests -->
                <div class="col-md-6">
                    <div class="p-4 rounded"
                        style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Received Requests</h3>
                        <div v-if="receivedRequests.length === 0" class="text-center text-muted">No request</div>
                        <div v-else>
                            <div v-for="request in receivedRequests" :key="request.id"
                                class="card mb-3 border-0 shadow-sm" style="background-color: #C2B9CB;">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title text-dark fw-bold mb-1">{{ request.card.title }}</h5>
                                        <p class="card-text text-muted mb-1"><i class="bi bi-calendar-event"></i> Due
                                            date: {{ request.card.due_date }}</p>
                                        <p class="card-text text-muted"><i class="bi bi-person"></i> From: {{
                                            request.old_user.name }}</p>
                                    </div>
                                    <div class="d-flex">
                                        <button @click="() => respondToRequest(request.id, 'accept')"
                                            class="btn btn-grad-outline me-2 px-3">Accept</button>
                                        <button @click="() => respondToRequest(request.id, 'reject')"
                                            class="btn btn-delete px-3">Reject</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sent Requests -->
                <div class="col-md-6">
                    <div class="p-4 rounded"
                        style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Sent Requests</h3>
                        <div v-if="sentRequests.length === 0" class="text-center text-muted">No request</div>
                        <div v-else>
                            <div v-for="request in sentRequests" :key="request.id" class="card mb-3 border-0 shadow-sm"
                                style="background-color: #C2B9CB;">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title text-dark fw-bold mb-1">{{ request.card.title }}</h5>
                                        <p class="card-text text-muted mb-1">
                                            <i class="bi bi-info-circle"></i> Status:
                                            <span :class="getStatusBadgeClass(request.status)"
                                                class="badge px-2 py-1">{{ request.status }}</span>
                                        </p>
                                        <p class="card-text text-muted">
                                            <i class="bi bi-person-check"></i> To: {{ request.new_user ?
                                            request.new_user.name : 'Pending' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-12">
                    <div class="p-4 rounded"
                        style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Request to Swap Task</h3>
                        <div v-for="task in props.tasks" :key="task.id" class="mb-3">
                            <label class="fw-bold">{{ task.title }}</label>

                            <div v-if="hasPendingRequest(task.id)" class="alert alert-warning mt-2">
                                <i class="bi bi-exclamation-circle"></i> You already have a pending request for this
                                task.
                            </div>

                            <div v-else>
                                <select v-model="selectedUser[task.id]" class="form-select">
                                    <option v-for="member in props.members" :key="member.id" :value="member.users.id">
                                        {{ member.users.name }} - {{ member.roles.name }}
                                    </option>
                                </select>
                                <button @click="requestSwap(task.id)" class="btn btn-grad-outline mt-2">Request
                                    Swap</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-success alert-dismissible fade show mt-4 custom-alert m-0" role="alert"
                v-if="successMessageVisible === true">
                <strong>Success!</strong> Task swap accepted successfully.
                <button type="button" class="close" @click="closeAlert('success')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="alert alert-success alert-dismissible fade show mt-4 custom-alert m-0" role="alert"
                v-if="successVisible === true">
                <strong>Success!</strong> Request has been sent.
                <button type="button" class="close" @click="closeAlert('success')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="alert alert-danger alert-dismissible fade show mt-4 custom-alert" role="alert"
                v-if="errorMessageVisible === true">
                <strong>Error!</strong> The task request has been rejected.
                <button type="button" class="close" @click="closeAlert('error')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

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

.custom-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    /* Ensure it appears on top */
    max-width: 400px;
    /* Adjust the width if needed */
    margin: 0;
    
    /* Align content with space between */
    align-items: center;
    /* Center the items vertically */
}

/* Optional: Adjust the button position and style */
.custom-alert .close {
    padding-left: 6px;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: inherit;
}
</style>
