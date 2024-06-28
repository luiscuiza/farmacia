<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\Batch;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        if ($user->role == 'admin') {
            $today = Carbon::today();
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $nextWeek = Carbon::now()->addWeek();
            // Ventas diarias
            $totalSalesToday = Sale::whereDate('date', $today)->sum('total');
            // Ventas mensuales
            $totalSalesMonth = Sale::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('total');
            // Productos con bajo stock < 10
            $lowStockProducts = Product::whereHas('batches', function($query) {
                $query->where('stock', '<', 10);
            })->count();
            // Productos sin stock
            $outStockProducts = Product::whereDoesntHave('batches', function($query) {
                $query->where('stock', '>', 0);
            })->count();
            // Lotes expirados este mes
            $expiredsMonth = Batch::whereBetween('expiration', [$startOfMonth, $endOfMonth])->count();
            // Lotes próximos a expirar en los siguientes 7 días
            $expiringNextWeek = Batch::whereBetween('expiration', [Carbon::now(), $nextWeek])->count();
            // Lotes vencidos
            $expiredBatchesQuery = Batch::where('expiration', '<', Carbon::now());
            $expiredBatches = $expiredBatchesQuery->count();
            // Productos vencidos
            $expiredProducts = $expiredBatchesQuery->sum('stock');
            return view('dashboard', compact(
                'totalSalesToday', 'totalSalesMonth', 'lowStockProducts', 'outStockProducts', 'expiredsMonth', 'expiringNextWeek', 'expiredBatches', 'expiredProducts'
            ));
        } elseif ($user->role == 'user') {
            $today = Carbon::today();
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // Ventas diarias del usuario
            $totalSalesToday = Sale::where('user_id', $user->id)
                                    ->whereDate('date', $today)
                                    ->sum('total');
            // Ventas mensuales del usuario
            $totalSalesMonth = Sale::where('user_id', $user->id)
                                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                                    ->sum('total');

            return view('dashboard', compact('totalSalesToday', 'totalSalesMonth'));
        } else {
            return redirect('/');
        }
    }
}
