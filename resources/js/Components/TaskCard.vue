<template>
    <div class="container text-cneter">
        <div class="row">
            <div class="col">
                <div class="card" style="width: 18rem;height: 12rem;">
                    <!-- <img :src="imageSrc" class="card-img-top" :alt="title"> -->
                    <div class="card-body">
                        <div class="border  rounded p-2 bg-light">
                            <h5 class="card-title">{{ title }}</h5>
                        </div>


                        <div class="d-flex align-self-center">
                            <p class="card-text mb-2">{{ description }}</p>
                        </div>

                        <div>
                            <a :href="link"
                                class="btn bg-gray-600 text-white position-absolute bottom-0 start-0 m-3">Open</a>
                               
                        </div> 
                        <div class="d-flex justify-content-end position-absolute bottom-0 end-0 m-3">

                       <a href="btn bg-warning"> <i class="bi bi-pencil-square me-1"></i></a>
                        <i class="bi bi-trash me-1"></i></div>
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
</style>
