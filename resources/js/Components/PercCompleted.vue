<template>
    <div class="relative" v-tooltip="progressPercentage">
        <div class="progress-bar absolute">
            <div class="progress" :style="{ width: progressPercentage, backgroundColor: progressColor }">
            </div>
        </div>
        <span class="progress-info text-xs absolute">
            {{progressPercentage}}
        </span>
    </div>
</template>


<script setup>
import { ref, computed, watch } from 'vue';

// Props
const props = defineProps({
    progress: {
        type: Number,
        required: true
    },
    projectColor: {
        type: String,
        required: true
    }
});

const projectColor = ref(props.projectColor);

const progress = ref(props.progress);

const progressPercentage = computed(() => {
    if (progress.value < 0) {
        return '0%';
    }
    if (progress.value > 100) {
        return '100%';
    }
    return `${Math.ceil(progress.value)}%`;
});

const progressColor = computed(() => {
    return projectColor.value;
});

watch(() => props.progress, () => {
    progress.value = props.progress;
});
watch(() => props.projectColor, () => {
    projectColor.value = props.projectColor;
});
</script>

<style scoped>
.progress-bar {
    width: 100%;
    height: 36px;
    background-color: transparent;
    overflow: hidden;
    top: -18px
}

.progress {
    height: 100%;
    transition: width 0.3s ease;
}

.progress-info {
    top: -8px;
    width: 100%;
    text-align: center;
}
</style>

