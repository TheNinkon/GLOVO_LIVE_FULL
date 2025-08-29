@extends('layouts/layoutMaster')

@section('title', 'Gestión de Forecasts')

{{-- Carga de Estilos Específicos de la Página para Datatables e iconos --}}
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

{{-- Carga de Scripts Específicos de la Página --}}
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'])
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Forecasts
  </h4>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="m-0">Listado de Forecasts</h5>
      <a href="{{ route('admin.forecasts.create') }}" class="btn btn-primary">
        {{-- CAMBIO AQUÍ: Usar el formato correcto `ti tabler-plus` --}}
        <i class="ti tabler-plus me-1"></i> Importar Forecast
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
                    {{-- CAMBIO AQUÍ: Usar el formato correcto `ti tabler-trash` --}}
                    <i class="ti tabler-trash me-1"></i> Eliminar
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
