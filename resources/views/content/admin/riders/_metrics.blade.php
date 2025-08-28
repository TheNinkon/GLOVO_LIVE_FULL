<div class="card mb-6">
  <h5 class="card-header d-flex justify-content-between align-items-center">
    Asignaciones de Cash Out y Propinas
    <div class="dropdown">
      <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti tabler-filter me-1"></i> Filtros
      </button>
      <ul class="dropdown-menu dropdown-menu-end p-3">
        <li>
          <label for="filter-assignment-type" class="form-label">Tipo de Asignación</label>
          <select id="filter-assignment-type" class="form-select text-capitalize mb-2">
            <option value="">Todos</option>
            <option value="cash_out">Cash Out</option>
            <option value="tips">Propinas</option>
          </select>
        </li>
        <li>
          <label for="filter-assignment-status" class="form-label">Estado</label>
          <select id="filter-assignment-status" class="form-select text-capitalize">
            <option value="">Todos</option>
            <option value="pending">Pendiente</option>
            <option value="paid">Pagado</option>
            <option value="deducted">Deducido</option>
          </select>
        </li>
      </ul>
    </div>
  </h5>
  <div class="card-datatable table-responsive">
    <table class="table datatable-assignments">
      <thead class="border-top">
        <tr>
          <th>ID</th>
          <th>Monto</th>
          <th>Tipo</th>
          <th>Estado</th>
          <th>Fecha de Asignación</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<div class="card mb-6">
  <h5 class="card-header">Métricas de Glovo</h5>
  <div class="card-datatable table-responsive">
    <table class="table datatable-metrics">
      <thead class="border-top">
        <tr>
          <th>Fecha</th>
          <th>Entregados</th>
          <th>Horas</th>
          <th>Ratio de Entrega</th>
          <th>Cancelados</th>
          <th>Reasignaciones</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
