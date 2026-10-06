@props(['user', 'uploadRoute', 'deleteRoute'])

<div class="card border-0 shadow-sm text-center p-4">
    {{-- Avatar --}}
    <div class="mx-auto mb-3">
        @if ($user->avatar)
            <img src="{{ Storage::url($user->avatar) }}"
                alt="Avatar"
                class="rounded-circle object-fit-cover"
                style="width:100px;height:100px">
        @else
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto text-white fw-black fs-2"
                style="width:100px;height:100px">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
    </div>

    <div class="fw-bold">{{ $user->name }}</div>
    <div class="text-muted small mb-4">{{ $user->email }}</div>

    {{-- Upload --}}
    <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data">
        @csrf
        <label for="avatarInput_{{ $user->id }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
            <i class="bi bi-camera me-1"></i>Ganti Foto
        </label>
        <input type="file" id="avatarInput_{{ $user->id }}" name="avatar"
            accept="image/*" class="d-none"
            onchange="this.form.submit()">
        @error('avatar')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </form>

    {{-- Hapus --}}
    @if ($user->avatar)
        <form method="POST" action="{{ $deleteRoute }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                onclick="return confirm('Hapus foto profil?')">
                <i class="bi bi-trash me-1"></i>Hapus Foto
            </button>
        </form>
    @endif
</div>