@extends('layouts/layoutMaster')

@section('title', 'Mis Métricas')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/flatpickr/l10n/es.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/rider-metrics.js'])
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Rider /</span> Mis Métricas
  </h4>
  <div class="card">
    <div class="card-header border-top">
      <h5 class="card-title mb-0">Filtros de Búsqueda</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Rango de Fechas:</label>
          <input type="text" id="filter-date-range" class="form-control" placeholder="YYYY-MM-DD a YYYY-MM-DD" />
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button id="apply-filters" class="btn btn-primary me-2">Aplicar</button>
          <button id="clear-filters" class="btn btn-label-secondary">Reiniciar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="card mt-4">
    <h5 class="card-header">Mis Registros Diarios</h5>
    <div class="table-responsive text-nowrap">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Ciudad</th>
            <th>Pedidos</th>
            <th>Horas</th>
            <th>CDT (min)</th>
          </tr>
        </thead>
        <tbody id="metrics-table-body">
          {{-- Las filas se insertarán aquí con JavaScript --}}
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex justify-content-center">
      <div id="pagination-links" class="d-flex justify-content-center"></div>
    </div>
  </div>
@endsection
