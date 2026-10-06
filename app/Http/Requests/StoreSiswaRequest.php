<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Gate/Policy bisa ditambah sesuai kebutuhan
    }

    public function rules(): array
    {
        return [
            'nis'           => ['required', 'string', 'max:20', 'unique:siswa,nis'],
            'nama'          => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat'        => ['nullable', 'string', 'max:500'],
            'no_hp'         => ['nullable', 'string', 'max:20'],
            'kelas_id'      => ['required', 'exists:kelas,id'],
            'user_id'       => ['nullable', 'exists:users,id', 'unique:siswa,user_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required'      => 'NIS wajib diisi.',
            'nis.unique'        => 'NIS sudah terdaftar.',
            'nama.required'     => 'Nama siswa wajib diisi.',
            'jenis_kelamin.in'  => 'Jenis kelamin tidak valid.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists'   => 'Kelas tidak ditemukan.',
        ];
    }
}
