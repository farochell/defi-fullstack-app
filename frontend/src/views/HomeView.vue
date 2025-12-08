<script setup lang="ts">
import HeaderFixed from '@/components/HeaderFixed.vue'
import LoginModal from '@/components/LoginModal.vue'
import type { DistanceResponse } from '@/types/api.ts'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { apiService } from '@/services/api.ts'
import StationAutocomplete from '@/components/StationAutocomplete.vue'
import stations from '@/data/stations.json'
import RegisterModal from '@/components/RegisterModal.vue'

const router = useRouter()

onMounted(() => {
  apiService.setRouter(router)
})

const TRAFFIC_TYPES = {
  FRET: 'fret',
  PASSAGER: 'passager',
} as const

const fromStationId = ref('')
const toStationId = ref('')
const analyticCode = ref(TRAFFIC_TYPES.FRET)
const error = ref('')
const loading = ref(false)
const result = ref<DistanceResponse | null>(null)
const showLoginModal = ref(false)
const showRegisterModal = ref(false)


const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const resetForm = () => {
  fromStationId.value = ''
  toStationId.value = ''
  analyticCode.value = TRAFFIC_TYPES.FRET
  error.value = ''
  result.value = null
}

const calculateDistance = async () => {
  if (!fromStationId.value || !toStationId.value) {
    error.value = 'Veuillez remplir les deux champs.'
    return
  }

  loading.value = true
  error.value = ''
  result.value = null

  try {
    const response = await apiService.calculateDistance({
      fromStationId: fromStationId.value,
      toStationId: toStationId.value,
      analyticCode: analyticCode.value,
    })

    if (response.error) {
      error.value = response.error.message
    } else if (response.data) {
      result.value = response.data
    } else {
      error.value = 'Une erreur inconnue est survenue.'
    }
  } catch {
    error.value = 'Une erreur inattendue est survenue.'
  } finally {
    loading.value = false
  }
}

const isLoggedIn = ref(apiService.isAuthenticated())
const updateAuthState = () => {
  isLoggedIn.value = apiService.isAuthenticated()
}

const handleLoginClick = () => {
  showLoginModal.value = true
}

const handleRegisterClick = () => {
  showRegisterModal.value = true
}

const handleLoginSuccess = () => {
  updateAuthState()
  router.push('/dashboard')
}

const handleRegisterSuccess = () => {
  showRegisterModal.value = false
  showLoginModal.value = true
}

const closeAllModals = () => {
  showLoginModal.value = false
  showRegisterModal.value = false
}
</script>

<template>
  <HeaderFixed
    logoSrc="/logo.svg"
    title="Calculateur de distance ferroviaire"
    :showLogin="!isLoggedIn"
    @login="handleLoginClick"
  />

  <LoginModal
    :show="showLoginModal"
    @close="closeAllModals"
    @success="handleLoginSuccess"
    @register="handleRegisterClick"
  />

  <RegisterModal
    :show="showRegisterModal"
    @close="closeAllModals"
    @success="handleRegisterSuccess"
  />

  <div class="max-w-2xl mx-auto p-6 mt-24">
    <div class="bg-white/5 rounded-xl p-8 shadow-lg">
      <form @submit.prevent="calculateDistance">
        <StationAutocomplete
          v-model="fromStationId"
          :stations="stations"
          label="Gare de départ"
          placeholder="Ex : Allières, ALLI, Les Avants..."
        />

        <StationAutocomplete
          v-model="toStationId"
          :stations="stations"
          label="Gare d'arrivée"
          placeholder="Ex : Bois-de-Chexbres, BCHX, Belmont-sur-Mx..."
        />
        <div class="mb-4">
          <label for="analyticCode" class="block text-sm font-medium mb-1">Type de trafic</label>
          <select
            v-model="analyticCode"
            id="analyticCode"
            :disabled="loading"
            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <option :value="TRAFFIC_TYPES.FRET">Fret</option>
            <option :value="TRAFFIC_TYPES.PASSAGER">Passager</option>
          </select>
        </div>

        <div class="flex gap-2">
          <button
            type="submit"
            :disabled="loading"
            class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Calcul en cours...' : 'Calculer la distance' }}
          </button>
          <button
            type="button"
            @click="resetForm"
            :disabled="loading"
            class="px-4 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Réinitialiser
          </button>
        </div>
      </form>
      <div
        v-if="error"
        role="alert"
        aria-live="polite"
        class="p-3 border border-red-300 mt-4 bg-red-100 text-red-700 rounded"
      >
        {{ error }}
      </div>
      <div v-if="result" class="mt-4 p-4 bg-green-50 rounded border border-green-200">
        <p><strong>Distance :</strong> {{ result.distanceKm }} km</p>
        <p><strong>Type de trafic :</strong> {{ result.analyticCode }}</p>
        <p><strong>Créé le :</strong> {{ formatDate(result.createdAt) }}</p>

        <p class="mt-2 font-semibold">Liste des stations :</p>
        <ul class="list-disc ml-6">
          <li v-for="station in result.path" :key="station.id">
            {{ station.shortName }} – {{ station.longName }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
