<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card d-flex flex-column shadow" style="position: relative; height: 100%;">
                    <!-- <img :src="imageSrc" class="card-img-top" :alt="title"> -->
                    <div class="card-body">
                        <h5 class="card-title" style="font-weight: bold;">{{ title }}</h5>

                        <div class="d-flex mb-2">
                            <p class="card-text">{{ description }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    link: {
        type: String,
        required: true
    }
});
</script>

<style scoped>
.btn:hover {
    background-color: #64676c;
    color: whitesmoke;
}

.card {
    padding: 1rem; /* Add padding inside the card */
    display: flex;
    flex-direction: column;
}

.card-body {
    flex: 1; /* Allow the card body to grow and take space */
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Space between title, text, and button */
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional hover effect */
    transform: translateY(-2px); /* Slightly lift the card on hover */
    cursor: pointer;
}

</style>
