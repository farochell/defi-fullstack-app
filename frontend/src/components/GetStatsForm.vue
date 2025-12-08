<script setup lang="ts">
import { computed } from 'vue'

type GroupBy = 'none' | 'day' | 'month' | 'year'

interface Model {
  from: string
  to: string
  groupBy: GroupBy
}

const props = defineProps<{
  modelValue: Model
  disabled?: boolean
  labelFrom?: string
  labelTo?: string
  labelGroupBy?: string
  submitLabel?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: Model): void
  (e: 'submit', value: Model): void
}>()

const from = computed({
  get: () => props.modelValue.from,
  set: (val: string) => emit('update:modelValue', { ...props.modelValue, from: val })
})

const to = computed({
  get: () => props.modelValue.to,
  set: (val: string) => emit('update:modelValue', { ...props.modelValue, to: val })
})

const groupBy = computed({
  get: () => props.modelValue.groupBy,
  set: (val: GroupBy) => emit('update:modelValue', { ...props.modelValue, groupBy: val })
})

const GROUP_BY_OPTIONS: { value: GroupBy; label: string }[] = [
  { value: 'none', label: 'Aucun regroupement' },
  { value: 'day', label: 'Jour' },
  { value: 'month', label: 'Mois' },
  { value: 'year', label: 'Année' }
]

const handleSubmit = () => {
  emit('submit', props.modelValue)
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div class="flex flex-col">
      <label class="text-sm font-medium mb-1">
        {{ labelFrom ?? 'Date de début' }}
      </label>
      <input
        type="date"
        :disabled="disabled"
        class="p-2 border border-gray-300 rounded-lg disabled:opacity-50"
        v-model="from"
      />
    </div>

    <div class="flex flex-col">
      <label class="text-sm font-medium mb-1">
        {{ labelTo ?? 'Date de fin' }}
      </label>
      <input
        type="date"
        :disabled="disabled"
        class="p-2 border border-gray-300 rounded-lg disabled:opacity-50"
        v-model="to"
      />
    </div>

    <div class="flex flex-col">
      <label class="text-sm font-medium mb-1">
        {{ labelGroupBy ?? 'Regrouper par' }}
      </label>
      <select
        v-model="groupBy"
        :disabled="disabled"
        class="p-2 border border-gray-300 rounded-lg disabled:opacity-50"
      >
        <option
          v-for="option in GROUP_BY_OPTIONS"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
    </div>

    <button
      type="submit"
      :disabled="disabled"
      class="w-full cursor-pointer mt-4 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
    >
      {{ submitLabel ?? 'Rechercher' }}
    </button>
  </form>
</template>

<style scoped>
</style>
