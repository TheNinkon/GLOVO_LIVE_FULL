@extends('layouts/layoutMaster')

@section('title', 'Asignar Cuenta')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Cuentas /</span> Asignar
  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">Asignar Cuenta: {{ $account->courier_id }} ({{ $account->email }})</h5>
        <div class="card-body">
          <form action="{{ route('admin.assignments.store', $account) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="rider_id" class="form-label">Seleccionar Rider Disponible</label>
              <select id="rider_id" name="rider_id" class="form-select" required>
                <option value="">-- Elige un Rider --</option>
                @forelse ($availableRiders as $id => $name)
                  <option value="{{ $id }}">{{ $name }}</option>
                @empty
                  <option value="" disabled>No hay riders disponibles en este momento</option>
                @endforelse
              </select>
              <small class="text-muted">Solo se muestran los riders activos que no tienen otra cuenta asignada.</small>
            </div>
            <div class="mb-3">
              <label for="start_at" class="form-label">Fecha de Asignación</label>
              <input class="form-control" type="datetime-local" id="start_at" name="start_at"
                value="{{ now()->format('Y-m-d\TH:i') }}" required />
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-primary me-2">Confirmar Asignación</button>
              <a href="{{ route('admin.accounts.index') }}" class="btn btn-label-secondary">Cancelar</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
