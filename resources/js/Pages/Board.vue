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

const form = useForm({
    title: '',
    description: ''
});

const submit = () => {
    form.post(route('workspace.create'), {
        onSuccess: () => {
            // Clear the form
            form.reset();
            // Close the modal
            modalVisible.value = false;
            // Refresh the page
            location.reload(); // This will reload the current page
            modalVisible.value = false;
        },
        onError: () => {
            // Handle any errors
            console.error('Submission failed:', form.errors);
        }
    });
};



</script>

<template>

    <Head title="Board" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Workspace</h2>
        </template>

        <div class="py-12">
            <div class="max-w-mx-auto sm:px-6 lg:px-8 ">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="d-flex flex-wrap container">
                        <div class="cols-7 md-8" v-for="workspace in workspaces" :key="workspace.id">
                            <TaskCard :style="{
                                marginTop: '30px',
                            }" :id="workspace.id" :title="workspace.title" :description="workspace.description" />

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
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>

                        </form>
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
</style>
