@php
    $user = Auth::user();
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Principal Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Menu Horizontal -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>
                <!-- Menu -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if ($user->role == 'admin')
                        <x-nav-link  href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
                            {{ __('Usuarios') }}
                        </x-nav-link>
                    @endif
                    <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.index') || request()->routeIs('batches.index')">
                        {{ __('Inventario') }}
                    </x-nav-link>
                    @if ($user->role == 'admin')
                        <x-nav-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index') || request()->routeIs('laboratories.index') || request()->routeIs('customers.index')">
                            {{ __('Contactos') }}
                        </x-nav-link>
                    @endif
                    <x-nav-link href="{{ route('sales.index') }}" :active="request()->routeIs('sales.index')">
                        {{ __('Ventas') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('cart.index') }}" :active="request()->routeIs('cart.index') || request()->routeIs('cart.details')">
                        <i class="fa fa-shopping-cart text-gray-500"></i>
                        <span class="ms-2 text-gray-500" id="cart-mount">{{ session('total', 0) }} Bs</span>
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>
                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Administrar Cuenta') }}
                            </div>
                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Perfil') }}
                            </x-dropdown-link>
                            <div class="border-t border-gray-200"></div>
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Salir') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if ($user->role == 'admin')
                <x-responsive-nav-link  href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.index')">
                {{ __('Productos') }}
            </x-responsive-nav-link>
            @if ($user->role == 'admin')
                <x-responsive-nav-link href="{{ route('batches.index') }}" :active="request()->routeIs('batches.index')">
                    {{ __('Lotes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('laboratories.index') }}" :active="request()->routeIs('laboratories.index')">
                    {{ __('Laboratorios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index')">
                    {{ __('Proveedores') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.index')">
                    {{ __('Clientes') }}
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link href="{{ route('sales.index') }}" :active="request()->routeIs('sales.index')">
                {{ __('Ventas') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('cart.index') }}" :active="request()->routeIs('cart.index') || request()->routeIs('cart.details')">
                {{ __('Carrito') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive User Profile -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('Salir') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

</nav>
