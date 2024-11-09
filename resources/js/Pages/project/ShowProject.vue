<template>
    <Head title="Project" />

    <AuthenticatedLayout> 
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project</h2>
        </template>

        <!-- Main Container -->
        <div class="cmax-w-mx-auto sm:px-6 lg:px-8 ">
            <!-- Sidebar -->
            <SideBar class="sidebar-container me-4" />

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
                        <div v-if="hasProjects" class="d-flex flex-wrap justify-content-start">
                            <div v-for="project in projects" :key="project.id" class="col-md-3 mb-3">
                                <div class="card  project-card me-5" @click="goToKanban(project.id, `/img/${project.image}`)" style="width: 300px;">
                                    <img :src="`/img/${project.image}`" class="card-img-top project-image" alt="Project Image" style="width: 400px; height:200px;">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ project.title }}</h5>
                                        <p class="card-text">{{ project.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center mt-4">
                            <p class="text-muted">No project created yet</p>
                        </div>
                    </div>

                    <!-- Add Project Button -->
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-primary add-workspace-btn" data-bs-toggle="modal" data-bs-target="#projectModal">
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
                                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus autocomplete="title" />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>
                            <!-- Project Description Input -->
                            <div class="mb-3">
                                <InputLabel for="description" value="Description" />
                                <TextInput id="description" type="text" class="mt-1 block w-full" v-model="form.description" required autocomplete="description" />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Template Image Selection -->
                            <div class="mb-3">
                                <InputLabel value="Choose Template" />
                                <div class="template-options d-flex flex-wrap gap-2">
                                    <div v-for="(template, index) in templates" :key="index" class="template-option">
                                        <input type="radio" :id="'template' + index" :value="template" v-model="form.image" class="d-none" />
                                        <label :for="'template' + index" class="template-label" :class="{ 'selected': form.image === template }">
                                            <img :src="template" alt="Template Image" class="template-image" />
                                        </label>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.image" />
                            </div>

                            <!-- Modal Footer -->
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

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideBar from '@/Components/SideBar.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { defineProps } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    projects: Array,
    hasProjects: Boolean,
    workspace: Object,
});

const modalVisible = ref(false);
const form = useForm({
    title: '',
    description: '',
    image: '',
    workspace_id: props.workspace.id,
});

const templates = [
    '/img/template-1.png',
    '/img/template-2.png',
    '/img/template-3.png',
    '/img/template-4.png',
];

function goToKanban(projectId, imagePath) {
    router.visit(`/kanban`, {
        data: { project: projectId, background: imagePath },
    });
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
    color: #002244;
    border: 0.5px solid #002244;
}

.add-workspace-btn:hover {
    background-color: #002244;
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
</style>
