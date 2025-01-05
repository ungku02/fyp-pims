<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    users: { type: Object, default: () => ({ data: [], prev_page_url: null, next_page_url: null }) },
    notifications: { type: Array, default: () => [] },
    workspaces: { type: Array, default: () => [] },
    role: { type: Object, default: () => [] },
});

const users = ref(props.users);
const showDeleteModal = ref(false);
const userIdToDelete = ref(null);

const deleteUser = (user) => {
    userIdToDelete.value = user.id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    Inertia.delete(`/users/${userIdToDelete.value}`, {
        onSuccess: () => {
            users.value.data = users.value.data.filter(u => u.id !== userIdToDelete.value);
            showDeleteModal.value = false;
        },
        onError: (errors) => {
            console.error('Error deleting user:', errors);
        }
    });
};

const toggleAvailability = (user) => {
    const newAvailability = user.availability === 'available' ? 'unavailable' : 'available';
    Inertia.put(`/users/${user.id}/availability`, { availability: newAvailability }, {
        onSuccess: () => {
            user.availability = newAvailability;
        },
        onError: (errors) => {
            console.error('Error updating availability:', errors);
        }
    });
};

const filter = ref({
    name: '',
    email: '',
    availability: ''
});

const applyFilter = () => {
    Inertia.get('/users/filter', filter.value, {
        onSuccess: (page) => {
            users.value = page.props.users;
        },
        onError: (errors) => {
            console.error('Error filtering users:', errors);
        }
    });
};

const resetFilter = () => {
    filter.value = {
        name: '',
        email: '',
        availability: ''
    };
    Inertia.get('/users', {}, {
        onSuccess: (page) => {
            users.value = page.props.users;
        },
        onError: (errors) => {
            console.error('Error resetting filter:', errors);
        }
    });
};

onMounted(() => {
    users.value = props.users;
});
</script>

<template>
    <AuthenticatedLayout :notifications="notifications">
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="p-4 rounded"
                        style="background-color: #ffffff; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <h3 class="mb-3" style="color: #2A4965; font-weight: bold;">Users</h3>
                        <div class="filter-form">
                            <input v-model="filter.name" type="text" placeholder="Search by name" />
                            <input v-model="filter.email" type="text" placeholder="Search by email" />
                            <select v-model="filter.availability">
                                <option value="">All</option>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                            <button @click="applyFilter">Filter</button>
                            <button @click="resetFilter">Reset</button>
                        </div>
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
                                <tr v-for="(user, index) in users.data" :key="user.id">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td class="text-center">
                                        <button @click="toggleAvailability(user)"
                                            :class="{ 'btn-availability': true, 'active': user.availability === 'available', 'inactive': user.availability === 'unavailable' }">
                                            {{ user.availability }}
                                        </button>
                                    </td>
                                    <td class="flex justify-content-center">
                                        <button class="btn btn-delete btn-sm btn-grad-outline text-center me-2"
                                            @click="() => deleteUser(user)" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="pagination">
                            <button @click="users.prev_page_url && $inertia.get(users.prev_page_url)"
                                :disabled="!users.prev_page_url">Previous</button>
                            <button @click="users.next_page_url && $inertia.get(users.next_page_url)"
                                :disabled="!users.next_page_url">Next</button>
                        </div>
                    </div>
                </div>
            </div>


        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
            :inert="!showDeleteModal" :aria-hidden="!showDeleteModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteModalLabel">Delete User</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this user? This action cannot be undone.</p>
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
.user-management {
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
    border: 1px solid rgb(175, 100, 100);
}

.btn-delete:hover {
    color: rgb(255, 255, 255);
    background-color: rgb(175, 100, 100);
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

.filter-form {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.filter-form input,
.filter-form select {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.filter-form button {
    padding: 5px 10px;
    background-color: #6C5B7B;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.filter-form button:hover {
    background-color: #5a4a6b;
}
</style>
