@php
    $user = Auth::user();
@endphp

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-slot name="header">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Dashboard') }}
                    </h2>
                </x-slot>
                @if ($user->role == 'admin')
                    @include('user.admin-dashboard', [
                        'totalSalesToday' => $totalSalesToday,
                        'totalSalesMonth' => $totalSalesMonth,
                        'lowStockProducts' => $lowStockProducts,
                        'outStockProducts' => $outStockProducts,
                        'expiredsMonth' => $expiredsMonth,
                        'expiringNextWeek' => $expiringNextWeek,
                        'expiredBatches' => $expiredBatches,
                        'expiredProducts' => $expiredProducts,
                    ])
                @elseif ($user->role == 'user')
                    @include('user.user-dashboard', [
                        'totalSalesToday' => $totalSalesToday,
                        'totalSalesMonth' => $totalSalesMonth,
                    ])
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
