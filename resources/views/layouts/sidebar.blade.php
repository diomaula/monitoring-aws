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
          {{-- <li>
            <a href="{{ url('laporan/mingguan') }}" class="{{ request()->is('laporan/mingguan') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Laporan Mingguan</span>
            </a>
          </li> --}}
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
    </li>  --}}

    <li class="nav-item" style="position: absolute; bottom: 10px; width: 90%;">
      <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="nav-link {{ request()->is('logout') ? '' : 'collapsed' }}">
              <i class="bi bi-door-open"></i>
              <span>Logout</span>
          </button>
      </form>
  </li>


    {{-- <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-text"></i><span>Forms</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="forms-elements.html">
            <i class="bi bi-circle"></i><span>Form Elements</span>
          </a>
        </li>
        <li>
          <a href="forms-layouts.html">
            <i class="bi bi-circle"></i><span>Form Layouts</span>
          </a>
        </li>
        <li>
          <a href="forms-editors.html">
            <i class="bi bi-circle"></i><span>Form Editors</span>
          </a>
        </li>
        <li>
          <a href="forms-validation.html">
            <i class="bi bi-circle"></i><span>Form Validation</span>
          </a>
        </li>
      </ul>
    </li><!-- End Forms Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-layout-text-window-reverse"></i><span>Tables</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="tables-general.html">
            <i class="bi bi-circle"></i><span>General Tables</span>
          </a>
        </li>
        <li>
          <a href="tables-data.html">
            <i class="bi bi-circle"></i><span>Data Tables</span>
          </a>
        </li>
      </ul>
    </li><!-- End Tables Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-bar-chart"></i><span>Charts</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="charts-chartjs.html">
            <i class="bi bi-circle"></i><span>Chart.js</span>
          </a>
        </li>
        <li>
          <a href="charts-apexcharts.html">
            <i class="bi bi-circle"></i><span>ApexCharts</span>
          </a>
        </li>
        <li>
          <a href="charts-echarts.html">
            <i class="bi bi-circle"></i><span>ECharts</span>
          </a>
        </li>
      </ul>
    </li><!-- End Charts Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-gem"></i><span>Icons</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="icons-bootstrap.html">
            <i class="bi bi-circle"></i><span>Bootstrap Icons</span>
          </a>
        </li>
        <li>
          <a href="icons-remix.html">
            <i class="bi bi-circle"></i><span>Remix Icons</span>
          </a>
        </li>
        <li>
          <a href="icons-boxicons.html">
            <i class="bi bi-circle"></i><span>Boxicons</span>
          </a>
        </li>
      </ul>
    </li><!-- End Icons Nav -->

    <li class="nav-heading">Pages</li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="users-profile.html">
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </li><!-- End Profile Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-faq.html">
        <i class="bi bi-question-circle"></i>
        <span>F.A.Q</span>
      </a>
    </li><!-- End F.A.Q Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-contact.html">
        <i class="bi bi-envelope"></i>
        <span>Contact</span>
      </a>
    </li><!-- End Contact Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-register.html">
        <i class="bi bi-card-list"></i>
        <span>Register</span>
      </a>
    </li><!-- End Register Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-login.html">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>Login</span>
      </a>
    </li><!-- End Login Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-error-404.html">
        <i class="bi bi-dash-circle"></i>
        <span>Error 404</span>
      </a>
    </li><!-- End Error 404 Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-blank.html">
        <i class="bi bi-file-earmark"></i>
        <span>Blank</span>
      </a>
    </li><!-- End Blank Page Nav --> --}}

  </ul>

</aside><!-- End Sidebar-->