@extends('layouts.petugas')

@section('title', 'Lahan Saya')
@section('page-title', 'Lahan Saya')

@section('content')
    <div class="card border-0 shadow-sm mb-4 animate-slide-up" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('petugas.lahan.index') }}" class="d-flex flex-wrap gap-2">
                <div class="input-group flex-grow-1" style="max-width: 400px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 bg-light" name="search" value="{{ request('search') }}" placeholder="Cari lahan, komoditas, petani...">
                </div>
                <select name="fase" class="form-select" style="max-width: 200px;">
                    <option value="">Semua Fase</option>
                    <option value="Persiapan" {{ request('fase') == 'Persiapan' ? 'selected' : '' }}>Persiapan</option>
                    <option value="Penanaman" {{ request('fase') == 'Penanaman' ? 'selected' : '' }}>Penanaman</option>
                    <option value="Pertumbuhan" {{ request('fase') == 'Pertumbuhan' ? 'selected' : '' }}>Pertumbuhan</option>
                    <option value="Panen" {{ request('fase') == 'Panen' ? 'selected' : '' }}>Panen</option>
                    <option value="Panen Selesai" {{ request('fase') == 'Panen Selesai' ? 'selected' : '' }}>Panen Selesai</option>
                </select>
                <button type="submit" class="btn btn-primary-custom">Cari</button>
                @if(request('search') || request('fase'))
                    <a href="{{ route('petugas.lahan.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card animate-slide-up" style="animation-delay: 0.1s;">
        <div class="card-body p-0">
            @if($lahan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Lahan</th>
                                <th>Petani</th>
                                <th>Komoditas</th>
                                <th>Luas (Ha)</th>
                                <th>Fase</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lahan as $index => $item)
                            <tr>
                                <td class="ps-4 text-muted">{{ ($lahan->currentPage() - 1) * $lahan->perPage() + $index + 1 }}</td>
                                <td style="font-weight:500;">{{ $item->nama_lahan }}</td>
                                <td>{{ $item->petani->nama ?? '-' }}</td>
                                <td>{{ $item->komoditas }}</td>
                                <td>{{ number_format($item->luas_lahan, 2) }}</td>
                                <td>
                                    <span class="badge" style="background: #E8F5E9; color: #2E7D32;">{{ $item->fase_lahan }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('petugas.lahan.show', $item->id) }}" class="btn btn-sm btn-primary" style="border-radius: 8px; background: var(--primary); border-color: var(--primary);">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($lahan instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="p-3 border-top d-flex justify-content-center">
                        {{ $lahan->links('vendor.pagination.custom') }}
                    </div>
                @endif
            @else
                <div class="p-5 text-center">
                    <i class="bi bi-map" style="font-size:48px; color:var(--text-muted); opacity:0.4;"></i>
                    <h6 class="mt-3 mb-1">Belum Ada Lahan</h6>
                    <p class="text-muted small mb-0">Anda belum memiliki lahan yang ditugaskan</p>
                </div>
            @endif
        </div>
    </div>
@endsection
