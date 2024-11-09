<template>
    <div>
        <div class="container-fluid d-flex justify-content-end px-2 py-3">
            <button v-if="canRegister" class="btn btn-outline-light me-3" @click="goToRegister">Register</button>
            <button v-if="canLogin" class="btn btn-light me-3" @click="goToLogin">Login</button>
        </div>

        <div class="container text-center ">
  <div class="row align-items-center mt-5">
    <div class="col m-2 " >
     
       <!-- Left column: Image frame -->
       <div class="col  m-2 shadow rounded d-flex justify-content-center align-items-center" style="height: 400px;">
          <div class="image-frame">
            <img src="/img/home.png" alt="Placeholder Image" class="img-fluid rounded" style="height: 400px;">
          </div>
        </div>
    </div>
    <!--Right Column-->
    <div class="col m-2">
      <div class="m-3 p-3">
            <h1 class="mb-4">Welcome to Our Homepage</h1>
            <p>Explore our features and join us today! We're here to help you make the most out of our platform.</p>
          </div>
    </div>
  </div>
</div>
        
    </div>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3';

// Get the props from the Inertia page
const { props } = usePage()
const { canLogin, canRegister } = props

// Methods to handle navigation
const goToLogin = () => {
  router.visit('/login')
}

const goToRegister = () => {
  router.visit('/register')
}
</script>

<style>
html, body {
  width: 100%;
  height: 100%;
}

body {
    background: linear-gradient(-45deg, #000000, #434343, #565555);
    background-size: 400% 400%;
    animation: gradient 10s ease infinite;
}

@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

h1 {
  font-size: 3rem;
  color: #ffffff; /* Inner text color */
  font-weight: bold; /* Makes the text bold */
}


p {
  font-size: 1.2rem;
  color: #b3a9a9;
}

</style>