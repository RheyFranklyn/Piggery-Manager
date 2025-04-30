<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import Toaster from '@/Components/ui/toast/Toaster.vue'
import { Head } from '@inertiajs/vue3'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/solid'
import { router as Inertia } from '@inertiajs/vue3'// put this if you use : Inertia.delete(`/Admin-Pigs/${id}`)

// UI components from shadcn
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
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
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table'

// Toast management
const { toast } = useToast()

// Inertia form state
const form = useForm({
  penNumber: '',
  dateBought: '',
  cost: '',
  breed: '',
  expectedSellDate: '',
  startingWeight: '',
})

// For live formatting of kg input
const startingWeightRaw = ref('')
let debounceTimer: ReturnType<typeof setTimeout>

// Watch and format the weight input
watch(startingWeightRaw, (val) => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    if (val && !val.includes('kg')) {
      startingWeightRaw.value = `${val} kg`
    }
  }, 500)
})

// Submit form handler
const submit = () => {
  form.post('/Admin-Pigs', {
    onSuccess: () => {
      toast({
        title: 'Pig Added',
        description: 'Successfully saved pig!',
      })
      form.reset()
    },
    onError: () => {
      const errors = form.errors as Record<string, string>
      console.log(errors.error)
      toast({
        title: 'Error',
        description: errors.error,
        class: 'bg-red-400 text-white border border-red-500',
      })
    },
  })
}

// Pig interface definition
interface Pig {
  id: number
  pig_id: string
  pen_number: number
  date_bought: string
  cost: number
  breed: string
  expected_sell_date: string
  starting_weight: string
  current_weight: string
}

// Accept `pigs` as a prop from parent component
const props = defineProps<{
  pigs: Pig[]
}>()

// Function to format the date in a human-readable format
function formatDate(date: string): string {
  const options: { year: 'numeric'; month: 'long'; day: 'numeric' } = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }

  return new Date(date).toLocaleDateString('en-US', options)
}


// Edit Pig (redirect to edit page)
const editPig = (id: number) => {
  // Redirect to the edit page (make sure the route is correct)
  Inertia.get(`/Admin-Pigs/${id}`)
}


// Delete confirmation dialog logic
const confirmDialogOpen = ref(false)
const pendingDeleteId = ref<number | null>(null)
const isDeleting = ref(false) //to disabled the delete btn once clicked

const askDelete = (id: number) => {
  pendingDeleteId.value = id
  confirmDialogOpen.value = true
}
// Delete Pig
const confirmDelete = () => {

  if (!pendingDeleteId.value) return

  isDeleting.value = true // Disable the button

  Inertia.delete(`/Admin-Pigs/${pendingDeleteId.value}`, {
    onSuccess: () => {
      toast({
        title: 'Deleted!',
        description: 'Pig has been deleted successfully.',
        variant: 'default',
      })
      confirmDialogOpen.value = false
      pendingDeleteId.value = null
    },
    onError: () => {
      toast({
        title: 'Error',
        description: 'Something went wrong.',
        variant: 'destructive',
      })
    },
    onFinish: () => {
      isDeleting.value = false // Re-enable the button
      confirmDialogOpen.value = false
      pendingDeleteId.value = null
    },
    
  })

}
</script>

<template>
  <Head title="| All Pigs" />
  <Toaster />
    
   <!-- Confirmation Dialog -->
  <Dialog v-model:open="confirmDialogOpen">
    <DialogContent class="text-center">
      <DialogHeader>
        <DialogTitle>Are you sure?</DialogTitle>
        <DialogDescription>This action cannot be undone.</DialogDescription>
      </DialogHeader>
      <DialogFooter class="justify-center gap-4 mt-4">
        <Button @click="confirmDelete" :disabled="isDeleting">
          <span v-if="isDeleting">Deleting...</span>
          <span v-else>Delete</span>
        </Button>
        <Button variant="secondary" @click="confirmDialogOpen = false" :disabled="isDeleting">
          Cancel
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>

  <!-- add btn -->
  <Dialog>
    <DialogTrigger as-child>
      <div class="flex justify-end items-end">
        <Button>Add Pig</Button>
      </div>
    </DialogTrigger>

    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Add Pig</DialogTitle>
        <DialogDescription>
          Add pig here. Click save when you're done.
        </DialogDescription>
      </DialogHeader>

      <form id="dialogForm" @submit.prevent="submit">
        <!-- Pen Number -->
        <FormField name="penNumber">
          <FormItem>
            <FormLabel>Pen Number</FormLabel>
            <FormControl>
              <Input type="number" placeholder="Pen Number" v-model="form.penNumber" />
            </FormControl>
            <FormDescription>
              Which pen do you want the pig to place.
            </FormDescription>
            <FormMessage  v-if="form.errors.penNumber" :message="form.errors.penNumber" class="text-red-600"/>
          </FormItem>
        </FormField>

        <!-- Date Bought -->
        <FormField name="dateBought">
          <FormItem>
            <FormLabel>Date Bought</FormLabel>
            <FormControl>
              <Input type="date" placeholder="Date Bought" v-model="form.dateBought" />
            </FormControl>
            <FormDescription>
              When the pig was purchased.
            </FormDescription>
            <FormMessage :message="form.errors.dateBought" />
          </FormItem>
        </FormField>

        <!-- Cost -->
        <FormField name="cost">
          <FormItem>
            <FormLabel>Cost</FormLabel>
            <FormControl>
              <Input type="number" placeholder="Cost" v-model="form.cost" />
            </FormControl>
            <FormDescription>
              Cost of the pig.
            </FormDescription>
            <FormMessage :message="form.errors.cost" />
          </FormItem>
        </FormField>

        <!-- Breed -->
        <FormField name="breed">
          <FormItem>
            <FormLabel>Breed</FormLabel>
            <FormControl>
              <Input type="text" placeholder="Breed" v-model="form.breed" />
            </FormControl>
            <FormDescription>
              Breed of the pig.
            </FormDescription>
            <FormMessage :message="form.errors.breed" />
          </FormItem>
        </FormField>

        <!-- Expected Sell Date -->
        <FormField name="expectedSellDate">
          <FormItem>
            <FormLabel>Expected Sell Date</FormLabel>
            <FormControl>
              <Input type="date" placeholder="Expected Sell Date" v-model="form.expectedSellDate" />
            </FormControl>
            <FormDescription>
              Expected Sell Date of the pig.
            </FormDescription>
            <FormMessage :message="form.errors.expectedSellDate" />
          </FormItem>
        </FormField>

        <!-- Starting Weight -->
        <FormField name="startingWeight">
          <FormItem>
            <FormLabel>Starting Weight</FormLabel>
            <FormControl>
              <Input
                type="text"
                v-model="startingWeightRaw"
                @input="(e) => {
                  const raw = e.target.value.replace(/[^0-9.]/g, '')
                  startingWeightRaw = raw
                  form.startingWeight = raw
                }"
                @blur="() => {
                  if (form.startingWeight && !form.startingWeight.includes('kg')) {
                    const withKg = `${form.startingWeight} kg`
                    form.startingWeight = withKg
                    startingWeightRaw = withKg
                  }
                }"
                @focus="() => {
                  startingWeightRaw = startingWeightRaw.replace(' kg', '')
                  form.startingWeight = startingWeightRaw
                }"
              />
            </FormControl>
            <FormDescription>
              Starting Weight of the pig.
            </FormDescription>
            <FormMessage :message="form.errors.startingWeight" />
          </FormItem>
        </FormField>
      </form>

      <DialogFooter>
        <Button type="submit" form="dialogForm" :disabled="form.processing">
          Save changes
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>

  <!--Table -->
  <Table>
    <TableCaption>A list of your Pigs.</TableCaption>
    <TableHeader>
      <TableRow>
        <TableHead class="w-[100px]">PIG-ID</TableHead>
        <TableHead>Pen #</TableHead>
        <TableHead>Breed</TableHead>
        <TableHead>Date Bought</TableHead>
        <TableHead>Expected SellDate</TableHead>
        <TableHead>Starting Wieght</TableHead>
        <TableHead>Current Wieght</TableHead>
        <TableHead class="text-right">Cost</TableHead>
        <TableHead class="text-right">Action</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <TableRow v-for="(pig) in pigs" :key="pig.id">
        <TableCell class="font-medium">{{pig.pig_id}}</TableCell>
        <TableCell>{{pig.pen_number}}</TableCell>
        <TableCell>{{pig.breed}}</TableCell>
        <TableCell>{{ formatDate(pig.date_bought) }}</TableCell>
        <TableCell>{{ formatDate(pig.expected_sell_date) }}</TableCell>
        <TableCell>{{pig.starting_weight}}</TableCell>
        <TableCell>{{pig.current_weight}}</TableCell>
        <TableCell class="text-right">{{ pig.cost }}</TableCell>
        <TableCell class="text-right">
          <button @click="editPig(pig.id)" class="text-blue-500 hover:text-blue-700">
            <PencilIcon class="h-5 w-5" />
          </button>

          <!-- Delete Icon -->
          <Button @click="askDelete(pig.id)" size="icon" variant="ghost">
            <TrashIcon class="w-4 h-4 text-red-500" />
          </Button>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>
