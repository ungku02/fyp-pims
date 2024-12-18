<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed, onMounted } from "vue";
import axios from "axios";

const props = defineProps({
    workspaces: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
});

const cards = ref([]);
const colors = ["#2c3e50", "#757519", "#ee9ca7", "#135058", "#00223E", "#99AAAA"];
const getRandomColor = () => colors[Math.floor(Math.random() * colors.length)];
const randomColors = computed(() => props.workspaces.map(() => getRandomColor()));

// Sample Calendar Events
const calendarEvents = ref([
    { time: "10:00 AM", title: "Team Standup Meeting", date: "Oct 20, 2021" },
    { time: "01:30 PM", title: "Task Management Review", date: "Oct 20, 2021" },
    { time: "10:00 AM", title: "UX Research", date: "Oct 21, 2021" },
    { time: "03:00 PM", title: "Mobile App Design", date: "Oct 22, 2021" },
]);

onMounted(async () => {
    try {
        const response = await axios.get("/user/cards");
        cards.value = response.data;
    } catch (error) {
        console.error("Error fetching cards:", error);
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="d-flex" style="height: 100vh;">
            <!-- Main Section -->
            <div class="flex-grow-1 d-flex flex-column">
                <header class="bg-light-purple p-4 rounded mb-4" style="background-color: #42275a;">
                    <h2 style="color:#fff; font-weight: bold;">Hi, {{ $page.props.auth.user.name }}</h2>
                    <p style="color:#fff;">Ready to start your day and dive into the projects?</p>
                </header>

                <!-- Workspaces -->
                <section class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="m-0" style="color: #42275a; font-weight: bold;">Workspaces</h3>
                        <button class="btn btn-link text-decoration-none" style="color: #2A4965; font-weight: bold;">
                            View All
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-12" v-for="(workspace, index) in workspaces"
                            :key="workspace.id">
                            <div class="card board-card" @click="goToKanban(workspace.id)"
                                :style="{ background: randomColors[index] }">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-weight: bold;">{{ workspace.title }}</h5>
                                    <div class="d-flex flex-wrap mt-3">
                                        <div v-for="member in workspace.members.slice(0, 3)" :key="member.id"
                                            class="rounded-circle name-logo d-flex justify-content-center align-items-center"
                                            style="width: 36px; height: 36px; color: #fff; background-color: #6C5B7B; margin: 0;">
                                            {{ member.users.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div v-if="workspace.members.length > 3" class="more-members" style="
                          width: 36px;
                          height: 36px;
                          border-radius: 50%;
                          background-color: #fff;
                          color: #6C5B7B;
                          display: flex;
                          justify-content: center;
                          align-items: center;
                          font-size: 14px;
                          font-weight: bold;
                        ">
                                            +{{ workspace.members.length - 3 }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center m-2">
                                    <p class="mb-0 me-1" style="font-size: 12px;">Progress:</p>
                                    <div class="d-flex align-items-center">
                                        <div class="progress" role="progressbar" aria-label="Example 20px high"
                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"
                                            style="height: 10px; width: 100px;">
                                            <div class="progress-bar bg-success" style="width: 25%; font-size: 7px;">
                                            </div>
                                        </div>
                                        <!-- Progress percentage outside the bar -->
                                        <span class="ms-2" style="font-size: 12px;">25%</span>
                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>
                </section> <!-- Task List -->
                <section
                    style="background-color: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <h3 class="m-3" style="color:#2A4965; font-weight: bold;">Task List</h3>
                    <div class="list-group d-flex"
                        style="display: flex; flex-direction: row; gap: 20px; justify-content: flex-start;">
                        <div v-for="card in cards" :key="card.id" style="flex: 0 0 auto; flex-wrap: wrap;"
                            class="flex-column">
                            <div v-if="card.urgency === 'urgent'" class="urgency-urgent"
                                style="border-left: solid 7px red; width: 300px; background-color: #fff; border-radius: 15px; padding: 15px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);">
                                <h4 style="font-weight: bold; margin-bottom: 3px;">{{ card.title }}</h4>
                                <p class="text-muted">Due: {{ card.due_date }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mx-3 d-flex flex-column">

                <!-- Calendar Section -->
                <aside class="calendar-section p-4" style="width: 300px; max-height: 100vh; background-color: #EBE2F3;">
                    <h4
                        style="color: #fff; font-weight: bold; background-color: #6C5B7B; padding: 4px; border-radius: 10px;;">
                        Calendar</h4>
                    <div v-for="(event, index) in calendarEvents" :key="index" class="calendar-event mb-3">
                        <p class="event-time" style="font-weight: bold; color: #2A4965; margin-bottom: 2px;">
                            {{ event.time }}
                        </p>
                        <p class="event-title" style="margin: 0; color: #6C5B7B; font-weight: 600;">
                            {{ event.title }}
                        </p>
                        <small style="color: #888;">{{ event.date }}</small>
                    </div>
                </aside>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.board-card {
    cursor: pointer;
    border-radius: 10px;
    background: linear-gradient(to right, #e96443, #904e95);
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s ease-in-out;
}

.board-card:hover {
    transform: scale(1.05);
}

.member-avatar img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 5px;
}

.more-members {
    display: inline-block;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #fff;
    color: #6C5B7B;
    text-align: center;
    line-height: 30px;
    font-size: 14px;
    font-weight: bold;
}

.calendar-section {
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.calendar-event {
    border-bottom: 1px solid #948E99;
    padding-bottom: 8px;
}

/* .calendar-event:last-child {
    border-bottom: none;
} */

/* .relative {
    position: relative;
} */

/* .absolute {
    position: absolute;
    white-space: nowrap;
} */
</style>
