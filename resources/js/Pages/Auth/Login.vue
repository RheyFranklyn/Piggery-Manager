<script lang="ts">
export const description = 'A login page with form and image.'
</script>

<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/components/ui/label';

// import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
// import InputLabel from '@/Components/InputLabel.vue';
// import PrimaryButton from '@/Components/PrimaryButton.vue';
// import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineOptions({ layout: GuestLayout }); // custom layout. to ensure login is under Guestlayout. so, no need to put GuestLayout tag after template tag

const props = defineProps({
    canResetPassword: {
        type: Boolean,
        default: true,
    },
    status: {
        type: String,
    },
    
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

//console.log('canResetPassword:', props.canResetPassword);
</script>

<template>
    <Head title="| Login"/>
    <div class="flex flex-col gap-6">
        <Card class="overflow-hidden">
            <CardContent class="grid p-0 md:grid-cols-2">
                <form @submit.prevent="submit" class="p-6 md:p-8">
                    <div class="flex flex-col gap-6">
                        <div class="flex flex-col items-center text-center">
                            <h1 class="text-2xl font-bold">
                                Welcome back
                            </h1>
                            <p class="text-balance text-muted-foreground">
                                Login to your account
                            </p>
                        </div>
                        <div class="grid gap-2">
                            <Label for="email" value="Email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    placeholder="m@example.com"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                        <div class="grid gap-2">
                            <div class="flex items-center">
                                <Label for="password" value="Password">Password</Label>
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="ml-auto text-sm underline-offset-2 hover:underline"
                                >
                                Forgot your password?
                            </Link>
                            </div>
                            <Input 
                                id="password" 
                                type="password" 
                                required 
                                v-model="form.password"
                                autocomplete="current-password"
                                />

                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>
                        <Button 
                            class="w-full"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                        Login
                        </Button>
                        <div class="relative text-sm text-center after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-border">
                            <span class="relative z-10 px-2 bg-background text-muted-foreground">
                                Or continue with
                            </span>
                        </div>
                        <div class="grid gap-4">
                            <Button variant="outline" class="w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21.35 11.1H12v2.9h5.3c-.7 2-2.6 3.4-5.3 3.4-3.2 0-5.8-2.6-5.8-5.8s2.6-5.8 5.8-5.8c1.5 0 2.9.6 3.9 1.5l2.1-2.1C16.9 3.6 14.6 2.5 12 2.5 6.7 2.5 2.5 6.7 2.5 12S6.7 21.5 12 21.5c5.2 0 9.5-4.3 9.5-9.5 0-.6-.1-1.3-.2-1.9z"/>
                                </svg>
                                <span class="sr-only">Login with Google</span>
                            </Button>
                        </div>
                    </div>
                </form>
                <div class="relative hidden bg-muted md:block">
                    <img
                        src="images/Logo.jpg"
                        alt="Image"
                        class="absolute inset-0 h-full w-full object-cover dark:brightness-[0.2] dark:grayscale"
                    >
                </div>
            </CardContent>
        </Card>
        <div class="text-balance text-center text-xs text-muted-foreground [&_a]:underline [&_a]:underline-offset-4 hover:[&_a]:text-primary">
        By clicking continue, you agree to our <a href="#">Terms of Service</a>
        and <a href="#">Privacy Policy</a>.
        </div>
    </div>
</template>



<!-- <script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full mt-1"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full mt-1"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="text-sm text-gray-600 ms-2"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template> -->
