@extends('layouts/layoutMaster')

@section('title', 'Mi Horario Semanal')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-style')
  <style>
    /* --- Layout Principal --- */
    .schedule-card {
      display: flex;
      flex-direction: column;
      height: calc(100vh - 10rem);
      overflow: hidden;
      padding: 0 !important
    }

    .schedule-card .card-body {
      flex-grow: 1;
      padding: 0;
      display: flex;
      flex-direction: column;
      overflow: hidden
    }

    .schedule-header {
      flex-shrink: 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: .75rem 1rem;
      border-bottom: 1px solid #e7e7e7;
      background: #fff;
      z-index: 11
    }

    .schedule-week-nav {
      font-size: 1rem;
      font-weight: 500
    }

    .deadline-wrapper {
      display: flex;
      align-items: center;
      gap: .75rem
    }

    .deadline-label {
      font-size: .85rem;
      font-weight: 500;
      color: #6c757d
    }

    #deadline-display {
      display: flex;
      align-items: center;
      gap: .5rem;
      font-size: .9rem;
      padding: .25rem .75rem;
      border-radius: 15px;
      background-color: #f8f8f8;
      border: 1px solid #e7e7e7;
      color: #6c757d
    }

    #deadline-display.expiring #countdown-text {
      background-color: rgba(255, 152, 0, .1);
      color: #ff9800;
      padding: .1rem .4rem;
      border-radius: 4px
    }

    #deadline-display.expired #countdown-text {
      background-color: rgba(244, 67, 54, .1);
      color: #f44336;
      padding: .1rem .4rem;
      border-radius: 4px
    }

    .sticky-header {
      position: sticky;
      top: 0;
      background-color: #fff;
      z-index: 10;
      flex-shrink: 0;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05)
    }

    .date-selector {
      display: flex;
      width: 100%;
      list-style: none;
      padding-left: 0;
      margin-bottom: 0;
      padding: .5rem
    }

    .date-selector-item {
      flex: 1 1 0;
      text-align: center;
      padding: .75rem .5rem;
      border-radius: 50px;
      cursor: pointer;
      transition: all .2s ease-in-out
    }

    .date-selector-item.active {
      background-color: #f33a58;
      color: #fff
    }

    .date-selector-item.active .day-name,
    .date-selector-item.active .day-number {
      color: #fff
    }

    .schedule-tabs {
      display: flex;
      justify-content: space-around;
      border-top: 1px solid #e7e7e7
    }

    .schedule-tab {
      padding: 1rem;
      cursor: pointer;
      font-weight: 600;
      color: #6c757d;
      border-bottom: 3px solid transparent
    }

    .schedule-tab.active {
      color: #f33a58;
      border-bottom-color: #f33a58
    }

    .schedule-scroll-area {
      flex-grow: 1;
      overflow-y: auto;
      padding: 1rem 0 120px 0
    }

    .schedule-content {
      display: none
    }

    .schedule-content.active {
      display: block
    }

    .daily-schedule-slot {
      display: flex;
      align-items: stretch;
      margin: 0 1rem 4px 1rem;
      min-height: 50px
    }

    .daily-schedule-slot .slot-time {
      flex-basis: 70px;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      color: #555
    }

    .daily-schedule-slot .slot-bar {
      flex-grow: 1;
      border-radius: 6px;
      transition: all .2s ease;
      padding: .5rem 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 1.1rem
    }

    /* Estilo de slots disponibles en blanco */
    .slot-bar.available {
      background-color: #fff;
      border: 1px solid #dcdcdc;
      /* Añadir un borde para distinguirlo */
      cursor: pointer;
    }

    .slot-bar.available:hover {
      background-color: #f0f2f5;
    }

    .slot-bar.available .ti-plus {
      color: #aaa;
      opacity: 0;
      transition: opacity .2s
    }

    .slot-bar.available:hover .ti-plus {
      opacity: 1
    }

    .slot-bar.mine {
      background-color: #28a745;
      color: #fff;
      font-weight: 700;
      cursor: pointer;
    }

    .slot-bar.locked {
      background-color: #dee2e6;
      cursor: not-allowed;
      color: #6c757d;
    }

    .slot-bar.locked .ti.tabler-lock {
      color: #aaa;
    }

    .slot-bar .ti.tabler-minus {
      color: #fff;
    }

    .floating-summary-bar {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: #fff;
      box-shadow: 0 -2px 10px rgba(0, 0, 0, .08);
      padding: 1rem;
      display: flex;
      justify-content: space-around;
      align-items: center;
      z-index: 11;
      border-top: 1px solid #e7e7e7
    }

    .summary-item {
      text-align: center
    }

    .summary-item .value {
      font-size: 1.2rem;
      font-weight: 700
    }

    .summary-item .label {
      font-size: .8rem;
      color: #6c757d
    }

    .reserved-slot-item {
      background-color: #f8f9fa;
      padding: 1rem;
      border-radius: 6px;
      margin: 0 1rem .5rem 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: 600
    }

    .not-clickable {
      pointer-events: none;
      opacity: 0.6;
    }
  </style>
@endsection

@section('content')
  <div class="card schedule-card"
    @if ($scheduleData) data-schedule-data="{{ json_encode($scheduleData) }}" @endif
    data-select-url="{{ route('rider.schedule.select') }}" data-deselect-url="{{ route('rider.schedule.deselect') }}"
    data-csrf-token="{{ csrf_token() }}" data-default-day="{{ $defaultDay }}">

    <div class="schedule-header">
      <a href="{{ $prevWeek ?? '#' }}" class="btn btn-icon rounded-pill {{ !$prevWeek ? 'disabled' : '' }}"><i
          class="ti tabler-chevron-left"></i></a>
      <div class="schedule-week-nav">{{ $startOfWeek->translatedFormat('j M') }} -
        {{ $endOfWeek->translatedFormat('j M, Y') }}</div>
      @if ($deadline)
        <div class="deadline-wrapper">
          <span class="deadline-label">Plazo de reserva:</span>
          <div id="deadline-display" data-deadline="{{ $deadline->toIso8601String() }}">
            <i class="ti tabler-clock"></i>
            <span id="countdown-text">Calculando...</span>
          </div>
        </div>
      @endif
      <a href="{{ $nextWeek ?? '#' }}" class="btn btn-icon rounded-pill {{ !$nextWeek ? 'disabled' : '' }}"><i
          class="ti tabler-chevron-right"></i></a>
    </div>

    <div class="card-body">
      <div class="sticky-header">
        <ul class="date-selector">
          @if ($weekDates)
            @foreach ($weekDates as $day)
              <li class="date-selector-item @if ($day['full'] === $defaultDay) active @endif"
                data-date="{{ $day['full'] }}">
                <div class="day-name">{{ $day['dayName'] }}</div>
                <div class="day-number">{{ $day['dayNum'] }}</div>
              </li>
            @endforeach
          @endif
        </ul>
        <div class="schedule-tabs">
          <div class="schedule-tab active" data-tab="disponibles">Horas Disponibles</div>
          <div class="schedule-tab" data-tab="reservadas">Horas Reservadas</div>
        </div>
      </div>

      <div class="schedule-scroll-area">
        <div id="disponibles-content" class="schedule-content active">
          @if (!$scheduleData)
            <div class="alert alert-warning text-center m-4">No hay un horario disponible para esta semana.</div>
          @else
            <div class="text-center p-4">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
          @endif
        </div>
        <div id="reservadas-content" class="schedule-content"></div>
      </div>
    </div>

    <div class="floating-summary-bar">
      <div class="summary-item">
        <div id="summary-contratadas" class="value">{{ $summary['contractedHours'] ?? 0 }}h</div>
        <div class="label">Contratadas</div>
      </div>
      <div class="summary-item">
        <div id="summary-reservadas" class="value">{{ number_format($summary['reservedHours'] ?? 0, 1) }}h</div>
        <div class="label">Reservadas</div>
      </div>
      <div class="summary-item">
        <div id="summary-comodines" class="value">{{ $summary['wildcards'] ?? 0 }}</div>
        <div class="label">Comodines</div>
      </div>
    </div>
  </div>
@endsection

@section('page-script')
  @vite(['resources/assets/js/schedule-picker.js'])
@endsection
