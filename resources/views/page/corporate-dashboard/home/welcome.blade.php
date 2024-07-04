@extends('layout.app')

@section('page-link', '/')
@section('page-title', 'Welcome')
@section('sub-page-title', 'Index')

@push('additional-header')
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        nav a {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s, padding 0.3s;
        }

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .menu-icon {
            display: none;
        }

        @media (max-width: 768px) {
            .menu-icon {
                display: block;
            }

            nav {
                display: none;
            }

            nav.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                background-color: rgb(81 86 190 / var(--tw-bg-opacity));
                padding: 1rem;
                border-radius: 0.5rem;
                z-index: 10;
            }
        }

        .hover-bright:hover {
            background-color: rgba(59, 130, 246, 0.8);
            /* Light blue color matching the background */
        }
    </style>
@endpush

@section('content')
    <div class="bg-gradient-to-b from-violet-500 to-violet-300 min-h-screen flex flex-col items-center">
        <main class="flex flex-col items-center mt-10 text-center px-4">
            <h1 class="text-3xl font-bold">Welcome to “ DOMINOS”</h1>
            <p class="mt-2 text-lg">"Behind Every Picture is a Secret Story: Discover it with Our Steganography"</p>
            <div class="flex text-white md:flex-col flex-row space-x-4 md:space-y-0 md:space-x-4 mt-10">
                <div class="bg-violet-700 hover:bg-sky-500 p-6 rounded-lg shadow-lg w-full md:w-64 hover-bright">
                    <a href="{{ route('corporate.encrypt.create') }}">
                        <i class="fas fa-lock text-4xl mb-4"></i>
                        <h2 class="text-2xl font-bold">Encode</h2>
                        <p class="mt-2">hide your secret files or messages inside a file securely and invisibly</p>
                    </a>
                </div>
                <div class="bg-violet-700 hover:bg-sky-500 p-6 rounded-lg shadow-lg w-full md:w-64 hover-bright">
                    <a href="{{ route('corporate.encrypt.upload_get') }}">
                        <i class="fas fa-unlock text-4xl mb-4"></i>
                        <h2 class="text-2xl font-bold">Decode</h2>
                        <p class="mt-2">Reveal secret messages or files hidden inside files easily and quickly</p>
                    </a>
                </div>
                <div class="bg-violet-700 hover:bg-sky-500 p-6 rounded-lg shadow-lg w-full md:w-64 hover-bright">
                    <i class="fas fa-image text-4xl mb-4"></i>
                    <h2 class="text-2xl font-bold">Sign Image</h2>
                    <p class="mt-2">Secure your digital assets and protect against manipulation</p>
                </div>
            </div>
        </main>
    </div>
@endsection
