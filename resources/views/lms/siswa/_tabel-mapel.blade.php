{{-- resources/views/lms/siswa/_tabel-mapel.blade.php
     Dipakai di dashboard & halaman kelas siswa.
     Variabel: $mapel (collection PengampuMapel), $total (opsional), $linkSemua (opsional, bool) --}}
@php
    $total = $total ?? $mapel->count();
    $linkSemua = $linkSemua ?? false;
@endphp

<section class="bg-white rounded-2xl shadow-sm border border-slate-200/70 overflow-hidden">
    <div class="p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Mata Pelajaran di Kelas Anda</h2>
            <p class="text-sm text-slate-500 mt-0.5">Daftar mata pelajaran beserta guru pengampu untuk kelas Anda.</p>
        </div>
        <div class="relative w-full md:w-72">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input id="mapelSearch" type="text" placeholder="Cari mata pelajaran / guru..."
                class="w-full pl-9 pr-3 py-2 text-sm rounded-lg bg-slate-100 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:bg-white transition">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table id="mapelTable" class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider">
                    <th class="py-3 px-5 sm:px-6 font-semibold">Mata Pelajaran</th>
                    <th class="py-3 px-4 font-semibold">Guru Pengampu</th>
                    <th class="py-3 px-4 font-semibold hidden md:table-cell">Semester & T.A</th>
                    <th class="py-3 px-5 sm:px-6 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($mapel as $p)
                    @php
                        $mp = $p->mataPelajaran;
                        $namaMapel = $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? 'Mata Pelajaran'));
                        $kodeMapel = $mp->kode ?? ($mp->kode_mapel ?? null);
                        $namaGuru = $p->guru->nama ?? ($p->guru->name ?? '-');
                        $inisialGuru = collect(preg_split('/\s+/', trim($namaGuru)))
                            ->take(2)
                            ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                            ->implode('');
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors group">
                        <td class="py-4 px-5 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                    <i class="bi bi-journal-text text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $namaMapel }}</div>
                                    @if ($kodeMapel)
                                        <div class="text-xs text-slate-400 font-mono">KODE: {{ $kodeMapel }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center text-xs font-semibold shrink-0">
                                    {{ $inisialGuru ?: '?' }}
                                </div>
                                <span class="font-semibold text-slate-800 truncate">{{ $namaGuru }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 hidden md:table-cell">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                {{ $p->semester ?? '-' }} · {{ $p->tahun_ajaran ?? '-' }}
                            </span>
                        </td>
                        <td class="py-4 px-5 sm:px-6">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('lms.siswa.materi.index', $p) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 hover:bg-blue-600 hover:text-white text-blue-600 text-xs font-semibold transition-all">
                                    <i class="bi bi-book"></i><span>Materi</span>
                                </a>
                                <a href="{{ route('lms.siswa.tugas.index', $p) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 hover:bg-blue-600 hover:text-white text-blue-600 text-xs font-semibold transition-all">
                                    <i class="bi bi-clipboard-check"></i><span>Tugas</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-500">
                            <i class="bi bi-inbox text-3xl text-slate-300 block mb-2"></i>
                            Belum ada mata pelajaran untuk kelas Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 sm:px-6 py-3.5 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-slate-500">
        <span>Menampilkan {{ $mapel->count() }} dari {{ $total }} mata pelajaran</span>
        @if ($linkSemua && $total > $mapel->count())
            <a href="{{ route('lms.siswa.kelas.index') }}" class="font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                Lihat semua <i class="bi bi-arrow-right"></i>
            </a>
        @endif
    </div>
</section>

@push('scripts')
    <script>
        document.getElementById('mapelSearch')?.addEventListener('input', function (e) {
            const q = e.target.value.toLowerCase();
            document.querySelectorAll('#mapelTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
@endpush