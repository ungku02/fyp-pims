<template>
    <AuthenticatedLayout :notifications="notifications">
        <h3 style="text-align: center; font-weight: bold;background: linear-gradient(to right,  #6C5B7B, #C2B9CB); color:#fff; max-width:300px; padding: 5px; border-radius: 20px;"
            class="font-semibold text-xl text-black-500 leading-tight ms-5 mb-4">Setting</h3>
        <div class="container mt-5">

            <div class="card shadow-lg p-4 rounded-lg">
                <div class="card-body">
                    <h3 class="card-title mb-3 text-center fw-medium">Set Your Availability Status:</h3>
                    <div v-if="!isEditing">
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="mb-0">
                                <strong>Status: </strong>
                                <span
                                    :class="form.availability === 'available' ? 'badge bg-success rounded-4' : 'badge bg-danger rounded-4'">
                                    {{ form.availability.charAt(0).toUpperCase() + form.availability.slice(1) }}
                                </span>
                            </p>
                            <button @click="isEditing = true" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                        </div>

                        <div v-if="form.availability === 'unavailable'" class="mt-3">
                            <p><strong>Start Date:</strong> {{ form.start_date }}</p>
                            <p><strong>End Date:</strong> {{ form.end_date }}</p>
                        </div>
                    </div>

                    <!-- Editable Form -->
                    <transition name="fade">
                        <div v-if="isEditing" class="mt-3">
                            <div class="mb-3">
                                <label for="availability" class="form-label">Availability Status</label>
                                <select id="availability" v-model="form.availability" class="form-select">
                                    <option value="available">Available</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>

                            <div v-if="form.availability === 'unavailable'" class="row">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" id="start_date" v-model="form.start_date" class="form-control" />
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" id="end_date" v-model="form.end_date" class="form-control" />
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button @click="isEditing = false" class="btn btn-outline-secondary">Cancel</button>
                                <button @click="updateStatus" class="btn btn-secondary">Save Changes</button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { reactive, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = reactive({
    availability: 'available',
    start_date: '',
    end_date: ''
});

const isEditing = ref(false);

function updateStatus() {
    if (form.availability === 'available') {
        form.start_date = null;
        form.end_date = null;
    }

    const formData = useForm({
        availability: form.availability,
        start_date: form.start_date,
        end_date: form.end_date
    });

    formData.post(route('settings.update'), {
        onSuccess: () => {
            alert('Status updated successfully');
            isEditing.value = false;
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

.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.badge {
    font-size: 1rem;
    padding: 0.4em 1em;
    text-transform: capitalize;
}

.btn-sm {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
