<template>
    <div ref="templateRef" class="flex flex-col bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 gap-6">
        <div class="text-xl font-light text-gray-500 flex flex-row justify-between">
            <span>
                {{ title }}
            </span>
            <span>
                <Icon @click="downloadTemplate" icon="mingcute:download-line" width="24" height="24" class="cursor-pointer mx-2"/>
            </span>
        </div>
        <div>
            <Doughnut :data="data" :options="options"/>
        </div>
    </div>
</template>
<script setup>
import {
    Chart as ChartJS,
    ArcElement, 
    Tooltip, 
    Legend
} from 'chart.js'
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs'
import html2canvas from 'html2canvas';
import { ref } from 'vue';
import { camelize } from '@/Utils/globalFunctions';
import { Icon } from '@iconify/vue';


const templateRef = ref(null);

ChartJS.register(
    ArcElement, 
    Tooltip, 
    Legend
)
const props = defineProps({
    dataSets: Object,
    title: String,
});
const data = computed(() => (
    props.dataSets
)
)

const options = {
    responsive: true,
    maintainAspectRatio: false,
}

const downloadTemplate = () => {
    html2canvas(templateRef.value).then(canvas => {
        const url = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.href = url;
        link.download = camelize(props.title) + '.png';
        link.click();
    });
}

</script>
