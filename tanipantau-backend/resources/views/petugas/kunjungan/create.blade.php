@extends('layouts.petugas')

@section('title', 'Tambah Kunjungan')
@section('page-title', 'Tambah Kunjungan')

@section('content')
    @include('petugas.kunjungan.partials.form', [
        'formAction' => route('petugas.kunjungan.store'),
        'submitText' => 'Simpan Kunjungan',
        'lahanList' => $lahanList,
        'selectedLahanId' => $selectedLahanId,
        'selectedLahan' => $selectedLahan,
    ])
@endsection
