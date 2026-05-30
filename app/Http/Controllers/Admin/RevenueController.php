<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\School;

class RevenueController extends Controller {
    public function index() {
        $planCounts = School::selectRaw('plan, COUNT(*) as count')->groupBy('plan')->pluck('count','plan');

        $plans = [
            ['label' => 'Pro · Lycées',       'key' => 'pro',     'price' => 2200, 'color' => '#7E5BEF'],
            ['label' => 'School · Collèges',  'key' => 'school',  'price' => 1100, 'color' => '#2563EB'],
            ['label' => 'Starter',            'key' => 'starter', 'price' => 450,  'color' => '#06B6D4'],
        ];

        $segments = collect($plans)->map(function ($p) use ($planCounts) {
            $count  = $planCounts[$p['key']] ?? 0;
            $amount = $count * $p['price'];
            return array_merge($p, ['count' => $count, 'amount' => $amount]);
        });

        $mrr    = $segments->sum('amount');
        $mrrFmt = number_format($mrr);

        $recentSchools = School::latest()->take(8)->get();

        return view('admin.revenue.index', compact('segments','mrr','mrrFmt','recentSchools','planCounts'));
    }
}
