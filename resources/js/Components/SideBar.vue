<template>
    <div>
        <!-- Menu Icon Button -->
        <button class="menu-icon" @click="toggleSidebar">
            ☰
        </button><br>

        <!-- Sidebar (conditionally shown) -->
        <div v-if="isSidebarVisible" class="sidebar">
            <div class="d-flex flex-column">
                <nav class="nav flex-column">
                    <Link :class="{'nav-link mt-5': true, 'active': route().current('dashboard')}" :href="route('dashboard')">Dashboards</Link>
                    <Link :class="{'nav-link': true, 'active': route().current('board')}" :href="route('board')">Workspaces</Link>
                    <Link :class="{'nav-link': true, 'active': route().current('project')}" :href="route('project')">Projects</Link>
                    <Link :class="{'nav-link': true, 'active': route().current('project/kanban')}" :href="route('project/kanban')">Kanban Board</Link>
                    <Link class="nav-link" href="#">Teams</Link>
                    <Link class="nav-link" href="#">Integration</Link>
                    <Link class="nav-link" href="#">Reports/Analytics</Link>
                    <Link class="nav-link" href="#">Archived</Link>
                    <Link class="nav-link" href="#">Activity Feed</Link>
                    <Link class="nav-link" href="#">Settings</Link>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3'; // Import the Link component

const isSidebarVisible = ref(false);

function toggleSidebar() {
    isSidebarVisible.value = !isSidebarVisible.value;
}
</script>

<style scoped>
/* Menu Icon Styling */
.menu-icon {
    position: absolute;
    top: 20px;
    left: 20px;
    font-size: 24px;
    cursor: pointer;
    background: none;
    border: none;
    color: #484c4f;
    transition: color 0.3s;
    z-index: 1000; /* Keep icon on top of content */
}

.menu-icon:hover {
    color: #0056b3;
}

/* Sidebar Styling */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background-color: #f8f9fa;
    padding-top: 20px;
    padding-left: 15px;
    padding-right: 15px;
    border-right: 1px solid #e0e0e0;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    transition: transform 0.3s ease;
    z-index: 999; /* Keep sidebar above other content */
}

.nav-link {
    color: #495057;
    font-size: 15px;
    padding: 10px 15px;
    margin: 5px 0;
    border-radius: 4px;
    transition: background-color 0.3s, color 0.3s;
}

.nav-link.active {
    background-color: #5a5c5f;
    color: #ffffff;
    font-weight: 500;
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
}

.nav-link:hover {
    background-color: #5a5c5f;
    color: #ffffff;
}

.nav-link:not(:last-child) {
    border-bottom: 1px solid #e9ecef;
}
</style>
