@extends('layouts.panel')

@section('title', 'Reportes')
@section('page-title', 'Reportes y Estadísticas')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    @php
    $reports = [
    [
    'title' => 'Reporte de Préstamos',
    'description' => 'Historial completo de préstamos filtrado por fechas y estado.',
    'route' => 'admin.reports.loans',
    'export' => 'loans',
    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    'color' => 'indigo',
    ],
    [
    'title' => 'Reporte de Multas',
    'description' => 'Detalle de multas generadas, pagadas y pendientes.',
    'route' => 'admin.reports.fines',
    'export' => 'fines',
    'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
    'color' => 'red',
    ],
    [
    'title' => 'Reporte de Usuarios',
    'description' => 'Actividad de usuarios, préstamos y multas por persona.',
    'route' => 'admin.reports.users',
    'export' => 'users',
    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    'color' => 'blue',
    ],
    ];
    @endphp

    @foreach($reports as $report)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
        <div class="w-12 h-12 rounded-xl bg-{{ $report['color'] }}-100 dark:bg-{{ $report['color'] }}-900/30 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-{{ $report['color'] }}-600 dark:text-{{ $report['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $report['icon'] }}" />
            </svg>
        </div>
        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $report['title'] }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 flex-1 mb-5">{{ $report['description'] }}</p>
        <div class="flex gap-2">
            <a href="{{ route($report['route']) }}"
                class="flex-1 text-center px-4 py-2.5 bg-{{ $report['color'] }}-600 hover:bg-{{ $report['color'] }}-700 text-white text-sm font-medium rounded-xl transition-colors">
                Ver reporte
            </a>
            <a href="{{ route('admin.reports.export', $report['export']) }}"
                class="px-4 py-2.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </a>
        </div>
    </div>
    @endforeach
</div>
@endsection