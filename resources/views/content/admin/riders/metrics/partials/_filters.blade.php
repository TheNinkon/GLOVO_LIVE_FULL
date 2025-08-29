<div class="card-datatable table-responsive p-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="card-title mb-0">Filtros</h5>
    <button id="filter-button" class="btn btn-primary">
      <i class="ti tabler-filter me-1"></i> Aplicar Filtros
    </button>
  </div>
  <form id="metrics-filters-form">
    <div class="row g-3">
      <div class="col-md-6 col-lg-4">
        <label class="form-label" for="date_range">Rango de Fechas</label>
        <input type="text" id="date_range" name="date_range" class="form-control flatpickr-range">
      </div>
      <div class="col-md-6 col-lg-4">
        <label class="form-label" for="transport">Transporte</label>
        <select id="transport" name="transport" class="form-select text-capitalize">
          <option value="">Selecciona un transporte</option>
          @foreach ($transports as $transport)
            <option value="{{ $transport }}">{{ $transport }}</option>
          @endforeach
        </select>
      </div>
      {{-- Nuevos campos para los valores dinámicos --}}
      <div class="col-md-6 col-lg-2">
        <label class="form-label" for="costo_por_hora">Costo por Hora (€)</label>
        <input type="number" id="costo_por_hora" name="costo_por_hora" class="form-control" value="15.00"
          step="0.01">
      </div>
      <div class="col-md-6 col-lg-2">
        <label class="form-label" for="ganancia_por_pedido">Ganancia por Pedido (€)</label>
        <input type="number" id="ganancia_por_pedido" name="ganancia_por_pedido" class="form-control" value="5.50"
          step="0.01">
      </div>
    </div>
  </form>
</div>
