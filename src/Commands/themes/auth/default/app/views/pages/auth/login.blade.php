@extends('layouts.auth')

@section('content')
    <div
        class="flex relative top-0 z-20 flex-col justify-center items-stretch px-10 py-8 w-full h-screen bg-white border-gray-200 sm:top-auto sm:h-full sm:border sm:rounded-xl">
        <div class="flex flex-col sm:mx-auto sm:w-full mb-5 sm:max-w-md items-center text-black">
            <a href="/">
                <img src="/favicon.ico" alt="Logo" class="size-10">
            </a>
            <h1 class="mt-4 mb-1 text-2xl lg:text-3xl font-semibold">Welcome back</h1>
            <h2 class="text-sm">Sign in to your account</h2>
        </div>

        <form class="space-y-5 text-black" action="/auth/login" method="POST">
            @csrf

            <div class="flex relative flex-col justify-center w-full">
                <input name="email" type="email" required="required" placeholder="Email" value="{{ $email ?? '' }}"
                    class="flex w-full h-11 px-3.5 text-sm border rounded-md border-gray-300 ring-offset-background placeholder:text-gray-500 focus:outline-none focus:ring-1 focus:ring-zinc-800 disabled:cursor-not-allowed disabled:opacity-50">
                <small class="text-red-900 text-sm">{{ $errors['email'] ?? ($errors['auth'] ?? null) }}</small>
            </div>

            <div class="flex relative flex-col justify-center w-full">
                <input name="password" type="password" required="required" value="{{ $password ?? '' }}"
                    placeholder="Password"
                    class="flex w-full h-11 px-3.5 text-sm border rounded-md border-gray-300 ring-offset-background placeholder:text-gray-500 focus:outline-none focus:ring-1 focus:ring-zinc-800 disabled:cursor-not-allowed disabled:opacity-50 ">
                <small class="text-red-900 text-sm">{{ $errors['password'] ?? null }}</small>
            </div>

            {{-- @if ($errors['password'] ?? null)
                <div class="mt-3 space-x-0.5 text-sm leading-5 text-left">
                    <a class="underline cursor-pointer opacity-[67%] hover:opacity-[80%]" href="/auth/forgot">
                        I forgot my password
                    </a>
                </div>
            @endif --}}

            <button type="submit"
                class="transition-all inline-flex justify-center rounded-lg text-sm font-semibold py-3 px-4 bg-green-600 hover:bg-green-500 text-white w-full"
                data-zero-component="Button">
                Continue
            </button>
        </form>

        <div class="mt-3 space-x-0.5 text-sm leading-5 text-left" style="color:#00173d">
            <span class="opacity-[47%]">Don't have an account?</span>
            <a class="underline cursor-pointer opacity-[67%] hover:opacity-[80%]" href="/auth/register">
                Sign up
            </a>
        </div>
    </div>
@endsection
