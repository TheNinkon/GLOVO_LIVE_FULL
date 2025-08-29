@extends('layouts/layoutMaster')

@section('title', 'Mis Métricas')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/rider-metrics.js'])
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="py-3 mb-0">
      <span class="text-muted fw-light">Mi Perfil /</span> Métricas
    </h4>
  </div>

  {{-- KPIs de motivación --}}
  <div class="row g-4 mb-4" id="kpis-container">
    {{-- Los KPIs se cargarán dinámicamente aquí --}}
  </div>

  <div class="card mt-4">
    <div class="card-datatable table-responsive p-3">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="card-title mb-0">Historial de Rendimiento</h5>
        <button id="filter-button" class="btn btn-primary">
          <i class="ti ti-filter me-1"></i> Aplicar Filtros
        </button>
      </div>
      <form id="metrics-filters-form">
        <div class="row g-3">
          <div class="col-md-6 col-lg-6">
            <label class="form-label" for="date_range">Rango de Fechas</label>
            <input type="text" id="date_range" name="date_range" class="form-control flatpickr-range">
          </div>
          <div class="col-md-6 col-lg-6">
            <label class="form-label" for="transport">Transporte</label>
            <select id="transport" name="transport" class="form-select text-capitalize">
              <option value="">Selecciona un transporte</option>
              @foreach ($transports as $transport)
                <option value="{{ $transport }}">{{ $transport }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Día</th>
            <th>Pedidos</th>
            <th>Horas</th>
            <th>Eficiencia</th>
            <th>Cancelaciones</th>
            <th>Reasignaciones</th>
            <th>Tiempo Promedio</th>
          </tr>
        </thead>
        <tbody id="metrics-table-body">
        </tbody>
      </table>
      <div class="d-flex justify-content-center mt-3" id="pagination-container">
      </div>
    </div>
  </div>
@endsection
