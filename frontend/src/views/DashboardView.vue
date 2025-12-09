<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiService } from '@/services/api'
import type { AnalyticDistanceResponse } from '@/types/api'
import HeaderFixed from '@/components/HeaderFixed.vue'
import GetStatsForm from '@/components/GetStatsForm.vue'
import DistanceChart from '@/components/DistanceChart.vue'

const router = useRouter()

const formData = ref({
  from: '',
  to: '',
  groupBy: 'none' as 'none' | 'day' | 'month' | 'year'
})

const statsData = ref<AnalyticDistanceResponse | null>(null)
const loading = ref(false)
const error = ref('')

onMounted(() => {
  apiService.setRouter(router)

  if (!apiService.isAuthenticated()) {
    router.push('/')
  } else {
    loadStats()
  }
})

const loadStats = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await apiService.getStats({
      from: formData.value.from || undefined,
      to: formData.value.to || undefined,
      groupBy: formData.value.groupBy
    })

    if (response.error) {
      error.value = response.error.message
      statsData.value = null
    } else if (response.data) {
      statsData.value = response.data
    }
  } catch {
    error.value = 'Une erreur inattendue est survenue.'
  } finally {
    loading.value = false
  }
}


</script>

<template>
  <div class="min-h-screen  from-blue-50 to-indigo-100">
    <HeaderFixed logoSrc="/logo.svg" title="Tableau de bord" />

    <div class="max-w-6xl mx-auto p-6 mt-24">
      <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-3xl font-bold text-gray-800">Tableau de bord</h1>

        </div>
        <div class="space-y-6">
          <div class="bg-gray-50 p-6 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Statistiques des trajets</h2>
            <p class="text-gray-600 mb-4">
              Consultez les statistiques de vos trajets ferroviaires et filtrez par période.
            </p>
          </div>

          <GetStatsForm
            v-model="formData"
            :disabled="loading"
            @submit="loadStats"
          />

          <div
            v-if="error"
            role="alert"
            aria-live="polite"
            class="p-3 border border-red-300 bg-red-100 text-red-700 rounded"
          >
            {{ error }}
          </div>

          <div v-if="loading" class="text-center py-8">
            <p class="text-gray-600">Chargement des statistiques...</p>
          </div>

          <DistanceChart v-else-if="statsData" :data="statsData" />

          <div v-else-if="!loading && !error" class="text-center py-8 text-gray-500">
            Aucune donnée disponible pour les critères sélectionnés.
          </div>

          <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Calculer un nouveau trajet</h3>
            <p class="text-blue-700 mb-4">
              Retournez à la page d'accueil pour calculer de nouveaux trajets.
            </p>
            <router-link
              to="/"
              class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Aller au calculateur
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
