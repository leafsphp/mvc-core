import { Head, Link, useForm } from "@inertiajs/react";

import Input from "@/components/form/input";
import InputError from "@/components/form/input-error";
import Button from "@/components/form/button";

export default function Login() {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
    });

    const submit = (e) => {
        e.preventDefault();

        post("/auth/login", {
            onFinish: () => reset("password"),
        });
    };

    return (
        <div className="bg-background flex relative z-30 flex-col justify-center w-screen min-h-screen items-stretch sm:items-center sm:py-10">
            <Head title="Log in" />

            <div className="flex relative top-0 z-20 flex-col justify-center items-stretch px-10 py-8 w-full h-screen bg-white border-gray-200 sm:top-auto sm:h-full sm:border sm:rounded-xl sm:max-w-md text-black">
                <div className="flex flex-col sm:mx-auto sm:w-full mb-5 sm:max-w-md items-center text-black">
                    <Link href="/">
                        <img
                            src="/favicon.ico"
                            alt="Logo"
                            className="size-10"
                        />
                    </Link>
                    <h1 className="mt-5 mb-1 text-2xl lg:text-3xl font-semibold">
                        Welcome back
                    </h1>
                    <h2 className="text-sm">Sign in to your account</h2>
                </div>

                <form onSubmit={submit} className="space-y-4 w-full">
                    <div>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            placeholder="Email"
                            value={data.email}
                            className="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                            autoComplete="username"
                            isFocused={true}
                            onChange={(e) => setData("email", e.target.value)}
                        />

                        <InputError
                            message={errors.email || errors?.auth}
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Password"
                            value={data.password}
                            className="mt-1 block w-full bg-white border-gray-300 rounded-md focus:border-indigo-300"
                            autoComplete="current-password"
                            onChange={(e) =>
                                setData("password", e.target.value)
                            }
                        />

                        <InputError
                            message={errors?.password}
                            className="mt-2"
                        />
                    </div>

                    <Button
                        className="w-full bg-primary hover:bg-primary-light text-white"
                        disabled={processing}
                    >
                        Log in
                    </Button>
                </form>

                <div className="mt-3 space-x-0.5 text-sm leading-5 text-left text-gray-950">
                    <span className="opacity-[60%]">
                        Don't have an account?
                    </span>
                    <Link
                        className="underline cursor-pointer opacity-[75%] hover:opacity-[85%]"
                        href="/auth/register"
                    >
                        Sign up
                    </Link>
                </div>
            </div>
        </div>
    );
}
