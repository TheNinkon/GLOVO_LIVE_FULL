@extends('layouts/layoutMaster')

@section('title', 'Perfil de Rider - ' . $rider->full_name)

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
  @vite('resources/assets/vendor/scss/pages/page-user-view.scss')
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
  <script>
    const assignmentsApiUrl = "{{ route('admin.riders.assignments', $rider->id) }}";
    const metricsApiUrl = "{{ route('admin.riders.metrics', $rider->id) }}";
  </script>

  @vite('resources/assets/js/rider-profile-metrics.js')
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Riders /</span> Perfil
  </h4>

  <div class="row">
    <div class="col-xl-4 col-lg-5 order-1 order-md-0">
      <div class="card mb-6">
        <div class="card-body pt-12">
          <div class="user-avatar-section">
            <div class="d-flex align-items-center flex-column">
              <img class="img-fluid rounded mb-4" src="{{ asset('assets/img/avatars/1.png') }}" height="120"
                width="120" alt="User avatar" />
              <div class="user-info text-center">
                <h5>{{ $rider->full_name }}</h5>
                <span class="badge bg-label-secondary">Rider</span>
              </div>
            </div>
          </div>
          <h5 class="pb-4 border-bottom mb-4">Detalles</h5>
          <div class="info-container">
            <ul class="list-unstyled mb-6">
              <li class="mb-2">
                <span class="h6">Nombre:</span>
                <span>{{ $rider->full_name }}</span>
              </li>
              <li class="mb-2">
                <span class="h6">Email:</span>
                <span>{{ $rider->email }}</span>
              </li>
              <li class="mb-2">
                <span class="h6">Estado:</span>
                <span>{{ $rider->status }}</span>
              </li>
              <li class="mb-2">
                <span class="h6">Rol:</span>
                <span>Rider</span>
              </li>
              <li class="mb-2">
                <span class="h6">Contacto:</span>
                <span>{{ $rider->phone }}</span>
              </li>
              <li class="mb-2">
                <span class="h6">Ciudad:</span>
                <span>{{ $rider->city }}</span>
              </li>
            </ul>
            <div class="d-flex justify-content-center">
              <a href="{{ route('admin.riders.edit', $rider->id) }}" class="btn btn-primary me-4">Editar</a>
              <a href="javascript:;" class="btn btn-label-danger suspend-user">Suspender</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-8 col-lg-7 order-0 order-md-1">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-6 row-gap-2">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#profile-account"><i
                class="ti ti-user-check icon-sm me-1_5"></i>Cuenta</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#profile-assignments"><i
                class="ti ti-report-money icon-sm me-1_5"></i>Asignaciones</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#profile-metrics"><i
                class="ti ti-chart-bar icon-sm me-1_5"></i>Métricas</button>
          </li>
        </ul>
      </div>
      <div class="tab-content pt-4">
        <div class="tab-pane fade show active" id="profile-account" role="tabpanel">
          <div class="card mb-6">
            <h5 class="card-header">Últimas Asignaciones</h5>
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
        </div>
        <div class="tab-pane fade" id="profile-assignments" role="tabpanel">
          <div class="card mb-6">
            <h5 class="card-header d-flex justify-content-between align-items-center">
              Asignaciones de Cash Out y Propinas
              <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <i class="ti ti-filter me-1"></i> Filtros
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
                      <option value="deduccion">Deducción</option>
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
        </div>
        <div class="tab-pane fade" id="profile-metrics" role="tabpanel">
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
        </div>
      </div>
    </div>
  </div>
@endsection
