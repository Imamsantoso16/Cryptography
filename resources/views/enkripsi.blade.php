@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
<div class="mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Enkripsi</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('enkripsi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="date">Tanggal</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="file">File</label>
                    <input type="file" name="file" id="file" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Keterangan</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Enkripsi</button>
            </form>
        </div>
    </div>
</div>
@stop
