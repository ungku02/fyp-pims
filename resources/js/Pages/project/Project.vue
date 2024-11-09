<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideBar from '@/Components/SideBar.vue';
import { Head } from '@inertiajs/vue3';
import { defineComponent } from 'vue';
import { VueDraggableNext } from 'vue-draggable-next';

export default defineComponent({
    components: {
        draggable: VueDraggableNext,
        AuthenticatedLayout,
        Head,
        SideBar,
    },
});
</script>

<template>
    <Head title="Project" />

    <AuthenticatedLayout>
        <!-- Page Header -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project</h2>
        </template>

        <!-- Container -->
            <div class="max-w-mx-auto sm:px-6 lg:px-8 ">
                <!-- Sidebar -->
                <SideBar />

                <!-- Main Content Area -->
               
                    
                        <p class="text-muted">Checkout and dive into your ongoing projects!</p>
               <!-- Project List Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="d-flex flex-wrap container">
                            <h5 class="section-title font-semibold text-xl text-gray-800 leading-tight">Project List</h5>
                            <!-- <p class="text-muted">text label paragraph</p> -->

                            <!-- Card Grid for Project List -->
                            <div class="row">
                                <!-- Card 1 -->
                                <div class="col-md-4 mt-3 mb-3">
                                    <div class="card d-flex">
                                        <img src="/img/template-1.png" class="card-img-top" style="width: 400px; height:200px;" alt="Project Image">
                                        <div class="card-body">
                                            <h5 class="card-title">Project 1</h5>
                                            <p class="card-text">A brief description of Project 1 goes here.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card 2 -->
                                <div class="col-md-4 mt-3  mb-3">
                                    <div class="card h-100">
                                        <img src="/img/template-2.png" class="card-img-top" style="width: 400px; height:200px;" alt="Project Image">
                                        <div class="card-body">
                                            <h5 class="card-title">Project 2</h5>
                                            <p class="card-text">A brief description of Project 2 goes here.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card 3 -->
                                <div class="col-md-4 mt-3  mb-3">
                                    <div class="card h-100">
                                        <img src="/img/template-3.png" class="card-img-top" style="width: 400px; height:200px;" alt="Project Image">
                                        <div class="card-body">
                                            <h5 class="card-title">Project 3</h5>
                                            <p class="card-text">A brief description of Project 3 goes here.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
     
    </AuthenticatedLayout>
</template>

<style scoped>
/* Optional additional styling */
.container {
    margin-top: 20px;
}

.card {
    transition: all 0.3s ease-in-out;
}

.card:hover {
    transform: scale(1.05);
}

.card-title {
    font-weight: 600;
}
</style>
