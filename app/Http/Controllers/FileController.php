<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // function yang digunakan untuk menampilkan halaman daftar-file beserta file dari database
    public function index(){
        // mengambil file dari database
        $files = File::all();
        // mengembalikan tampilan daftar-file bersama dengan file yang diambil sebelumnya
        return view('daftar-file', compact('files'));
    }
    
    // function yang digunakan untuk menyimpan dan mengenkripsi file
    public function store(Request $request)
    {
        // mencoba untuk  menyimpan dan mengenkripsi file
        try {
            // memulai waktu enkripsi dan penyimpanan file
            $start_time = microtime(true);
            // validasi tiap input apakah sudah sesuai dengan aturan yang berlaku
            $request->validate([
                'date'       => 'required|date',
                'file'       => 'required|file|mimes:jpg,jpeg,png,pdf,docx,txt,xls|max:10240',
                'password'   => 'required|string|min:8',
                'key'        => 'required|string|size:16',
                'description'=> 'required|string',
            ]);

            // mengambil file dari user
            $file     = $request->file('file');
            // membuat nama file berdasarkan nama dari file yang dikirim user
            $file_name = time() . '_' . $file->getClientOriginalName();
            // menyimpan file yang dikirim user ke folder files
            $file_path = $file->storeAs('files', $file_name);
            // mengambil key yang dikirim user
            $key      = $request->key;
            // mengambil password yang dikirim user
            $password = $request->password;
            // proses encrypt file yang sudah disimpan di folder files
            $encrypted_file = $this->tripleAesEncrypt(Storage::get($file_path), $key);
            // mengubah nama dari file yang sudah diencrypt
            $encrypted_file_name = 'enc_' . rand(1000, 9999) . '_' . $file_name;
            // menyimpan file yang sudah diencrypt ke folder encrypted
            Storage::put('encrypted/' . $encrypted_file_name, $encrypted_file);

            // memanggil model file untuk menyimpan tiap pada tiap field
            $file_model = new File();
            // menyimpan field username
            $file_model->username       = auth()->user()->username;
            // menyimpan field file_name
            $file_model->file_name      = $file_name;
            // menyimpan field encrypted_file
            $file_model->encrypted_file = $encrypted_file_name;
            // menyimpan field decrypted_file
            $file_model->decrypted_file = "";
            // menyimpan field password
            $file_model->password       = $password;
            // menyimpan field key
            $file_model->key            = $key;
            // menyimpan field keterangan
            $file_model->keterangan     = $request->description;
            
            // mendapatkan ukuran file dalam bentuk byte
            $file_size_in_bytes = $file->getSize();
            // mendapatkan ukuran file dalam bentuk kilo byte
            $file_size_in_kB    = $file_size_in_bytes / 1024;
            // menyimpan file field_size dalam ukuran kilo byte
            $file_model->file_size       = $file_size_in_kB;
            // menyimpan file status
            $file_model->status          = 'Terenkripsi';
            // menyimpan file tanggal
            $file_model->tanggal         = $request->date;
            // menyimpan file tanggal_enkripsi
            $file_model->tanggal_enkripsi= now();
            // menyimpan semua field tadi ke tabel file
            $file_model->save();

            // mendapatkan waktu selesai proses encrypt dan penyimpanan file
            $end_time = microtime(true);
            // menghitung selisih waktu dari waktu mulai dan waktu selesai
            $duration = round($end_time - $start_time, 3);
            
            // kembali dengan status success dan waktu enkripsinya
            return back()->with('success', 'File berhasil dienkripsi!')
                        ->with('encryption_time', $duration);

        } catch (\Exception $e) {
            // kembali dengan status error
            return back()->with('error', $e->getMessage());
        }
    }

    // function untuk encrypt file
    private function tripleAesEncrypt($data, $key_from_user)
    {
        // membuat random byte 16 karakter
        $iv = random_bytes(16);
        // membuat key yang digabungkan dengan key dari user
        $key = substr(hash('sha256', $key_from_user, true), 0, 16);

        // enkripsi aes-128 pertama
        $encrypted_data = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        // enkripsi aes-128 kedua
        $encrypted_data = openssl_encrypt($encrypted_data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        // enkripsi aes-128 ketiga
        $encrypted_data = openssl_encrypt($encrypted_data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        
        // mengembalikan hasil encrypt
        return base64_encode($iv . $encrypted_data);
    }

    // function untuk menampilkan tampilan dekripsi beserta file yang statusnya Terenkripsi
    public function showDekripsiForm()
    {
        // mengambil file yang statusnya Terenkripsi dari tabel file
        $files = File::where('status', 'Terenkripsi')->get();
        // mengembalikan tampilan dekripsi bersama dengan file yang statusnya Terenkripsi
        return view('dekripsi', compact('files'));
    }

    // function yang digunakan untuk menyimpa file yang terdekripsi dan mendekripsikan file
    public function dekripsi(Request $request)
    {
        // mencoba untuk  menyimpan dan mengenkripsi file
        try {
            // memulai waktu enkripsi dan penyimpanan file
            $start_time = microtime(true);

            // validasi tiap input apakah sudah sesuai dengan aturan yang berlaku
            $request->validate([
                'file_id'  => 'required|exists:files,id',
                'password' => 'required|string|min:8',
            ]);

            // mencari file berdasarkan id
            $file = File::findOrFail($request->file_id);

            // memeriksa apakah password yang dimasukan user benar atau salah
            if ($file->password !== $request->password) {
                return back()->withErrors(['password' => 'Password salah!']);
            }

            // mengambil file yang sudah terenkripsi sebelumnya
            $encrypted_file = Storage::get('encrypted/' . $file->encrypted_file);
            // mengambil key dari tabel file
            $key = $file->key;
            // mendekripsikan file yang sudah diambil sebelumnya
            $decryptedFile = $this->tripleAesDecrypt($encrypted_file, $key);
            // memberikan nama file yang di decrypt
            $decryptedFile_name = 'dec_' . rand(1000, 9999) . '_' . $file->file_name;
            // menyimpan file yang di decrypt ke database
            $file->decrypted_file = $decryptedFile_name;
            // menyimpan file yang di decrypt ke folder decrypted
            Storage::put('decrypted/' . $decryptedFile_name, $decryptedFile);
            // mengubah field status ke Terdekripsi
            $file->status = 'Terdekripsi';
            // menumpan perubahan pada database
            $file->save();
            // mendapatkan waktu selesai proses encrypt dan penyimpanan file
            $end_time = microtime(true);
            // menghitung selisih waktu dari waktu mulai dan waktu selesai
            $duration = round($end_time - $start_time, 3);

            // kembali dengan status success, durasi decrypt dan nama file yang di decrypt
            return back()->with('success', 'File berhasil didekripsi!')
                        ->with('decryption_time', $duration)
                        ->with('file', $decryptedFile_name);

        } catch (\Exception $e) {
            // kembali dengan status error
            return back()->with('error', $e->getMessage());
        }
    }

    // Function untuk mendekripsi file menggunakan triple AES decryption
    private function tripleAesDecrypt($data, $password)
    {
        // Decode data dari format Base64
        $data = base64_decode($data);
        // Ambil 16 byte pertama sebagai initialization vector (IV)
        $iv = substr($data, 0, 16);
        // Sisanya adalah data terenkripsi
        $encrypted_data = substr($data, 16);

        // Hash password dengan SHA-256 dan ambil 16 byte pertama sebagai key
        $key = substr(hash('sha256', $password, true), 0, 16);
        // Lakukan dekripsi pertama menggunakan AES-128-CBC
        $decryptedData = openssl_decrypt($encrypted_data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        // Lakukan dekripsi kedua menggunakan AES-128-CBC
        $decryptedData = openssl_decrypt($decryptedData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        // Lakukan dekripsi ketiga menggunakan AES-128-CBC
        $decryptedData = openssl_decrypt($decryptedData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

        // Kembalikan data yang sudah didekripsi
        return $decryptedData;
    }

    // function untuk menghapus file
    public function destroy($file_id)
    {
        // proses percobaan penghapusan file
        try {
            // mencari file berdasarkan id
            $file = File::findOrFail($file_id);

            // lokasi file dari user
            $file_path           = 'files/' . $file->file_name;
            // lokasi file yang dienkripsi
            $encrypted_file_path  = 'encrypted/' . $file->encrypted_file;
            // lokasi file yang didekripsi
            $decrypted_file_path  = 'decrypted/' . $file->decrypted_file;

            // menghapus file dari user jika ada
            if (Storage::exists($encrypted_file_path)) {
                Storage::delete($encrypted_file_path);
            }
            
            // menghapus file yang terenkripsi jika ada
            if (Storage::exists($decrypted_file_path)) {
                Storage::delete($decrypted_file_path);
            }
            
            // menghapus file yang terdekripsi jika ada
            if (Storage::exists($file_path)) {
                Storage::delete($file_path);
            }
            
            // menghapus data file dari database
            $file->delete();
            // kembali dengan success
            return back()->with('success', 'File dan data berhasil dihapus!');
        } catch (\Exception $e) {
            // kembali dengan error
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // function untun download
    public function download($file_id, $type)
    {
        // mencari file berasarkan id
        $file = File::findOrFail($file_id);
        // apakah file terenkripsi?
        if ($type === 'encrypted') {
            // jika file terenkripsi maka ambil dari folder encrypted
            $file_path = storage_path('app/encrypted/' . $file->encrypted_file);
        } else {
            // jika file terdekripsi maka ambil dari folder decrypted
            $file_path = storage_path('app/decrypted/' . $file->decrypted_file);
        }

        // jk a file tidak ada maka kembali dengan pesan error "file tidak ditmukan"
        if (!file_exists($file_path)) {
            return back()->withErrors(['error' => 'File tidak ditemukan.']);
        }
        
        // memberikan file yang ingin didownload
        return response()->download($file_path);
    }
}