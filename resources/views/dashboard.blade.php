@php
    $user = Auth::user();
@endphp

<x-app-layout>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <x-slot name="header">
            <div class="d-flex justify-content-between align-items-center bg-white">
                <h2 class="h5 text-dark font-weight-bold">
                    {{ __('Dashboard') }}
                </h2>
            </div>
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
</x-app-layout>
