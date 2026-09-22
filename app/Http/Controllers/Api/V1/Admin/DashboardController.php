<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Inventory\LowStockAlertService;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponder;

    /**
     * Chiffres clés affichés en haut du dashboard admin (app mobile) :
     * commandes du jour, livrées aujourd'hui, stock faible, et le badge de
     * commandes en attente sur la tuile "Commandes". Chaque groupe n'est
     * calculé (et renvoyé) que si l'employé a la permission correspondante
     * — un employé sans "orders.view" ne doit pas voir de chiffres sur les
     * commandes, même agrégés.
     */
    public function stats(Request $request, LowStockAlertService $lowStockAlerts): JsonResponse
    {
        $user = $request->user();
        $data = [];

        if ($user->hasPermission('orders.view')) {
            $data['orders_today'] = Order::whereDate('placed_at', today())->count();
            $data['delivered_today'] = Order::whereDate('delivered_at', today())->count();
            $data['pending_orders'] = Order::where('status', OrderStatus::PENDING)->count();
        }

        if ($user->hasPermission('inventory.view')) {
            $data['low_stock_count'] = $lowStockAlerts->count();
        }

        return $this->success($data);
    }
}
