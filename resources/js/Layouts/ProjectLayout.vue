<script setup>
import { ref, onMounted,computed } from 'vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    project: {
        type: Object,
        required: true
    },

    notifications: {
        type: Array,
        default: () => [],
    },
});

// const { notifications } = usePage().props.value;
const unreadCount = computed(() =>
    props.notifications.filter((notification) => !notification.read).length
);
function goToKanban(projectId) {
    router.visit(route('project.kanban', { project: projectId }));
}

function goToSwapTasks(projectId) {
    router.visit(route('project.swapTasks', { project: projectId }));
}

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
            <aside v-if="isSidebarOpen" class="text-white" style="width: 250px !important; ">
                <div class="d-flex flex-wrap justify-content-start">
                    <ApplicationLogo />
                    <div class="p-3 mt-auto">
                        <button class="btn w-100 btn-pims" style="background-color: #e5e5be; font-size: 14px;">Create
                            New Workspace <i class="bi bi-plus-circle ms-2"></i></button>
                    </div>
                </div>
                <nav>
                    <ul class="nav flex-column min-vh-100 px-3">
                        <li class="nav-link p-0">
                            <a :class="{ 'nav-link': true, 'active': route().current('dashboard') }" href="/dashboard">
                                <i class="bi bi-house-door me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('project/show') }"
                                :href="route('project/show', { id: project.id })">
                                <i class="bi bi-person-workspace me-2"></i> Projects
                            </a>
                        </li>

                        <li class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('project.swapTasks') }"
                                href="javascript:void(0)" class="text-white" @click="goToSwapTasks(project.id)">
                                <i class="bi bi-people me-2"></i> Swap Tasks
                            </a>
                        </li>
                        <li class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('project.kanban') }"
                                href="javascript:void(0)" class="text-white" @click="goToKanban(project.id)">
                                <i class="bi bi-kanban me-2"></i> Board
                            </a>
                        </li>

                        <li class="nav-item my-2">
                            <a href="" class="nav-link text-white">
                                <i class="bi bi-tools me-2"></i> Tools
                            </a>
                        </li>
                        <li class="nav-item my-2">
                            <h5 class="text-white">You are in:</h5>
                            <ul class="nav flex-column">
                                <li class="nav-item d-flex align-items-center gap-2 my-2">
                                    <div class="d-flex justify-content-center align-items-center rounded-circle"
                                        style="width: 36px; height: 36px;background: linear-gradient(to right,  #6C5B7B, #C2B9CB); color:#6C5B7B;">
                                        <span class="text-white fw-bold">
                                            {{ project.workspace?.title ?
                                                project.workspace.title.charAt(0).toUpperCase()
                                            : '' }}
                                        </span>
                                    </div>
                                    <a class="nav-link text-white">
                                        {{ project.workspace?.title }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <div :class="{ 'flex-grow': isSidebarOpen, 'w-100': !isSidebarOpen, 'h-100': true }">
                <!-- Header -->
                <nav class="navbar navbar-expand-lg text-white m-2" style="background: transparent;">
                    <div class="container d-flex align-items-center justify-content-between">
                        <div class="flex gap-2">
                            <button @click="toggleSidebar" class="btn btn-light me-3" style="width: 45px; height:45px;">
                                <i :class="isSidebarOpen ? 'bi bi-list' : 'bi bi-list'" style="font-size: 20px;"></i>
                            </button>
                            <div>
                                <div class="p-2 rounded-5 d-flex justify-content-center"
                                    style="background: linear-gradient(to right,  #6C5B7B, #C2B9CB); color:#fff; max-width:500px;">
                                    <h3 class="fw-bold mb-0">{{ project.workspace?.title }} : {{ project.title }}</h3>
                                </div>
                                <p class="mb-0" style="color: #42275a;">{{ currentDate }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-envelope fs-5" style="color: #42275a;"></i>
                            <div class="position-relative">
                                <i class="bi bi-bell fs-5" style="color: #42275a;"></i>
                                <span v-if="unreadCount > 0"
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ unreadCount }}
                                </span>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light d-flex align-items-center gap-2" id="userDropdown"
                                    data-bs-toggle="dropdown">
                                    <div class="rounded-circle name-logo d-flex justify-content-center align-items-center"
                                        style="width: 36px; height: 36px; color: #fff;">
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
    background-color: #42275a;
    border-radius: 0 20px 20px 0;
    width: 270px;
}

.nav-link {
    color: white;
}

.nav-link.active {
    background-color: #EBE2F3;
    padding: 10px 20px;
    width: 100%;
    color: #42275a !important;
    border-radius: 10px;
    font-weight: bold;
}

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
