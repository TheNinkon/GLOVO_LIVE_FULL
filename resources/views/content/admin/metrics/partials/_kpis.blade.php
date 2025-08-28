{{-- resources/views/content/admin/metrics/partials/_kpis.blade.php (Corregido) --}}

<div class="row g-4">
  {{-- Pedidos --}}
  <div class="col-lg-3 col-sm-6">
    <div class="card card-border-shadow-primary h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            {{-- CORRECCIÓN: Se añadió "tabler-" --}}
            <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-package ti-md"></i></span>
          </div>
          <h4 class="ms-1 mb-0" id="kpi-total-orders">--</h4>
        </div>
        <p class="mb-1">Pedidos Entregados</p>
      </div>
    </div>
  </div>
  {{-- Eficiencia --}}
  <div class="col-lg-3 col-sm-6">
    <div class="card card-border-shadow-success h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            {{-- CORRECCIÓN: Se añadió "tabler-" --}}
            <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-rocket ti-md"></i></span>
          </div>
          <h4 class="ms-1 mb-0" id="kpi-avg-ratio">--</h4>
        </div>
        <p class="mb-1">Eficiencia (Pedidos/Hora)</p>
      </div>
    </div>
  </div>
  {{-- Horas --}}
  <div class="col-lg-3 col-sm-6">
    <div class="card card-border-shadow-warning h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            {{-- CORRECCIÓN: Se añadió "tabler-" --}}
            <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-clock ti-md"></i></span>
          </div>
          <h4 class="ms-1 mb-0" id="kpi-total-hours">--</h4>
        </div>
        <p class="mb-1">Horas Totales</p>
      </div>
    </div>
  </div>
  {{-- CDT Promedio --}}
  <div class="col-lg-3 col-sm-6">
    <div class="card card-border-shadow-info h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            {{-- CORRECCIÓN: Se añadió "tabler-" --}}
            <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-hourglass-low ti-md"></i></span>
          </div>
          <h4 class="ms-1 mb-0" id="kpi-avg-cdt">--</h4>
        </div>
        <p class="mb-1">CDT Promedio</p>
      </div>
    </div>
  </div>
  {{-- Ganancia Bruta --}}
  <div class="col-lg-3 col-sm-6 mt-4">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div class="card-title mb-0">
          <h5 class="mb-0 me-2" id="kpi-ganancia-total">--</h5>
          <small>Ganancia Bruta</small>
        </div>
        <div class="card-icon"><span class="badge bg-label-success rounded-circle p-2"><i
              class="ti tabler-arrow-up-right ti-sm"></i></span></div>
      </div>
    </div>
  </div>
  {{-- Costo Operativo --}}
  <div class="col-lg-3 col-sm-6 mt-4">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div class="card-title mb-0">
          <h5 class="mb-0 me-2" id="kpi-costo-total">--</h5>
          <small>Costo Operativo</small>
        </div>
        <div class="card-icon"><span class="badge bg-label-danger rounded-circle p-2"><i
              class="ti tabler-arrow-down-right ti-sm"></i></span></div>
      </div>
    </div>
  </div>
  {{-- Ganancia Neta --}}
  <div class="col-lg-3 col-sm-6 mt-4">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div class="card-title mb-0">
          <h5 class="mb-0 me-2" id="kpi-utilidad">--</h5>
          <small>Ganancia Neta</small>
        </div>
        <div class="card-icon"><span class="badge bg-label-info rounded-circle p-2"><i
              class="ti tabler-currency-euro ti-sm"></i></span></div>
      </div>
    </div>
  </div>
</div>
