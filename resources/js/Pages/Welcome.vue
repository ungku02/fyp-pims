<template>
    <div style="overflow-y: hidden;">
        <!-- Navbar -->
        <div class="d-flex justify-content-between align-items-center px-4 py-0">
            <!-- Logo -->
            <h1 class="logo m-0 p-0">
                <img src="img/logo4.png" alt="PIMS Logo" style="height: 150px; width: 220px; margin-top: 0;">
            </h1>
            <div>
                <button v-if="canRegister" class="btn btn-grad me-3" @click="goToRegister">
                    Sign Up
                </button>
                <button v-if="canLogin" class="btn btn-grad-outline" @click="goToLogin">
                    Log In
                </button>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="hero-container d-flex align-items-center">
            <!-- Left Side: Content -->
            <div class="hero-content text-light" data-aos="fade-right">
                <h1 class="hero-title">
                    Streamline Projects with Our Management System
                </h1>
                <p class="hero-subtitle">
                    Optimize workflows, enhance collaboration, and make informed
                    decisions with our Project Integration Management System.
                </p>
                <button class="btn btn-grad btn-lg mt-3" style="width: 150px">
                    Learn More
                </button>
            </div>

            <!-- Right Side: Image -->
            <div class="hero-image-container">
                <img src="img/home4.png" alt="Hero Image" class="hero-image" data-aos="fade-left" />
            </div>
        </div>
        <!-- Benefits Section -->
        <div class="benefits-section container d-flex align-items-center justify-content-between">
            <!-- Left: Single Image -->
            <div class="benefits-image-container" data-aos="fade-right">
                <img src="img/home5.png" alt="Learning Benefits" class="benefits-image" />
            </div>

            <!-- Right: Benefits Content -->
            <div class="benefits-content" data-aos="fade-left">
                <h2 class="benefits-title">
                    <span class="highlight">Benefits</span> Project Integration
                    Management System
                </h2>
                <ul class="benefits-list">
                    <li>
                        <i class="bi bi-diagram-3 icon"></i>
                        <div>
                            <h4 class="benefits-item-title">Efficiency</h4>
                            <p>
                                Streamline workflows and improve efficiency
                                across teams.
                            </p>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-people-fill icon"></i>
                        <div>
                            <h4 class="benefits-item-title">
                                Team Collaboration
                            </h4>
                            <p>Enhance team collaboration and communication.</p>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-bar-chart-line icon"></i>
                        <div>
                            <h4 class="benefits-item-title">
                                Support Decision Making
                            </h4>
                            <p>
                                Gain actionable insights for informed
                                decision-making.
                            </p>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-lightbulb icon"></i>
                        <div>
                            <h4 class="benefits-item-title">
                                Resources Management
                            </h4>
                            <p>
                                Optimize resource allocation and project
                                tracking.
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Popular Courses Section -->
        <div class="popular-courses-section container text-center mb-4" data-aos="fade-right">
            <h2 class="course-text">Our System Features</h2>

            <h3 class="mb-5" style="font-size: 20px;">
                the key features of our Project Integration Management System,
                designed to streamline your project management and enhance team
                collaboration.
            </h3>

            <div class="courses-grid d-flex justify-content-between">
                <!-- Course Card 1 -->
                <div class="course-card" data-aos="fade-up">
                    <img src="img/home6.png" class="course-image" />
                    <div class="course-info">
                        <span class="badge category">Kanban Board</span>
                        <h3>Efficient Task Management</h3>
                    </div>
                </div>

                <!-- Course Card 2 -->
                <div class="course-card" data-aos="fade-up" data-aos-delay="100">
                    <img src="img/home7.png" class="course-image" />
                    <div class="course-info">
                        <span class="badge category">Real-time Notifications</span>
                        <h3>Completion & Unavailability Alerts</h3>
                    </div>
                </div>

                <!-- Course Card 3 -->
                <div class="course-card" data-aos="fade-up" data-aos-delay="200">
                    <img src="img/home8.png" class="course-image" />
                    <div class="course-info">
                        <span class="badge category">Custom Calendar</span>
                        <h3>Optimize Your Schedule</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section
        <div class="container stats-section my-5 text-center text-light">
            <div class="row">
                <div class="col-md-4">
                    <h3>50+</h3>
                    <p>Online Courses</p>
                </div>
                <div class="col-md-4">
                    <h3>10k+</h3>
                    <p>Online Students</p>
                </div>
                <div class="col-md-4">
                    <h3>5</h3>
                    <p>Expert Teachers</p>
                </div>
            </div>
        </div> -->

        <!-- Courses Section -->
        <!-- <div class="container courses-section my-5">
            <h2 class="text-center mb-4">Our Popular Courses</h2>
            <div class="row">
                <div class="col-md-4" v-for="book in newArrivals" :key="book.id">
                    <div class="card h-100 shadow">
                        <img :src="book.image" alt="Course Image" class="card-img-top" />
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ book.title }}</h5>
                            <p class="card-text">{{ book.description }}</p>
                            <button class="btn btn-outline-primary">
                                Join Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</template>

<script setup>
import { usePage, router } from "@inertiajs/vue3";
import { onMounted } from "vue";
// Get the props from the Inertia page
const { props } = usePage();
const { canLogin, canRegister } = props;
import AOS from "aos";
import "aos/dist/aos.css";

// Methods to handle navigation
const goToLogin = () => {
    router.visit("/login");
};

const goToRegister = () => {
    router.visit("/register");
};

// Initialize AOS when the component is mounted
onMounted(() => {
    AOS.init({
        duration: 1000, // Animation duration in ms
        offset: 100, // Offset from the top when the animation triggers
        easing: "ease-out", // Smooth easing for animations
        once: false, // Trigger animations only once
    });
});
</script>

<style>
/* General Styles */
body {
    margin: 0;
    font-family: "Arial", sans-serif;
    background: linear-gradient(to bottom, #492A65

    , #948E99);
    color: #fff;
}

/* Navbar */
.logo {
    font-weight: bold;
    font-size: 1.5rem;
}

/* Hero Section */
/* Hero Section Styles */
.hero-container {
    height: 70vh;
    display: flex;
    justify-content: space-between;
    padding: 0 5%;
    margin-top: 30px;
}

.hero-content {
    flex: 1;
    max-width: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: bold;
}

.hero-subtitle {
    margin-top: 15px;
    font-size: 1.2rem;
}

/* Right Side: Image */
.hero-image-container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 15px;
}

.hero-image {
    max-width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 50px;
}

/* Benefits Section */
/* Section Styling */
.benefits-section {
    /* background: rgba(255, 255, 255, 0.2); Semi-transparent white */
    /* backdrop-filter: blur(12px); Glass effect */
    /* -webkit-backdrop-filter: blur(12px); For Safari */
    /* border: 1px solid rgba(255, 255, 255, 0.3); Subtle border for glass effect */
    /* border-radius: 16px; */
    padding: 1rem;
    /* box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); */
    gap: 3rem;
    margin-top: 70px;
}

/* Left Image Styling */
.benefits-image-container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

.benefits-image {
    max-width: 100%;
    height: auto;
    /* border-radius: 12px; */
    /* box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); */
}

/* Right Content Styling */
.benefits-content {
    flex: 1;
    padding-left: 1.5rem;
}

.benefits-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #444;
    margin-bottom: 1.5rem;
}

.highlight {
    color: #aa076b;
}

.benefits-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.benefits-list li {
    display: flex;
    align-items: start;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.benefits-item-title {
    font-size: 1.25rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
    color: #333;
}

.benefits-list p {
    margin: 0;
    color: #666;
}

/* Icon Styling */
.icon {
    font-size: 1.5rem;
    color: #e5e5be;
    flex-shrink: 0;
    border: 1px solid #c56f92;
    background-color: #c56f92;
    border-radius: 50%;
    padding: 10px 15px;
}

/* Popular Courses Section */
.popular-courses-section {
    /* padding:  0; */

    /* color: white; */
    border-radius: 12px;
    width: 100%;
    /* margin: 0 auto;  */
    margin-top: 70px;
}

.popular-courses-section h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #ffffff;
}

.popular-courses-section p {
    font-size: 1rem;
    margin-bottom: 2rem;
    color: #f5f5f5;
}

.courses-grid {
    display: flex;
    gap: 1.5rem;
}

.course-card {
    background: rgba(255, 255, 255, 0.2);
    /* Light transparent background */
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    flex: 1;

    /* Glass-like background effect */
    backdrop-filter: blur(10px);
    /* Blur effect behind the card */
    -webkit-backdrop-filter: blur(10px);
    /* For Safari */
}

.course-image {
    width: 100%;
    /* height: 160px; */
    object-fit: cover;
}

.course-info {
    padding: 1rem;
}

.badge {
    display: inline-block;
    padding: 0.3rem 0.6rem;
    border-radius: 16px;
    font-size: 0.75rem;
    color: #fff;
    background: #c56f92;
    margin-bottom: 0.5rem;
}

.course-info h3 {
    font-size: 1.2rem;
    margin: 0.5rem 0;
    color: #c56f92;
}

.course-details {
    font-size: 0.9rem;
    color: #666;
    margin: 0.5rem 0;
}

.course-footer {
    font-size: 1rem;
    color: #333;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

.price {
    font-weight: bold;
    font-size: 1.1rem;
    color: #ff6200;
}

.instructor-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

/* Stats Section */
.stats-section .row div {
    margin: 20px 0;
}

.stats-section h3 {
    font-size: 2rem;
    font-weight: bold;
}

.stats-section p {
    font-size: 1.1rem;
    color: #e5e5be;
}

/* Courses Section */
.courses-section {
    text-align: center;
}

.courses-section h2 {
    margin-bottom: 30px;
    font-size: 2rem;
    color: #fff;
}

.card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.card-title {
    font-size: 1.2rem;
    color: #1e293b;
    margin-bottom: 15px;
}

.card-text {
    font-size: 1rem;
    color: #64748b;
}

.card .btn-outline-primary {
    border-radius: 20px;
}

.card:hover {
    transform: translateY(-10px);
    transition: 0.3s;
}

/* Button Gradient */
.btn-grad {
    background-image: linear-gradient(to bottom, #654ea3, #eaafc8);
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 30px;
}

.btn-grad:hover {
    background-image: linear-gradient(to right, #654ea3, #eaafc8);
}

.btn-grad-outline {
    background-color: transparent;
    color: #fff;
    border: 2px solid #654ea3;
    padding: 10px 20px;
    border-radius: 30px;
}

.btn-grad-outline:hover {
    background-image: linear-gradient(to right, #654ea3, #eaafc8);
    color: #fff;
}

/* Animation Keyframes */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(50px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Hero Content Animation */
.animate-content {
    animation: fadeIn 1s ease-in-out;
}

/* Hero Image Animation */
.animate-image {
    animation: slideIn 1s ease-in-out;
    animation-delay: 0.5s;
    /* Delay for a staggered effect */
    opacity: 0;
    /* Ensures animation starts hidden */
    animation-fill-mode: forwards;
}

.course-text {
    /* color: #AA076B;  */
    font-weight: bold;
    background-color: #aa076b;
    border-radius: 20px;
    width: 350px;
    margin: 0 auto;
    padding: 10px;
}
</style>
