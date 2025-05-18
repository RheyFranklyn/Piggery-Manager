<template>
  <Head title="| Expense" />
  
  <div class="space-y-4">
    
    <div class="flex items-end justify-between">
      <!-- Search bar -->
      <SearchInput v-model="searchQuery" />

      <!-- Add New Expense Button -->
      <Button @click="openNewExpenseDialog">Add New Expense</Button>
    </div>

    <!-- Expense Table -->
    <ExpenseTable
      :expenses="searchResults"
      @edit-expense="openEditDialog"
      @confirm-delete="askDelete"
    />

  </div>

  <!-- Expense Form Dialog -->
  <ExpenseFormDialog
    :open="editDialogOpen"
    :editingExpense="editingExpense"
    :pigs="pigs" 
    :batches="batches"
    @update:open="editDialogOpen = $event"
    @close-dialog="closeEditDialog"
    @add-expense="addExpense"
    @update-expense="updateExpense"
  /> <!-- the :pigs="pigs" is data/props pass thru index -->

  <!-- Delete Confirmation Dialog -->
  <DeleteConfirmDialog
    :open="confirmDialogOpen"
    :expenseId="pendingDeleteId"
    @update:open="confirmDialogOpen = $event"
    @close-dialog="closeDeleteDialog"
    @delete-expense="deleteExpense"
  />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from '@/Components/ui/toast/use-toast'
// import Toaster from '@/Components/ui/toast/Toaster.vue'
import { Button } from '@/Components/ui/button'
import { usePage } from '@inertiajs/vue3'

// Components
import SearchInput from '@/Components/SearchInput.vue'
import ExpenseTable from '@/Components/adminExpenses/ExpenseTable.vue'
import ExpenseFormDialog from '@/Components/adminExpenses/ExpenseFormDialog.vue'
import DeleteConfirmDialog from '@/Components/adminExpenses/DeleteConfirmDialog.vue'

// Composables
import { useSearch } from '@/composables/useSearch'

// Types
import type { Expense } from '@/Components/adminExpenses/expense'

//to fetch the data or props comming from index controller
const page = usePage()
const pigs = page.props.pigs
const batches = page.props.batches

// All expenses (initial data)
const allExpenses = ref<Expense[]>([
  { id: 1, description: 'Feed', amount: 500, category: 'Feed', date: '2025-05-01' },
  { id: 2, description: 'Vitamins', amount: 300, category: 'Medicine', date: '2025-05-03' },
])

// Search composable
const { searchQuery, searchResults, noResultsFound } = useSearch(allExpenses, [
  'description',
  'category',
  'date',
])

// Dialog states
const editDialogOpen = ref(false)
const confirmDialogOpen = ref(false)
const pendingDeleteId = ref<number | null>(null)
const editingExpense = ref<Expense | null>(null)

// Toast
const { toast } = useToast()

// Dialog handlers
const openNewExpenseDialog = () => {
  editingExpense.value = null
  editDialogOpen.value = true
}

const openEditDialog = (expense: Expense) => {
  editingExpense.value = expense
  editDialogOpen.value = true
}

const closeEditDialog = () => {
  editDialogOpen.value = false
  editingExpense.value = null
}

const askDelete = (expenseId: number) => {
  pendingDeleteId.value = expenseId
  confirmDialogOpen.value = true
}

const closeDeleteDialog = () => {
  confirmDialogOpen.value = false
  pendingDeleteId.value = null
}

// Expense handlers
const addExpense = (newExpense: Expense) => {
  allExpenses.value.push(newExpense)
  toast({
    title: 'Expense Added',
    description: `The expense for ${newExpense.description} was successfully added.`,
  })
}

const updateExpense = (updatedExpense: Expense) => {
  const index = allExpenses.value.findIndex(e => e.id === updatedExpense.id)
  if (index !== -1) allExpenses.value[index] = updatedExpense
  toast({
    title: 'Expense Updated',
    description: `The expense for ${updatedExpense.description} was successfully updated.`,
  })
}

const deleteExpense = (expenseId: number) => {
  allExpenses.value = allExpenses.value.filter(e => e.id !== expenseId)
  toast({
    title: 'Expense Deleted',
    description: 'The expense was successfully deleted.',
  })
}
</script>
