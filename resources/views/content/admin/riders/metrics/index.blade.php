@extends('layouts/layoutMaster')

@section('title', 'Métricas de Rider: ' . $rider->full_name)

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js'
    {{-- 'resources/assets/vendor/libs/flatpickr/l10n/es.js' --}}
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/admin-rider-metrics.js'])
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="py-3 mb-0">
      <span class="text-muted fw-light">Admin / Riders /</span> Métricas de {{ $rider->full_name }}
    </h4>
  </div>

  @include('content.admin.riders.metrics.partials._kpis')

  <div class="card mt-4">
    @include('content.admin.riders.metrics.partials._filters')
    @include('content.admin.riders.metrics.partials._table')
  </div>
@endsection
