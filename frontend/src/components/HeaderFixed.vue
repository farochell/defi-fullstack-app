<script setup lang="ts">
import {apiService} from "@/services/api.ts";
import {useRouter} from "vue-router";

const router = useRouter()

defineProps<{
  title?: string
  logoSrc?: string
  showLogin?: boolean
}>()

const emit = defineEmits<{
  (e: 'login'): void
}>()
const handleLogout = () => {
  apiService.logout()
  router.push('/')
}
const handleDashboard = () => {
  router.push('/dashboard')
}
const handleHome = () => {
  router.push('/')
}
</script>

<template>
  <header class="fixed top-0 left-0 w-full bg-blue-900 text-white shadow-md z-50">
    <div class="max-w-6xl mx-auto flex items-center justify-between px-6 py-4 relative">
      <div class="flex items-center space-x-2 relative z-10 cursor-pointer">
        <img v-if="logoSrc" :src="logoSrc" alt="Logo" @click="handleHome" class="h-8 w-auto" />
        <span class="font-semibold text-lg">{{ title }}</span>
      </div>

      <div class="absolute left-1/2 transform -translate-x-1/2 z-0">
        <slot name="center"></slot>
      </div>

      <div class="flex-shrink-0 relative z-10">
        <button
          v-if="showLogin"
          @click="emit('login')"
          class="bg-white cursor-pointer text-blue-600 font-medium px-4 py-1 rounded hover:bg-gray-100 transition-colors"
        >
          Se connecter
        </button>
        <button
          v-if="!showLogin"
          @click="handleDashboard"
          class="bg-white cursor-pointer text-blue-600 font-medium px-4 py-1 rounded hover:bg-gray-100 transition-colors"
        >
          Tableau de bord
        </button>
        <button
          v-if="!showLogin"
          @click="handleLogout"
          class="ml-2 px-4 py-2 cursor-pointer bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
        >
          Se déconnecter
        </button>
      </div>
    </div>
  </header>
</template>

<style scoped></style>
