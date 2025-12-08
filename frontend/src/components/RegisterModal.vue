<script setup lang="ts">
import { ref } from 'vue'
import { apiService } from '@/services/api'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleSubmit = async () => {
  if (!email.value || !password.value) {
    error.value = 'Veuillez remplir tous les champs.'
    return
  }

  loading.value = true
  error.value = ''

  try {
    const response = await apiService.createUser({
      email: email.value,
      password: password.value,
    })

    if (response.error) {
      error.value = response.error.message
    } else if (response.data) {
      emit('success')
      closeModal()
    } else {
      error.value = 'Une erreur inconnue est survenue.'
    }
  } catch {
    error.value = 'Une erreur inattendue est survenue.'
  } finally {
    loading.value = false
  }
}

const closeModal = () => {
  email.value = ''
  password.value = ''
  error.value = ''
  emit('close')
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>

        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6">
          <button
            @click="closeModal"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors"
            aria-label="Fermer"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
          <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Création de compte</h2>

            <form @submit.prevent="handleSubmit" class="space-y-4">
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                  Email
                </label>
                <input
                  v-model="email"
                  type="email"
                  id="email"
                  :disabled="loading"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  placeholder="exemple@email.com"
                  required
                />
              </div>

              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                  Mot de passe
                </label>
                <input
                  v-model="password"
                  type="password"
                  id="password"
                  :disabled="loading"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  placeholder="••••••••"
                  required
                />
              </div>

              <div
                v-if="error"
                role="alert"
                aria-live="polite"
                class="p-3 border border-red-300 bg-red-100 text-red-700 rounded text-sm"
              >
                {{ error }}
              </div>

              <div class="flex gap-3 pt-2">
                <button
                  type="button"
                  @click="closeModal"
                  :disabled="loading"
                  class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Annuler
                </button>
                <button
                  type="submit"
                  :disabled="loading"
                  class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ loading ? 'Connexion...' : 'Valider' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped></style>
