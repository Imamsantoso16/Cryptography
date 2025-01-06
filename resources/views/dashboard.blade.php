@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
<div class="mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Dashboard</h5>
        </div>
        <div class="card-body">
            <h5>Hi, Welcome Administrator</h5>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="w-full d-flex justify-content-center align-items-center">
                <img class="mb-4" style="object-fit: contain;" src="/gbe-logo.png" alt="gbe logo" width="100%"
                    height="200px">
            </div>
            <p><b>The Building Management and Solutions</b></p>
            <p>
                PT. Gedung Bank Exim merupakan Perusahaan swasta nasional yang bergerak di bidang Pengelolaan
                Gedung,
                Konstruksi, Pengelolaan Residensial, Pengelolaan Air Limbah Gedung, Penyewaan Ruang Kantor dan
                Sumberdaya Manusia</p>
        </div>
    </div>
</div>
@stop