<template>
    <div class="container action cursor-pointer" @click.stop="clicked">
        <span>{{ message }}</span>
    </div>
</template>

<script setup>
const props = defineProps( {
    message: String,
    func: Function,
});

const clicked = () => {
    props.func();
};
</script>
