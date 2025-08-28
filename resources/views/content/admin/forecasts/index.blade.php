@extends('layouts/layoutMaster')
@section('title', 'Gestión de Forecasts')

@section('content')
  <div class="d-flex justify-content-between align-items-center py-3 mb-4">
    <h4 class="mb-0">
      <span class="text-muted fw-light">Admin /</span> Forecasts
    </h4>
    <a href="{{ route('admin.forecasts.create') }}" class="btn btn-primary">
      <i class="ti tabler-plus me-1"></i> Importar Forecast
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Ciudad</th>
            <th>Semana del</th>
            <th>Fecha de subida</th>
          </tr>
        </thead>
        <tbody>
          @forelse($forecasts as $forecast)
            <tr>
              <td>{{ $forecast->city }}</td>
              <td>{{ $forecast->week_start_date->format('d/m/Y') }}</td>
              <td>{{ $forecast->created_at->format('d/m/Y H:i') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center">No hay forecasts importados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="mt-3">
        {{ $forecasts->links() }}
      </div>
    </div>
  </div>
@endsection
