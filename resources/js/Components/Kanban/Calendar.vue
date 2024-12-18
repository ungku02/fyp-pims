<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Calendar</h2>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { defineProps } from 'vue';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

const props = defineProps({
    cards: Array,
});

onMounted(() => {
    const calendarEl = document.getElementById('calendar');
    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin],
        events: props.cards.map(card => ({
            title: card.title,
            start: card.due_date,
        })),
    });
    calendar.render();
});
</script>

<style>
@import '@fullcalendar/core/main.css';
@import '@fullcalendar/daygrid/main.css';
</style>