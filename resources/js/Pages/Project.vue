<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import { defineComponent } from 'vue'
import { VueDraggableNext } from 'vue-draggable-next'
export default defineComponent({
    components: {
        draggable: VueDraggableNext,
        AuthenticatedLayout,
        Head,
    },
    data() {
        return {
            enabled: true,
            list: [
                { name: 'John', id: 1 },
                { name: 'Joao', id: 2 },
                { name: 'Jean', id: 3 },
                { name: 'Gerard', id: 4 },
            ],
            dragging: false,
        }
    },
    methods: {
        log(event) {
            console.log(event)
        },
    },
})

</script>

<template>

    <Head title="Project" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project</h2>
        </template>

        <div class="d-flex flex-wrap justify-content-evenly container">
            <!--Assign Task-->
            <div class="flex m-5 bg-white rounded shadow" style="width:25%;">
                <draggable class="dragArea list-group w-full" :list="list" @change="log">
                    <div class="d-flex justify-content-center font-bold m-2">
                        <h1>Assign</h1>
                    </div>

                    <div class="list-group-item bg-gray-300 m-1 p-3 rounded-md text-center" v-for="element in list"
                        :key="element.name">
                        {{ element.name }}
                    </div>
                </draggable>
            </div>
            <!--Doing-->
            <div class="flex m-5 bg-white rounded shadow" style="width:25%;">

                <draggable class="dragArea list-group w-full" :list="list" @change="log">
                    <div class="d-flex justify-content-center font-bold m-2">
                        <h1>Doing</h1>
                    </div>
                    <div class="list-group-item bg-gray-300 m-1 p-3 rounded-md text-center" v-for="element in list"
                        :key="element.name">
                        {{ element.name }}
                    </div>
                </draggable>
            </div>
            <!--Done-->
            <div class="flex m-5 bg-white rounded shadow" style="width:25%;">
                <draggable class="dragArea list-group w-full" :list="list" @change="log">
                    <div class="d-flex justify-content-center font-bold m-2">
                        <h1>Done</h1>
                    </div>

                    <div class="list-group-item bg-gray-300 m-1 p-3 rounded-md text-center" v-for="element in list"
                        :key="element.name">
                        {{ element.name }}
                    </div>
                </draggable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>