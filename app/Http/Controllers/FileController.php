<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(){
        $files = File::all();
        return view('daftar-file', compact('files'));
    }
    
    public function store(Request $request)
    {
        try{
            $request->validate([
                'date' => 'required|date',
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,txt|max:10240',
                'password' => 'required|string|min:8',
                'description' => 'required|string',
            ]);
    
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('files', $fileName);
    
            $password = $request->password;
    
            $encryptedFile = $this->tripleAesEncrypt(Storage::get($filePath), $password);
    
            $encryptedFileName = 'enc_' . rand(1000, 9999) . '_' . $fileName;
            Storage::put('encrypted/' . $encryptedFileName, $encryptedFile);
    
            $fileModel = new File();
            $fileModel->username = auth()->user()->username;
            $fileModel->file_name = $fileName;
            $fileModel->encrypted_file = $encryptedFileName;
            $fileModel->decrypted_file = "";
            $fileModel->password = $password;
            $fileModel->keterangan = $request->description;
            $fileSizeInBytes = $file->getSize();
            $fileSizeInKB = $fileSizeInBytes / 1024;
            $fileModel->file_size = $fileSizeInKB;
            $fileModel->status = 'Terenkripsi';
            $fileModel->tanggal = $request->date;
            $fileModel->tanggal_enkripsi = now();
            $fileModel->save();
    
            return back()->with('success', 'File berhasil dienkripsi!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function tripleAesEncrypt($data, $password)
    {
        $iv = random_bytes(16);
        $key = substr(hash('sha256', $password, true), 0, 16);

        $encryptedData = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $encryptedData = openssl_encrypt($encryptedData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $encryptedData = openssl_encrypt($encryptedData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $encryptedData);
    }

    public function showDekripsiForm()
    {
        $files = File::where('status', 'Terenkripsi')->get();
        return view('dekripsi', compact('files'));
    }

    public function dekripsi(Request $request)
{
    try {
        $request->validate([
            'file_id' => 'required|exists:files,id',
            'password' => 'required|string|min:8',
        ]);

        $file = File::findOrFail($request->file_id);

        if ($file->password !== $request->password) {
            return back()->withErrors(['password' => 'Password salah!']);
        }

        $encryptedFile = Storage::get('encrypted/' . $file->encrypted_file);

        $password = $request->password;
        $iv = Str::random(16);

        $decryptedFile = $this->tripleAesDecrypt($encryptedFile, $password, $iv);

        $decryptedFileName = 'dec_' . rand(1000, 9999) . '_' . $file->file_name;
        $file->decrypted_file = $decryptedFileName;
        Storage::put('decrypted/' . $decryptedFileName, $decryptedFile);

        $file->status = 'Terdekripsi';
        $file->save();

        return back()->with('success', 'File berhasil didekripsi!')->with('file', $decryptedFileName);
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}

    private function tripleAesDecrypt($data, $password, $iv)
    {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encryptedData = substr($data, 16);

        $key = substr(hash('sha256', $password, true), 0, 16);
        $decryptedData = openssl_decrypt($encryptedData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $decryptedData = openssl_decrypt($decryptedData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $decryptedData = openssl_decrypt($decryptedData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return $decryptedData;
    }

    public function destroy($fileId)
    {
        try {
            $file = File::findOrFail($fileId);

            $filePath           = 'files/' . $file->file_name;
            $encryptedFilePath  = 'encrypted/' . $file->encrypted_file;
            $decryptedFilePath  = 'decrypted/' . $file->decrypted_file;

            if (Storage::exists($encryptedFilePath)) {
                Storage::delete($encryptedFilePath);
            }

            if (Storage::exists($decryptedFilePath)) {
                Storage::delete($decryptedFilePath);
            }

            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            $file->delete();
            return back()->with('success', 'File dan data berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function download($fileId, $type)
    {
        $file = File::findOrFail($fileId);
        if ($type === 'encrypted') {
            $filePath = storage_path('app/encrypted/' . $file->encrypted_file);
        } else {
            $filePath = storage_path('app/decrypted/' . $file->decrypted_file);
        }

        if (!file_exists($filePath)) {
            return back()->withErrors(['error' => 'File tidak ditemukan.']);
        }

        return response()->download($filePath);
    }
}