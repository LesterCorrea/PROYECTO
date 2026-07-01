<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataStructures\SearchAlgorithms;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query      = $request->get('q');
        $event      = $request->get('evento');
        $from       = $request->get('desde', now()->subDays(7)->toDateString());
        $to         = $request->get('hasta', now()->toDateString());
        $sortDir    = $request->get('direccion', 'desc');

        $logs = Activity::with('causer')
            ->when($query, fn($q) => $q->where('description', 'like', "%{$query}%"))
            ->when($event,  fn($q) => $q->where('event', $event))
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->orderBy('created_at', $sortDir)
            ->paginate(30);

        $events = Activity::distinct()->pluck('event')->filter();

        return view('admin.logs.index', compact('logs', 'events', 'query', 'event', 'from', 'to'));
    }
}