<script setup>
import { ref, onMounted, computed } from 'vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3'; // Import the Link and useRoute components
import axios from 'axios';

const { props } = usePage();
const workspaces = ref(props.workspaces);
const role = ref(props.role || {});
const currentDate = ref("");
const isSidebarOpen = ref(true);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const goToBoard = () => {
    window.location.href = route('board');
};

const unreadCount = computed(() => {
    return props.notifications ? props.notifications.filter((notification) => !notification.read).length : 0;
});

const isOwner = computed(() => {
    return workspaces.value.some(workspace => workspace.user_id === props.auth.user.id);
});

const canManageRoles = computed(() => {
    return role.value.name === 'Project Manager' || role.value.name === 'System Analyst';
});

const canManageUsers = computed(() => {
    return role.value.name === 'Project Manager';
});

const pageTitle = computed(() => {
    if (route().current('dashboard'))
        return 'Dashboard';
    else if (route().current('board'))
        return 'Workspaces';
    else if (route().current('swap.tasks'))
        return 'Swap Tasks';
    else if (route().current('settings'))
        return 'Settings';
    else if (route().current('preview'))
        return 'Preview';
    else if (route().current('roles.index'))
        return 'Role Management';
    else if (route().current('show.workspace'))
        return 'Workspace';
    else if (route().current('users.index'))
        return 'User Management';
    else if (route().current('users.filter'))
        return 'User Management';
    else
        return 'PIMS';
});

const markAsRead = async (notificationId) => {
    try {
        await axios.patch(route('notifications.read', { id: notificationId }));
        // Update the notification status locally
        const notification = props.notifications.find(n => n.id === notificationId);
        if (notification) {
            notification.read = true;
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

// Fungsi untuk tentukan class berdasarkan jenis notifikasi
const getNotificationClass = (notification) => {
    if (notification.type === 'App\\Notifications\\TaskNotification') {
        return notification.data.type === 'new_task' ? 'bg-light' : 'bg-danger text-white';
    }
    if (notification.type === 'App\\Notifications\\UserUnavailabilityNotification') {
        return 'bg-warning text-dark';
    }
    if (notification.type === 'App\\Notifications\\UserWorkspaceNotification') {
        return 'bg-info text-white';
    }
    return 'bg-light';
};

// Fungsi untuk tentukan ikon berdasarkan jenis notifikasi
const getNotificationIcon = (notification) => {
    if (notification.type === 'App\\Notifications\\TaskNotification') {
        return notification.data.type === 'new_task' ? '🔵' : '🔴'; // New Task = Biru, Overdue Task = Merah
    }
    if (notification.type === 'App\\Notifications\\UserUnavailabilityNotification') {
        return '🟡'; // Kuning untuk User Unavailability
    }
    if (notification.type === 'App\\Notifications\\UserWorkspaceNotification') {
        return '🟢'; // Hijau untuk Workspace
    }
    return '⚫'; // Hitam untuk default
};


// Tukar `\n` kepada `<br>` untuk multiline text
const formatMessage = (message) => {
    return message.replace(/\n/g, '<br>');
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
                        <button @click="goToBoard" class="btn w-100 btn-pims"
                            style="background-color: #e5e5be; font-size: 14px;">
                            Create New Workspace <i class="bi bi-plus-circle ms-2"></i>
                        </button>
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
                            <a :class="{ 'nav-link': true, 'active': route().current('board') }" :href="route('board')">
                                <i class="bi bi-person-workspace me-2"></i> Workspaces
                            </a>
                        </li>
                        <!-- <li class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('swap.tasks') }" href="/leads">
                                <i class="bi bi-people me-2"></i> Swap Tasks
                            </a>
                        </li>
                       
                        <li class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('preview') }" href="/preview">
                                <i class="bi bi-eye me-2"></i> Preview
                            </a>
                        </li> -->
                        <li v-if="canManageRoles" class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('roles.index') }" href="/roles">
                                <i class="bi bi-shield-lock me-2"></i> Role Management
                            </a>
                        </li>
                        <li v-if="canManageRoles" class="nav-item my-2">
                            <a :class="{
                                'nav-link': true,
                                'active': route().current('users.index') || route().current('users.filter')
                                }" href="/users">
                                <i class="bi bi-people me-2"></i> User Management
                            </a>
                        </li>
                        <li v-else class="nav-item my-2">
                            <a :class="{ 'nav-link': true, 'active': route().current('settings') }" href="/settings">
                                <i class="bi bi-gear me-2"></i> Settings
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
                                <h1 class="fs-5 fw-bold mb-0" style="color:#42275a;">{{ pageTitle }}</h1>
                                <p class="mb-0" style="color: #42275a;">{{ currentDate }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-envelope fs-5" style="color: #42275a;"></i>
                            <div class="position-relative">
                                <button class="btn btn-light p-0" id="notificationDropdown" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="bi bi-bell fs-5" style="color: #42275a;"></i>
                                    <span v-if="unreadCount > 0"
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ unreadCount }}
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-3 p-2"
                                    style="min-width: 320px; max-width: 700px;" aria-labelledby="notificationDropdown">

                                    <!-- Jika Tiada Notifikasi -->
                                    <li v-if="!props.notifications || props.notifications.filter(n => !n.read).length === 0"
                                        class="dropdown-item text-center text-muted py-3">
                                        <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                                        <span>You don't have any notifications yet!</span>
                                    </li>

                                    <li v-for="notification in props.notifications.filter(n => !n.read)"
                                        :key="notification.id"
                                        class="dropdown-item d-flex align-items-center gap-2 p-3 border-bottom"
                                        @click="markAsRead(notification.id)">

                                        <!-- Titik Warna Sebagai Indikator -->
                                        <span class="fs-6">{{ getNotificationIcon(notification) }}</span>

                                        <!-- Mesej Notifikasi -->
                                        <div class="flex-grow-1">
                                            <div v-html="formatMessage(notification.data.message)"></div>
                                        </div>

                                        <!-- Badge Status -->
                                        <span class="badge bg-primary">New</span>
                                    </li>

                                </ul>
                            </div>
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

.nav-link.disabled {
    pointer-events: none;
    opacity: 0.6;
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
