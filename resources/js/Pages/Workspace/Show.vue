<script setup>
import { reactive, ref, watch, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import MembersLayout from '@/Layouts/MembersLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    workspace: {
        type: Object,
        required: true
    },
    workspaces: {
        type: Array,
        default: () => [],
    },
    project: {
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
});

console.log("Notifications:", props.notifications);

const unreadCount = computed(() =>
    props.notifications.filter((notification) => !notification.read).length
);
const toggleAvailability = (member) => {
    member.availability = member.availability === 'available' ? 'unavailable' : 'available';
};

const form = useForm({
    title: '',
    description: '',
});
const modalVisible = ref(false);
const deleteModalVisible = ref(false);
const deleteTitle = ref('');

const openEditModal = (workspace) => {
    form.title = workspace.title || '';
    form.description = workspace.description || '';
    modalVisible.value = true;
};

const submitEdit = () => {
    form.put(route('workspace.update', { id: props.workspace.id }), {
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
    if (deleteTitle.value === props.workspace.title) {
        router.delete(route('workspace.destroy', { id: props.workspace.id }), {
            onSuccess: () => {
                deleteModalVisible.value = false;
                location.reload();
            },
            onError: () => {
                console.error('Deletion failed');
            },
        });
    } else {
        alert('Workspace title does not match.');
    }
};

// Watch modal visibility to set inert attribute correctly
watch(modalVisible, (newValue) => {
    document.getElementById('editModal').inert = !newValue;
});
watch(deleteModalVisible, (newValue) => {
    document.getElementById('deleteModal').inert = !newValue;
});

const roles = ref([]);
const roleForm = useForm({
    name: '',
});

const fetchRoles = async () => {
    const response = await axios.get(route('roles.index'));
    roles.value = response.data;
};

const addRole = () => {
    roleForm.post(route('roles.store'), {
        onSuccess: () => {
            roleForm.reset();
            fetchRoles();
        },
        onError: () => {
            console.error('Role creation failed:', roleForm.errors);
        },
    });
};

const updateRole = (role) => {
    roleForm.put(route('roles.update', { role: role.id }), {
        onSuccess: () => {
            fetchRoles();
        },
        onError: () => {
            console.error('Role update failed:', roleForm.errors);
        },
    });
};

const deleteRole = (role) => {
    router.delete(route('roles.destroy', { role: role.id }), {
        onSuccess: () => {
            fetchRoles();
        },
        onError: () => {
            console.error('Role deletion failed');
        },
    });
};

fetchRoles();
</script>

<template>
    <MembersLayout :workspace="workspace" :workspaces="workspaces" :notifications="notifications">
        <!-- <main class="container py-4"> -->
        <header class="bg-white shadow p-4 rounded mb-4" style="background-color: #fff; border: solid 1px #6C5B7B;">
            <h5
                style="text-align: center;font-weight: bold;background: linear-gradient(to right,  #6C5B7B, #C2B9CB); color:#fff; max-width:100px; padding: 5px; border-radius: 20px;">
                {{ workspace.title }}</h5>
            <h4 style="color:#2A4965; margin-top: 5px;">{{ workspace.description }}</h4>
            <div class="flex justify-content-end fs-5">
                <button @click="openEditModal(workspace)" data-bs-toggle="modal" data-bs-target="#editModal"> <i
                        class="bi bi-pencil-square me-2"></i> </button>
                <button @click="openDeleteModal" data-bs-toggle="modal" data-bs-target="#deleteModal"> <i
                        class="bi bi-trash me-2"></i> </button>
            </div>
        </header>
        <div class="row g-4">
            <!-- Members Section -->
            <div class="col-md-8">
                <div class="p-4 rounded"
                    style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                    <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Workspace's Members</h3>
                    <button @click="openAddMemberModal" class="btn btn-grad-outline mb-3"><i
                            class="bi bi-person-plus-fill"></i>Member</button>
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Availability</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(member, index) in members.data" :key="member.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ member.users.name }}</td>
                                <td>{{ member.users.email }}</td>
                                <td class="text-center">
                                    <button @click="toggleAvailability(member)"
                                        :class="{ 'btn-availability': true, 'active': member.availability === 'available', 'inactive': member.availability === 'unavailable' }">
                                        {{ member.availability }}
                                    </button>
                                </td>
                                <td class="flex justify-content-center">

                                    <button class="btn btn-sm btn-grad-outline text-center">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="m-3" style="color:#2A4965; font-weight: bold;">Projects</h3>
                        <button @click="openAddMemberModal" class="btn btn-grad-outline mb-3"><i
                                class="bi bi-plus-circle"></i>Project</button>
                    </div>
                    <div v-if="workspace.project.length === 0" class="bg-light-purple p-4 rounded mb-4"
                        style="background-color: #fff;">
                        <p style="color:#67283F;">No project available yet.</p>
                    </div>
                    <div v-else class="list-group">
                        <div class="card mb-3" v-for="project in workspace.project" :key="project.id"
                            style="background-color: #C2B9CB; border: none;">

                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>
                                    <p class="card-title" style="color: #2A4965; font-weight: bold;">{{
                                        project.title }}</p>
                                    <p class="card-text text-muted">{{ project.description }}</p>
                                </div>

                            </div>
                            <div class="card-footer text-end" style="background-color: transparent; border: none;">
                                <button class="btn btn-grad me-2">
                                    Open
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- </main> -->
    </MembersLayout>

    <!-- Edit Workspace Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" :inert="!modalVisible"
        :aria-hidden="!modalVisible">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Workspace</h1>
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
                            <TextInput id="editDescription" type="text" class="mt-1 block w-full"
                                v-model="form.description" required autofocus autocomplete="description" />
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

    <!-- Delete Workspace Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
        :inert="!deleteModalVisible" :aria-hidden="!deleteModalVisible">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteModalLabel">Delete Workspace</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this workspace? Please enter the workspace title to confirm.</p>
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

    <!-- Add/Edit Role Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" :inert="!modalVisible" :aria-hidden="!modalVisible">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="roleModalLabel">Add/Edit Role</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="addRole">
                        <div class="mb-3">
                            <InputLabel for="roleName" value="Role Name" />
                            <TextInput id="roleName" type="text" class="mt-1 block w-full" v-model="roleForm.name" required autofocus autocomplete="name" />
                            <InputError class="mt-2" :message="roleForm.errors.name" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn create-btn">Save Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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

.btn i {
    margin-right: 5px;
    vertical-align: middle;
}

.btn-grad {
    background-color: #fff;
    color: #6C5B7B;
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
}

.btn-grad-outline:hover {
    background-color: #6C5B7B;
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
