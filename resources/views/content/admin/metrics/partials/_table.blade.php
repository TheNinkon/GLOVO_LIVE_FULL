<div class="table-responsive text-nowrap">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Rider</th>
        <th>Fecha</th>
        <th>Ciudad</th>
        <th>Vehículo</th>
        <th class="text-center">Pedidos</th>
        <th class="text-center">Horas</th>
        <th class="text-center">Eficiencia</th>
        <th class="text-center">Cancelados</th>
        <th class="text-center">Reasignados</th>
        <th class="text-center">No Show</th>
        <th class="text-center">CDT (min)</th>
        <th class="text-center">Ganancia</th>
        <th class="text-center">Costo</th>
        <th class="text-center">Neta</th>
      </tr>
    </thead>
    <tbody id="metrics-table-body">
      {{-- Las filas se insertarán aquí con JavaScript --}}
    </tbody>
  </table>
</div>

{{-- Pie con selector de per_page + paginación --}}
<div class="card-footer d-flex justify-content-between align-items-center">
  <div class="d-flex align-items-center">
    <label for="per-page-select" class="me-2 mb-0">Resultados por página:</label>
    <select id="per-page-select" class="form-select form-select-sm" style="width: 80px;">
      <option value="15">15</option>
      <option value="25">25</option>
      <option value="50" selected>50</option>
      <option value="100">100</option>
    </select>
  </div>

  <div id="pagination-links" class="d-flex justify-content-center">
    {{-- La paginación se insertará aquí --}}
  </div>
</div>
