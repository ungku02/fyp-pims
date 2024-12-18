<script setup>
import { ref, onMounted } from 'vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3'; // Import the Link component

const { props } = usePage();
const workspaces = ref(props.workspaces);

const showingNavigationDropdown = ref(false);
const currentDate = ref("");
const isSidebarOpen = ref(true);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

onMounted(() => {
    const options = { weekday: "long", year: "numeric", month: "long", day: "numeric" };
    currentDate.value = new Date().toLocaleDateString(undefined, options);
});
</script>

<template>
    <div class="min-h-screen" style="background-color: #fff;">
        <!-- Sidebar -->
        <div class="d-flex" style="border-radius: 20px;">
            <aside v-if="isSidebarOpen" class="text-white" style="width: 250px; ">
                <div class="d-flex flex-wrap justify-content-start">
                    <ApplicationLogo />
                    <!-- <img :src="asset('img/logo4.png')" alt="PIMS Logo"
                        style="height: 100px; width: 150px; margin-top: 0; padding: 0;"> -->
                    <div class="p-3 mt-auto">
                        <button class="btn w-100 btn-pims" style="background-color: #e5e5be; font-size: 14px;">Create
                            New Workspace <i class="bi bi-plus-circle ms-2"></i></button>
                    </div>
                </div>
                <nav>
                    <ul class="nav flex-column min-vh-100 px-3">
                        <li class="nav-link p-0">
                            <a :class="{ 'nav-link' : true, 'active': route().current('dashboard') }" href="/dashboard">
                                <i class="bi bi-house-door me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('board') }" :href="route('board')">
                                <i class="bi bi-person-workspace me-2"></i> Workspaces
                            </a>
                        </li>
                        <li class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('swap.tasks') }" href="/leads">
                                <i class="bi bi-people me-2"></i> Swap Tasks
                            </a>
                        </li>
                        <li class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('settings') }" href="/settings">
                                <i class="bi bi-gear me-2"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('preview') }" href="/preview">
                                <i class="bi bi-eye me-2"></i> Preview
                            </a>
                        </li>
                        <li class="nav-item my-2">
                            <h5 class="text-white">Your Workspaces</h5>
                            <ul class="nav flex-column">
                                <li v-for="workspace in workspaces" :key="workspace.id"
                                    class="nav-item d-flex align-items-center gap-2 my-2">
                                    <!-- Workspace Icon -->
                                    <div class="d-flex justify-content-center align-items-center rounded-circle"
                                        style="width: 36px; height: 36px; background: linear-gradient(to bottom, #654ea3, #eaafc8);">
                                        <span class="text-white fw-bold">
                                            {{ workspace.title.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>

                                    <!-- Workspace Link -->
                                    <a :href="`/show/workspace/${workspace.id}`"
                                        :class="{ 'nav-link': true, 'text-white': true, 'active': route().current('show.workspace') && route().params.id == workspace.id }">
                                        {{ workspace.title }}
                                    </a>
                                </li>
                            </ul>
                        </li>


                    </ul>
                </nav>
                <!-- <div class="p-3 mt-auto">
                    <button class="btn btn-light w-100">Upgrade to PRO <i class="bi bi-box-arrow-up ms-2"></i></button>
                </div> -->
            </aside>

            <!-- Main Content -->
            <!-- <div :class="{ 'flex-grow-1': isSidebarOpen, 'w-100': !isSidebarOpen }"> -->
            <div :class="{ 'flex-grow': isSidebarOpen, 'w-100': !isSidebarOpen, 'h-100': true }">
                <!-- Header -->
                <nav class="navbar navbar-expand-lg text-white m-2" style="background: transparent;">
                    <div class="container d-flex align-items-center justify-content-between">
                        <div class="flex gap-2">
                            <button @click="toggleSidebar" class="btn me-3" style="background-color: #EBE2F3;">
                                <i :class="isSidebarOpen ? 'bi bi-list' : 'bi bi-list'" style="font-size: 20px;"></i>
                            </button>
                            <div>
                                <h1 class="fs-5 fw-bold mb-0" style="color:#42275a;">Dashboard</h1>
                                <p class="mb-0" style="color: #42275a;">{{ currentDate }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-envelope fs-5" style="color: #42275a;"></i>
                            <i class="bi bi-bell fs-5" style="color: #42275a;"></i>
                            <div class="dropdown">
                                <button class="btn btn-light d-flex align-items-center gap-2" id="userDropdown"
                                    data-bs-toggle="dropdown">
                                    <!-- Display Initial and Name -->
                                    <div class="rounded-circle name-logo d-flex justify-content-center align-items-center"
                                        style="width: 36px; height: 36px; color:#e5e5be;">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="fw-semibold">{{ $page.props.auth.user.name }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li>
                                        <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                                    </li>
                                    <li>
                                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </ResponsiveNavLink>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <main class="m-4">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Sidebar Gradient */
aside {
    /* height: 100% */
    /* z-index: 1000; */
    background-color: #42275a;
    border-radius: 0 20px 20px 0;
}

/* Header Gradient
nav.navbar {
    background-color: #aa076b;
} */
 .nav-link {
    color: white;
 }
.nav-link.active {
    background-color: #EBE2F3;
    padding: 10px 20px;
    width: 100%;
    /* Light Yellow Background */
    color: #42275a !important;
    /* Text Color */
    border-radius: 10px;
    /* Smooth Rounded Corners */
    font-weight: bold;
    /* Emphasized Text */
}


/* Text Styling for Contrast */
.text-white {
    color: #fff !important;
}

.name-logo {
    background-color: #aa076b;
}

.btn-pims:hover {
    background-color: #e5e5be;
    color: #aa076b;
}
</style>
