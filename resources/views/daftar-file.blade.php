@extends('adminlte::page')

@section('title', 'Daftar File')

@section('content')
<div class="mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Daftar File</h5>
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
            <table id="filesTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama File</th>
                        <th>Nama File Enkripsi</th>
                        <th>Nama File Dekripsi</th>
                        <th>Ukuran Berkas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $index => $file)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $file->username }}</td>
                            <td>{{ $file->file_name }}</td>
                            <td>{{ $file->encrypted_file }}</td>
                            <td>{{ $file->decrypted_file }}</td>
                            <td>{{ $file->file_size . " KB" }}</td>
                            <td>{{ $file->status }}</td>
                            <td class="d-flex">
                                @if($file->status === 'Terenkripsi')
                                    <a href="{{ route('files.download', ['file' => $file->id, 'type' => 'encrypted']) }}" class="btn btn-info btn-sm mr-2">
                                        <i class="fas fa-solid fa-download"></i>
                                    </a>
                                @else
                                    <a href="{{ route('files.download', ['file' => $file->id, 'type' => 'decrypted']) }}" class="btn btn-info btn-sm mr-2">
                                        <i class="fas fa-solid fa-download"></i>
                                    </a>
                                @endif
                                <form action="{{ route('files.destroy', $file->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-solid fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#filesTable').DataTable({
                processing: true,
                serverSide: false,
                paging: false,
                searching: false,
                ordering: false,
            });
        });
    </script>
@stop
