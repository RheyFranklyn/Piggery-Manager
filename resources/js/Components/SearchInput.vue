<template>
  <div class="relative w-full max-w-sm">
    <Input
        class="pl-10"
        type="text"
        placeholder="Search expenses..."
        v-model="modelValue"
    />
    <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
        <Search class="size-6 text-muted-foreground" /> <!-- the icon -->
    </span>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, computed } from 'vue'
import { Search } from 'lucide-vue-next'
import { Input } from '@/Components/ui/input'

const props = defineProps<{ modelValue: string }>()
const emit = defineEmits(['update:modelValue'])

const modelValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})
</script>
