<template>
    <Head title="Calendar" />

    <AuthenticatedLayout>
        <template #header>
            <NavKanban :projectId="project" />
        </template>
        <div :style="{ backgroundImage: `url(${background})` }" class="bg-size">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="bg-white shadow-lg sm:rounded-lg">
                    <div class="p-8 border-b border-gray-300">
                        <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center mb-6">
                            Project Calendar
                        </h2>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import { defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavKanban from '@/Components/Kanban/NavKanban.vue';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

const props = defineProps({
    cards: Array,
    project: {
        type: String,
        required: true,
    },
    background: {
        type: String,
        required: true,
    },
    userId: {
        type: Number,
        required: true,
    },
});

onMounted(() => {
    const calendarEl = document.getElementById('calendar');
    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin],
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay',
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day',
        },
        events: props.cards.map(card => ({
            title: card.title,
            start: card.due_date,
            description: card.description,
            urgency: card.urgency,
            textColor: '#ffffff',
           
        })),
        eventContent: function (info) {
            return {
                html: `
                    <div class="fc-event-content">
                        <div><strong>${info.event.title}</strong></div>
                        <div>${info.event.extendedProps.description}</div>
                        <div>Urgency: <span style="color: ${
                            info.event.extendedProps.urgency === 'critical'
                                ? 'red'
                                : info.event.extendedProps.urgency === 'urgent'
                                ? 'orange'
                                : 'green'
                        }">${info.event.extendedProps.urgency}</span></div>
                    </div>
                `,
            };
        },
    });
    calendar.render();
});
</script>
