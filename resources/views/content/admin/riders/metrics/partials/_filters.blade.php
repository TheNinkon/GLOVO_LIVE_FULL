<div class="card-datatable table-responsive p-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="card-title mb-0">Filtros</h5>
    <button id="filter-button" class="btn btn-primary">
      <i class="ti ti-filter me-1"></i> Aplicar Filtros
    </button>
  </div>
  <form id="metrics-filters-form">
    <div class="row g-3">
      <div class="col-md-6 col-lg-3">
        <label class="form-label" for="date_range">Rango de Fechas</label>
        <input type="text" id="date_range" name="date_range" class="form-control flatpickr-range">
      </div>
      <div class="col-md-6 col-lg-3">
        <label class="form-label" for="city">Ciudad</label>
        <select id="city" name="city" class="form-select text-capitalize">
          <option value="">Selecciona una ciudad</option>
          @foreach ($cities as $city)
            <option value="{{ $city }}">{{ $city }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6 col-lg-3">
        <label class="form-label" for="transport">Transporte</label>
        <select id="transport" name="transport" class="form-select text-capitalize">
          <option value="">Selecciona un transporte</option>
          @foreach ($transports as $transport)
            <option value="{{ $transport }}">{{ $transport }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6 col-lg-3">
        <label class="form-label" for="weekday">Día de la semana</label>
        <select id="weekday" name="weekday" class="form-select text-capitalize">
          <option value="">Todos los días</option>
          <option value="1">Domingo</option>
          <option value="2">Lunes</option>
          <option value="3">Martes</option>
          <option value="4">Miércoles</option>
          <option value="5">Jueves</option>
          <option value="6">Viernes</option>
          <option value="7">Sábado</option>
        </select>
      </div>
    </div>
  </form>
</div>
