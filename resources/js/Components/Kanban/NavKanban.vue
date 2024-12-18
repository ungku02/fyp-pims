<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 ">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-8">Kanban Board</h2>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:flex">
                    <!-- Board link -->
                    <NavLink @click="goToKanban('/kanban')"
                        :active="route().current('project/kanban')">
                        Board
                    </NavLink>
                    
                    <!-- Task List link -->
                    <NavLink @click="goToKanban('/dashboard')"
                        :active="route().current('dashboard')">
                        Task List
                    </NavLink>
                    
                    <!-- Calendar link -->
                    <NavLink @click="goToCalendar"
                        :active="route().current('calendar')">
                        Calendar
                    </NavLink>
                    
                    <!-- Team link -->
                    <NavLink @click="goToKanban('/kanban/team')"
                        :active="route().current('project/team')">
                        Team
                    </NavLink>
                </div>
            </div>

            <!-- Hamburger for mobile menu -->
            <div class="sm:hidden">
                <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                    class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';
import NavLink from '@/Components/NavLink.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    projectId: {
        type: String,
        required: true,
    },
    background: {
        type: String,
        required: true,
    },
});

// Method to handle navigation like goToKanban
function goToKanban(route) {
    router.visit(route, {
        data: {
            project: props.projectId,
            background: props.background,
        },
    });
}

function goToCalendar() {
    router.visit('/calendar', {
        data: {
            project: props.projectId,
            background: props.background,
        },
    });
}
</script>
