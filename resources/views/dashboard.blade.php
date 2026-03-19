@extends('layouts.app')

@section('title', 'Dashboard - Gapuro System')

@section('content')
<div class="flex items-start gap-6">
    <div class="flex-1">
        <h2 class="text-2xl font-bold text-gray-700 mb-2">WELCOME TO GAPURO SITE.</h2>
        <p class="text-sm text-gray-500 mb-6">Current Period : 2025-10-01 - 2025-12-31</p>

        <!-- main blank content area -->
        <div class="h-96 bg-gray-50 border border-dashed border-gray-200 rounded flex items-center justify-center">
            <span class="text-gray-400 text-lg">Dashboard content will be here (charts, stats, widgets)</span>
        </div>
    </div>

    <!-- right small panel (optional) -->
    <div class="w-64">
        <div class="bg-gray-50 border rounded p-4">
            <div class="text-xs text-gray-500 mb-2">Quick Links</div>
            <ul class="space-y-2 text-sm">
                <li><a href="#" class="text-blue-600 hover:underline">Parts Receiving</a></li>
                <li><a href="#" class="text-blue-600 hover:underline">Parts Storage</a></li>
                <li><a href="#" class="text-blue-600 hover:underline">Parts Request</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection