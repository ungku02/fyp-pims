<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Register" />

    <div class="d-flex vh-100" style="overflow: hidden;">
        

        <!-- Left Section: Welcome Message -->

        <div class="col-6 d-flex justify-content-center align-items-center text-center">
        
            <img src="img/login.png" alt="">
        </div>

        <!-- Right Section: Signup Form -->
        <div class="col-6 p-5 d-flex flex-column justify-content-center ">
            <div class="form-content" style="width: 500px;">
                <h1 class="mb-4 text-center" style="color: white;">Sign Up</h1>
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" type="text" class="form-control mt-1" v-model="form.name" required
                            autofocus autocomplete="name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" class="form-control mt-1" v-model="form.email" required
                            autocomplete="email" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password"
                            required autocomplete="new-password" style="color: black;" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                            v-model="form.password_confirmation" required autocomplete="new-password"
                            style="color: black;" />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>



                    <div class="d-flex justify-content-center mt-7">
                        <button type="submit" class="btn-grad">
                            Sign Up
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Already have an account?
                            <Link :href="route('login')"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign in</Link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style>
body {
    margin: 0;
    font-family: "Arial", sans-serif;
    background: linear-gradient(to bottom, #492A65
    , #948E99);
    /* color: #fff; */
}

h1 {
    font-size: 2.5rem;
    font-weight: 700;

}

.form-content {
    background: rgba(255, 255, 255, 0.15);
    /* Semi-transparent background */
    border-radius: 15px;
    /* Rounded corners */
    padding: 2rem;
    /* Padding for spacing */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    /* Subtle shadow effect */
    backdrop-filter: blur(10px);
    /* Glass-like blur effect */
    -webkit-backdrop-filter: blur(10px);
    /* For Safari compatibility */
    border: 1px solid rgba(255, 255, 255, 0.3);
    /* Subtle border */
}

/* Button Gradient */
.btn-grad {
    background-color: #492A65;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 30px;
    width: 150px;
}

.btn-grad:hover {
    background-color: #5E3681;

}
</style>
