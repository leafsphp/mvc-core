<script>
    import { Link, useForm } from "@inertiajs/svelte";

    import Input from "@/components/form/input.svelte";
    import InputError from "@/components/form/input-error.svelte";
    import Button from "@/components/form/button.svelte";
    // import Label from "@/components/form/label.svelte";

    const { request } = $props();

    const form = useForm({
        name: "",
        email: request?.email || "",
        password: "",
        confirmPassword: "",
        invite: request?.invite || "",
    });

    const submit = (e) => {
        e.preventDefault();

        $form.post("/auth/register", {
            onFinish: () => $form.reset("password", "confirmPassword"),
        });
    };
</script>

<svelte:head>
    <title>Register</title>
</svelte:head>

<div
    class="bg-background flex relative z-30 flex-col justify-center w-screen min-h-screen items-stretch sm:items-center sm:py-10"
>
    <div
        class="flex relative top-0 z-20 flex-col justify-center items-stretch px-10 py-8 w-full h-screen bg-white border-gray-200 sm:top-auto sm:h-full sm:border sm:rounded-xl sm:max-w-md text-black"
    >
        <div
            class="flex flex-col sm:mx-auto sm:w-full mb-5 sm:max-w-md items-center text-black"
        >
            <Link href="/">
                <img
                    src="/favicon.ico"
                    alt="Logo"
                    class="size-10"
                />
            </Link>
            <h1 class="mt-5 mb-1 text-2xl lg:text-3xl font-semibold">
                Hello there
            </h1>
            <h2 class="text-sm">Create your account</h2>
        </div>

        <form onsubmit={submit} class="space-y-4 w-full">
            <div>
                <Input
                    id="name"
                    type="text"
                    name="name"
                    placeholder="Name"
                    class="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                    autoComplete="name"
                    isFocused={true}
                    value={$form.name}
                    onchange={(e) => $form.name = e.target.value}
                />

                <InputError message={$form.errors.name} class="mt-2" />
            </div>

            <div>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="Email"
                    class="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                    autoComplete="username"
                    isFocused={true}
                    value={$form.email}
                    onchange={(e) => $form.email = e.target.value}
                />

                <InputError
                    message={$form.errors.email || $form.errors?.auth}
                    class="mt-2"
                />
            </div>

            <div>
                <Input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Password"
                    class="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                    autoComplete="new-password"
                    value={$form.password}
                    onchange={(e) => $form.password = e.target.value}
                />

                <InputError message={$form.errors?.password} class="mt-2" />
            </div>

            <div>
                <Input
                    id="confirmPassword"
                    type="password"
                    name="confirmPassword"
                    placeholder="Confirm Password"
                    class="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                    autoComplete="new-password"
                    value={$form.confirmPassword}
                    onchange={(e) => $form.confirmPassword = e.target.value}
                />

                <InputError
                    message={$form.errors?.confirmPassword}
                    class="mt-2"
                />
            </div>

            <Button
                class="w-full bg-background hover:bg-primary-light text-white"
                disabled={$form.processing}
            >
                Sign up
            </Button>
        </form>

        <div class="mt-3 space-x-0.5 text-sm leading-5 text-left text-gray-950">
            <span class="opacity-[60%]"> Already have an account? </span>
            <Link
                class="underline cursor-pointer opacity-[75%] hover:opacity-[85%]"
                href="/auth/login"
            >
                Sign In
            </Link>
        </div>
    </div>
</div>
