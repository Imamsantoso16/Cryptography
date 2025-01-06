@extends('adminlte::page')

@section('title', 'Dekripsi')

@section('content')
<div class="mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Dekripsi</h5>
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

            @if(session('decryption_time'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    Durasi dekripsi: {{ session('decryption_time') }} detik
                </div>
            @endif

            <form action="{{ route('dekripsi.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="file">File Enkripsi</label>
                    <select name="file_id" id="file" class="form-control" required>
                        <option value="">Pilih file yang terenkripsi</option>
                        @foreach($files as $file)
                            <option value="{{ $file->id }}">{{ $file->file_name }}</option>
                        @endforeach
                    </select>
                    @error('file_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary mt-3">Dekripsi</button>
            </form>
        </div>
    </div>
</div>
@stop
