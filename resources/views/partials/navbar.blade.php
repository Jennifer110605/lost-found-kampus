<nav class="navbar navbar-expand-lg sticky-top" id="mainNav">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand" href="{{ route('items.index') }}">
            <i class="bi bi-search-heart me-1"></i>
            Lost<span class="text-accent">&</span>Found
            <small>FATEK</small>
        </a>

        {{-- Toggle mobile --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Menu --}}
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('items.index') ? 'active' : '' }}"
                       href="{{ route('items.index') }}">
                        <i class="bi bi-house me-1"></i>Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('items.index') && request('type') === 'lost' ? 'active' : '' }}"
                       href="{{ route('items.index', ['type' => 'lost']) }}">
                        <i class="bi bi-question-circle me-1"></i>Barang Hilang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('items.index') && request('type') === 'found' ? 'active' : '' }}"
                       href="{{ route('items.index', ['type' => 'found']) }}">
                        <i class="bi bi-check-circle me-1"></i>Barang Ditemukan
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav align-items-lg-center gap-2">
                @auth

                    {{-- Tombol buat postingan --}}
                    <li class="nav-item">
                        <a href="{{ route('items.create') }}" class="btn btn-accent btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Buat Postingan
                        </a>
                    </li>

                    {{-- Dropdown user --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                           href="#"
                           data-bs-toggle="dropdown">

                            <span class="avatar-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>

                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow">

                            <li>
                                <span class="dropdown-item-text small text-muted">
                                    {{ Auth::user()->email }}
                                </span>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item" href="{{ route('items.my') }}">
                                    <i class="bi bi-collection me-2"></i>Postingan Saya
                                </a>
                            </li>

                            {{-- MENU PROFILE BARU --}}
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="bi bi-person me-2"></i>Edit Profil
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </li>

                @else

                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                            Daftar
                        </a>
                    </li>

                @endauth
            </ul>
        </div>
    </div>
</nav>