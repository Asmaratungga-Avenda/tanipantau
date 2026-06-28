@extends('layouts.petugas')

@section('title', 'Edit Kunjungan')
@section('page-title', 'Edit Kunjungan')

@section('content')
    @include('petugas.kunjungan.partials.form', [
        'formAction' => route('petugas.kunjungan.update', $kunjungan->id),
        'methodField' => 'PUT',
        'submitText' => 'Update Kunjungan',
        'lahanList' => $lahanList,
        'selectedLahan' => $selectedLahan,
        'selectedLahanId' => $kunjungan->lahan_id,
        'kunjungan' => $kunjungan,
    ])
@endsection
