@extends('adminlte::page')

@section('title', 'Panduan')

@section('content')
<div class="mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Panduan</h5>
        </div>
        <div class="card-body">
            <li>
                <strong>Login ke Aplikasi:</strong>
                <p>Masukkan email dan password Anda untuk mengakses aplikasi.</p>
            </li>
            <li>
                <strong>Mengelola Data:</strong>
                <p>Gunakan menu <em>Daftar File</em> untuk mengunggah atau mengelola data Anda.</p>
            </li>
            <li>
                <strong>Enkripsi dan Dekripsi:</strong>
                <p>Gunakan menu <em>Enkripsi</em> untuk mengenkripsi file dan <em>Dekripsi</em> untuk membuka file terenkripsi.</p>
            </li>
            <li>
                <strong>Logout:</strong>
                <p>Pastikan Anda keluar dari aplikasi setelah selesai menggunakan.</p>
            </li>
            <hr>

            <h6><strong>FAQ</strong></h6>
            <p><strong>Tanya:</strong> Bagaimana cara mengunggah file?</p>
            <p><strong>Jawab:</strong> Gunakan menu <em>Daftar File</em> dan klik tombol "Unggah File".</p>

            <p><strong>Tanya:</strong> Apakah data saya aman?</p>
            <p><strong>Jawab:</strong> Aplikasi ini menggunakan enkripsi untuk melindungi file Anda.</p>
        </div>
    </div>
</div>
@stop
