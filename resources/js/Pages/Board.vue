<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import TaskCard from '@/Components/TaskCard.vue';
import { ref } from 'vue';


const props = defineProps({
    workspaces: {
        type: Array,
        default: () => [],
    },
});

const modalVisible = ref(false);

const memberEmail = ref('');
const searchResult = ref(null);

const form = useForm({
    title: '',
    description: '',
    members: [],
    errors: {},
});

async function searchUser() {
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
}

function addMember() {
    if (searchResult.value && searchResult.value !== 'not_found') {
        form.members.push({
            email: searchResult.value.email,
            name: searchResult.value.name,
        });
        memberEmail.value = '';
        searchResult.value = null;
    }
}

function removeMember(index) {
    form.members.splice(index, 1);
}

function submit() {
    form.post(route('workspace.create'), {
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

<template>

    <Head title="Board" />

    <AuthenticatedLayout :notifications="notifications">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Workspace</h2>
        </template>

        <div>
            <h3 style="text-align: center; font-weight: bold;background: linear-gradient(to right,  #6C5B7B, #C2B9CB); color:#fff; max-width:300px; padding: 5px; border-radius: 20px;"
                class="font-semibold text-xl text-black-500 leading-tight ms-5 mb-4">Workspace List</h3>
            <div class="max-w-mx-auto sm:px-6 lg:px-8 ">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    style="border-radius: 10px; border: 1px solid #6C5B7B;">
                    <div>
                        <div class="d-flex flex-wrap container" v-if="workspaces && workspaces.length > 0">
                            <div class="cols-7 md-8" v-for="workspace in workspaces" :key="workspace.id">
                                <TaskCard :style="{ marginTop: '30px' }" :key="workspace.id" :title="workspace.title"
                                    :description="workspace.description" :link="`/show/workspace/${workspace.id}`"
                                    :workspace="workspace" />
                            </div>
                        </div>
                        <div v-else class="text-center w-100 mt-5">
                            <p class="text-muted">No workspace created yet</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3 me-3 mb-3"><button type="button"
                            class="btn bi bi-plus-circle-fill add-workspace-btn" data-bs-toggle="modal"
                            data-bs-target="#exampleModal"></button></div>
                </div>
            </div>
        </div>

        <!-- Dialog for popup form -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
            modalVisible.value=true;>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Create New Workspace</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <InputLabel for="title" value="Title" />
                                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title"
                                    required autofocus autocomplete="title" />
                                <InputError class="mt-2" :message="form.errors.title" />

                                <!-- <input type="text" name="title" v-model="form.title" class="col-form-label"> -->
                            </div>
                            <div class="mb-3">
                                <InputLabel for="descripiton" value="Description" />
                                <TextInput id="description" type="text" class="mt-1 block w-full"
                                    v-model="form.description" required autofocus autocomplete="description" />
                                <InputError class="mt-2" :message="form.errors.description" />

                                <!-- <input type="text" name="description" v-model="form.description" class="col-form-label"> -->
                            </div>
                            <div class="mb-3">
                                <label>Add Workspace Members</label>
                                <div class="d-flex gap-2 align-items-center mb-3">
                                    <input type="email" class="form-control rounded" placeholder="Enter member's email"
                                        v-model="memberEmail" @input="searchUser" autocomplete="off" />
                                    <button class="btn add-workspace-btn" type="button" @click="addMember">Add</button>
                                </div>
                                <InputError class="mt-2" :message="form.errors.description" />

                                <!-- <input type="text" name="description" v-model="form.description" class="col-form-label"> -->
                            </div>
                            <div class="mb-3">

                                <div v-if="form.members.length > 0"
                                    class="added-members-tooltip mt-2 p-2 bg-light border rounded">
                                    <p class="text-muted mb-1">Members added:</p>
                                    <ul class="list-unstyled mb-0">
                                        <li v-for="(member, index) in form.members" :key="member.email"
                                            class="text-secondary">
                                            {{ member.email }} ({{ member.role }})
                                            <button type="button"
                                                class="btn btn-sm p-0 text-danger ms-2 justify-content-end"
                                                @click="removeMember(index)">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div v-if="searchResult === 'not_found'" class="text-danger">User not found.</div>
                                <div v-else-if="searchResult" class="text-success">User found: {{ searchResult.name }}
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn create-btn">Create</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background-color: black; color: white;">
                    <div class="modal-header">
                        <h5 class="modal-title rounded" id="roleModalLabel">Assign Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Select a role for <strong>{{ memberEmail }}</strong></p>
                        <select v-model="selectedRole" class="form-select mt-3 role-select-dropdown rounded">
                            <option disabled value="">Select Role</option>
                            <option v-for="role in props.roles" :value="role.id" :key="role.id">{{ role.name }}</option>
                        </select>
                    </div>
                    <div class="modal-footer role-modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-outline-secondary" @click="confirmAddMember"
                            data-bs-dismiss="modal">Add Member</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.add-workspace-btn {
    background-color: white;
    color: #002244;
    border: 0.5px solid #002244;
}

.add-workspace-btn:hover {
    background-color: #002244;
    color: white;
}

.create-btn {
    border: solid 1px #492A65;
    color: #492A65;
}

.create-btn:hover {
    background-color: #5E3681;
    color: #fff;
}
</style>
