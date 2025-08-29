<div class="card-header border-bottom">
  <h5 class="card-title">Historial de Asignaciones</h5>
</div>
<div class="card-body">
  <div class="table-responsive text-nowrap">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Cuenta Asignada</th>
          <th>Fecha de Asignación</th>
          <th>Fecha de Fin</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($assignments as $assignment)
          <tr>
            <td>{{ $assignment->account->courier_id }}</td>
            <td>{{ \Carbon\Carbon::parse($assignment->start_at)->format('d/m/Y H:i') }}</td>
            <td>{{ $assignment->end_at ? \Carbon\Carbon::parse($assignment->end_at)->format('d/m/Y H:i') : 'N/A' }}</td>
            <td><span
                class="badge bg-label-{{ $assignment->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($assignment->status) }}</span>
            </td>
            <td>
              @if ($assignment->status == 'active')
                <form action="{{ route('admin.assignments.end', $assignment->id) }}" method="POST" class="d-inline">
                  @csrf
                  {{-- CORRECCIÓN: Usar la clase correcta para el icono de "x" --}}
                  <button type="submit" class="btn btn-sm btn-danger"><i class="ti ti-x me-1"></i> Terminar</button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">No hay asignaciones para este rider.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
