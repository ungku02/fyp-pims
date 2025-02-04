<script setup>
import { defineProps, computed, watch } from 'vue';
import ProjectLayout from '@/Layouts/ProjectLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import axios from 'axios';

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
    notifications: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const unreadCount = computed(() =>
    props.notifications.filter((notification) => !notification.read).length
);


const form = useForm({
    memberEmail: '',
    selectedRole: '',
    title: '',
    description: '',
    errors: {}
});

const openAddMemberModal = () => {
    const addMemberModal = new bootstrap.Modal(document.getElementById('addMemberModal'));
    addMemberModal.show();
};

const searchResult = ref(null);
const memberEmail = ref('');
const selectedRole = ref('');

const searchUser = async () => {
    if (memberEmail.value) {
        try {
            const response = await axios.post('/search-user', { email: memberEmail.value });
            if (response.data && response.data.email) {
                searchResult.value = response.data;
            } else {
                searchResult.value = 'not_found';
            }
        } catch (error) {
            if (error.response && error.response.status === 404) {
                searchResult.value = 'not_found';
            } else {
                console.error("An error occurred:", error);
                searchResult.value = null;
            }
        }
    } else {
        searchResult.value = 'not_found';
    }
};

const openRoleModal = () => {
    if (searchResult.value && searchResult.value !== 'not_found') {
        const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));
        roleModal.show();
    }
};


const confirmAddMember = async () => {
    if (selectedRole.value) {
        try {
            await axios.post(route('project.addMember', { id: props.project.id }), {
                email: searchResult.value.email,
                role_id: selectedRole.value,
            });

            // Ensure form.members is defined and accessible
            if (!form.members) {
                form.members = [];
            }

            form.members.push({
                email: searchResult.value.email,
                name: searchResult.value.name,
                role_id: selectedRole.value,
                role: props.roles.find(role => role.id === selectedRole.value).name
            });

            selectedRole.value = '';
            memberEmail.value = '';
            searchResult.value = null;

            // Paksa reload route
            location.reload();
        } catch (error) {
            console.error("An error occurred while adding the member:", error);
        }
    }
};


const removeMember = (index) => {
    form.members.splice(index, 1);
};

const modalVisible = ref(false);
const deleteModalVisible = ref(false);
const deleteTitle = ref('');

const openEditModal = (project) => {
    form.title = project.title || '';
    form.description = project.description || '';
    modalVisible.value = true;
};

const submitEdit = () => {
    form.put(route('project.update', { id: props.project.id }), {
        onSuccess: () => {
            form.reset();
            modalVisible.value = false;
            location.reload();
        },
        onError: () => {
            console.error('Submission failed:', form.errors);
        },
    });
};

const openDeleteModal = () => {
    deleteModalVisible.value = true;
};

const confirmDelete = () => {
    if (deleteTitle.value === props.project.title) {
        router.delete(route('project.destroy', { id: props.project.id }), {
            onSuccess: () => {
                deleteModalVisible.value = false;
                location.reload();
            },
            onError: () => {
                console.error('Deletion failed');
            },
        });
    } else {
        alert('Project title does not match.');
    }
};

// Watch modal visibility to set inert attribute correctly
watch(modalVisible, (newValue) => {
    document.getElementById('editModal').inert = !newValue;
});
watch(deleteModalVisible, (newValue) => {
    document.getElementById('deleteModal').inert = !newValue;
});

const editMemberModalVisible = ref(false);
const memberToEdit = ref(null);

const openEditMemberModal = (member) => {
    memberToEdit.value = {
        id: member.id,
        email: member.users.email,
        role_id: member.roles.id,
    };
    editMemberModalVisible.value = true;
};

const confirmEditMember = async () => {
    if (memberToEdit.value.role_id) {
        try {
            await axios.put(route('project.updateMemberRole', { id: props.project.id, memberId: memberToEdit.value.id }), {
                role_id: memberToEdit.value.role_id,
            });

            // Update the member's role in the local members list
            const memberIndex = members.data.findIndex(member => member.id === memberToEdit.value.id);
            console.log('Member index:', memberIndex);
            if (memberIndex !== -1) {
                members.data[memberIndex].roles.id = memberToEdit.value.role_id;
                members.data[memberIndex].roles.name = props.roles.find(role => role.id === memberToEdit.value.role_id).name;
            }

            editMemberModalVisible.value = false;
            memberToEdit.value = null;

            // Force reload route
            location.reload();
        } catch (error) {
            console.error("An error occurred while editing the member:", error);
        }
    }
};

console.log('Project prop in Show.vue:', props.project);
</script>

<template>
    <ProjectLayout :project="project" :notifications="notifications">
        <header class="bg-light-purple p-4 rounded " style="background-color: #fff; border: solid 1px #6C5B7B;">
            <h5
                style="text-align: center; font-weight: bold;background: linear-gradient(to right,  #6C5B7B, #C2B9CB); color:#fff; max-width:300px; padding: 5px; border-radius: 20px;">
                {{ project.title }}</h5>
            <p style="color:#2A4965; margin-top: 5px;">{{ project.description }}</p>
            <div class="flex justify-content-end fs-5">
                <button @click="openEditModal(project)" data-bs-toggle="modal" data-bs-target="#editModal"> <i
                        class="bi bi-pencil-square me-2"></i> </button>
                <button @click="openDeleteModal" data-bs-toggle="modal" data-bs-target="#deleteModal"> <i
                        class="bi bi-trash me-2"></i> </button>
            </div>
        </header>
        <div class="row g-4 mt-3">
            <!-- Members Section -->
            <div class="col-md-8">
                <div class="p-4 rounded"
                    style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                    <div class="d-flex justify-content-between">
                        <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Project's Members</h3>
                        <button @click="openAddMemberModal" class="btn btn-grad-outline mb-3"><i
                                class="bi bi-person-plus-fill"></i>Member</button>
                    </div>
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Role</th>
                                <!-- <th>Availability</th> -->
                                <!-- <th>Actions</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(member, index) in members.data" :key="member.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ member.users.name }}</td>
                                <td>{{ member.roles.name }}</td>
                                <!-- <td class="text-center">
                                        <button @click="toggleAvailability(member)" 
                                                :class="{'btn-availability': true, 'active': member.availability === 'available', 'inactive': member.availability === 'unavailable'}">
                                            Available
                                        </button>
                                    </td> -->
                                <!-- <td class="flex justify-content-center">
                                    <button @click="openEditMemberModal(member)"
                                        class="btn btn-sm btn-grad-outline text-center">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <button @click="members.prev_page_url && $inertia.get(members.prev_page_url)"
                            :disabled="!members.prev_page_url">Previous</button>
                        <button @click="members.next_page_url && $inertia.get(members.next_page_url)"
                            :disabled="!members.next_page_url">Next</button>
                    </div>
                </div>
            </div>

            <!-- Projects Section -->
            <div class="col-md-4">
                <div class="p-4 rounded"
                    style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                    <h3 class="m-3" style="color:#2A4965; font-weight: bold;">Board</h3>
                    <!-- <div class="row row-cols-1 row-cols-md-2 g-4"> -->
                    <!-- <div class="col"> -->
                    <div class="card board-card" @click="goToKanban(project.id)">
                        <div class="card-body">
                            <h5 class="card-title" style="font-weight: bold;">{{ project.title }}</h5>
                            <div class="d-flex flex-wrap mt-3">
                                <div v-for="member in project.user_role.slice(0, 3)" :key="member.id"
                                    class="rounded-circle name-logo d-flex justify-content-center align-items-center"
                                    style="width: 36px; height: 36px; color: #fff; background-color: #6C5B7B; margin: 0; display: inline-block;">

                                    {{ member.users.name.charAt(0).toUpperCase() }}
                                </div>
                                <div v-if="project.user_role.length > 3" class="more-members"
                                    style="width: 36px; height: 36px; border-radius: 50%; background-color: #fff; color: #6C5B7B; display: flex; justify-content: center; align-items: center; font-size: 14px; font-weight: bold;">
                                    +{{ project.user_role.length - 3 }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center m-2">
                            <p class="mb-0 me-1" style="font-size: 12px;">Progress:</p>
                            <div class="d-flex align-items-center">
                                <div class="progress" role="progressbar" aria-label="Example 20px high"
                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"
                                    style="height: 10px; width: 100px;">
                                    <div class="progress-bar bg-secondary" style="width: 25%; font-size: 7px;">
                                    </div>
                                </div>
                                <!-- Progress percentage outside the bar -->
                                <span class="ms-2" style="font-size: 12px;">25%</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button>Open</button>
                        </div>
                        <!-- </div> -->
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Member Modal -->
        <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMemberModalLabel">Add Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="confirmAddMember">
                            <!-- Email Input -->
                            <div class="mb-3">
                                <InputLabel for="email" value="Email" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="memberEmail"
                                    @input="searchUser" required />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>
                            <div v-if="searchResult === 'not_found'" class="text-danger">User not found.</div>
                            <div v-else-if="searchResult" class="text-success">User found: {{ searchResult.name }}</div>
                            <!-- Role Selection -->
                            <div class="mb-3">
                                <InputLabel for="role" value="Role" />
                                <select v-model="selectedRole" class="form-select mt-1 block w-full" required>
                                    <option disabled value="">Select Role</option>
                                    <option v-for="role in roles" :value="role.id" :key="role.id">{{ role.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>
                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-grad-outline">Add Member</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Project Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" :inert="!modalVisible"
            :aria-hidden="!modalVisible">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editModalLabel">Edit Project</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitEdit">
                            <div class="mb-3">
                                <InputLabel for="editTitle" value="Title" />
                                <TextInput id="editTitle" type="text" class="mt-1 block w-full" v-model="form.title"
                                    required autofocus autocomplete="title" />
                                <!-- <InputError class="mt-2" :message="form.errors.title" /> -->
                            </div>
                            <div class="mb-3">
                                <InputLabel for="editDescription" value="Description" />
                                <textarea id="editTitle" class="mt-1 block w-full" v-model="form.description" required
                                    autofocus autocomplete="title" rows="4"></textarea>
                                <!-- <InputError class="mt-2" :message="form.errors.description" /> -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn create-btn">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Project Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
            :inert="!deleteModalVisible" :aria-hidden="!deleteModalVisible">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteModalLabel">Delete Project</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this project? Please enter the project title to confirm.</p>
                        <TextInput id="deleteTitle" type="text" class="mt-1 block w-full" v-model="deleteTitle" required
                            autofocus />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" @click="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Member Modal -->
        <div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel"
            :inert="!editMemberModalVisible" :aria-hidden="!editMemberModalVisible" v-if="editMemberModalVisible">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMemberModalLabel">Edit Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="confirmEditMember">
                            <!-- Email Input (View Only) -->
                            <div class="mb-3">
                                <InputLabel for="editMemberEmail" value="Email" />
                                <TextInput id="editMemberEmail" type="email" class="mt-1 block w-full"
                                    v-model="memberToEdit.value.email" readonly />
                            </div>
                            <!-- Role Selection -->
                            <div class="mb-3">
                                <InputLabel for="editMemberRole" value="Role" />
                                <select v-model="memberToEdit.value.role_id" class="form-select mt-1 block w-full"
                                    required>
                                    <option disabled value="">Select Role</option>
                                    <option v-for="role in roles" :value="role.id" :key="role.id">{{ role.name }}
                                    </option>
                                </select>
                            </div>
                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-grad-outline">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </ProjectLayout>
</template>

<style>
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

.card {
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.board-card {
    cursor: pointer;
    border-radius: 10px;
    background: linear-gradient(to right, #6C5B7B, #C2B9CB);
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s ease-in-out;
}

.board-card:hover {
    transform: scale(1.05);
}

.member-avatar img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 5px;
}

.more-members {
    display: inline-block;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #fff;
    color: #6C5B7B;
    text-align: center;
    line-height: 30px;
    font-size: 14px;
    font-weight: bold;
}


.btn i {
    margin-right: 5px;
    vertical-align: middle;
}

.btn-grad {
    background-color: #fff;
    color: #6C5B7B;
    border: none;
    border-radius: 30px;
}

.btn-grad:hover {
    background-color: #6C5B7B;
    color: #fff;

}

.btn-grad-outline {
    background-color: transparent;
    color: #6C5B7B;
    border: 1px solid #6C5B7B;
    /* border-radius: 30px; */
}

.btn-grad-outline:hover {
    background-color: #6C5B7B;
    color: #fff;
}

.create-btn {
    border: solid 1px #6c5b7b;
    color: #6c5b7b;
}

.create-btn:hover {
    background-color: #6c5b7b;
    color: #fff;
}

.btn-availability {
    border: none;
    background: none;
    cursor: pointer;
    padding: 0;
    font-size: 14px;
    font-weight: bold;
}

.btn-availability.active {
    color: beige;
    background-color: green;
    padding: 5px;
    border-radius: 20px;
    text-transform: capitalize;
}

.btn-availability.inactive {
    color: rgb(255, 255, 255);
    background-color: red;
    padding: 5px;
    border-radius: 20px;
    text-transform: capitalize;

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
