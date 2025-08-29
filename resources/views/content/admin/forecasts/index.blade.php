@extends('layouts/layoutMaster')
@section('title', 'Gestión de Forecasts')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Forecasts
  </h4>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="m-0">Listado de Forecasts</h5>
      <a href="{{ route('admin.forecasts.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Importar Forecast
      </a>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Ciudad</th>
            <th>Semana del</th>
            <th>Fecha de subida</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($forecasts as $forecast)
            <tr>
              <td>{{ $forecast->city }}</td>
              <td>{{ $forecast->week_start_date->format('d/m/Y') }}</td>
              <td>{{ $forecast->created_at->format('d/m/Y H:i') }}</td>
              <td>
                <form action="{{ route('admin.forecasts.destroy', $forecast) }}" method="POST"
                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar este forecast y todas las reservas asociadas?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="ti ti-trash me-1"></i> Eliminar
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center">No hay forecasts importados.</td>
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
