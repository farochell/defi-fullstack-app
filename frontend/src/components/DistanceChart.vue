<script setup lang="ts">
import { onMounted, onBeforeUnmount, watch, ref } from 'vue'
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend,
} from 'chart.js'

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend)

const CHART_COLORS = {
  fret: {
    background: 'rgba(54, 162, 235, 0.7)',
    border: 'rgba(54, 162, 235, 1)',
  },
  passager: {
    background: 'rgba(255, 99, 132, 0.7)',
    border: 'rgba(255, 99, 132, 1)',
  },
  default: {
    background: 'rgba(201, 203, 207, 0.7)',
    border: 'rgba(201, 203, 207, 1)',
  },
} as const

const props = defineProps<{
  data: {
    from: string
    to: string
    groupBy: 'day' | 'month' | 'year' | 'none'
    items: {
      analyticCode: 'fret' | 'passager'
      totalDistanceKm: number
      periodStart: string
      periodEnd: string
      group?: string | null
    }[]
  }
}>()

const canvasRef = ref<HTMLCanvasElement | null>(null)
let chart: Chart | null = null

const getColor = (code: string) => {
  const normalizedCode = code.toLowerCase()
  return CHART_COLORS[normalizedCode as keyof typeof CHART_COLORS] || CHART_COLORS.default
}

const buildChart = () => {
  if (!canvasRef.value) return

  try {
    if (chart) {
      chart.destroy()
      chart = null
    }

    const items = props.data.items ?? []

    if (items.length === 0) {
      return
    }

    const labels = items.map((item) => {
      const period =
        item.periodStart === item.periodEnd
          ? item.periodStart
          : `${item.periodStart} - ${item.periodEnd}`

      return `${item.analyticCode} — ${period}`
    })

    const distances = items.map((i) => i.totalDistanceKm)

    const backgroundColors = items.map((i) => getColor(i.analyticCode).background)
    const borderColors = items.map((i) => getColor(i.analyticCode).border)

    chart = new Chart(canvasRef.value, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: 'Distance totale (km)',
            data: distances,
            backgroundColor: backgroundColors,
            borderColor: borderColors,
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: true },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const item = items[ctx.dataIndex]
                if (!item) return ''
                return `${item.analyticCode}: ${item.totalDistanceKm} km`
              },
            },
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Distance (km)' },
          },
          x: {
            title: { display: true, text: 'Période + code analytique' },
          },
        },
      },
    })
  } catch (error) {
    console.error('Error building chart:', error)
  }
}

watch(
  () => props.data,
  () => buildChart(),
  { deep: true }
)

onMounted(() => buildChart())

onBeforeUnmount(() => {
  if (chart) {
    chart.destroy()
    chart = null
  }
})
</script>

<template>
  <div class="w-full p-4 bg-white rounded-lg shadow">
    <div v-if="!data.items || data.items.length === 0" class="text-center py-8 text-gray-500">
      Aucune donnée à afficher pour cette période
    </div>
    <canvas
      v-else
      ref="canvasRef"
      role="img"
      aria-label="Graphique des distances par période et type de trafic"
    ></canvas>
  </div>
</template>
