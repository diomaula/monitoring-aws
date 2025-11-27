<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    @can('superadmin')
    <li class="nav-item">
      <a class="nav-link {{ request()->is('users') ? '' : 'collapsed' }}">
        <i class="bi bi-person"></i>
        <span>Users</span>
      </a>
    </li>
    @endcan

    @canany(['forecast'])
      <li class="nav-item">
        <a class="nav-link {{ request()->is('dashboard') ? '' : 'collapsed' }}" href="/dashboard">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

    <li class="nav-item">
      <a class="nav-link {{ request()->is('aws/*') ? '' : 'collapsed' }}"
        data-bs-target="#components-nav"
        data-bs-toggle="collapse"
        href="#">
        <i class="bi bi-menu-button-wide"></i><span>AWS</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="components-nav"
        class="nav-content collapse {{ request()->is('aws/*') ? 'show' : '' }}">
        <li>
          <a href="{{ url('aws/5000000031') }}" class="{{ request()->is('aws/5000000031') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>AWS Digi Banyuwangi</span>
          </a>
        </li>
        <li>
          <a href="{{ url('aws/3000000046') }}" class="{{ request()->is('aws/3000000046') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>AWS Maritim Gilimanuk</span>
          </a>
        </li>
        <li>
          <a href="{{ url('aws/3000000007') }}" class="{{ request()->is('aws/3000000007') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>AWS Maritim Ketapang</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#laporan-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-text"></i><span>Laporan</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="laporan-nav" class="nav-content collapse">
        <li>
          <a href="{{ url('laporan') }}" class="{{ request()->is('laporan') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Laporan Bulanan</span>
          </a>
        </li>
      </ul>
    </li>
    @endcanany

    @canany(['teknisi'])
      <li class="nav-item">
        <a class="nav-link {{ request()->is('dashboard') ? '' : 'collapsed' }}" href="/dashboard">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('aws/*') ? '' : 'collapsed' }}" 
          data-bs-target="#components-nav" 
          data-bs-toggle="collapse" 
          href="#">
          <i class="bi bi-menu-button-wide"></i><span>AWS</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" 
            class="nav-content collapse {{ request()->is('aws/*') ? 'show' : '' }}">
          <li>
            <a href="{{ url('aws/5000000031') }}" class="{{ request()->is('aws/5000000031') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>AWS Digi Banyuwangi</span>
            </a>
          </li>
          <li>
            <a href="{{ url('aws/3000000046') }}" class="{{ request()->is('aws/3000000046') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>AWS Maritim Gilimanuk</span>
            </a>
          </li>
          <li>
            <a href="{{ url('aws/3000000007') }}" class="{{ request()->is('aws/3000000007') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>AWS Maritim Ketapang</span>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#laporanHarian-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Laporan</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="laporanHarian-nav" class="nav-content collapse">
          <li>
            <a href="{{ url('laporanHarian') }}" class="{{ request()->is('laporanHarian') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Laporan Harian</span>
            </a>
          </li>
          {{-- <li>
            <a href="{{ url('laporan/mingguan') }}" class="{{ request()->is('laporan/mingguan') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Laporan Mingguan</span>
            </a>
          </li> --}}
        </ul>
      </li>
    @endcanany

    {{-- <li class="nav-item" style="position: absolute; bottom: 10px; width: 90%;">
        <a class="nav-link {{ request()->is('logout') ? '' : 'collapsed' }}" href="/logout">
    <i class="bi bi-door-open"></i>
    <span>Logout</span>
    </a>
    </li> --}}

    <li class="nav-item" style="position: absolute; bottom: 10px; width: 90%;">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="nav-link {{ request()->is('logout') ? '' : 'collapsed' }}">
          <i class="bi bi-door-open"></i>
          <span>Logout</span>
        </button>
      </form>
    </li>

  </ul>

</aside><!-- End Sidebar-->