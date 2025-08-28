{{-- Contenido de la Navbar --}}
<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

  {{-- Menú Hamburguesa (Visible en móvil) --}}
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      {{-- CORRECCIÓN: Se añadió "tabler-" --}}
      <i class="ti tabler-menu-2 ti-sm"></i>
    </a>
  </div>

  {{-- Perfil de Usuario y Logout (Alineado a la derecha) --}}
  <ul class="navbar-nav flex-row align-items-center ms-auto">
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="h-auto rounded-circle">
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="javascript:void(0);">
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="h-auto rounded-circle">
                </div>
              </div>
              <div class="flex-grow-1">
                <span class="fw-medium d-block">
                  @if (Illuminate\Support\Facades\Auth::guard('web')->check())
                    {{ Illuminate\Support\Facades\Auth::guard('web')->user()->name }}
                  @elseif(Illuminate\Support\Facades\Auth::guard('rider')->check())
                    {{ Illuminate\Support\Facades\Auth::guard('rider')->user()->full_name }}
                  @endif
                </span>
                <small class="text-muted">
                  @if (Illuminate\Support\Facades\Auth::guard('web')->check())
                    Admin
                  @elseif(Illuminate\Support\Facades\Auth::guard('rider')->check())
                    Rider
                  @endif
                </small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          @if (Illuminate\Support\Facades\Auth::guard('web')->check())
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <a class="dropdown-item" href="{{ route('admin.logout') }}"
                onclick="event.preventDefault(); this.closest('form').submit();">
                {{-- CORRECCIÓN: Se añadió "tabler-" --}}
                <i class='ti tabler-logout me-2'></i>
                <span class="align-middle">Cerrar Sesión</span>
              </a>
            </form>
          @elseif(Illuminate\Support\Facades\Auth::guard('rider')->check())
            <form method="POST" action="{{ route('rider.logout') }}">
              @csrf
              <a class="dropdown-item" href="{{ route('rider.logout') }}"
                onclick="event.preventDefault(); this.closest('form').submit();">
                {{-- CORRECCIÓN: Se añadió "tabler-" --}}
                <i class='ti tabler-logout me-2'></i>
                <span class="align-middle">Cerrar Sesión</span>
              </a>
            </form>
          @endif
        </li>
      </ul>
    </li>
  </ul>
</div>
