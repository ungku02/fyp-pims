<template>
    <div class="container mt-5">
        <h1 class="mb-4">Settings</h1>
        <div class="card p-4">
            <h5 class="card-title">Set Your Availability Status</h5>
            <div class="form-group mb-3">
                <label for="availability">Availability Status</label>
                <select id="availability" v-model="form.availability" class="form-control">
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
            <button @click="updateStatus" class="btn btn-primary">Update Status</button>
        </div>
    </div>
</template>


<script setup>
import { reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = reactive({
    availability: 'available'
});

function updateStatus() {
    const formData = useForm({ availability: form.availability });
    formData.post(route('settings.update'), {
        onSuccess: () => {
            alert('Status updated successfully');
        },
        onError: () => {
            console.error('Failed to update status');
        },
    });
}
</script>

<style scoped>
.container {
    max-width: 600px;
}
</style>
