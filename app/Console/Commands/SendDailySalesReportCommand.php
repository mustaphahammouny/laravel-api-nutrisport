<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\Site;
use App\Models\User;
use App\Notifications\DailySalesReportNotification;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Throwable;

class SendDailySalesReportCommand extends Command
{
    protected $signature = 'nutrisport:send-daily-sales-report {--date= : YYYY-MM-DD (Yesterday by default)}';

    protected $description = 'Send daily sales report';

    public function handle(): int
    {
        $date = (string) $this->option('date');

        if (empty($date)) {
            $date = now()->subDay();
        } else {
            try {
                $date = CarbonImmutable::createFromFormat('Y-m-d', $date);
            } catch (Throwable) {
                return self::INVALID;
            }
        }

        $productQuery = Product::query()
            ->select('products.*')
            ->selectRaw('SUM(order_items.quantity) as quantities_count')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereDate('orders.created_at', $date)
            ->where('orders.status', '!=', OrderStatus::Cancelled);

        $mostSoldProduct = (clone $productQuery)
            ->groupBy('products.id')
            ->orderByDesc('quantities_count')
            ->first();

        $leastSoldProduct = (clone $productQuery)
            ->groupBy('products.id')
            ->orderBy('quantities_count')
            ->first();

        $revenueQuery = Product::query()
            ->select('products.*')
            ->selectRaw('SUM(order_items.line_total) as lines_total_count')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereDate('orders.created_at', $date)
            ->where('orders.status', '!=', OrderStatus::Cancelled);

        $highestRevenueProduct = (clone $revenueQuery)
            ->groupBy('products.id')
            ->orderByDesc('lines_total_count')
            ->first();

        $lowestRevenueProduct = (clone $revenueQuery)
            ->groupBy('products.id')
            ->orderBy('lines_total_count')
            ->first();

        $revenueBySite = Site::query()
            ->select('sites.*')
            ->selectRaw('SUM(orders.total) as totals_count')
            ->join('orders', 'sites.id', '=', 'orders.site_id')
            ->whereDate('orders.created_at', $date)
            ->where('orders.status', '!=', OrderStatus::Cancelled)
            ->groupBy('sites.id')
            ->orderByDesc('totals_count')
            ->get();

        $report = [
            'most_sold_product' => $mostSoldProduct,
            'least_sold_product' => $leastSoldProduct,
            'highest_revenue_product' => $highestRevenueProduct,
            'lowest_revenue_product' => $lowestRevenueProduct,
            'revenue_by_site' => $revenueBySite,
            'report_date' => $date->format('Y-m-d'),
        ];

        User::query()
            ->where('id', 1)
            ->orWhere('can_view_orders', true)
            ->get()
            ->each(fn(User $admin) => $admin->notify(new DailySalesReportNotification($report)));

        return self::SUCCESS;
    }
}
