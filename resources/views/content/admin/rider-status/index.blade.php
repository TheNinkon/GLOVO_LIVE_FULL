@extends('layouts/layoutMaster')

@section('title', 'Estado de Riders')

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/clipboard/clipboard.js'])
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Estado de Riders
  </h4>

  <div class="card">
    <div class="card-header text-center">
      <div class="d-flex justify-content-between align-items-center">
        <a href="{{ $nav['prev'] ?? '#' }}"
          class="btn btn-icon rounded-pill {{ !($nav['prev'] ?? null) ? 'disabled' : '' }}"><i
            class="ti tabler-chevron-left"></i></a>
        <div>
          <h5 class="mb-0">{{ $nav['current'] }}</h5>
          <div class="dropdown mt-1">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="cityDropdown"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ $selectedCity }}
            </button>
            <div class="dropdown-menu" aria-labelledby="cityDropdown">
              @if (isset($availableCities))
                @foreach ($availableCities as $city)
                  <a class="dropdown-item"
                    href="{{ route('admin.rider-status.index', ['city' => $city, 'week' => $startOfWeek->format('Y-m-d')]) }}">{{ $city }}</a>
                @endforeach
              @endif
            </div>
          </div>
        </div>
        <a href="{{ $nav['next'] ?? '#' }}"
          class="btn btn-icon rounded-pill {{ !($nav['next'] ?? null) ? 'disabled' : '' }}"><i
            class="ti tabler-chevron-right"></i></a>
      </div>
    </div>

    <div class="card-body">
      @if (isset($riders) && $riders->count() > 0)
        <div class="accordion" id="riderStatusAccordion">
          @foreach ($riders as $rider)
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-{{ $rider->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapse-{{ $rider->id }}" aria-expanded="false"
                  aria-controls="collapse-{{ $rider->id }}">
                  <div class="d-flex justify-content-between w-100 me-3">
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <span
                          class="avatar-initial rounded-circle bg-label-secondary">{{ substr($rider->full_name, 0, 1) }}</span>
                      </div>
                      <span class="fw-bold">{{ $rider->full_name }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-label-info me-2">
                        Reservadas: <strong>{{ number_format($rider->reserved_hours, 1) }}h</strong>
                      </span>
                      <span class="badge bg-label-primary me-2">
                        Contrato: <strong>{{ $rider->weekly_contract_hours }}h</strong>
                      </span>
                      <span class="badge bg-label-warning">
                        Comodines: <strong>{{ $rider->edits_remaining }}</strong>
                      </span>
                    </div>
                  </div>
                </button>
              </h2>
              <div id="collapse-{{ $rider->id }}" class="accordion-collapse collapse"
                aria-labelledby="heading-{{ $rider->id }}" data-bs-parent="#riderStatusAccordion">
                <div class="accordion-body">
                  <div class="d-flex justify-content-between align-items-start">
                    <textarea readonly class="form-control-plaintext bg-light p-2 rounded w-100"
                      style="font-family: monospace; font-size: 0.9rem;" rows="{{ substr_count($rider->formatted_schedule, "\n") + 1 }}">{{ $rider->formatted_schedule }}</textarea>
                    <button class="btn btn-sm btn-icon btn-outline-secondary copy-btn ms-2 flex-shrink-0"
                      data-clipboard-text="{{ $rider->formatted_schedule }}" title="Copiar para Excel">
                      <i class="ti tabler-copy"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="alert alert-warning text-center">
          {{ $error ?? 'No hay riders con horarios en esta ciudad o no se encontró un forecast para esta semana.' }}
        </div>
      @endif
    </div>
  </div>
@endsection

@section('page-script')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const clipboard = new ClipboardJS('.copy-btn');
      clipboard.on('success', function(e) {
        const icon = e.trigger.querySelector('.ti');
        if (icon) {
          icon.classList.replace('tabler-copy', 'tabler-check');
          e.trigger.classList.add('btn-success');
          setTimeout(() => {
            icon.classList.replace('tabler-check', 'tabler-copy');
            e.trigger.classList.remove('btn-success');
          }, 2000);
        }
        e.clearSelection();
      });
    });
  </script>
@endsection
