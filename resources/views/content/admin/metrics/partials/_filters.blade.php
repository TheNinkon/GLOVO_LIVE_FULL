<div class="card-header border-top">
  <h5 class="card-title mb-0">Filtros de Búsqueda</h5>
</div>
<div class="card-body">
  <div class="row g-3">
    <div class="col-md-3"><label class="form-label">Rango de Fechas:</label><input type="text" id="filter-date-range"
        class="form-control" placeholder="YYYY-MM-DD a YYYY-MM-DD" /></div>
    <div class="col-md-2"><label class="form-label">Ciudad:</label><select id="filter-city" class="form-select">
        <option value="">Todas</option>
        @foreach ($cities as $city)
          <option value="{{ $city }}">{{ $city }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2"><label class="form-label">Vehículo:</label><select id="filter-transport" class="form-select">
        <option value="">Todos</option>
        @foreach ($transports as $transport)
          <option value="{{ $transport }}">{{ $transport }}</option>
        @endforeach
      </select></div>
    <div class="col-md-2"><label class="form-label">Courier ID:</label><input type="text" id="filter-courier-id"
        class="form-control" placeholder="Buscar ID..."></div>
    {{-- NUEVO FILTRO POR NOMBRE DEL RIDER --}}
    <div class="col-md-3"><label class="form-label">Rider:</label><input type="text" id="filter-rider-name"
        class="form-control" placeholder="Buscar por nombre..."></div>
    <div class="col-md-3"><label class="form-label">Día de la Semana:</label><select id="filter-weekday"
        class="form-select">
        <option value="">Todos</option>
        <option value="2">Lunes</option>
        <option value="3">Martes</option>
        <option value="4">Miércoles</option>
        <option value="5">Jueves</option>
        <option value="6">Viernes</option>
        <option value="7">Sábado</option>
        <option value="1">Domingo</option>
      </select></div>
  </div>
  <div class="row g-3 mt-2">
    <div class="col-md-3"><label class="form-label">Costo por Pedido (€):</label><input type="number" step="0.01"
        id="filter-cost-order" class="form-control" value="5.50"></div>
    <div class="col-md-3"><label class="form-label">Costo por Hora (€):</label><input type="number" step="0.01"
        id="filter-cost-hour" class="form-control" value="12.00"></div>
    <div class="col-md-3 d-flex align-items-end">
      <button id="apply-filters" class="btn btn-primary me-2">Aplicar</button>
      <button id="clear-filters" class="btn btn-label-secondary">Reiniciar</button>
    </div>
  </div>
</div>
