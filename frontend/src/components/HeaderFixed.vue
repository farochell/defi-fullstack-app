<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps<{
  title?: string
  logoSrc?: string
  showLogin?: boolean
}>()

const emit = defineEmits<{
  (e: 'login'): void
}>()

</script>

<template>
  <header class="fixed top-0 left-0 w-full bg-blue-900 text-white shadow-md z-50">
    <div class="max-w-6xl mx-auto flex items-center justify-between px-6 py-4 relative">
      <!-- Logo à gauche -->
      <div class="flex items-center space-x-2 relative z-10">
        <img v-if="logoSrc" :src="logoSrc" alt="Logo" class="h-8 w-auto" />
        <span class="font-semibold text-lg">{{ title }}</span>
      </div>

      <!-- Slot centré -->
      <div class="absolute left-1/2 transform -translate-x-1/2 z-0">
        <slot name="center"></slot>
      </div>

      <!-- Bouton login à droite -->
      <div class="flex-shrink-0 relative z-10">
        <button
          v-if="showLogin"
          @click="onLogin"
          class="bg-white text-blue-600 font-medium px-4 py-1 rounded hover:bg-gray-100 transition-colors"
        >
          Se connecter
        </button>
      </div>
    </div>
  </header>
</template>

<style scoped></style>
