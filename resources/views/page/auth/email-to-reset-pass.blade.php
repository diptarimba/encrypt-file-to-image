<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8" />
    <title>Send Email Reset Password - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="{{ config('app.name') }}" name="description" />
    <meta content="" name="Developer" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets-dashboard/images/stegano.svg') }}" />


    <link rel="stylesheet" href="{{ asset('assets-dashboard/libs/swiper/swiper-bundle.min.css') }}">


    <link rel="stylesheet" href="{{ asset('assets-dashboard/css/icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets-dashboard/css/tailwind.css') }}" />



</head>

<body data-mode="light" data-sidebar-size="lg">



    <div class="container-fluid">
        <div class="h-screen md:overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12 ">
                <div class="col-span-12 md:col-span-5 lg:col-span-4 xl:col-span-3 relative z-50">
                    <div class="w-full bg-white xl:p-12 p-10 dark:bg-zinc-800">
                        <div class="flex h-[90vh] flex-col">
                            <div class="mx-auto">
                                <a href="index.html" class="">
                                    <img src="{{ asset('assets-dashboard/images/stegano.svg') }}" alt=""
                                        class="h-8 inline"> <span
                                        class="text-xl align-middle font-medium ltr:ml-2 rtl:mr-2 dark:text-white">{{ config('app.name') }}</span>
                                </a>
                            </div>

                            <div class="my-auto">
                                <div class="text-center">
                                    <h5 class="text-gray-600 dark:text-gray-100">Reset your password<h5>
                                </div>

                                @if (session('success'))
                                    <x-util.alert color="green" message="{{ session('success') }}" />
                                @endif

                                @if (session('error'))
                                    <x-util.alert color="red" message="{{ session('error') }}" />
                                @endif

                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <x-util.alert color="red" message="{{ $error }}" />
                                    @endforeach
                                @endif

                                <form class="mt-4 pt-2" action="{{ route('reset_password.send') }}" method="post">
                                    @csrf
                                    <div class="mb-4">
                                        <label
                                            class="text-gray-600 dark:text-gray-100 font-medium mb-2 block">Email</label>
                                        <input oninput="this.value = this.value.replace(/[^a-zA-Z0-9@_.]/g, '');" name="email" type="email"
                                            class="w-full rounded placeholder:text-sm py-2 border-gray-100 dark:bg-zinc-700/50 dark:border-zinc-600 dark:text-gray-100 dark:placeholder:text-zinc-100/60"
                                            id="email" placeholder="Enter your email">
                                    </div>
                                    <div class="mb-3">
                                        <button
                                            class="btn border-transparent bg-violet-500 w-full py-2.5 text-white w-100 waves-effect waves-light shadow-md shadow-violet-200 dark:shadow-zinc-600"
                                            type="submit">Reset Password</button>
                                    </div>
                                </form>

                                {{-- <div class="mt-4 pt-2 text-center">
                                    <div>
                                        <h6 class="text-14 mb-3 text-gray-500 dark:text-gray-100 font-medium">- Sign in with -</h6>
                                    </div>

                                    <div class="flex justify-center gap-3">
                                        <a href="" class="h-9 w-9 bg-violet-500 leading-[2.9] rounded-full">
                                            <i class="mdi mdi-facebook text-lg text-white"></i>
                                        </a>
                                        <a href="" class="h-9 w-9 bg-sky-500 leading-[2.9] rounded-full">
                                            <i class="mdi mdi-twitter text-lg text-white"></i>
                                        </a>
                                        <a href="" class="h-9 w-9 bg-red-400 leading-[2.9] rounded-full">
                                            <i class="mdi mdi-google text-lg text-white"></i>
                                        </a>
                                    </div>
                                </div> --}}

                                {{-- <div class="mt-12 text-center">
                                    <p class="text-gray-500 dark:text-gray-100">Don't have an account ? <a
                                            href="{{ route('register.index') }}" class="text-violet-500 font-semibold">
                                            Signup now </a> </p>
                                </div> --}}
                            </div>


                            <div class=" text-center">
                                <p class="text-gray-500 dark:text-gray-100 relative mb-5">©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> {{ config('app.name') }} . Crafted with <i
                                        class="mdi mdi-heart text-red-400"></i> by {{config('app.app_dev')}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 md:col-span-7 lg:col-span-8 xl:col-span-9">
                    <div class="h-screen bg-cover relative p-5 bg-[url('../images/auth-bg.jpg')]">
                        <div class="absolute inset-0 bg-violet-500/90"></div>

                        <ul class="bg-bubbles absolute top-0 left-0 w-full h-full overflow-hidden animate-square">
                            <li class="h-10 w-10 rounded-3xl bg-white/10 absolute left-[10%] "></li>
                            <li class="h-28 w-28 rounded-3xl bg-white/10 absolute left-[20%]"></li>
                            <li class="h-10 w-10 rounded-3xl bg-white/10 absolute left-[25%]"></li>
                            <li class="h-20 w-20 rounded-3xl bg-white/10 absolute left-[40%]"></li>
                            <li class="h-24 w-24 rounded-3xl bg-white/10 absolute left-[70%]"></li>
                            <li class="h-32 w-32 rounded-3xl bg-white/10 absolute left-[70%]"></li>
                            <li class="h-36 w-36 rounded-3xl bg-white/10 absolute left-[32%]"></li>
                            <li class="h-20 w-20 rounded-3xl bg-white/10 absolute left-[55%]"></li>
                            <li class="h-12 w-12 rounded-3xl bg-white/10 absolute left-[25%]"></li>
                            <li class="h-36 w-36 rounded-3xl bg-white/10 absolute left-[90%]"></li>
                        </ul>

                        <div class="grid grid-cols-12 content-center h-screen">
                            <div class="col-span-8 col-start-3">
                                <div class="swiper login-slider">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <i class="bx bxs-quote-alt-left text-green-600 text-5xl"></i>
                                            <h3 class="mt-4 text-white text-22">“I feel confident imposing change on
                                                myself. It's a lot more progressing fun than looking back. That's why I
                                                ultricies enim at malesuada nibh diam on tortor neaded to throw curve
                                                balls.”</h3>
                                            <div class="flex mt-6 mb-10 pt-4">
                                                <img src="{{ asset('assets-dashboard/images/users/avatar-1.jpg') }}"
                                                    class="h-12 w-12 rounded-full" alt="...">
                                                <div class="flex-1 ltr:ml-3 rtl:mr-2 mb-4">
                                                    <h5 class="font-size-18 text-white">Ilse R. Eaton</h5>
                                                    <p class="mb-0 text-white/50">Manager
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <i class="bx bxs-quote-alt-left text-green-600 text-5xl"></i>
                                            <h3 class="mt-4 text-white text-22">“I feel confident imposing change on
                                                myself. It's a lot more progressing fun than looking back. That's why I
                                                ultricies enim at malesuada nibh diam on tortor neaded to throw curve
                                                balls.”</h3>
                                            <div class="flex mt-6 mb-10 pt-4">
                                                <img src="{{ asset('assets-dashboard/images/users/avatar-2.jpg') }}"
                                                    class="h-12 w-12 rounded-full" alt="...">
                                                <div class="flex-1 ltr:ml-3 rtl:mr-2 mb-4">
                                                    <h5 class="font-size-18 text-white">Mariya Willam</h5>
                                                    <p class="mb-0 text-white/50">Designer
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <i class="bx bxs-quote-alt-left text-green-600 text-5xl"></i>
                                            <h3 class="mt-4 text-white text-22">“I feel confident imposing change on
                                                myself. It's a lot more progressing fun than looking back. That's why I
                                                ultricies enim at malesuada nibh diam on tortor neaded to throw curve
                                                balls.”</h3>
                                            <div class="flex mt-6 mb-10 pt-4">
                                                <img src="{{ asset('assets-dashboard/images/users/avatar-3.jpg') }}"
                                                    class="h-12 w-12 rounded-full" alt="...">
                                                <div class="flex-1 ltr:ml-3 rtl:mr-2 mb-4">
                                                    <h5 class="font-size-18 text-white">Jiya Jons</h5>
                                                    <p class="mb-0 text-white/50">Developer
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-pagination"></div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- <div class="fixed ltr:right-5 rtl:left-5 bottom-10 flex flex-col gap-3 z-40 animate-bounce">
            <!-- light-dark mode button -->
            <a href="javascript: void(0);" id="ltr-rtl" class="px-3.5 py-4 z-40 text-14 transition-all duration-300 ease-linear text-white bg-violet-500 hover:bg-violet-600 rounded-full font-medium" onclick="changeMode(event)">
                <span class="ltr:hidden">LTR</span>
                <span  class="rtl:hidden">RTL</span>
            </a>
        </div> --}}

    <script src="{{ asset('assets-dashboard/libs/@popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets-dashboard/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets-dashboard/libs/metismenujs/metismenujs.min.js') }}"></script>
    <script src="{{ asset('assets-dashboard/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets-dashboard/libs/feather-icons/feather.min.js') }}"></script>

    <script src="{{ asset('assets-dashboard/libs/swiper/swiper-bundle.min.js') }}"></script>

    <script src="{{ asset('assets-dashboard/js/pages/login.init.js') }}"></script>

    <script src="{{ asset('assets-dashboard/js/app.js') }}"></script>
</body>

</html>
