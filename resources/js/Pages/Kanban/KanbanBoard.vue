<script setup>
import { defineProps, ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Kanban/Card.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import ProjectLayout from '@/Layouts/ProjectLayout.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    background: {
        type: String,
        required: true,
    },
    project: {
        type: Object,
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
    status: {
        type: Array,
        default: () => []
    },
    members: {
        type: Array,
        required: true,
    },
    projectTitle: {
        type: String,
        required: true,
    },
    notifications: {
        type: Array,
        default: () => []
    },

});

// Define references
const selectedColumnId = ref(null);
const statusId = ref(null);
const selectedMember = ref(null);
const selectedCard = ref({
    title: '',
    status: '',
    description: '',
    due_date: '',
    urgency: '',
    user_role: {
        users: {
            name: ''
        },
        roles: {
            name: ''
        }
    },
    attachment: '[]'
});

const today = new Date().toISOString().split('T')[0];

const columnForm = useForm({
    project_id: props.project.id,
    status_id: '',
    name: '',
});

const editColumnForm = useForm({
    id: null,
    name: '',
    status: '', // Add status field
});

function openEditColumnModal(column) {
    editColumnForm.id = column.id;
    editColumnForm.name = column.name;
    editColumnForm.status = column.status ? column.status.name : ''; // Ensure status name is accessed correctly
    console.log('Edit Column:', column.status_id ? column.status.name : 'No status');
}

function submitEditColumn() {
    editColumnForm.put(route('column.update', editColumnForm.id), {
        onSuccess: () => {
            editColumnForm.reset();
            location.reload(); // Reload to see the updated column
        },
        onError: () => {
            console.error('Column update failed:', editColumnForm.errors);
        },
    });
}

function deleteColumn(columnId) {
    if (confirm('Are you sure you want to delete this column?')) {
        axios.delete(route('column.delete', columnId))
            .then(() => {
                location.reload(); // Reload to see the changes
            })
            .catch(error => {
                console.error('Column deletion failed:', error);
            });
    }
}

// Open the add column modal
function openColumnModal() {
    columnForm.reset();
}

// Submit column form
function submitColumn() {
    columnForm.post(route('column.create'), {
        onSuccess: () => {
            columnForm.reset();
            location.reload(); // Reload to see the new column
        },
        onError: () => {
            console.error('Column submission failed:', columnForm.errors);
        },
    });
}

const form = useForm({
    title: '',
    description: '',
    column_id: selectedColumnId.value,
    status_id: statusId.value,
    urgency: 'normal',
    due_date: '',
    user_project_id: null, // Ensure this is correctly set
    attachment: [], // Ensure this is an array to handle multiple attachments
});

// Method to open modal with column ID
function openModal(column) {
    selectedColumnId.value = column.id;
    statusId.value = column.status_id;
}

// Handle file upload
function handleFileUpload(event) {
    form.attachment = Array.from(event.target.files);
}

function submit() {
    form.column_id = selectedColumnId.value;
    form.status_id = statusId.value;

    const data = {
        title: form.title,
        description: form.description,
        column_id: form.column_id,
        status_id: form.status_id,
        urgency: form.urgency,
        due_date: form.due_date,
        user_project_id: form.user_project_id, // Ensure this is correctly set
        attachment: form.attachment, // Include attachments in the data
    };

    form.post(route('card.create'), {
        data,
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

function deleteCard(cardId) {
    if (confirm('Are you sure you want to delete this card?')) {
        axios.delete(route('card.delete', cardId))
            .then(() => {
                location.reload(); // Reload to see the changes
            })
            .catch(error => {
                console.error('Card deletion failed:', error);
            });
    }
}


function isImage(url) {
    return /\.(jpg|jpeg|png|gif)$/i.test(url);
}

function getFileName(url) {
    return url.split('/').pop();
}

const draggingCard = ref(null);
const hoveredColumn = ref(null);

function onDragStart(card, event) {
    draggingCard.value = card;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', card.id);
    event.target.classList.add('dragging');
}

function onDragEnd(event) {
    event.target.classList.remove('dragging');
    draggingCard.value = null;
    hoveredColumn.value = null;
}

function onDragOver(column, event) {
    event.preventDefault();
    hoveredColumn.value = column.id;
}

function onDrop(column, event) {
    event.preventDefault();
    const cardId = event.dataTransfer.getData('text/plain');
    if (draggingCard.value && draggingCard.value.id == cardId) {
        const updatedCard = {
            ...draggingCard.value,
            column_id: column.id,
            status_id: column.status_id,
        };

        axios.put(route('card.update', draggingCard.value.id), updatedCard)
            .then(() => {
                location.reload(); // Reload to see the changes
            })
            .catch(error => {
                console.error('Card update failed:', error);
            });

        draggingCard.value = null;
        hoveredColumn.value = null;
    }
}

function openCardDetailsModal(card) {
    selectedCard.value = card;
    console.log('Selected Card:', card);
}

const filters = ref({
    assignedUser: '',
    owner: '',
    title: '',
    urgency: '',
    status_id: '',
});

const filteredColumns = computed(() => {
    return props.columns.map(column => {
        return {
            ...column,
            cards: column.cards.filter(card => {
                return (
                    (!filters.value.assignedUser || card.user_role?.users?.name.includes(filters.value.assignedUser)) &&
                    (!filters.value.owner || card.user_role?.users?.name.includes(filters.value.owner)) &&
                    (!filters.value.title || card.title.includes(filters.value.title)) &&
                    (!filters.value.urgency || card.urgency === filters.value.urgency) &&
                    (!filters.value.status_id || card.status_id === filters.value.status_id)
                );
            })
        };
    });
});

function resetFilters() {
    filters.value = {
        assignedUser: '',
        owner: '',
        title: '',
        urgency: '',
        status_id: '',
    };
}
</script>

<template>

    <Head title="Kanban Board" />

    <ProjectLayout :project="project" :notifications="notifications">


        <!-- Kanban Board Layout with Sidebar and Board Container -->

        <!-- Kanban Board Container with Fixed Background -->
        <div class="kanban-board d-flex flex-column"> <!-- Add Column Button -->


            <!-- Filter Modal -->
            <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="filterModalLabel">Filter Tasks</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="filter-form">
                                <input v-model="filters.assignedUser" type="text" placeholder="Filter by assigned user"
                                    class="form-control mb-2" />
                                <input v-model="filters.owner" type="text" placeholder="Filter by owner"
                                    class="form-control mb-2" />
                                <input v-model="filters.title" type="text" placeholder="Filter by title"
                                    class="form-control mb-2" />
                                <select v-model="filters.urgency" class="form-select mb-2">
                                    <option value="">All Urgencies</option>
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="critical">Critical</option>
                                </select>
                                <select v-model="filters.status_id" class="form-select mb-2">
                                    <option value="">All Statuses</option>
                                    <option v-for="statusOption in status" :key="statusOption.id"
                                        :value="statusOption.id">
                                        {{ statusOption.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Apply Filters</button>
                            <button type="button" class="btn btn-secondary" @click="resetFilters">Reset Filters</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class=" d-flex justify-content-end bg-transparent">
                <button type="button" class="btn btn-primary mb-2 me-2" @click="openColumnModal" data-bs-toggle="modal"
                    data-bs-target="#addColumnModal">
                    Add Column
                </button>
                <button type="button" class="btn btn-secondary mb-2" data-bs-toggle="modal"
                    data-bs-target="#filterModal">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
            <!-- Add Column Modal -->
            <div class="modal fade" id="addColumnModal" tabindex="-1" aria-labelledby="addColumnModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="addColumnModalLabel">Add New Column</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="submitColumn">
                                <!-- Status ID Field -->
                                <div class="mb-3">
                                    <InputLabel for="status_id" value="Status" />
                                    <select id="status_id" v-model="columnForm.status_id"
                                        class="form-select mt-1 block w-full rounded">
                                        <option v-for="statusOption in status" :key="statusOption.id"
                                            :value="statusOption.id">
                                            {{ statusOption.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="columnForm.errors.status_id" />
                                </div>

                                <!-- Column Name Field -->
                                <div class="mb-3">
                                    <InputLabel for="name" value="Column Name" />
                                    <TextInput id="name" type="text" class="mt-1  w-full" v-model="columnForm.name" />
                                    <InputError class="mt-2" :message="columnForm.errors.name" />
                                </div>

                                <!-- Modal Footer Buttons -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Create Column</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex mx-5">

                <div v-if="project">
                    <div class="kanban-columns">
                        <div v-for="column in filteredColumns" :key="column.id" class="kanban-column"
                            @drop.prevent="onDrop(column, $event)" @dragover.prevent="onDragOver(column, $event)"
                            :class="{ 'hovered': hoveredColumn === column.id }">
                            <div class="card">
                                <div class="card-title flex justify-between">
                                    <h3 class="text-center" style="font-weight: bold;">{{ column.name }}</h3>
                                    <button style="margin:0px; border: none; background-color: transparent;"
                                        @click="openEditColumnModal(column)" data-bs-toggle="modal"
                                        data-bs-target="#editColumnModal"><i
                                            class="bi bi-pencil-square me-1"></i></button>
                                </div>

                                <div class="card-list">
                                    <div v-for="card in column.cards" :key="card.id" draggable="true"
                                        @dragstart="onDragStart(card, $event)" @dragend="onDragEnd"
                                        @click="openCardDetailsModal(card)" data-bs-toggle="modal"
                                        data-bs-target="#cardDetailsModal">
                                        <Card :style="{ marginTop: '10px' }" :title="card.title || 'No Title'"
                                            :description="card.description" :dueDate="card.due_date"
                                            :urgency="card.urgency " :assignedUser="card.user_role?.users?.name"
                                            :assignedRole="card.user_role?.roles?.name" :attachment="card.attachment"
                                            @delete-card="deleteCard(card.id)" />

                                        <!-- <pre>{{ card }}</pre> Add this line to debug -->
                                    </div>
                                </div>
                                <button type="button" class="btn bi bi-plus-circle-fill add-workspace-btn"
                                    @click="openModal(column)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Add Card
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
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Card</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submit">
                            <!-- Title Field -->
                            <div class="mb-3">
                                <InputLabel for="title" value="Title" />
                                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title"
                                    autofocus autocomplete="title" />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <!-- Description Field -->
                            <div class="mb-3">
                                <InputLabel for="description" value="Description" />
                                <TextInput id="description" type="text" class="mt-1 block w-full"
                                    v-model="form.description" autocomplete="description" />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Urgency Level Field with Color Flags -->
                            <div class="mb-3">
                                <InputLabel for="urgency" value="Urgency" />
                                <select id="urgency" v-model="form.urgency" class="form-select rounded block">
                                    <option value="normal" :class="{ 'flag-green': form.urgency === 'normal' }">
                                        Normal
                                    </option>
                                    <option value="urgent" :class="{ 'flag-yellow': form.urgency === 'urgent' }">
                                        Urgent
                                    </option>
                                    <option value="critical" :class="{ 'flag-red': form.urgency === 'critical' }">
                                        Critical
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.urgency" />
                            </div>

                            <!-- Due Date Field -->
                            <div class="mb-3">
                                <InputLabel for="due_date" value="Due Date" />
                                <input id="due_date" type="date" class="form-control mt-1 rounded"
                                    v-model="form.due_date" :min="today" />
                                <InputError class="mt-2" :message="form.errors.due_date" />
                            </div>

                            <!-- Assign to Member Field -->
                            <div class="mb-3">
                                <InputLabel for="user_project_id" value="Assign to Member" />
                                <select id="user_project_id" v-model="form.user_project_id"
                                    class="form-select rounded block">
                                    <option disabled value="">Select Member</option>
                                    <option v-for="member in members" :key="member.id" :value="member.users.id">
                                        {{ member.users.name }} - {{ member.roles.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.user_project_id" />
                            </div>

                            <!-- File Attachment Field -->
                            <div class="mb-3">
                                <InputLabel for="attachment" value="File Attachments" />
                                <input id="attachment" type="file" class="form-control p-2 rounded" multiple
                                    style="border: 1px solid black;" @change="handleFileUpload" />
                                <InputError class="mt-2" :message="form.errors.attachment" />
                            </div>

                            <!-- Modal Footer Buttons -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Column Modal -->
        <div class="modal fade" id="editColumnModal" tabindex="-1" aria-labelledby="editColumnModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editColumnModalLabel">Edit Column</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitEditColumn">
                            <!-- Column Name Field -->
                            <div class="mb-3">
                                <InputLabel for="edit-name" value="Column Name" />
                                <TextInput id="edit-name" type="text" class="mt-1 block w-full"
                                    v-model="editColumnForm.name" />
                                <InputError class="mt-2" :message="editColumnForm.errors.name" />
                            </div>
                            <div class="mb-3">
                                <InputLabel for="edit-status" value="Column Status" />
                                <TextInput id="edit-status" type="text" class="mt-1 block w-full"
                                    v-model="editColumnForm.status" />
                                <InputError class="mt-2" :message="editColumnForm.errors.status" />
                            </div>

                            <!-- Modal Footer Buttons -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update Column</button>
                                <button type="button" class="btn"
                                    style="background-color: #F43E3E; color: #fff; border: none;"
                                    @click="deleteColumn(editColumnForm.id)">Delete Column</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="cardDetailsModal" tabindex="-1" aria-labelledby="cardDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-grey text-dark rounded-3">
                    <div class="modal-header border-0 pb-0">
                        <div class="w-100 d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h3 class="modal-title fw-bold fs-4" id="cardDetailsModalLabel">{{ selectedCard.title }}
                                </h3>
                                <div class="divider mt-2" style="border-top: solid 1px black;"></div>

                                <!-- <span class="badge bg-secondary">{{ selectedCard.status.name }}</span> -->
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <h6 class="fw-bold"><i class="bi bi-bookmark-star me-2"></i>Status</h6>
                            <span class="badge bg-secondary" style="font-size: 15px;">{{ selectedCard.status.name }}</span>

                        </div>
                        <div class="mb-4">
                            <h6 class="fw-bold"><i class="bi bi-card-text me-2"></i>Description</h6>
                            <p class="text-light-emphasis">{{ selectedCard.description }}</p>
                        </div>
                        <div class="mb-4">
                            <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Details</h6>
                            <p><strong>Due Date:</strong> {{ selectedCard.due_date }}</p>
                            <p><strong>Urgency:</strong> {{ selectedCard.urgency }}</p>
                            <p><strong>Assigned User:</strong> {{ selectedCard.user_role?.users?.name }}</p>
                            <p><strong>Assigned Role:</strong> {{ selectedCard.user_role?.roles?.name }}</p>
                        </div>
                        <div v-if="selectedCard.attachment && JSON.parse(selectedCard.attachment).length" class="mb-4">
                            <h6 class="fw-bold"><i class="bi bi-paperclip me-2"></i>Attachments</h6>
                            <div v-for="file in JSON.parse(selectedCard.attachment)" :key="file.url"
                                class="d-flex align-items-center mb-2 p-2 bg-secondary rounded-3">
                                <div v-if="isImage(file.url)" class="me-3">
                                    <img :src="file.url" alt="Attachment" class="rounded"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <a :href="file.url" target="_blank" class="text-light">{{ file.name }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                        <div>
                            <button class="btn btn-secondary me-2"><i class="bi bi-pencil"></i> Edit</button>
                            <!-- <button class="btn btn-secondary"><i class="bi bi-share"></i> Share</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </ProjectLayout>

</template>

<style scoped>
.bg-size {
    background-size: cover;
    background-position: center;
    overflow-x: auto;
}

.kanban-board {
    flex-grow: 1;
    /* Allow board to take remaining space */
    background-size: cover;
    background-position: center;
    overflow-x: auto;
    padding: 1rem;
}

.kanban-columns {
    display: flex;
    margin-right: 70px;
    /* overflow-x: auto; */
    gap: 0.5rem;
    /* Add gap between columns */
}

.kanban-column {
    width: 250px;
    display: flex;
    flex-direction: column;
    transition: background-color 0.3s ease, border 0.3s ease;
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
    flex-grow: 1;
    /* Allow cards to take available space */
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
    color: #6d6969;
}

.dragging {
    opacity: 0.5;
    transform: scale(1.05);
    transition: transform 0.2s ease;
}



.list-group-item {
    display: flex;
    align-items: center;
}

.list-group-item i {
    font-size: 1.25rem;
    color: #0d6efd;
}

.list-group-item a {
    margin-left: 0.5rem;
    text-decoration: none;
    color: #0d6efd;
}

.list-group-item a:hover {
    text-decoration: underline;
}


</style>
