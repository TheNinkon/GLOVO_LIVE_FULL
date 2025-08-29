@extends('layouts/layoutMaster')

@section('title', 'Métricas de Operación')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/admin/metrics/index.js'])
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="py-3 mb-0">
      <span class="text-muted fw-light">Admin /</span> Métricas de Operación
    </h4>
    <button id="sync-button" class="btn btn-success">
      {{-- Corrección de la clase del ícono --}}
      <i class="ti tabler-refresh me-1"></i> Sincronizar Métricas
    </button>
  </div>

  @include('content.admin.metrics.partials._kpis')

  <div class="card mt-4">
    @include('content.admin.metrics.partials._filters')
    @include('content.admin.metrics.partials._table')
  </div>
@endsection
