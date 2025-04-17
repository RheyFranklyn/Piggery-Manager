<!-- resources/js/Components/BusinessNameDisplay.vue -->
<script setup lang="ts">
import { GalleryVerticalEnd } from 'lucide-vue-next'

const business = {
  name: 'Piggery Management',
  logo: 'images/Logo.png',
  plan: 'Enterprise',
  url: 'http://127.0.0.1:8000/dashboard',
}
</script>

<template>
  <div class="flex items-center gap-3 p-2">
    <a :href="business.url" class="flex aspect-square size-8 items-center justify-center rounded-lg ">
      <img :src="business.logo" alt="Business Logo" class="rounded-lg object-cover size-8" />
    </a>
    <div class="text-sm leading-tight">
      <div class="font-semibold text-sidebar-foreground">
        {{ business.name }}
      </div>
      <div class="text-xs text-muted-foreground">
        {{ business.plan }}
      </div>
    </div>
  </div>
</template>
