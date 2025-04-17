<script setup lang="ts">
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table'

import { Button } from '@/Components/ui/button';

// Dialog or modal
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
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/Components/ui/form'

import { Input } from '@/Components/ui/input'
import { toast } from '@/Components/ui/toast'
import { toTypedSchema } from '@vee-validate/zod'
import { h } from 'vue'
import * as z from 'zod'

// Define a validation schema using Zod and convert it for use with vee-validate
const formSchema = toTypedSchema(z.object({
  penNumber: z.string().min(1, 'Pen Number is required'), // At least 1 character
  dateBought: z.string().min(1, 'Date bought is required'), // At least 1 character
}))

// Format date and show values in toast on submit
function onSubmit(values: any) {
  console.log('onSubmit called', values) // ✅ Add this for testing

  const formattedDate = new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: '2-digit',
    year: 'numeric',
  }).format(new Date(values.dateBought))

  values.dateBought = formattedDate

  
  console.log("Formatted values:", values); // Log formatted values
  
  toast({
    title: 'You submitted the following values:',
    description: h(
      'pre',
      { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' },
      h('code', { class: 'text-white' }, JSON.stringify(values, null, 2))
    ),
  })
}
</script>


<template>
  <Head title="| All Pigs" />

  <!-- Dialog or modal for add pigs btn -->
  <Form v-slot="{ handleSubmit }" as="" keep-values :validation-schema="formSchema">
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

        <form id="dialogForm" @submit="handleSubmit($event, onSubmit)">

          <!-- Pen Number -->
          <FormField v-slot="{ componentField }" name="penNumber">
            <FormItem>
              <FormLabel>Pen Number</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Pen Number" v-bind="componentField" />
              </FormControl>
              <FormDescription>
                Which pen do you want the pig to place.
              </FormDescription>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Date bought -->
          <FormField v-slot="{ componentField }" name="dateBought">
            <FormItem>
              <FormLabel>Date bought</FormLabel>
              <FormControl>
                <Input type="date" placeholder="Date bought" v-bind="componentField" />
              </FormControl>
              <FormDescription>
                When the pig was purchased.
              </FormDescription>
              <FormMessage />
            </FormItem>
          </FormField>
        </form>

        <DialogFooter>
          <Button type="submit" form="dialogForm">
            Save changes
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </Form>
  
  <Table>
    <TableCaption>A list of your recent invoices.</TableCaption>
    <TableHeader>
      <TableRow>
        <TableHead class="w-[100px]">
          Invoice
        </TableHead>
        <TableHead>Status</TableHead>
        <TableHead>Method</TableHead>
        <TableHead class="text-right">
          Amount
        </TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <TableRow>
        <TableCell class="font-medium">
          INV001
        </TableCell>
        <TableCell>Paid</TableCell>
        <TableCell>Credit Card</TableCell>
        <TableCell class="text-right">
          $250.00
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>