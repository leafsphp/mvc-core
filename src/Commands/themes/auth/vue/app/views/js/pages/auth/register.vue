<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import { Button } from '@/components/form/button';
import { Input, InputError } from '@/components/form/input';

const form = useForm({
    name: "",
    email: "",
    password: "",
    confirmPassword: "",
});

const submit = (e) => {
    form.post("/auth/register", {
        onFinish: () => form.reset("password", "confirmPassword"),
    });
};
</script>

<template>
    <div
        class="bg-background flex relative z-30 flex-col justify-center w-screen min-h-screen items-stretch sm:items-center sm:py-10">

        <Head title="Sign up" />

        <div
            class="flex relative top-0 z-20 flex-col justify-center items-stretch px-10 py-8 w-full h-screen bg-white border-gray-200 sm:top-auto sm:h-full sm:border sm:rounded-xl sm:max-w-md text-black">
            <div class="flex flex-col sm:mx-auto sm:w-full mb-5 sm:max-w-md items-center text-black">
                <Link href="/">
                <img src="/favicon.ico" alt="Logo" class="size-10" />
                </Link>
                <h1 class="mt-5 mb-1 text-2xl lg:text-3xl font-semibold">
                    Hello there
                </h1>
                <h2 class="text-sm">Create your account</h2>
            </div>

            <form @submit.prevent="submit" class="space-y-4 w-full">
                <div>
                    <Input id="name" type="text" name="name" placeholder="Name"
                        class="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                        autoComplete="name" :isFocused="true" v-model="form.name" />

                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <Input id="email" type="email" name="email" placeholder="Email"
                        class="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                        autoComplete="username" :isFocused="true" v-model="form.email" />

                    <InputError :message="form.errors.email || form.errors?.auth" class="mt-2" />
                </div>

                <div>
                    <Input id="password" type="password" name="password" placeholder="Password"
                        class="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                        autoComplete="current-password" v-model="form.password" />

                    <InputError :message="form.errors?.password" class="mt-2" />
                </div>

                <div>
                    <Input id="confirmPassword" type="password" name="confirmPassword" placeholder="Confirm Password"
                        class="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                        autoComplete="current-password" v-model="form.confirmPassword" />

                    <InputError :message="form.errors?.confirmPassword" class="mt-2" />
                </div>

                <Button class="w-full bg-black hover:bg-gray-900 text-white" :disabled="form.processing">
                    Sign up
                </Button>
            </form>

            <div class="mt-3 space-x-0.5 text-sm leading-5 text-left text-gray-950">
                <span class="opacity-[60%]">
                    Already have an account?
                </span>
                <Link class="underline cursor-pointer opacity-[75%] hover:opacity-[85%]" href="/auth/login">
                Sign In
                </Link>
            </div>
        </div>
    </div>
</template>
