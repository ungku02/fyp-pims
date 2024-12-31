<template>
    <div class="container text-center">
        <div class="row">
            <div class="col">
                <div class="card" style="width: 18rem;height: 12rem;">
                    <!-- <img :src="imageSrc" class="card-img-top" :alt="title"> -->
                    <div class="card-body">
                        <div class="border  rounded p-2 bg-light">
                            <h5 class="card-title">{{ title }}</h5>
                        </div>


                        <div class="d-flex align-self-center ">
                            <p class="card-text mb-2 card-description">{{ description }}</p>
                        </div>

                        <div>
                            <a :href="link"
                                class="btn bg-gray-600 text-white position-absolute bottom-0 start-0 m-3">Open</a>
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

<style>
.btn:hover {
    background-color: #64676c;
    color: whitesmoke;
}

.card-description {
    height: auto;
    /* Adjust height dynamically */
    overflow: hidden;
    /* Hide overflowing text */
    text-overflow: ellipsis;
    /* Add ellipsis for overflowed text */
    display: -webkit-box;
    /* Use flex container for multiline truncation */
    -webkit-line-clamp: 3;
    /* Limit text to 3 lines */
    -webkit-box-orient: vertical;
    /* Ensure vertical orientation */
    white-space: normal;
    /* Ensure text wraps to the next line */
}
</style>
