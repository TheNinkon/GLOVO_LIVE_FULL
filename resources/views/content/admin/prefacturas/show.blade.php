@extends('layouts/layoutMaster')

@section('title', 'Detalles de Prefactura')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/prefacturas-show.js'])
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Prefacturas /</span> Detalles
  </h4>

  {{-- BLOQUE DE MÉTRICAS --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card p-3">
        <h5 class="card-title text-success">Cash Out Total</h5>
        <h3 class="fw-bold mb-0">{{ number_format($metrics->total_cash_out, 2) }} €</h3>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-3">
        <h5 class="card-title text-danger">Cash Out Pendiente</h5>
        <h3 class="fw-bold mb-0">{{ number_format($metrics->cash_out_pending, 2) }} €</h3>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-3">
        <h5 class="card-title text-info">Tips Total</h5>
        <h3 class="fw-bold mb-0">{{ number_format($metrics->total_tips, 2) }} €</h3>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-3">
        <h5 class="card-title text-warning">Tips Pendientes</h5>
        <h3 class="fw-bold mb-0">{{ number_format($metrics->tips_pending, 2) }} €</h3>
      </div>
    </div>
  </div>
  {{-- FIN BLOQUE DE MÉTRICAS --}}

  <div class="card">
    <div class="card-header">
      <h5 class="card-title">Prefactura de {{ $prefactura->start_at->format('d M Y') }} a
        {{ $prefactura->end_at->format('d M Y') }}</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="items-table">
          <thead>
            <tr>
              <th>Courier ID</th>
              <th>Cash Out</th>
              <th>Tips</th>
              <th>Asignaciones</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($prefactura->items as $item)
              <tr>
                <td>{{ $item->courier_id }}</td>
                <td>
                  <span class="d-block fw-bold">Total: {{ number_format($item->cash_out, 2) }} €</span>
                  @php
                    $cashOutAssigned = $item->assignments->where('type', 'cash_out')->sum('amount');
                  @endphp
                  <small>Asignado: {{ number_format($cashOutAssigned, 2) }} €</small><br>
                  <small class="text-danger">Restante: {{ number_format($item->cash_out - $cashOutAssigned, 2) }}
                    €</small>
                </td>
                <td>
                  <span class="d-block fw-bold">Total: {{ number_format($item->tips, 2) }} €</span>
                  @php
                    $tipsAssigned = $item->assignments->where('type', 'tips')->sum('amount');
                  @endphp
                  <small>Asignado: {{ number_format($tipsAssigned, 2) }} €</small><br>
                  <small class="text-info">Restante: {{ number_format($item->tips - $tipsAssigned, 2) }} €</small>
                </td>
                <td>
                  @if ($item->assignments->count() > 0)
                    <ul class="list-unstyled">
                      @foreach ($item->assignments as $assignment)
                        <li>
                          <span class="fw-bold">{{ $assignment->rider->full_name ?? 'Rider eliminado' }}</span> -
                          {{ number_format($assignment->amount, 2) }} € ({{ ucfirst($assignment->type) }})
                          <br>
                          <small class="text-muted">Estado: <span
                              class="badge rounded-pill bg-label-{{ $assignment->status === 'paid' || $assignment->status === 'deducted' ? 'success' : 'secondary' }}">{{ ucfirst($assignment->status) }}</span></small>
                        </li>
                      @endforeach
                    </ul>
                  @else
                    <span class="badge bg-label-warning">Sin asignar</span>
                  @endif
                </td>
                <td>
                  <form class="assign-form" data-item-id="{{ $item->id }}">
                    <div class="mb-2">
                      <select name="rider_id" class="form-select select2" required>
                        <option value="">Seleccionar Rider</option>
                        @foreach ($riders as $id => $name)
                          <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="input-group mb-2">
                      <span class="input-group-text">€</span>
                      <input type="number" name="amount" class="form-control" placeholder="Monto" step="0.01"
                        required />
                      <select name="type" class="form-select">
                        <option value="cash_out">Cash Out</option>
                        <option value="tips">Tips</option>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Asignar</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
