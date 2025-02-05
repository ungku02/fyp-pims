<script setup>
import { ref, onMounted, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Inertia } from '@inertiajs/inertia';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    workspaces: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
    notifications: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
});

const roles = ref(props.roles);
const form = useForm({ name: '' });
const roleform = useForm({ name: '' });
const showViewModal = ref(false);
const modalVisible = ref(false);
const roleUsers = ref([]);
const roleIdToUpdate = ref(null);
const showDeleteModal = ref(false);


const addRole = () => {
    form.post('/roles/store', {
        onSuccess: () => {
            roles.value.push({ name: form.name });
            form.reset();
        },
        onError: (errors) => {
            console.error('Error adding role:', errors);
        }
    });
};

const submitEdit = () => {
    roleform.put(route('roles.update', { id: roleIdToUpdate.value }), {
        onSuccess: () => {
            roleform.reset();
            modalVisible.value = false;
            location.reload();
        },
        onError: () => {
            console.error('Submission failed:', roleform.errors);
        },
    });
};

const deleteRole = (role) => {
        roleIdToUpdate.value = role.id;
        showDeleteModal.value = true;
};

const confirmDelete = () => {
    Inertia.delete(`/roles/${roleIdToUpdate.value}`, {
        onSuccess: () => {
            roles.value = roles.value.filter(r => r.id !== roleIdToUpdate.value);
            showDeleteModal.value = false;
        },
        onError: (errors) => {
            console.error('Error deleting role:', errors);
        }
    });
};

const viewRole = (role) => {
    Inertia.get(`/roles/${role.id}/users`, {
        onSuccess: ({ props }) => {
            roleUsers.value = props.users;
            showViewModal.value = true;
        },
        onError: (errors) => {
            console.error('Error fetching role users:', errors);
        }
    });
};

const openEditModal = (role) => {
        roleform.name = role.name || '';
        roleIdToUpdate.value = role.id; // Save the ID of the role being edited
        modalVisible.value = true;
    
};



watch(modalVisible, (newValue) => {
    document.getElementById('editModal').inert = !newValue;
});

onMounted(() => {
    roles.value = props.roles;
});
</script>

<template>
    <AuthenticatedLayout :notifications="notifications">
        <div class="role-management">
            <header class="bg-light-purple p-3 rounded" style="background-color: #fff; border: solid 1px #6C5B7B;">
                <h5
                    style="text-align: center; font-weight: bold; background: linear-gradient(to right, #6C5B7B, #C2B9CB); color: #fff; max-width: 300px; padding: 5px; border-radius: 20px;">
                    Role Management
                </h5>
                <p>The role management only visible for the Project Manager view only.
                    Here you can manage the roles of the users in the system. <br>
                    Keep in mind, the updated or deleted role that is being assigned to the user will be affected.
                </p>
            </header>
            <div class="row g-4 mt-3">
                <div class="col-md-6">
                    <div class="p-4 rounded"
                        style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Add New Role</h3>
                        <form @submit.prevent="addRole">
                            <div class="mb-3">
                                <label for="roleName" class="form-label">Role Name</label>
                                <input type="text" class="form-control" id="roleName" v-model="form.name">
                            </div>
                            <button type="submit" class="btn btn-grad-outline">Add Role</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 rounded"
                        style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <div class="d-flex justify-content-between">
                            <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Roles</h3>
                        </div>
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(role, index) in roles" :key="role.id">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ role.name }}</td>
                                    <td class="flex justify-content-center">
                                        <button class="btn btn-sm btn-grad-outline text-center me-2"
                                            @click="openEditModal(role)"
                                            :disabled="role.name === 'Project Manager'"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-delete btn-sm btn-grad-outline text-center me-2"
                                            @click="() => deleteRole(role)"
                                            :disabled="role.name === 'Project Manager'"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <!-- <button class="btn btn-sm btn-grad-outline text-center me-2"
                                            @click="() => viewRole(role)">
                                            <i class="bi bi-eye"></i>
                                        </button> -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Role Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" :inert="!modalVisible"
            :aria-hidden="!modalVisible">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editModalLabel">Edit Role</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitEdit">
                            <div class="mb-3">
                                <InputLabel for="editTitle" value="Role Name" />
                                <TextInput id="editTitle" type="text" class="mt-1 block w-full" v-model="roleform.name"
                                    required autofocus autocomplete="name" />
                                <InputError class="mt-2" :message="roleform.errors.name" />
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


        View Role Modal
        <div v-if="showViewModal.value" class="modal">
            <div class="modal-content">
                <span class="close" @click="showViewModal.value = false">&times;</span>
                <h3>Users with Role: {{ roleIdToUpdate.value }}</h3>
                <ul>
                    <li v-for="user in roleUsers" :key="user.id">
                        {{ user.name }} - Project: {{ user.project.title }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Delete Role Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
            :inert="!showDeleteModal" :aria-hidden="!showDeleteModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteModalLabel">Delete Role</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this role? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" @click="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.role-management {
    padding: 20px;
}

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


.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.btn.create-btn {
    background-color: #6C5B7B;
    color: #fff;
}

.btn.create-btn:hover {
    background-color: #C2B9CB;
    color: #fff;
}

.btn-danger {
    background-color: #dc3545;
    color: #fff;
}

.btn-danger:hover {
    background-color: #c82333;
    color: #fff;
}

.btn-delete {
    color: rgb(175, 100, 100);
    border:1px solid rgb(175, 100, 100)
}
.btn-delete:hover {
    color: rgb(255, 255, 255);
    background-color: rgb(175, 100, 100);
}

</style>
