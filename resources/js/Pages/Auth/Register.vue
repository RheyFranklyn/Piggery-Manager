<script setup>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, useForm } from '@inertiajs/vue3'

// Form for adding pigs
const form = useForm({
    penNumber: '',
    dateBought: '',
    cost: '',
    breed: '',
    expectedSellDate: '',
    startingWeight: '',
})

// Submit function
const submit = () => {
    form.post(route('Admin-Pigs.store'), {
        onSuccess: () => {
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Add Pig" />

    <form @submit.prevent="submit">
        <!-- Pen Number -->
        <div class="mt-4">
            <InputLabel for="penNumber" value="Pen Number" />
            <TextInput id="penNumber" type="text" class="mt-1 block w-full" v-model="form.penNumber" required />
            <InputError class="mt-2" :message="form.errors.penNumber" />
        </div>

        <!-- Date Bought -->
        <div class="mt-4">
            <InputLabel for="dateBought" value="Date Bought" />
            <TextInput id="dateBought" type="date" class="mt-1 block w-full" v-model="form.dateBought" required />
            <InputError class="mt-2" :message="form.errors.dateBought" />
        </div>

        <!-- Cost -->
        <div class="mt-4">
            <InputLabel for="cost" value="Cost" />
            <TextInput id="cost" type="number" class="mt-1 block w-full" v-model="form.cost" required />
            <InputError class="mt-2" :message="form.errors.cost" />
        </div>

        <!-- Breed -->
        <div class="mt-4">
            <InputLabel for="breed" value="Breed (optional)" />
            <TextInput id="breed" type="text" class="mt-1 block w-full" v-model="form.breed" />
            <InputError class="mt-2" :message="form.errors.breed" />
        </div>

        <!-- Expected Sell Date -->
        <div class="mt-4">
            <InputLabel for="expectedSellDate" value="Expected Sell Date" />
            <TextInput id="expectedSellDate" type="date" class="mt-1 block w-full" v-model="form.expectedSellDate" required />
            <InputError class="mt-2" :message="form.errors.expectedSellDate" />
        </div>

        <!-- Starting Weight -->
        <div class="mt-4">
            <InputLabel for="startingWeight" value="Starting Weight (kg)" />
            <TextInput
                id="startingWeight"
                type="text"
                class="mt-1 block w-full"
                v-model="form.startingWeight"
                placeholder="e.g. 12.5 kg"
                required
            />
            <InputError class="mt-2" :message="form.errors.startingWeight" />
        </div>

        <!-- Submit -->
        <div class="mt-6 flex justify-end">
            <PrimaryButton :disabled="form.processing" class="ml-4">
                Save Pig
            </PrimaryButton>
        </div>
    </form>
</template>
