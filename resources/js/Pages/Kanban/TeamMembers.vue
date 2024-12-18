<template>

    <Head title="Kanban Board" />

    <AuthenticatedLayout>
        <template #header>
            <NavKanban :projectId="project" />
        </template>

        <div class="flex justify-content-center">
            <div class="container mt-3">
                <!-- Add Member Button -->
                <button @click="openAddMemberModal" class="btn btn-member mb-3">Add Member</button>
                <div class="table-responsive rounded-4">
                    <table class="table table-striped">
                        <thead class="bg-gray-50">
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th class="text-center">Actions</th>
                                <th>Availability</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="member in members" :key="member.id">
                                <td>{{ member.users.name }}</td>
                                <td>{{ member.roles.name }}</td>
                                <td class="text-center">
                                    <!-- View Button with Bootstrap Icon -->
                                    <button @click="viewMember(member)" class="text-blue-600 hover:text-blue-900 p-3">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <!-- Edit Button with Bootstrap Icon -->
                                    <button @click="editMember(member)"
                                        class="text-yellow-600 hover:text-yellow-900 p-3">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <!-- Delete Button with Bootstrap Icon -->
                                    <button @click="deleteMember(member)" class="text-red-600 hover:text-red-900 p-3">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                                <td>
                                    <!-- Availability Dropdown with Conditional Styling -->
                                    <select v-model="member.isAvailable" @change="toggleAvailability(member)" :class="{
                                        'bg-green-100 text-green-800': member.isAvailable,
                                        'bg-red-100 text-red-800': !member.isAvailable
                                    }" class="rounded-4">
                                        <option :value="true">Available</option>
                                        <option :value="false">Unavailable</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.memberEmail"
                                    required />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>
                            <!-- Role Selection -->
                            <div class="mb-3">
                                <InputLabel for="role" value="Role" />
                                <select v-model="form.selectedRole" class="form-select mt-1 block w-full" required>
                                    <option disabled value="">Select Role</option>
                                    <option v-for="role in roles" :value="role.id" :key="role.id">{{ role.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>
                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-member">Add Member</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavKanban from '@/Components/Kanban/NavKanban.vue';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    background: {
        type: String,
        required: true,
    },
    project: {
        type: String,
        required: true,
    },
    members: {
        type: Array,
        required: true,
    },
});

const viewMember = (member) => {
    console.log('View member:', member);
};

const editMember = (member) => {
    console.log('Edit member:', member);
};

const deleteMember = (member) => {
    console.log('Delete member:', member);
};

const toggleAvailability = (member) => {
    console.log('Availability toggled for:', member);
};

const addMember = () => {
    console.log('Add member to project:', props.project);
    // Logic to add a new member
};

const roles = ref([]); // Fetch roles from the server or define them here

const form = useForm({
    memberEmail: '',
    selectedRole: '',
    errors: {}
});

const openAddMemberModal = () => {
    const addMemberModal = new bootstrap.Modal(document.getElementById('addMemberModal'));
    addMemberModal.show();
};

const confirmAddMember = () => {
    console.log('Add member:', form.memberEmail, 'with role:', form.selectedRole);
    // Logic to add the member to the project
    // Reset form fields
    form.memberEmail = '';
    form.selectedRole = '';
    const addMemberModal = bootstrap.Modal.getInstance(document.getElementById('addMemberModal'));
    addMemberModal.hide();
};
</script>

<style scoped>
.btn-member {
    background-color: white;
    color: #002244;
    border: 0.5px solid #002244;
}

.btn-member:hover {
    background-color: #002244;
    color: white;
}
</style>
