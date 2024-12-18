<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card d-flex flex-column shadow" style="position: relative; height: 100%;">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between" style="font-weight: bold; font-size:13px;">
                            {{ title }}
                            <i class="bi bi-trash" style="cursor: pointer;" @click="confirmDelete"></i>
                        </h5>

                        <div class="d-flex mb-2" style="font-size: 13px;">
                            <p class="card-text">{{ description }}</p>
                        </div>

                        <!-- Due Date -->
                        <div v-if="dueDate" class="d-flex justify-content-between align-items-center mb-2">
                            <span :class="['due-date', dueDateClass]">
                                <i class="bi bi-clock" style="margin-right: 5px;"></i> {{ dueDate }}
                            </span>
                            <!-- Urgency Indicator -->
                            <span v-if="urgency" :class="['urgency-dot', urgency]" :title="urgency"></span>
                        </div>

                        <!-- Assigned User and Role -->
                        <div v-if="assignedUser" class="d-flex justify-content-between align-items-center mb-2">
                            <span class="assigned-user">
                                <div class="profile-icon" :title="assignedUser || ''">
                                    {{ assignedUser ? assignedUser.charAt(0) : '' }}
                                </div>
                            </span>
                        </div>

                        <!-- Attachment -->
                        <div v-if="attachment && JSON.parse(attachment).length">
                            <div v-for="file in JSON.parse(attachment)" :key="file.url"
                                 class="attachment-thumbnail d-flex flex-wrap justify-content-start align-items-center mb-2 rounded shadow border p-2">
                                <i class="bi bi-paperclip" style="color:grey; font-size: 15px; margin-right: 10px;"></i>
                                <div v-if="isImage(file.url)" class="thumbnail-img-container">
                                    <img :src="file.url" alt="Attachment" class="thumbnail-img">
                                </div>
                                <div v-else class="file-name-container">
                                    <a :href="file.url" target="_blank" class="file-name">{{ file.name }}</a>
                                </div>
                            </div>
                            <a :href="JSON.parse(attachment)[0].url" target="_blank">
                                <i class="bi bi-paperclip" style="color:grey; font-size: 15px;"></i>
                                {{ JSON.parse(attachment).length }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineEmits, defineProps, computed } from 'vue';

const emit = defineEmits(['delete-card']);

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    link: {
        type: String,
        required: true
    },
    dueDate: {
        type: String,
        required: true
    },
    urgency: {
        type: String,
        required: true
    },
    assignedUser: {
        type: String,
        required: true
    },
    assignedRole: {
        type: String,
        required: true
    },
    attachment: {
        type: String,
        required: false
    }
});

function isImage(url) {
    return /\.(jpg|jpeg|png|gif)$/i.test(url);
}

function getFileName(url) {
    return url.split('/').pop();
}

function confirmDelete() {
    emit('delete-card');
}

const dueDateClass = computed(() => {
    const today = new Date();
    const dueDate = new Date(props.dueDate);
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) {
        return 'due-date-past';
    } else if (diffDays <= 2) {
        return 'due-date-soon';
    } else {
        return 'due-date-far';
    }
});
</script>

<style scoped>
/* Styling for the card and attachments */
.card {
    padding: 1rem;
    display: flex;
    flex-direction: column;
}

.card-body {
    flex: 1;
    padding: 0 !important;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transform: translateY(-2px);
    cursor: pointer;
}

.due-date {
    font-size: 0.9rem;
    padding: 3px;
    border-radius: 3px;
}

.due-date-past {
    color: #ad2b2b;
    background-color: #ffb4b4;
}

.due-date-soon {
    color: #ad8b2b;
    background-color: #fff4b4;
}

.due-date-far {
    color: #2bad2b;
    background-color: #b4ffb4;
}

.urgency-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.urgency-dot.normal {
    background-color: green;
}

.urgency-dot.urgent {
    background-color: orange;
}

.urgency-dot.critical {
    background-color: red;
}

.assigned-user {
    font-size: 0.9rem;
    color: #555;
}

.profile-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    cursor: pointer;
}

.thumbnail-img-container {
    width: 50px;
    height: 50px;
    margin-right: 10px;
}

.thumbnail-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.file-name-container {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.file-name {
    font-size: 12px;
    color: #333;
    text-decoration: none;
}

.file-name:hover {
    text-decoration: underline;
}
</style>
