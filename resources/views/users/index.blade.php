<!DOCTYPE html>
<html lang="en">

@include('layouts.header')

<head>
    <style>
        /* (biarkan semua CSS kamu di sini tanpa perubahan) */
    </style>
</head>

<body>
    @include('layouts.loading')
    @include('layouts.navbar')
    @include('layouts.sidebar')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Users</h1>
            <nav>
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                    <h5 class="card-title mb-0">Daftar Pengguna</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus"></i> Tambah
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Username</th>
                                <th scope="col">Role</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($user->role == 'superadmin') bg-danger 
                                            @elseif($user->role == 'teknisi') bg-success 
                                            @else bg-primary @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Tombol Edit -->
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editUserModal{{ $user->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus user {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada pengguna.</td>
                                </tr>
                            @endforelse
                        </tbody>                      
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        @foreach ($users as $user)
            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                            </div>
                            <div class="mb-3 position-relative">
                                <label>Password (Kosongkan jika tidak diubah)</label>
                                <div class="input-group">
                                    <input type="password" 
                                        name="password" 
                                        class="form-control" 
                                        id="passwordInput{{ $user->id }}">
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePassword('passwordInput{{ $user->id }}', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small id="passwordError{{ $user->id }}" class="text-danger d-none">
                                    Password tidak boleh kurang dari 5 karakter.
                                </small>
                            </div>
                            <div class="mb-3">
                                <label>Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                    <option value="teknisi" {{ $user->role == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
                                    <option value="forecast" {{ $user->role == 'forecast' ? 'selected' : '' }}>Forecast</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="updateBtn{{ $user->id }}">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        <!-- Modal Tambah User -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel">
            <div class="modal-dialog">
                <form action="{{ route('users.store') }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="teknisi">Teknisi</option>
                                <option value="forecast">Forecast</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    </main><!-- End #main -->

    @include('layouts.footer')
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Validasi real-time panjang password
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[id^="passwordInput"]').forEach(input => {
                const id = input.id.replace('passwordInput', '');
                const errorText = document.getElementById('passwordError' + id);
                const updateBtn = document.getElementById('updateBtn' + id);

                if (!errorText || !updateBtn) return;

                input.addEventListener('input', () => {
                    const value = input.value.trim();

                    // Hanya validasi jika field tidak kosong
                    if (value.length > 0 && value.length < 5) {
                        errorText.classList.remove('d-none');
                        updateBtn.disabled = true;
                    } else {
                        errorText.classList.add('d-none');
                        updateBtn.disabled = false;
                    }
                });
            });
        });
    </script>


    @include('layouts.script')
</body>

</html>
