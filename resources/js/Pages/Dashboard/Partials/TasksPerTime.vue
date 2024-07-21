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
            <Line :data="data" :options="options"/>
        </div>
    </div>
</template>
<script setup>
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'
import { computed } from 'vue';
import { Line } from 'vue-chartjs'
import html2canvas from 'html2canvas';
import { ref } from 'vue';
import { camelize } from '@/Utils/globalFunctions';
import { Icon } from '@iconify/vue';


const templateRef = ref(null);

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)
const props = defineProps({
    dataLabels: Array,
    dataSets: Array,
    title: String,
});
const data = computed(() => (
    {
        labels: props.dataLabels,
        datasets: props.dataSets,
    }
)
)

const options = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                usePointStyle: true,
                padding: 16,
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
            },
        },
        x: {
            display: false,
        }
    },
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
