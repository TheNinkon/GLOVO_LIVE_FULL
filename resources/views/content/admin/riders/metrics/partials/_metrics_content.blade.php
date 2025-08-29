<div class="row" id="kpis-container">
  {{-- Los KPIs se renderizarán aquí dinámicamente desde el JavaScript --}}
</div>

<div class="card mt-4">
  @include('content.admin.riders.metrics.partials._filters', compact('cities', 'transports'))
  @include('content.admin.riders.metrics.partials._table')
</div>
