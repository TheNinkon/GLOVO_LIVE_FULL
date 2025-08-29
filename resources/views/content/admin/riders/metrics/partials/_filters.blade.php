<div class="card-datatable table-responsive p-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="card-title mb-0">Filtros</h5>
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
