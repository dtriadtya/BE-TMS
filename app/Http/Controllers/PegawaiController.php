<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Pegawai;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;

use function PHPUnit\Framework\isEmpty;

class PegawaiController extends Controller
{

    public function getPegawai($userId)
    {
        $pegawai = Pegawai::where('id_user', $userId)->first();
        error_log($pegawai['alamat_pegawai']);

        if ($pegawai) {
            if ($pegawai['id_divisi'] != null) {
                $divisi = Divisi::find($pegawai['id_divisi']);
                $pegawai['divisi'] = $divisi['nama_divisi'];
                error_log($divisi);
            } else {
                $pegawai['divisi'] = '-';
            }
            $pegawai['foto_profil'] = '/pegawai/image/' . $pegawai['id_pegawai'];
            return response()->json($pegawai, 200);
        } else {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }
    }

    public function getPhotoByPegawaiId($pegawaiId)
    {
        $pegawai = Pegawai::find($pegawaiId);

        if ($pegawai) {
            $fotoBlob = $pegawai->foto_profil;

            if ($fotoBlob) {
                header('Content-Type: image/jpeg');

                echo $fotoBlob;
            } else {
                $defaultImage = public_path('image/no_pict.png');
                if (file_exists($defaultImage)) {
                    header('Content-Type: image/jpeg');
                    readfile($defaultImage);
                }
            }
        }
    }

    // public function imageStore(Request $request)
    // {

    //     try {
    //         $this->validate($request, [
    //             'id_pegawai' => 'required|integer',
    //             'image' => 'required|file|mimes:jpg,png,jpeg,gif,svg|max:2048',
    //         ]);

    //         $image = $request->file('image');
    //         $imageData = file_get_contents($image->getRealPath());

    //         // Simpan BLOB ke kolom 'foto_profil' di tabel 'Pegawai' dengan ID 1
    //         DB::table('pegawai')
    //             ->where('id_pegawai', $request['id_pegawai'])
    //             ->update(['foto_profil' => $imageData]);
    //         return response(["message" => "Berhasil"], 200);
    //     } catch (Exception $e) {
    //         // Tangani pengecualian jika berkas tidak sesuai format
    //         return response("Gagal: " . $e->getMessage(), Response::HTTP_BAD_REQUEST);
    //     }
    // }

    public function imageStore(Request $request)
    {

        try {
            $this->validate($request, [
                'id_pegawai' => 'required|integer',
                'image' => 'required|file|mimes:jpg,png,jpeg,gif,svg|max:10000',
            ]);

            $image = $request->file('image');
            $img = Image::make($image->getRealPath());

            // Menyesuaikan ukuran gambar ke ukuran tetap
            $img->fit(250, 250);
            $imageData = $img->encode();

            // Simpan BLOB ke kolom 'foto_profil' di tabel 'Pegawai' dengan ID 1
            DB::table('pegawai')
                ->where('id_pegawai', $request['id_pegawai'])
                ->update(['foto_profil' => $imageData]);
            return response("Berhasil", Response::HTTP_CREATED);
        } catch (Exception $e) {
            error_log("testtt2".$e->getMessage());
            // Tangani pengecualian jika berkas tidak sesuai format
            return response("Gagal: " . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'id_user' => 'required|integer',
            'alamat_pegawai' => 'nullable',
            'email_user' => 'nullable|email',
            'nohp_pegawai' => 'nullable',
        ]);
        error_log("testtt". $request);
        try {
            $pegawai = Pegawai::where('id_user', $data['id_user'])->update([
                'alamat_pegawai' => $data['alamat_pegawai'],
                'nohp_pegawai' => $data['nohp_pegawai'],
            ]);
            if($data['email_user']!=null){
                $user = User::where('id_user', $data['id_user'])->update([
                    'email_user' => $data['email_user']
                ]);
            }
            error_log("testtt2");
            return response()->json(["message" => "Berhasil menyimpan profil"], 200);
        } catch (Exception $ex) {
  
            return response("Gagal: " . $ex->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    
}
