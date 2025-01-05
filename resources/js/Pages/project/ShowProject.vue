<template>

    <Head title="Project" />

    <MembersLayout :workspace="workspace" :workspaces="workspaces" :notifications="notifications">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project</h2>
        </template>

        <!-- Main Container -->
        <div class="cmax-w-mx-auto sm:px-6 lg:px-8 ">

            <!-- Main Content Area -->
            <div class="flex-grow">
                <div class="content-header p-3 bg-light rounded shadow mb-4">
                    <p class="text-muted">Checkout and dive into your ongoing projects!</p>
                </div>

                <!-- Project List Section -->
                <div class="bg-white shadow-lg sm:rounded-lg p-4 mb-3">
                    <h5 class="font-semibold text-xl text-gray-800 mb-3">{{ workspace.title }}</h5>
                    <p class="text-muted mb-4">Explore and manage your projects below.</p>

                    <!-- Project Cards -->
                    <div class="row">
                        <!-- <div v-if="hasProjects" class="d-flex flex-wrap justify-content-start"> -->
                            <div v-for="project in props.projects" :key="project.id" class="col-md-3 mb-3">
                                <div class="card  project-card me-5"
                                    @click="goToKanban(project.id)" style="width: 300px;">
                                    <img :src="`/img/${project.image}`" class="card-img-top project-image"
                                        alt="Project Image" style="width: 400px; height:200px;">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ project.title }}</h5>
                                        <p class="card-text">{{ project.description }}</p>
                                    </div>
                                </div>
                            </div>
                        <!-- </div> -->
                        <!-- <div v-else class="text-center mt-4">
                            <p class="text-muted">No project created yet</p>
                        </div> -->
                    </div>

                    <!-- Add Project Button -->
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-primary add-workspace-btn" data-bs-toggle="modal"
                            data-bs-target="#projectModal">
                            <i class="bi bi-plus-circle-fill"></i> Add Project
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Creating New Project -->
        <div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectModalLabel">Create New Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submit">
                            <!-- Project Title Input -->
                            <div class="mb-3">
                                <InputLabel for="title" value="Title" />
                                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title"
                                    required autofocus autocomplete="title" />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>
                            <!-- Project Description Input -->
                            <div class="mb-3">
                                <InputLabel for="description" value="Description" />
                                <TextInput id="description" type="text" class="mt-1 block w-full"
                                    v-model="form.description" required autocomplete="description" />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Template Image Selection -->
                            <div class="mb-3">
                                <InputLabel value="Choose Template" />
                                <div class="template-options d-flex flex-wrap gap-2">
                                    <div v-for="(template, index) in templates" :key="index" class="template-option">
                                        <input type="radio" :id="'template' + index" :value="template"
                                            v-model="form.image" class="d-none" />
                                        <label :for="'template' + index" class="template-label"
                                            :class="{ 'selected': form.image === template }">
                                            <img :src="template" alt="Template Image" class="template-image" />
                                        </label>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.image" />
                            </div>

                            <!-- Project Members Section -->
                            <div class="mb-3">
                                <label>Add Project Members</label>

                                <!-- Search Input for Adding Members by Email -->
                                <div class="d-flex gap-2 align-items-center mb-3">
                                    <input type="email" class="form-control rounded" placeholder="Enter member's email"
                                        v-model="memberEmail" @input="searchUser" autocomplete="off" />
                                    <button class="btn add-workspace-btn" type="button"
                                        @click="openRoleModal">Add</button>
                                </div>

                                <!-- Tooltip for showing added members -->
                                <div v-if="form.members.length > 0"
                                    class="added-members-tooltip mt-2 p-2 bg-light border rounded">
                                    <p class="text-muted mb-1">Members added:</p>
                                    <ul class="list-unstyled mb-0">
                                        <li v-for="member in form.members" :key="member.email" class="text-secondary">
                                            {{ member.email }} ({{ member.role }})

                                            <button type="button"
                                                class="btn btn-sm p-0 text-danger ms-2 justify-content-end"
                                                @click="removeMember(index)">
                                                <i class="bi bi-x-circle-fill"></i>
                                                <!-- Cross icon from Bootstrap Icons -->
                                            </button>

                                        </li>

                                    </ul>
                                </div>

                                <!-- Display Search Result or Not Found Message -->

                                <!-- Display Search Result or Not Found Message -->
                                <div v-if="searchResult === 'not_found'" class="text-danger">User not found.</div>
                                <div v-else-if="searchResult" class="text-success">User found: {{ searchResult.name }}
                                </div>



                                <!-- Role Selection Modal -->
                                <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content" style="background-color: black; color: white;">
                                            <div class="modal-header">
                                                <h5 class="modal-title rounded" id="roleModalLabel">Assign Role</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body ">
                                                <p>Select a role for <strong>{{ memberEmail }}</strong></p>
                                                <select v-model="selectedRole"
                                                    class="form-select mt-3 role-select-dropdown rounded">
                                                    <option disabled value="">Select Role</option>
                                                    <option v-for="role in roles" :value="role.id" :key="role.id">{{
                                                        role.name
                                                        }}</option>
                                                </select>

                                            </div>
                                            <div class="modal-footer role-modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-outline-secondary"
                                                    @click="confirmAddMember" data-bs-dismiss="modal">Add
                                                    Member</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                    @click="resetForm">Close</button>
                                <button type="submit" class="btn add-workspace-btn">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MembersLayout>

</template>

<script setup>
import MembersLayout from '@/Layouts/MembersLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { defineProps } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    workspace: {
        type: Object,
        required: true
    },
    workspaces: {
        type: Array,
        default: () => [],
    },
    projects: {
        type: Array,
        default: () => [],
    },
    members: {
        type: Object,
        required: true,
    },
    notifications: {
        type: Array,
        default: () => [],
    },

    roles: {
        type: Array,
        default: () => [],
    },
});

const modalVisible = ref(false);
const memberEmail = ref('');
const searchResult = ref(null);
const selectedRole = ref('');

const form = useForm({
    title: '',
    description: '',
    image: '',
    workspace_id: props.workspace.id,
    members: [],
    errors: {},
});

const templates = [
    '/img/template-1.png',
    '/img/template-2.png',
    '/img/template-3.png',
    '/img/template-4.png',
];

function goToKanban(projectId) {
    window.location.href = route('project/show', { id: projectId });
}

async function searchUser() {
    if (memberEmail.value) {
        try {
            const response = await axios.post('/search-user', { email: memberEmail.value });
            // Check if the response has user data
            if (response.data && response.data.email) {
                searchResult.value = response.data;
            } else {
                // No user found, set to 'not_found'
                searchResult.value = 'not_found';
            }
        } catch (error) {
            if (error.response && error.response.status === 404) {
                // If the server responds with 404, set searchResult to 'not_found'
                searchResult.value = 'not_found';
            } else {
                // Other error cases
                console.error("An error occurred:", error);
                searchResult.value = null;
            }
        }
    } else {
        searchResult.value = 'not_found';
    }
}

function openRoleModal() {
    if (searchResult.value && searchResult.value !== 'not_found') {
        const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));
        roleModal.show();
    }
}

function confirmAddMember() { 
    console.log(selectedRole.value)
    if (selectedRole.value) {
        form.members.push({
            email: searchResult.value.email,
            name: searchResult.value.name,
            role_id: selectedRole.value,
            role: props.roles.find(role => role.id === selectedRole.value).name

           
        });
        selectedRole.value = '';
        memberEmail.value = '';
        searchResult.value = null;
    }
}
// Method to remove a member from the project members list
function removeMember(index) {
    form.members.splice(index, 1);
}

function resetForm() {
    form.reset();
    searchResult.value = null;
    memberEmail.value = '';
    modalVisible.value = false;
}


function submit() {
    form.workspace_id = props.workspace.id;
    form.image = form.image.split('/').pop();

    form.post(route('project.create'), {
        onSuccess: () => {
            form.reset();
            modalVisible.value = false;
            location.reload();
        },
        onError: () => {
            console.error('Submission failed:', form.errors);
        },
    });
}

</script>

<style scoped>
.container {
    margin-top: 20px;
}

.sidebar-container {
    min-width: 200px;
    max-width: 250px;
}

.content-header {
    background-color: #f8f9fa;
}

.card {
    transition: transform 0.3s ease-in-out;
}

.card:hover {
    transform: scale(1.05);
}

.project-card {
    cursor: pointer;
}

.add-workspace-btn {
    background-color: white;
    color: #000000;
    border: 0.5px solid #000000;
}

.add-workspace-btn:hover {
    background-color: #000000;
    color: white;
}

.template-options {
    display: flex;
    gap: 10px;
}

.template-label {
    border: 2px solid transparent;
    transition: border-color 0.2s;
}

.template-label.selected,
.template-label:hover {
    border-color: #555;
}

.template-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
}

.added-members-tooltip {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 8px;
    max-width: 100%;
}

.added-members-tooltip p {
    font-size: 0.9rem;
    color: #6c757d;
}

.added-members-tooltip li {
    font-size: 0.85rem;
}
</style>
