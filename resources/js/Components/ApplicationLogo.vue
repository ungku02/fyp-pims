<template>
    <img :src="asset('img/logo4.png')" alt="" style="height: 100px; width: 150px; margin-top: 0; padding: 0;" >
</template>

<script>
export default {
    methods: {
        asset(path) {
            return `/${path}`;
        }
    }
}
</script>
