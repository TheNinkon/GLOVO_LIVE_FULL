@extends('layouts/layoutMaster')

@section('title', 'Dashboard del Rider')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Dashboard /</span> Mi Panel
  </h4>

  {{-- SECCIÓN DE ASIGNACIÓN ACTUAL --}}
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Asignación Actual</h5>
      @if ($activeAssignment)
        <small class="text-muted">Asignada el {{ $activeAssignment->start_at->format('d/m/Y') }}</small>
      @endif
    </div>
    <div class="card-body">
      @if ($activeAssignment && $activeAssignment->account)
        <div class="d-flex align-items-center">
          <div class="avatar avatar-lg me-3">
            {{-- CORRECCIÓN: Se añadió "tabler-" --}}
            <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti tabler-user-check ti-md"></i></span>
          </div>
          <div>
            <h6 class="mb-0">{{ $activeAssignment->account->courier_id }}</h6>
            <small>{{ $activeAssignment->account->email }}</small>
          </div>
        </div>
      @else
        <div class="alert alert-secondary mb-0" role="alert">
          Actualmente no tienes ninguna cuenta asignada.
        </div>
      @endif
    </div>
  </div>


  {{-- NUEVA SECCIÓN: MÉTRICAS DE DESEMPEÑO --}}
  <div class="row">
    <div class="col-12 mb-4">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tu Desempeño</h5>
        @if ($kpis['has_metrics'])
          <small class="text-muted">Datos actualizados al {{ $kpis['latest_date'] }}</small>
        @endif
      </div>
    </div>

    @if ($kpis['has_metrics'])
      {{-- Pedidos (Último día) --}}
      <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-border-shadow-primary h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2 pb-1">
              <div class="avatar me-2">
                {{-- CORRECCIÓN: Se añadió "tabler-" --}}
                <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-package ti-md"></i></span>
              </div>
              <h4 class="ms-1 mb-0">{{ number_format($kpis['last_day_orders'], 0) }}</h4>
            </div>
            <p class="mb-1">Pedidos (Último día)</p>
          </div>
        </div>
      </div>
      {{-- Tiempo Promedio (Último día) --}}
      <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-border-shadow-info h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2 pb-1">
              <div class="avatar me-2">
                {{-- CORRECCIÓN: Se añadió "tabler-" --}}
                <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-hourglass-low ti-md"></i></span>
              </div>
              <h4 class="ms-1 mb-0">{{ number_format($kpis['last_day_cdt'], 1) }} min</h4>
            </div>
            <p class="mb-1">Tiempo/Entrega (Último día)</p>
          </div>
        </div>
      </div>
      {{-- Pedidos (7 Días) --}}
      <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-border-shadow-success h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2 pb-1">
              <div class="avatar me-2">
                {{-- CORRECCIÓN: Se añadió "tabler-" --}}
                <span class="avatar-initial rounded bg-label-success"><i
                    class="ti tabler-calendar-stats ti-md"></i></span>
              </div>
              <h4 class="ms-1 mb-0">{{ number_format($kpis['last_7_days_orders'], 0) }}</h4>
            </div>
            <p class="mb-1">Pedidos (Últimos 7 días)</p>
          </div>
        </div>
      </div>
      {{-- Pedidos (30 Días) --}}
      <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-border-shadow-secondary h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2 pb-1">
              <div class="avatar me-2">
                {{-- CORRECCIÓN: Se añadió "tabler-" --}}
                <span class="avatar-initial rounded bg-label-secondary"><i class="ti tabler-calendar ti-md"></i></span>
              </div>
              <h4 class="ms-1 mb-0">{{ number_format($kpis['last_30_days_orders'], 0) }}</h4>
            </div>
            <p class="mb-1">Pedidos (Últimos 30 días)</p>
          </div>
        </div>
      </div>
    @else
      <div class="col-12">
        <div class="alert alert-info" role="alert">
          <h6 class="alert-heading mb-1">¡Pronto verás tus métricas aquí!</h6>
          <span>Aún no tenemos datos de desempeño registrados para tu cuenta. Una vez completes tus primeros días, tus
            estadísticas aparecerán en esta sección.</span>
        </div>
      </div>
    @endif
  </div>
@endsection
