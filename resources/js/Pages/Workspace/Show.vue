<script setup>
import { ref, watch, computed } from 'vue';
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
    email: '',
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

const deleteMemberModalVisible = ref(false);
const memberToDelete = ref(null);

const openDeleteMemberModal = (member) => {
    memberToDelete.value = member.user_id;
    deleteMemberModalVisible.value = true;
};

const confirmDeleteMember = () => {
    if (memberToDelete.value) {
        router.delete(route('workspace.deleteMember', { workspaceId: props.workspace.id, memberId: memberToDelete.value}), {
            onSuccess: () => {
                deleteMemberModalVisible.value = false;
                location.reload();
            },
            onError: (errors) => {
                console.error('Deletion failed:', errors);
            },
        });
    }
};

const addMemberModalVisible = ref(false);
const searchResult = ref(null);

const openAddMemberModal = () => {
    addMemberModalVisible.value = true;
};

const searchUser = async () => {
    if (form.email) {
        try {
            const response = await axios.post('/search-user', { email: form.email });
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

const addMember = () => {
    if (searchResult.value && searchResult.value !== 'not_found') {
        form.members.push({
            email: searchResult.value.email,
            name: searchResult.value.name,
        });
        form.email = '';
        searchResult.value = null;
    }
};

const submitAddMember = () => {
    form.post(route('workspace.addMember', { id: props.workspace.id }), {
        onSuccess: () => {
            form.reset();
            addMemberModalVisible.value = false;
            location.reload();
        },
        onError: () => {
            console.error('Submission failed:', form.errors);
        },
    });
};

// Watch modal visibility to set inert attribute correctly
watch(modalVisible, (newValue) => {
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.inert = !newValue;
    }
});
watch(deleteModalVisible, (newValue) => {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.inert = !newValue;
    }
});
watch(deleteMemberModalVisible, (newValue) => {
    const deleteMemberModal = document.getElementById('deleteMemberModal');
    if (deleteMemberModal) {
        deleteMemberModal.inert = !newValue;
    }
});
watch(addMemberModalVisible, (newValue) => {
    const addMemberModal = document.getElementById('addMemberModal');
    if (addMemberModal) {
        addMemberModal.inert = !newValue;
    }
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
                    <button @click="openAddMemberModal" class="btn btn-grad-outline mb-3" data-bs-toggle="modal"
                        data-bs-target="#addMemberModal"><i class="bi bi-person-plus-fill"></i>Member</button>
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
                                <td>
                                    <div v-if="member.users.availability === 'available'" style=" color: beige;
                                            background-color: green;
                                            border-radius: 20px;
                                            text-transform: capitalize;
                                            text-align: center;
                                            padding:3px;">
                                        {{ member.users.availability }}
                                    </div>
                                    <div v-else="member.users.availability === 'unavailable'" style="color: rgb(255, 255, 255);
                                            background-color: red;
                                            border-radius: 20px;
                                            text-transform: capitalize;
                                            text-align: center;">
                                        {{ member.users.availability }}
                                    </div>
                                </td>

                                <td class="flex justify-content-center">

                                    <button @click="openDeleteMemberModal(member)"
                                        class="btn btn-delete btn-sm btn-grad-outline text-center"
                                        data-bs-toggle="modal" data-bs-target="#deleteMemberModal">
                                        <i class="bi bi-trash"></i>
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
                            <textarea id="editDescription" type="text" class="mt-1 block w-full"
                                v-model="form.description" required autofocus autocomplete="description"
                                rows="4"></textarea>
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

    <!-- Delete Member Modal -->
    <div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-labelledby="deleteMemberModalLabel"
        :inert="!deleteMemberModalVisible" :aria-hidden="!deleteMemberModalVisible" v-if="deleteMemberModalVisible">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteMemberModalLabel">Delete Member</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this member?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" @click="confirmDeleteMember">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel"
        :inert="!addMemberModalVisible" :aria-hidden="!addMemberModalVisible" v-if="addMemberModalVisible">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addMemberModalLabel">Add Workspace Member</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitAddMember">
                        <div class="mb-3">
                            <InputLabel for="memberEmail" value="Member Email" />
                            <TextInput id="memberEmail" type="email" class="mt-1 block w-full" v-model="form.email"
                                @input="searchUser" required autofocus autocomplete="email" />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                        <div v-if="searchResult === 'not_found'" class="text-danger">User not found.</div>
                        <div v-else-if="searchResult" class="text-success">User found: {{ searchResult.name }}</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn create-btn">Add Member</button>
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
