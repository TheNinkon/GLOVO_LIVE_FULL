@extends('layouts/layoutMaster')

@section('title', 'Gestión de Riders')

{{-- Carga de Estilos Específicos de la Página --}}
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/riders-list.js')

  <script>
    const riderListApi = "{{ route('admin.riders.list') }}";
  </script>
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Riders
  </h4>

  <div class="row g-6 mb-6">
    <div class="col-sm-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading">Total de Riders</span>
              <div class="d-flex align-items-center my-1">
                <h4 class="mb-0 me-2" id="total-riders-count">0</h4>
              </div>
              <small class="mb-0">Usuarios registrados</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-primary">
                <i class="ti tabler-users icon-26px"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading">Riders Activos</span>
              <div class="d-flex align-items-center my-1">
                <h4 class="mb-0 me-2" id="active-riders-count">0</h4>
              </div>
              <small class="mb-0">Estado 'Activo'</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-success">
                <i class="ti tabler-user-check icon-26px"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading">Riders Inactivos</span>
              <div class="d-flex align-items-center my-1">
                <h4 class="mb-0 me-2" id="inactive-riders-count">0</h4>
              </div>
              <small class="mb-0">Estado 'Inactivo'</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-danger">
                <i class="ti tabler-user-exclamation icon-26px"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading">Riders Pendientes</span>
              <div class="d-flex align-items-center my-1">
                <h4 class="mb-0 me-2" id="pending-riders-count">0</h4>
              </div>
              <small class="mb-0">Estado 'Pendiente'</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-warning">
                <i class="ti tabler-user-search icon-26px"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Listado de Riders</h5>
      <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
        <div class="col-md-4">
          <div class="dataTables_filter">
            <label for="filter-by-name">Buscar por nombre:</label>
            <input type="text" id="filter-by-name" class="form-control" placeholder="Nombre del rider">
          </div>
        </div>
        <div class="col-md-4">
          <div class="dataTables_filter">
            <label for="filter-by-status">Filtrar por estado:</label>
            <select id="filter-by-status" class="form-select text-capitalize">
              <option value="">Seleccionar estado</option>
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
              <option value="blocked">Bloqueado</option>
              <option value="pending">Pendiente</option>
            </select>
          </div>
        </div>
        <div class="col-md-4 text-end">
          <a href="{{ route('admin.riders.create') }}" class="btn btn-primary">
            <i class="ti tabler-plus me-1"></i> Crear Rider
          </a>
        </div>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="datatables-riders table">
        <thead class="border-top">
          <tr>
            <th></th>
            <th>ID</th>
            <th>Nombre Completo</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Ciudad</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
@endsection
