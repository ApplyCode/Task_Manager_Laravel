<template>
  <div class="flex" :class="dialogBoxClasses">
    <div class="flex items-center" :class="dialogBoxClasses">
      <div class="inline-block rounded-full h-[24px] w-[24px] text-white flex items-center justify-center text-sm font-bold uppercase" :style="{ backgroundColor: stringToColour(props.userName) }" v-tooltip="props.userName">
        {{ props.userName.charAt(0) }}
      </div>
      <div class="bg-gray-200 py-2 px-4 mx-2 rounded-lg sm:max-w-xs max-w-[10rem]">
        <p class="text-sm">{{ text }}</p>
        <p class="text-xs text-gray-500 text-end">{{ timestamp }}</p>
      </div>
    </div>
  </div>
</template>
<script setup>
import { computed, ref } from 'vue';
import { stringToColour } from '@/Utils/globalFunctions';

const props = defineProps({
    userName: {
        type: String,
        required: true,
    },
    text: {
        type: String,
        required: true,
    },
    timestamp: {
        type: String,
        required: true,
    },
    isLeft: {
        type: Boolean,
        default: true,
    },
});

const userName = ref(props.userName);
const text = ref(props.text);
const timestamp = ref((new Date(props.timestamp)).toLocaleString());

const reverse = computed(() => !props.isLeft);
const dialogBoxClasses = computed(() => ({
    'flex-row-reverse': reverse.value,
    'flex-row': !reverse.value,
}));

</script>