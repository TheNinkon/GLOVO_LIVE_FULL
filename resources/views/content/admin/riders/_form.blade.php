@extends('layouts/layoutMaster')

@section('title', 'Perfil de ' . $rider->full_name)

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('page-style')
  @vite('resources/assets/vendor/scss/pages/page-user-view.scss')
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/modal-edit-user.js', 'resources/assets/js/app-user-view.js', 'resources/assets/js/app-user-view-account.js', 'resources/assets/js/admin/rider/metrics/index.js'])
@endsection

@section('content')
  <div class="row" id="user-profile-view">
    <div class="col-xl-4 col-lg-5 order-1 order-md-0" id="profile-sidebar">
      <div class="card mb-6">
        <div class="card-body pt-12">
          <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-label-secondary btn-sm toggle-profile-sidebar">
              {{-- Corrección: Se cambió "ti-menu-2" por "tabler-menu-2" --}}
              <i class="ti-menu-2"></i>
            </button>
          </div>

          <div class="user-avatar-section">
            <div class=" d-flex align-items-center flex-column">
              <img class="img-fluid rounded mb-4" src="{{ asset('assets/img/avatars/1.png') }}" height="120"
                width="120" alt="User avatar" />
              <div class="user-info text-center">
                <h5>{{ $rider->full_name }}</h5>
                <span class="badge bg-label-secondary">Rider</span>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
            <div class="d-flex align-items-center me-5 gap-4">
              <div class="avatar">
                <div class="avatar-initial bg-label-primary rounded">
                  {{-- Corrección: Se cambió "ti ti-clock-hour-3" por "ti tabler-clock-hour-3" --}}
                  <i class="ti tabler-clock-hour-3 ti-lg"></i>
                </div>
              </div>
              <div>
                <h5 class="mb-0">{{ $rider->weekly_contract_hours }}</h5>
                <span>Horas Contrato</span>
              </div>
            </div>
            <div class="d-flex align-items-center gap-4">
              <div class="avatar">
                <div class="avatar-initial bg-label-primary rounded">
                  {{-- Corrección: Se cambió "ti ti-pencil" por "ti tabler-pencil" --}}
                  <i class="ti tabler-pencil ti-lg"></i>
                </div>
              </div>
              <div>
                <h5 class="mb-0">{{ $rider->edits_remaining }}</h5>
                <span>Ediciones Restantes</span>
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
                <span
                  class="badge bg-label-{{ $rider->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($rider->status) }}</span>
              </li>
              <li class="mb-2">
                <span class="h6">DNI:</span>
                <span>{{ $rider->dni }}</span>
              </li>
              <li class="mb-2">
                <span class="h6">Contacto:</span>
                <span>{{ $rider->phone }}</span>
              </li>
              <li class="mb-2">
                <span class="h6">Fecha de Inicio:</span>
                <span>{{ \Carbon\Carbon::parse($rider->start_date)->format('d/m/Y') }}</span>
              </li>
            </ul>
            <div class="d-flex justify-content-center">
              <a href="{{ route('admin.riders.edit', $rider->id) }}" class="btn btn-primary me-4"
                data-bs-target="#editUser" data-bs-toggle="modal">Editar</a>
              <a href="javascript:;" class="btn btn-label-danger suspend-user">Suspender</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-8 col-lg-7 order-0 order-md-1" id="profile-content">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-6 row-gap-2">
          <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#metrics-tab" role="tab" aria-selected="true">
              <i class="ti ti-chart-bar ti-sm me-1_5"></i>Métricas
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#assignments-tab" role="tab" aria-selected="false">
              <i class="ti ti-history ti-sm me-1_5"></i>Asignaciones
            </a>
          </li>
        </ul>
      </div>
      <div class="tab-content p-0">
        <div class="tab-pane fade show active" id="metrics-tab" role="tabpanel">
          @include(
              'content.admin.riders.metrics.partials._metrics_content',
              compact('rider', 'transports'))
        </div>
        <div class="tab-pane fade" id="assignments-tab" role="tabpanel">
          @include('content.admin.riders.metrics.partials._assignments_table', [
              'assignments' => $rider->assignments,
          ])
        </div>
      </div>
    </div>
  </div>
@endsection
