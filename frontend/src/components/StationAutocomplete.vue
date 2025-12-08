<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import Fuse from 'fuse.js'
import type { Station } from '@/types/api.ts'

const props = defineProps<{
  modelValue: string
  label: string
  placeholder?: string
  stations: Station[]
}>()

const emit = defineEmits(['update:modelValue'])

const query = ref('')
const showList = ref(false)

const fuse = new Fuse(props.stations, {
  keys: ['shortName', 'longName'],
  threshold: 0.3,
  distance: 100,
  includeScore: true,
})

const filtered = computed(() => {
  if (!query.value) return []
  return fuse.search(query.value).map(r => r.item)
})

const selectStation = (station: Station) => {
  query.value = `${station.shortName} – ${station.longName}`
  emit('update:modelValue', station.shortName)
  showList.value = false
}

watch(
  () => props.modelValue,
  (val) => {
    if (!val) {
      query.value = ''
      return
    }

    const found = props.stations.find(s => s.shortName === val)
    if (found) {
      query.value = `${found.shortName} – ${found.longName}`
    }
  },
  { immediate: true }
)

</script>

<template>
  <div class="mb-4 relative">
    <label class="block text-sm font-medium mb-1">{{ label }}</label>

    <input
      v-model="query"
      @input="showList = true"
      type="text"
      class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      :placeholder="placeholder ?? 'Rechercher une gare…'"
    />

    <ul
      v-if="showList && filtered.length"
      class="absolute z-10 w-full bg-white border rounded-lg shadow max-h-48 overflow-auto"
    >
      <li
        v-for="station in filtered"
        :key="station.id"
        @click="selectStation(station)"
        class="p-2 hover:bg-gray-100 cursor-pointer"
      >
        <strong>{{ station.shortName }}</strong> – {{ station.longName }}
      </li>
    </ul>
  </div>
</template>
