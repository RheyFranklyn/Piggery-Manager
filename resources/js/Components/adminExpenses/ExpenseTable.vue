<template>
  <!-- Expense Table -->
  <div class="border rounded-md overflow-hidden max-w-full">
    <div class="max-h-[75vh] overflow-y-auto">
      <table class="min-w-full table-fixed text-sm">
        <!-- Sticky Header -->
        <thead class="bg-white sticky top-0 z-10 shadow-sm">
          <tr>
            <th class="px-4 py-2 w-[140px] text-left">Description</th>
            <th class="px-4 py-2 w-[80px] text-center">Amount</th>
            <th class="px-4 py-2 w-[120px] text-left">Category</th>
            <th class="px-4 py-2 w-[140px] text-left">Date</th>
            <th class="px-2 py-2 w-[80px] text-center">Action</th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody>
          <!-- Loop through expenses -->
          <tr
            v-for="expense in props.expenses"
            :key="expense.id"
            class="border-t hover:bg-gray-50 transition"
          >
            <td class="px-4 py-2 font-medium truncate">{{ expense.description }}</td>
            <td class="px-4 py-2 text-center">{{ expense.amount }}</td>
            <td class="px-4 py-2">{{ expense.category }}</td>
            <td class="px-4 py-2">{{ formatDate(expense.date) }}</td>
            <td class="px-2 py-2 text-center space-x-1">
              <button
                @click="editExpense(expense)"
                class="text-blue-500 hover:text-blue-700"
                title="Edit"
              >
                <PencilIcon class="h-4 w-4 inline" />
              </button>
              <Button
                @click="confirmDelete(expense.id)"
                size="icon"
                variant="ghost"
                class="text-red-500 hover:text-red-700"
                title="Delete"
              >
                <TrashIcon class="w-4 h-4" />
              </Button>
            </td>
          </tr>

          <!-- No Data Fallback -->
          <tr v-if="props.expenses.length === 0">
            <td colspan="5" class="text-center text-muted-foreground text-red-600 py-4">
              No matching records found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="text-center text-gray-500 mt-4">A list of your Expenses</div>
</template>


<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'
import { Expense } from '@/Components/adminExpenses/expense'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/solid'

// Props
const props = defineProps<{
  expenses: Expense[]
}>()

// Emit events to parent
const emit = defineEmits()

// Edit expense handler
const editExpense = (expense: Expense) => {
  emit('edit-expense', expense)
}

// Confirm delete handler
const confirmDelete = (id: number) => {
  emit('confirm-delete', id)
}

// Format date function
function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>
