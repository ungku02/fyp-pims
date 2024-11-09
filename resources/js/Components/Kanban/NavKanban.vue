<template>
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-8">Kanban Board</h2>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:flex">  
                    <NavLink :href="route('project/kanban')" :active="route().current('project/kanban')">
                        Board
                    </NavLink>
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        Task List
                    </NavLink>
                    <NavLink :href="route('board')" :active="route().current('board')">
                       Calender
                    </NavLink>
                    <NavLink :href="route('project')" :active="route().current('project/show')">
                        Team
                    </NavLink>
                  
                </div>
            </div>

            <!-- Hamburger for mobile menu -->
            <div class="sm:hidden">
                <button
                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                    class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path
                            :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>
