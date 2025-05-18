<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import Toaster from '@/Components/ui/toast/Toaster.vue'
import { Head } from '@inertiajs/vue3'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/solid'
import { router as Inertia } from '@inertiajs/vue3'// put this if you use : Inertia.delete(`/Admin-Pigs/${id}`)
import { Search } from 'lucide-vue-next' //for search icon

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

//==============================================================================

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

//==============================================================================

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

//==============================================================================
// for fetching data from database
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

//==============================================================================

// Function to format the date in a human-readable format
function formatDate(date: string): string {
  const options: { year: 'numeric'; month: 'long'; day: 'numeric' } = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }

  return new Date(date).toLocaleDateString('en-US', options)
}

//==============================================================================

//edit functionality
//initializing
const editDialogOpen = ref(false)
const editingPig = ref<Pig | null>(null)
const isEditSaving = ref(false) //to disabled the save changes  btn once clicked

//must do this first in populating form
const updateForm = useForm({
  penNumber: '',
  expectedSellDate: '',
  currentWeight: '',
})

const openEditDialog = (pig: Pig) => {
  editingPig.value = { ...pig } // clone to avoid mutating original / the data in a specific row based ehere u click it, this like a flag for submut edit cuz if way sud siya return siya dritso sa submitedit

  // When the user clicks "Edit", populate the form with the selected pig's data:
  updateForm.penNumber = String(pig.pen_number)
  updateForm.expectedSellDate = pig.expected_sell_date
  updateForm.currentWeight = pig.current_weight
  
  editDialogOpen.value = true
}

const editPig = (pig: Pig) => {//function to be called nga naas edit icon para moshow ag dialog for edit
  openEditDialog(pig)
}

const submitEdit = () => {
  if (!editingPig.value) return

  isEditSaving.value = true // disabled save changes btn in edit dialog

  updateForm.put(`/Admin-Pigs/${editingPig.value.id}`, {
    preserveScroll: true, //keep the scroll position exactly where it was. instead of scrolling up like the usual or when sending a request or reload 
    onSuccess: () => {
      toast({
        title: 'Updated!',
        description: 'Pig details have been updated successfully.',
      })
      editDialogOpen.value = false
    },
    onError: () => {
      const errors = updateForm.errors as Record<string, string>
      console.log(errors)
      toast({
        title: 'Error',
        description: 'Something went wrong while updating.',
        class: 'bg-red-400 text-white border border-red-500',
      })
    },
    onFinish: () => {
      isEditSaving.value = false // Re-enable the button
    },
  })
}


//==============================================================================

//delete functionality
// Delete confirmation dialog logic Initialization
const confirmDialogOpen = ref(false)
const pendingDeleteId = ref<number | null>(null)
const isDeleting = ref(false) //to disabled the delete btn once clicked

//use on delete icon
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

//==============================================================================

//Search functionality ,dynamically.
const searchQuery = ref('') //for v-model to automatically update or show what you searched. which putted in search input

// Define a computed property that returns a filtered list of pigs based on the search query
const filteredPigs = computed(() => {

  const query = searchQuery.value.toLowerCase().trim() // Convert the user's search input to lowercase and remove leading/trailing spaces

  if (!query) return props.pigs  // If the search query is empty, return the full list of pigs without filtering

   // Otherwise, filter the pigs list based on whether any column includes the search query
  return props.pigs.filter((pig) => {

    // For each pig, check if any of its relevant properties contain the search text.
    // Convert all values to strings and lowercase so we can compare uniformly.
    return (
      pig.pig_id.toLowerCase().includes(query) ||  // Check if the pig's ID includes the query
      pig.pen_number.toString().includes(query) || // Check if the pen number includes the query (converted to string)
      pig.breed.toLowerCase().includes(query) || // Check if the breed includes the query
      formatDate(pig.date_bought).toLowerCase().includes(query) || // Check if the formatted date bought includes the query
      formatDate(pig.expected_sell_date).toLowerCase().includes(query) || // Check if the formatted expected sell date includes the query
      pig.starting_weight.toString().includes(query) || // Check if the starting weight includes the query
      pig.current_weight.toString().includes(query) || // Check if the current weight includes the query
      pig.cost.toString().includes(query) // Check if the cost includes the query
    )
  })
})

//==============================================================================
// Table sorting

//Expected sell date ASC sort

// This variable holds the name of the column currently being used for sorting (e.g., "expected_sell_date")
// Initially it's empty, meaning no column is sorted yet
const sortBy = ref('')

// This variable determines whether sorting is in ascending (true) or descending (false) order
const sortAsc = ref(true)


// This function is triggered when the user clicks on a table header to sort by that column
function toggleSort(column: string) {
  // If the clicked column is the same as the current one being sorted...
  if (sortBy.value === column) {
    // ...then toggle the sort direction (asc -> desc, desc -> asc)
    sortAsc.value = !sortAsc.value
  } else {
    // If it's a new column, set it as the active column to sort by
    sortBy.value = column

    // Default the sort direction to ascending when a new column is selected
    sortAsc.value = true
  }
}

// This computed property returns the list of pigs sorted according to the selected column and direction
const sortedPigs = computed(() => {
  // Make a shallow copy of the filtered pigs (so we don’t mutate the original array)
  return [...filteredPigs.value].sort((a, b) => {
    
    // Check if we're currently sorting by 'expected_sell_date'
    if (sortBy.value === 'expected_sell_date') {
      //get the first two data then campare the two, then if it finished comparingit get the next two data again and so-on until it gets all the data and got compared
      // Convert both expected sell dates into timestamps (numbers) for accurate comparison
      const dateA = new Date(a.expected_sell_date).getTime()
      const dateB = new Date(b.expected_sell_date).getTime()

      // If sortAsc is true, return the difference (asc)
      // If sortAsc is false, flip the order (desc)
      return sortAsc.value ? dateA - dateB : dateB - dateA //If a - b results in a positive number, it means "a" should come after "b".then vice versa na
    }

    // Sort by current_weight (e.g., "15 kg")
    if (sortBy.value === 'current_weight') {
      const weightA = parseFloat(a.current_weight) // Extract number from string
      const weightB = parseFloat(b.current_weight)

      return sortAsc.value ? weightA - weightB : weightB - weightA//If a - b results in a positive number, it means "a" should come after "b".then vice versa na
    }

    //sort by date_bought
    if (sortBy.value === 'date_bought') {
      const dateBoughtA = new Date(a.date_bought).getTime()
      const dateBoughtB = new Date(b.date_bought).getTime()

      return sortAsc.value ? dateBoughtA - dateBoughtB : dateBoughtB - dateBoughtA//If a - b results in a positive number, it means "a" should come after "b".then vice versa na
    }

    //sort by PenNumber
    if (sortBy.value === 'penNum') {
      const penA = a.pen_number
      const penB = b.pen_number

      return sortAsc.value ? penA - penB : penB - penA //If a - b results in a positive number, it means "a" should come after "b".then vice versa na
    }

    //sort by pig-id
    // Sort by pig_id (e.g., "PIG-01", "PIG-2")
    if (sortBy.value === "pig_id") {
      // Extract the numeric part from pig_id and convert to integer
      const pig_idA = parseInt(a.pig_id.split('-')[1], 10) || 0;// a.pig_id.split('-')[1]: This splits the pig_id string at the dash (-) and gets the part after it (e.g., for "PIG-01", it gets "01").
      const pig_idB = parseInt(b.pig_id.split('-')[1], 10) || 0;

      // Ascending or descending based on sortAsc
      return sortAsc.value ? pig_idA - pig_idB : pig_idB - pig_idA;//If a - b results in a positive number, it means "a" should come after "b".then vice versa na
    }

    // If no sort column matches, return 0 (no sorting applied)
    return 0
  })
})


</script>

<template>
  <Head title="| All Pigs" />
  <Toaster />
    
   <!-- Confirmation Dialog for delete-->
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

  
  <Dialog>
    <div class="flex items-center justify-between gap-4">
      <!-- search input -->
       <div class="relative w-full max-w-sm items-center">
          <Input id="search" v-model="searchQuery" type="text" placeholder="Search..." class="pl-10" />
          <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
            <Search class="size-6 text-muted-foreground" /> <!-- the icon -->
          </span>
        </div>

      <!-- add btn -->
      <DialogTrigger as-child>
        <div class="flex justify-end items-end">
          <Button>Add Pig</Button>
        </div>
      </DialogTrigger>

    </div>

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

  <!-- EDit Dialog -->
  <Dialog v-model:open="editDialogOpen">
  <DialogContent>
    <DialogHeader>
      <DialogTitle>Edit Pig</DialogTitle>
      <DialogDescription>Update the pig’s details.</DialogDescription>
    </DialogHeader>

    <form @submit.prevent="submitEdit">
      <FormField name="expectedSellDate">
        <FormItem>
          <FormLabel>Expected SellDate</FormLabel>
          <FormControl>
            <Input v-model="updateForm.expectedSellDate" type="date"/>
          </FormControl>
        </FormItem>

        <FormItem name="penNumber">
          <FormLabel>Pen Number</FormLabel>
          <FormControl>
            <Input v-model="updateForm.penNumber" type="number" />
          </FormControl>
        </FormItem>

        <FormItem name="currentWeight">
          <FormLabel>Current Wieght</FormLabel>
          <FormControl>
            <Input v-model="updateForm.currentWeight" type="text" />
          </FormControl>
        </FormItem>

        <!-- Add other fields as needed -->
      </FormField>

      <DialogFooter class="mt-4">
        <Button type="submit" :disabled="isEditSaving">
          <span v-if="isEditSaving">Saving...</span>
          <span v-else>Save Changes</span>
        </Button>
        <Button type="button" variant="secondary" @click="editDialogOpen = false">Cancel</Button>
      </DialogFooter>
    </form>
  </DialogContent>
</Dialog>

<!-- Pig's Table -->
  <div class="border rounded-md overflow-hidden max-w-full">
  <div class="max-h-[75vh] overflow-y-auto"> <!-- to make the table scrollable when it gets to 75vh height -->
    <table class="min-w-full table-fixed text-sm">
      <!-- Sticky Header -->
      <thead class="bg-white sticky top-0 z-10 shadow-sm">
        <tr>
          <th class="px-4 py-2 w-[100px] text-left cursor-pointer select-none" @click="toggleSort('pig_id')"> <!-- class="select-none": If you try to highlight or select the text inside the button, it won’t work. -->
            PIG-ID
            <span v-if="sortBy === 'pig_id'">
              {{ sortAsc ?  '↑' : '↓' }}
            </span>
          </th>
          <th class="px-4 py-2 w-[60px] text-center cursor-pointer select-none" @click="toggleSort('penNum')">
            Pen #
            <span v-if="sortBy === 'penNum'">
              {{ sortAsc ? '↑' : '↓' }}
            </span>
          </th>
          <th class="px-4 py-2 w-[120px] text-left">Breed</th>
          <th class="px-4 py-2 w-[130px] text-left cursor-pointer select-none" @click="toggleSort('date_bought')">
            Date Bought
            <span v-if="sortBy === 'date_bought'">
              {{ sortAsc ? '↑' : '↓' }}
            </span>
          </th>
          <th class="px-4 py-2 w-[170px] text-left cursor-pointer select-none" @click="toggleSort('expected_sell_date')">
            Expected Sell Date
            <span v-if="sortBy === 'expected_sell_date'">
              {{ sortAsc ? '↑' : '↓' }}
            </span>
          </th>
          <th class="px-4 py-2 w-[130px] text-left">Starting Weight</th>
          <th class="px-4 py-2 w-[130px] text-left cursor-pointer select-none" @click="toggleSort('current_weight')">
            Current Weight
            <span v-if="sortBy === 'current_weight'">
              {{ sortAsc ? '↑' : '↓' }}
            </span>
          </th>
          <th class="px-4 py-2 w-[100px] text-right">Cost</th>
          <th class="px-4 py-2 w-[100px] text-right">Action</th>
        </tr>
      </thead>

      <!-- Table Body -->
      <tbody>
        <tr v-for="pig in sortedPigs" :key="pig.id" class="border-t hover:bg-gray-50 transition">
          <td class="px-4 py-2 font-medium">{{ pig.pig_id }}</td>
          <td class="px-4 py-2 text-center">{{ pig.pen_number }}</td>
          <td class="px-4 py-2">{{ pig.breed }}</td>
          <td class="px-4 py-2">{{ formatDate(pig.date_bought) }}</td>
          <td class="px-4 py-2">{{ formatDate(pig.expected_sell_date) }}</td>
          <td class="px-4 py-2">{{ pig.starting_weight }}</td>
          <td class="px-4 py-2">{{ pig.current_weight }}</td>
          <td class="px-4 py-2 text-right">{{ pig.cost }}</td>
          <td class="px-4 py-2 text-right space-x-2">
            <button @click="editPig(pig)" class="text-blue-500 hover:text-blue-700">
              <PencilIcon class="h-5 w-5 inline" />
            </button>

            <Button @click="askDelete(pig.id)" size="icon" variant="ghost">
              <TrashIcon class="w-4 h-4 text-red-500" />
            </Button>
          </td>
        </tr>

        <!-- No Data Fallback -->
        <tr v-if="filteredPigs.length === 0">
          <td colspan="9" class="text-center text-muted-foreground text-red-800 py-4">
            No records found.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="text-center text-gray-500">A list of your Pigs</div>


  <!--Table -->
  <!-- <div class="max-h-[75vh] overflow-y-auto">
  <Table>
    <TableCaption>A list of your Pigs.</TableCaption>
    <TableHeader >
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
    <TableBody> -->

      <!--If there are matching pigs or naay data result, render them -->
      <!-- <TableRow v-for="pig in filteredPigs" :key="pig.id">
        <TableCell class="font-medium">{{pig.pig_id}}</TableCell>
        <TableCell>{{pig.pen_number}}</TableCell>
        <TableCell>{{pig.breed}}</TableCell>
        <TableCell>{{ formatDate(pig.date_bought) }}</TableCell>
        <TableCell>{{ formatDate(pig.expected_sell_date) }}</TableCell>
        <TableCell>{{pig.starting_weight}}</TableCell>
        <TableCell>{{pig.current_weight}}</TableCell>
        <TableCell class="text-right">{{ pig.cost }}</TableCell>
        <TableCell class="text-right"> -->
          <!-- edit icon -->
          <!-- <button @click="editPig(pig)" class="text-blue-500 hover:text-blue-700">
            <PencilIcon class="h-5 w-5" />
          </button>  -->

          <!-- Delete Icon -->
          <!-- <Button @click="askDelete(pig.id)" size="icon" variant="ghost">
            <TrashIcon class="w-4 h-4 text-red-500" />
          </Button>
        </TableCell>
      </TableRow> -->

       <!-- Show this row when no pigs match the search -->
      <!-- <TableRow v-if="filteredPigs.length === 0">
        <TableCell colspan="9" class="text-center text-muted-foreground text-red-800">
          No records found.
        </TableCell>
      </TableRow>

    </TableBody>
  </Table>
  </div>  -->
</template>
