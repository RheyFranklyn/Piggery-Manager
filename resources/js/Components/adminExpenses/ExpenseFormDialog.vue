<template>
  <Toaster />
  <Dialog :open="open" @update:open="updateOpen" max-width="lg">
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>{{ editingExpense ? 'Edit' : 'Add' }} Expense</DialogTitle>
        <DialogDescription>
          {{ editingExpense ? 'Edit the expense details' : 'Add a new expense' }}.
        </DialogDescription>
      </DialogHeader>

      <!-- Expense Form -->
      <form id="dialogForm" @submit.prevent="submitExpense" enctype="multipart/form-data" class="max-h-[75vh] overflow-y-auto">
        <!-- Pig Select -->
        <FormField name="pig_id">
          <FormItem>
            <FormLabel>Pig</FormLabel>
            <FormControl>
              <Select v-model="form.pig_id" >
                <SelectTrigger>
                  <SelectValue placeholder="Select a pig (optional)" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Pigs</SelectLabel>
                    <SelectItem :value="null">None</SelectItem>
                    <SelectItem v-for="pig in pigs" :key="pig.id" :value="pig.id">
                      Pig #{{ pig.id }}
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </FormControl>
            <FormDescription>Select a pig (optional).</FormDescription>
            <FormMessage v-if="form.errors.pig_id" :message="form.errors.pig_id" />
          </FormItem>
        </FormField>

        <!-- Batch Select -->
        <FormField name="batch_id">
          <FormItem>
            <FormLabel>Batch</FormLabel>
            <FormControl>
              <Select v-model="form.batch_id">
                <SelectTrigger>
                  <SelectValue placeholder="Select a batch (optional)" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Batches</SelectLabel>
                    <SelectItem :value="null">None</SelectItem>
                    <SelectItem v-for="batch in batches" :key="batch.id" :value="batch.id">
                      Batch #{{ batch.id }}
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </FormControl>
            <FormDescription>Select a batch (optional).</FormDescription>
            <FormMessage v-if="form.errors.batch_id" :message="form.errors.batch_id" />
          </FormItem>
        </FormField>

        <!-- Description -->
        <FormField name="description">
          <FormItem>
            <FormLabel>Description</FormLabel>
            <FormControl>
              <Input v-model="form.description" placeholder="Description of expense" />
            </FormControl>
            <FormDescription>Provide a brief description of the expense.</FormDescription>
            <FormMessage v-if="form.errors.description" :message="form.errors.description" />
          </FormItem>
        </FormField>

        <!-- Amount -->
        <FormField name="amount">
          <FormItem>
            <FormLabel>Amount</FormLabel>
            <FormControl>
              <Input type="number" v-model="form.amount" placeholder="Amount" />
            </FormControl>
            <FormDescription>The total amount of the expense.</FormDescription>
            <FormMessage v-if="form.errors.amount" :message="form.errors.amount" />
          </FormItem>
        </FormField>

        <!-- Category -->
        <FormField name="category">
          <FormItem>
            <FormLabel>Category</FormLabel>
            <FormControl>
              <Select v-model="form.category">
                <SelectTrigger>
                  <SelectValue placeholder="Select a category" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Expense Categories</SelectLabel>
                    <SelectItem value="Feed">Feed</SelectItem>
                    <SelectItem value="Medicine">Medicine</SelectItem>
                    <SelectItem value="Repair">Repair</SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </FormControl>
            <FormDescription>Select a category for this expense.</FormDescription>
            <FormMessage v-if="form.errors.category" :message="form.errors.category" />
          </FormItem>
        </FormField>

        <!-- Date -->
        <FormField name="date">
          <FormItem>
            <FormLabel>Date</FormLabel>
            <FormControl>
              <Input type="date" v-model="form.date" placeholder="Date of expense" />
            </FormControl>
            <FormDescription>When the expense was incurred.</FormDescription>
            <FormMessage v-if="form.errors.date" :message="form.errors.date" />
          </FormItem>
        </FormField>

        <!-- Is Recurring -->
        <FormField name="is_recurring">
          <FormItem>
          <div class="flex items-center mt-2 mb-2 space-x-2">
            <FormControl>
              <Checkbox v-model="form.is_recurring" id="isRecurring" />
            </FormControl>
            <FormLabel for="isRecurring">Is Recurring?</FormLabel>
          </div>
          </FormItem>
        </FormField>

        <!-- Recurrence Type (only if recurring) -->
         <FormField name="recurrence_type" v-if="form.is_recurring">
          <FormItem>
            <FormLabel>Recurrence Type</FormLabel>
            <FormControl>
              <Select v-model="form.recurrence_type">
                <SelectTrigger>
                  <SelectValue placeholder="Select a type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel value="null">Select frequency</SelectLabel>
                    <SelectItem value="daily">daily</SelectItem>
                    <SelectItem value="weekly">weekly</SelectItem>
                    <SelectItem value="monthly">monthly</SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </FormControl>
            <FormDescription>Select a Recurrence Type (optional).</FormDescription>
            <FormMessage v-if="form.errors.recurrence_type" :message="form.errors.recurrence_type" />
          </FormItem>
        </FormField>

        <!-- Receipt Upload -->
        <FormField name="receipt">
          <FormItem>
            <FormLabel>Upload Receipt (optional)</FormLabel>
            <FormControl>
              <Input type="file" @change="handleFileUpload" accept="image/*" />
            </FormControl>
            <FormMessage v-if="form.errors.receipt" :message="form.errors.receipt" />
          </FormItem>
        </FormField>
      </form>

      <!-- Footer -->
      <DialogFooter>
        <Button variant="secondary" @click="closeDialog">Cancel</Button>
        <Button type="submit" form="dialogForm" :disabled="LoadingAddSaving">
          <template v-if="LoadingAddSaving">
            <Loader2 class="w-4 h-4 mr-2 animate-spin" />
            Saving...
          </template>
          <template v-else>
            {{ editingExpense ? 'Save Changes' : 'Add Expense' }}
          </template>
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>


<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Loader2 } from 'lucide-vue-next'

import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/Components/ui/dialog'

import {
  FormField,
  FormItem,
  FormLabel,
  FormControl,
  FormDescription,
  FormMessage,
} from '@/Components/ui/form'

import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select' 

import { Checkbox } from '@/components/ui/checkbox'
import { useToast } from '@/Components/ui/toast/use-toast'
import Toaster from '@/Components/ui/toast/Toaster.vue'



const props = defineProps<{
  open: boolean
  editingExpense: Expense | null
  pigs: Array<{ id: number }> 
  batches: Array<{ id: number }>
}>()

const emit = defineEmits(['update:open', 'close-dialog', 'add-expense', 'update-expense'])

const { toast } = useToast()

const form = useForm({
  pig_id: null,
  batch_id: null,
  description: '',
  amount: '',
  category: '',
  date: '',
  is_recurring: false,
  recurrence_type: null,
  receipt: null,
})

watch(
  () => props.editingExpense,
  (expense) => {
    if (expense) {
      form.pig_id = expense.pig_id ?? null
      form.batch_id = expense.batch_id ?? null
      form.description = expense.description
      form.amount = expense.amount.toString()
      form.category = expense.category
      form.date = expense.date
      form.is_recurring = expense.is_recurring ?? false
      form.recurrence_type = expense.recurrence_type ?? null
      form.receipt = null
    } else {
      form.reset()
    }
  },
  { immediate: true }
)

const LoadingAddSaving = ref(false)

function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    form.receipt = target.files[0]
  }
}

const submitExpense = () => {
  LoadingAddSaving.value = true

  // Validate required fields
  if (!form.description || !form.amount || !form.category || !form.date ) {
    LoadingAddSaving.value = false
    toast({
      title: 'Validation Error',
      description: 'Please fill all required fields.',
      class: 'bg-red-400 text-white border border-red-500',
    })
    return
  }

  if (props.editingExpense) {
  form.put(`/admin/expenses/${props.editingExpense.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      emit('close-dialog')
      emit('update-expense', {
        id: props.editingExpense.id,
        ...form.data(),
      })
      toast({
        title: 'Expense Updated',
        description: `Expense "${form.description}" updated successfully.`,
      })
    },
    onError: () => {
      const errors = form.errors as Record<string, string>
      console.error(errors)
      toast({
        title: 'Update Failed',
        description: errors,
        class: 'bg-red-500 text-white border border-red-600',
      })
    },
    onFinish: () => {
      LoadingAddSaving.value = false
    },
  })
} else {
  form.post(`/Admin-Expenses`, {
    preserveScroll: true,
    onSuccess: () => {
      emit('close-dialog')
      emit('add-expense', form.data())
      toast({
        title: 'Expense Added',
        description: `Expense "${form.description}" added successfully.`,
      })
    },
    onError: () => {
      const errors = form.errors as Record<string, string>
      console.error(errors)
      toast({
        title: 'Creation Failed',
        description: errors,
        class: 'bg-red-500 text-white border border-red-600',
      })
    },
    onFinish: () => {
      LoadingAddSaving.value = false
    },
  })
}
}

const closeDialog = () => {
  emit('update:open', false)
}

const updateOpen = (value: boolean) => {
  emit('update:open', value)
}

interface Expense {
  id: number
  pig_id?: number | null
  batch_id?: number | null
  description: string
  amount: number
  category: string
  date: string
  is_recurring?: boolean
  recurrence_type?: string | null
  receipt?: File | null
}


</script>
