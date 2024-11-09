<template>
    <Head title="Kanban Board" />

    <AuthenticatedLayout>
        <template #header>
            <!-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kanban Board</h2> -->
            <NavKanban />
        </template>

        <!-- Kanban Board Layout with Sidebar and Board Container -->
        <div class="kanban-layout">
            <SideBar />

            <!-- Kanban Board Container with Fixed Background -->
            <div :style="{ backgroundImage: `url(${background})` }" class="kanban-board">
                <div class="container d-flex justify-content-center">
                    <!-- Filter options -->
                    <div v-if="project">
                        <div class="kanban-columns">
                            <div v-for="column in columns" :key="column.id" class="kanban-column">
                                <div class="card">
                                    <div class="card-title">
                                        <h3 class="text-center" style="font-weight: bold;">{{ column.name }}</h3>
                                    </div>

                                  

                                    <div class="card-list">
                                        <div v-for="card in column.cards" :key="card.id">
                                            <Card :style="{ marginTop: '10px' }" 
                                                :title="card.title"
                                                :description="card.description" />
                                        </div>
                                    </div>
                                     <!-- Add Card Button with @click to set column ID -->
                                    <button type="button" class="btn bi bi-plus-circle-fill add-workspace-btn"
                                        @click="openModal(column)" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal">
                                        Add Project
                                    </button> 
                                    <!-- Additional card content here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="d-flex p-3 mt-2 rounded" style="border: 1px solid grey;">
                            <div class="card-body">
                                <p class="text-muted">No project selected</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dialog for popup form -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Add Task</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="submit">
                                <div class="mb-3">
                                    <InputLabel for="title" value="Title" />
                                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title"
                                        required autofocus autocomplete="title" />
                                    <InputError class="mt-2" :message="form.errors.title" />
                                </div>
                                <div class="mb-3">
                                    <InputLabel for="description" value="Description" />
                                    <TextInput id="description" type="text" class="mt-1 block w-full"
                                        v-model="form.description" required autofocus autocomplete="description" />
                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Create</button>
                                </div>
                            </form>
                        </div>
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
import SideBar from '@/Components/SideBar.vue';
import Card from '@/Components/Kanban/Card.vue';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import NavKanban from '@/Components/Kanban/NavKanban.vue';

const props = defineProps({
    background: {
        type: String,
        required: true,
    },
    project: {
        type: String,
        required: true,
    },
    columns: {
        type: Array,
        default: () => [],
    },
    cards: { // Assume cards are passed as a prop
        type: Array,
        default: () => [],
    },
});

// Define references
const selectedColumnId = ref(null);
const statusId = ref(null);
const cardName = ref(null);

const form = useForm({
    title: '',
    description: '',
    column_id: selectedColumnId.value,
    status_id: statusId.value
});

// Method to open modal with column ID
function openModal(column) {
    selectedColumnId.value = column.id;
    statusId.value = column.status_id;
}

// Handle form submission
function submit() {
    form.column_id = selectedColumnId.value;
    form.status_id = statusId.value;

    form.post(route('card.create'), {
        onSuccess: () => {
            form.reset();
            location.reload();
        },
        onError: () => {
            console.error('Submission failed:', form.errors);
        },
    });

    form.reset(); // Reset form fields after submission
}
</script>

<style scoped>
.kanban-layout {
    display: flex;
    height: 85vh;
    overflow: hidden;
}

.kanban-board {
    flex-grow: 1; /* Allow board to take remaining space */
    background-size: cover;
    background-position: center;
    overflow-x: auto;
    padding: 1rem;
}

.kanban-columns {
    display: flex;
    overflow-x: auto;
    gap: 1rem; /* Add gap between columns */
}

.kanban-column {
    flex: 1; /* Allow columns to grow equally */
    min-width: 250px; /* Minimum width to maintain card layout */
    display: flex;
    flex-direction: column;
}

.card {
    padding: 1rem;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-title h3 {
    margin: 0;
}

.card-list {
    flex-grow: 1; /* Allow cards to take available space */
    display: flex;
    flex-direction: column;
}

button {
    margin-top: 10px;
    padding: 0.5rem 1rem;
    background-color: #ffffff;
    color: rgb(0, 0, 0);
    border: 1px solid black;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #000000;
    color: #ffffff;
}
</style>
