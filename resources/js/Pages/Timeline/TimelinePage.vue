<template>
    <div class="timeline-container">
        <h1>Project Timeline</h1>
        <ul class="timeline">
            <li v-for="event in events" :key="event.id" class="timeline-event">
                <div class="timeline-content">
                    <h2>{{ event.title }}</h2>
                    <p>{{ event.description }}</p>
                    <span class="timeline-date">{{ event.date }}</span>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    events: {
        type: Array,
        required: true,
    },
});
</script>

<style scoped>
.timeline-container {
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
}

.timeline {
    list-style: none;
    padding: 0;
}

.timeline-event {
    margin-bottom: 20px;
    position: relative;
}

.timeline-event::before {
    content: '';
    position: absolute;
    left: -10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #ccc;
}

.timeline-content {
    padding: 10px 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.timeline-date {
    display: block;
    margin-top: 10px;
    color: #888;
}
</style>
