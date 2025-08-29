@extends('layouts/layoutMaster')

@section('title', 'Dashboard de Cobertura')

@section('page-style')
  <style>
    .coverage-table {
      border-collapse: collapse;
      width: 100%;
      table-layout: fixed;
    }

    .coverage-table th,
    .coverage-table td {
      border: 1px solid #e7e7e8;
      text-align: center;
      padding: 0.5rem;
      font-weight: 600;
    }

    .coverage-table thead th {
      position: sticky;
      top: 0;
      background-color: #fff;
      z-index: 10;
    }

    .time-header {
      background-color: #f7f7f8 !important;
      min-width: 85px;
    }

    .day-header {
      min-width: 120px;
    }

    .coverage-cell {
      font-size: 0.95rem;
      height: 50px;
    }

    /* Colores de Estado */
    .status-gap {
      background-color: rgba(255, 62, 29, 0.1);
    }

    /* Rojo */
    .status-ok {
      background-color: rgba(113, 221, 55, 0.1);
    }

    /* Verde */
    .status-over {
      background-color: rgba(105, 108, 255, 0.1);
    }

    /* Azul */
    .status-zero {
      background-color: #f7f7f8;
      color: #a3a4c2;
    }

    .metric-value {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.3rem;
    }

    .metric-value .ti {
      font-size: 1.1rem;
    }
  </style>
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Dashboard de Cobertura
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
                    href="{{ route('admin.coverage.index', ['city' => $city, 'week' => $startOfWeek->format('Y-m-d')]) }}">{{ $city }}</a>
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
      @if (isset($coverageData))
        <div class="table-responsive text-nowrap">
          <table class="coverage-table">
            <thead>
              <tr>
                <th class="time-header">Hora</th>
                @foreach ($days as $day)
                  <th class="day-header">{{ $day['name'] }} <br> <small>{{ $day['date'] }}</small></th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach ($timeSlots as $time)
                <tr>
                  <td class="time-header">{{ $time }}</td>
                  @foreach ($days as $day)
                    @php
                      $slot = $coverageData[$day['key']][$time] ?? ['demand' => 0, 'booked' => 0];
                      $gap = $slot['demand'] - $slot['booked'];

                      $statusClass = 'status-zero'; // Por defecto
                      if ($slot['demand'] > 0) {
                          if ($gap > 0) {
                              $statusClass = 'status-gap';
                          }
                          // Faltan
                          elseif ($gap == 0) {
                              $statusClass = 'status-ok';
                          }
                          // Completo
                          else {
                              $statusClass = 'status-over';
                          } // Sobra
                      }
                    @endphp
                    <td class="coverage-cell {{ $statusClass }}">
                      <div class="metric-value" title="Forecast">
                        <i class="ti tabler-cloud text-secondary"></i>
                        <span>{{ $slot['demand'] }}</span>
                      </div>
                      <div class="metric-value" title="Tus trabajadores">
                        <i class="ti tabler-user-check text-success"></i>
                        <span>{{ $slot['booked'] }}</span>
                      </div>
                    </td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-warning text-center">
          {{ $error ?? 'No hay datos de cobertura para mostrar.' }}
        </div>
      @endif
    </div>
  </div>
@endsection
