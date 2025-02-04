<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed, onMounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    workspaces: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
    notifications: { type: Array, default: () => [] },
    role: { type: Object, default: () => [] },
    events: { type: Array, default: () => [] },
});

const unreadCount = computed(() =>
    props.notifications.filter((notification) => !notification.read).length
);
const cards = ref([]);
const completedTasks = ref(0);
const pendingTasks = ref(0);
const totalTasks = ref(0);
const colors = ["#90B1CB", "#B89BBF", "#D9819F", "#ABC4AB", "#D79E84", "#D6D383"];
const getRandomColor = () => colors[Math.floor(Math.random() * colors.length)];
const randomColors = computed(() => props.workspaces.map(() => getRandomColor()));

const showEventForm = ref(false);
const newEvent = useForm({
    title: '',
    time: '',
    date: ''
});

const addEvent = () => {
    showEventForm.value = true;
};

const editEvent = (event) => {
    newEvent.title = event.title;
    newEvent.time = event.time;
    newEvent.date = event.date;
    newEvent.id = event.id;
    showEventForm.value = true;
};

function updateEvent() {
    newEvent.put(route('events.update', newEvent.id), {
        onSuccess: () => {
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editEventModal'));
            if (editModal) editModal.hide();

            showEventForm.value = false;
            successMessage.value = 'Event updated successfully';
            
            // Reload events or handle success
        },
        onError: () => {
            console.error('Error updating event:', newEvent.errors);
        },
    });
}

const showDeleteModal = ref(false);
const eventToDelete = ref(null);
const successMessage = ref('');

watch(successMessage, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            successMessage.value = '';
        }, 3000);
    }
});

const closeSuccessMessage = () => {
    successMessage.value = '';
};

const confirmDeleteEvent = (event) => {
    eventToDelete.value = event;
    showDeleteModal.value = true;
};

function deleteEvent() {
    newEvent.delete(route('events.destroy', eventToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            successMessage.value = 'Event deleted successfully';
            // Reload events or handle success
        },
        onError: () => {
            console.error('Error deleting event:', newEvent.errors);
        },
    });
}

function saveEvent() {
    newEvent.post(route('events.store'), {
        onSuccess: () => {
            // Hide modal
            const addModal = bootstrap.Modal.getInstance(document.getElementById('addEventModal'));
            if (addModal) addModal.hide();

            // Clear form
            newEvent.title = '';
            newEvent.time = '';
            newEvent.date = '';

            // Reset form visibility and display success message
            showEventForm.value = false;
            successMessage.value = 'Event added successfully';

            // Reload events or handle additional success logic
            console.log('Event successfully added!');
        },
        onError: () => {
            console.error('Error saving event:', newEvent.errors);
        },
    });
}


function formatTime(time) {
    const [hours, minutes] = time.split(':');
    const date = new Date();
    date.setHours(hours, minutes);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

const goToWorkspace = (workspaceId) => {
    window.location.href = route('workspace.show', { id: workspaceId });
};

const goToBoard = () => {
    window.location.href = route('board');
};

// const successMessage = ref('');
const errorMessage = ref('');

// const errorMessage = ref('');

async function sendNotification() {
    try {
        const response = await axios.get('send-notification'); // Panggil GET request ke Laravel
        successMessage.value = 'Notifikasi berjaya dihantar!';
        console.log('Respons dari server:', response.data);
    } catch (error) {
        errorMessage.value = 'Gagal menghantar notifikasi';
        console.error('Ralat:', error);
    }
}

onMounted(async () => {
    try {
        const response = await axios.get("/user/cards");
        cards.value = response.data;
        completedTasks.value = cards.value.filter(card => card.columns.status.name === 'Completed').length;
        pendingTasks.value = cards.value.filter(card => card.columns.status.name === 'In-process').length;
        totalTasks.value = cards.value.length;
        console.log(cards.value);
    } catch (error) {
        console.error("Error fetching cards:", error);
    }
});
</script>

<template>
    <AuthenticatedLayout :notifications="notifications">
        <div class="dashboard-container">
            <div class="content">
                <!-- Main Content -->
                <div class="main-content">
                    <header class="header">
                        <h2 style="text-transform: capitalize;">Hi, {{ $page.props.auth.user.name }}!</h2>
                        <p>Ready to start your day and dive into the projects?</p>
                        <button class="btn btn-primary" @click="sendNotification">Hantar Notifikasi</button>
                    </header>

                    <!-- Task Summary Section -->
                    <section class=" mt-4 mb-4">

                        <div class="summary-cards mt-3">
                            <div class="summary-card">
                                <h4>Completed Tasks</h4>
                                <p>{{ completedTasks }}</p>
                            </div>
                            <div class="summary-card">
                                <h4>Pending Tasks</h4>
                                <p>{{ pendingTasks }}</p>
                            </div>
                            <div class="summary-card">
                                <h4>Total Tasks</h4>
                                <p>{{ totalTasks }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Workspaces Section -->
                    <section class="workspaces">
                        <div class="section-header">
                            <h3 style="font-weight: bold;">Workspaces</h3>
                            <button @click="goToBoard" class="view-all text-muted">View All</button>
                        </div>
                        <div class="workspace-list">
                            <div v-if="workspaces.length === 0" class="no-workspaces">
                                No workspace available yet.
                            </div>
                            <div v-else class="workspace-card" v-for="(workspace, index) in workspaces"
                                :key="workspace.id" @click="goToWorkspace(workspace.id)"
                                :style="{ background: randomColors[index] }">
                                <h5>{{ workspace.title }}</h5>
                                <div class="members">
                                    <div v-for="member in workspace.members.slice(0, 3)" :key="member.id"
                                        class="member-avatar">
                                        {{ member.users.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div v-if="workspace.members.length > 3" class="more-members">
                                        +{{ workspace.members.length - 3 }}
                                    </div>
                                </div>
                                <div class="owner">Owner: {{ workspace.users.name }}</div>
                            </div>
                        </div>
                    </section>

                    <!-- Task List Section -->
                    <section class="tasks mt-3">
                        <div class="section-header">
                            <h3 style="font-weight: bold;">Task List</h3>
                            <button class="view-all text-muted">View All</button>
                        </div>
                        <div class="task-list mt-3">
                            <div v-if="cards.length === 0" class="no-tasks">
                                No tasks available yet.
                            </div>
                            <div v-else v-for="card in cards.slice(0, 3)" :key="card.id" class="task-card">
                                <div v-if="card.urgency === 'urgent'" class="task-card-content urgent">
                                    <h4>{{ card.title }}</h4>
                                    <p class="date">Due: {{ card.due_date }}</p>
                                </div>
                                <div v-else-if="card.urgency === 'normal'" class="task-card-content normal">
                                    <h4>{{ card.title }}</h4>
                                    <p class="date">Due: {{ card.due_date }}</p>
                                </div>
                                <div v-else class="task-card-content other">
                                    <h4>{{ card.title }}</h4>
                                    <p class="date">Due: {{ card.due_date }}</p>
                                </div>
                            </div>
                        </div>
                    </section>


                </div>

                <!-- Calendar Section -->
                <aside class="calendar">
                    <h4>Calendar</h4>

                    <!-- Check if there are any events -->
                    <div v-if="events.length === 0">
                        <p>You don't have any events yet. Create one!</p>
                    </div>

                    <div v-else>
                        <div class="calendar-event" v-for="(event, index) in events" :key="index"
                            style="display: flex; align-items: center; justify-content: space-between;">
                            <!-- Left section with time and title -->
                            <div>
                                <p class="time" style="margin: 0;">{{ new Date(event.date).toLocaleDateString('en-US', {
                                    month: 'short', day: 'numeric', year: 'numeric' }) }}</p>
                                <p class="title" style="margin: 0;">{{ event.title }}</p>
                                <small>{{ formatTime(event.time) }}</small>
                            </div>

                            <!-- Right section with icons -->
                            <div>
                                <button class="edit-icon" @click="editEvent(event)" data-bs-toggle="modal"
                                    data-bs-target="#editEventModal"
                                    style="background: none; border: none; cursor: pointer;">
                                    <i class="bi bi-pencil-square ms-2"></i>
                                </button>
                                <button class="delete-icon" @click="confirmDeleteEvent(event)"
                                    style="background: none; border: none; cursor: pointer;">
                                    <i class="bi bi-trash ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button @click="addEvent" class="btn-add-event" data-bs-toggle="modal"
                        data-bs-target="#addEventModal">Add Event<i class="bi bi-calendar-week ms-1"></i></button>
                </aside>
            </div>
        </div>

        <!-- Add Event Form Popup -->
        <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel"
            :aria-hidden="!showEventForm" :inert="!showEventForm" showEventForm.value="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addEventModalLabel">Add Event</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveEvent">
                            <div class="mb-3">
                                <InputLabel for="title" value="Title" />
                                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="newEvent.title"
                                    required autofocus autocomplete="title" />
                                <InputError class="mt-2" :message="newEvent.errors.title" />
                            </div>
                            <div class="mb-3">
                                <InputLabel for="time" value="Time" />
                                <TextInput id="time" type="time" class="mt-1 block w-full" v-model="newEvent.time"
                                    required autofocus autocomplete="time" />
                                <InputError class="mt-2" :message="newEvent.errors.time" />
                            </div>
                            <div class="mb-3">
                                <InputLabel for="date" value="Date" />
                                <TextInput id="date" type="date" class="mt-1 block w-full" v-model="newEvent.date"
                                    required autofocus autocomplete="date" />
                                <InputError class="mt-2" :message="newEvent.errors.date" />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn create-btn">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Event Form Popup -->
        <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel"
            :aria-hidden="!showEventForm" :inert="!showEventForm">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editEventModalLabel">Edit Event</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="updateEvent">
                            <div class="mb-3">
                                <InputLabel for="title" value="Title" />
                                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="newEvent.title"
                                    required autofocus autocomplete="title" />
                                <InputError class="mt-2" :message="newEvent.errors.title" />
                            </div>
                            <div class="mb-3">
                                <InputLabel for="time" value="Time" />
                                <TextInput id="time" type="time" class="mt-1 block w-full" v-model="newEvent.time"
                                    required autofocus autocomplete="time" />
                                <InputError class="mt-2" :message="newEvent.errors.time" />
                            </div>
                            <div class="mb-3">
                                <InputLabel for="date" value="Date" />
                                <TextInput id="date" type="date" class="mt-1 block w-full" v-model="newEvent.date"
                                    required autofocus autocomplete="date" />
                                <InputError class="mt-2" :message="newEvent.errors.date" />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn create-btn">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showDeleteModal" class="delete-modal-popup">
            <div class="delete-modal">
                <h3>Confirm Delete</h3>
                <p>Are you sure you want to delete this event?</p>
                <div class="modal-buttons">
                    <button @click="deleteEvent" class="btn-confirm-delete">Yes, Delete</button>
                    <button @click="showDeleteModal = false" class="btn-cancel-delete">Cancel</button>
                </div>
            </div>
        </div>

        <div v-if="successMessage" class="success-message">
            <p>{{ successMessage }}</p>
            <button @click="closeSuccessMessage" class="close-success-message">&times;</button>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.dashboard-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px;
    overflow-x: hidden;
    /* Disable horizontal scrolling */
}

.content {
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.main-content {
    flex: 1;
    min-width: 300px;
}

.calendar {
    flex: 0 0 300px;
    flex-direction: column;
    max-width: 300px;
    padding: 20px;
    background-color: #ebe2f3;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.calendar h4 {
    background-color: #6c5b7b;
    color: white;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
}

.btn-add-event {
    padding: 10px;
    border: 1px solid #6c5b7b;
    border-radius: 5px;
    /* background: #6c5b7b; */
    color: #6c5b7b;
    cursor: pointer;
    margin-top: 10px;
    width: 100%;
}

.btn-add-event:hover {
    background: #6c5b7b;
    color: white;
}

.edit-icon:hover {
    color:#e08646
}

.delete-icon:hover {
    color: #cf5353
}

.create-btn {
    border: solid 1px #6c5b7b;
    color: #6c5b7b;
}

.create-btn:hover {
    background-color: #6c5b7b;
    color: #fff;
}

.calendar-event {
    margin-top: 10px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #948e99;
}

.calendar-event:last-child {
    border-bottom: 1px solid #948e99;
}

.calendar-event .time {
    font-weight: bold;
    color: #2a4965;
}

.calendar-event .title {
    color: #6c5b7b;
    font-weight: 600;
    margin: 0;
}

/* Responsive adjustments */
@media screen and (max-width: 768px) {
    .content {
        flex-direction: column;
    }

    .calendar {
        max-width: 100%;
        flex: none;
    }
}

.header {
    background-color: #42275a;
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: start;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.workspace-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.workspace-card {
    padding: 20px;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    transition: transform 0.2s;
    display: flex;
    flex-direction: column;
    gap: 10px;
    /* max-width: 250px; */
}

.workspace-card:hover {
    transform: scale(1.05);
}

.members {
    display: flex;
    gap: 5px;
}

.member-avatar,
.more-members {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #6c5b7b;
    color: white;
    font-weight: bold;
}

.progress-wrapper {
    display: flex;
    align-items: center;
    gap: 5px;
}

.progress {
    flex: 1;
    height: 10px;
    background-color: #ddd;
    border-radius: 5px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background-color: #28a745;
}

.tasks {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 2px 4px 6px rgba(0, 0, 0, 0.1);
}

.task-list {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.task-card {
    flex: 1 1 250px;
    max-width: 250px;
}

.task-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    /* Ensures spacing between title and date */
    border-left: 5px solid;
    background-color: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    flex: 1 1 250px;
    max-width: 250px;
    height: 100px;
}

.task-card-content h4 {
    margin: 0;
}

.task-card-content .date {
    margin-top: auto;
    /* Pushes the date to the bottom */
    font-size: 0.9rem;
    color: #555;
}

.urgent {
    border-left-color: red;
}

.normal {
    border-left-color: green;
}

.other {
    border-left-color: orange;
}

.task-summary {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 2px 4px 6px rgba(0, 0, 0, 0.1);
}

.summary-cards {
    display: flex;
    gap: 20px;
}

.summary-card {
    flex: 1;
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.summary-card h4 {
    margin-bottom: 10px;
    font-size: 1.2rem;
    color: #6c5b7b;
}

.summary-card p {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2a4965;
}

.event-form-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.event-form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.event-form h3 {
    margin: 0;
}

.event-form input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.event-form button {
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.event-form button:first-of-type {
    background: #28a745;
    color: white;
}

.event-form button:last-of-type {
    background: #dc3545;
    color: white;
}

.delete-modal-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.delete-modal {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 20px;
    text-align: center;
}

.modal-buttons {
    display: flex;
    justify-content: space-around;
    gap: 10px;
}

.btn-confirm-delete {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background: #dc3545;
    color: white;
    cursor: pointer;
}

.btn-cancel-delete {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background: #6c757d;
    color: white;
    cursor: pointer;
}

.success-message {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.close-success-message {
    background: none;
    color: rgb(255, 255, 255);
    font-size: 2rem;
    cursor: pointer;
    margin-left: 2px;
}
</style>
