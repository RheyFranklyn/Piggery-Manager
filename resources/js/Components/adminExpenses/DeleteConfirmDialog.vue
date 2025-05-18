<template>
  <Dialog :open="open" @update:open="updateOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Confirm Deletion</DialogTitle>
        <DialogDescription>
          Are you sure you want to delete this expense? This action cannot be undone.
        </DialogDescription>
      </DialogHeader>

      <DialogFooter>
        <Button @click="closeDialog" variant="outline">Cancel</Button>
        <Button @click="confirmDelete" >Delete</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

// shadcn/ui components
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/Components/ui/dialog'
import { Button } from '@/Components/ui/button'

// Props
const props = defineProps<{
  open: boolean
  expenseId: number | null
}>()

const emit = defineEmits(['update:open', 'close-dialog', 'delete-expense'])

const updateOpen = (value: boolean) => {
  emit('update:open', value)
}

const closeDialog = () => {
  emit('close-dialog')
}

const confirmDelete = () => {
  if (props.expenseId !== null) {
    emit('delete-expense', props.expenseId)
  }
}
</script>
