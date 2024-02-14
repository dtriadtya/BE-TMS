<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
    public function index(){
        $user = User::all();
        return response()->json($user);
    }

    public function register(Request $request){
        try {
            $data = $request->validate([
                'nama_user' => 'required|max:100',
                'email_user' => 'required|email|max:100|unique:user,email_user',
                'password_user' => 'required|max:10'
            ]);
    
            $data['password_user'] = md5($data['password_user']);
            $data['role'] = 1;
            $user = User::create($data);

            $dataPegawai = [
                'id_user' => $user['id_user'],
                'foto_profil' => null,
                'alamat_pegawai' => null,
                'nohp_pegawai' => null,
                'nip' => null,
                'id_divisi' => null,
            ];

            $pegawai = Pegawai::create($dataPegawai);
    
            return response()->json($user, 201); // 201 Created
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validasi gagal
            $errors = $e->validator->getMessageBag();
    
            return response()->json(['message' => 'Validasi gagal', 'errors' => $errors], 422); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            // Kesalahan umum lainnya
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500); // 500 Internal Server Error
        }
    }

    public function login(Request $request){
        try {
            $data = $request->validate([
                'email_user' => 'required|email|max:100',
                'password_user' => 'required|max:10'
            ]);

            $userCurr =  User::where('email_user', $data['email_user'])->first();
            if(!($userCurr == null)){
                if($userCurr['password_user'] == md5($data['password_user'])){
                    return response()->json(['id_user' => $userCurr['id_user'], 'email_user' => $userCurr['email_user'], 'nama_user' => $userCurr['nama_user'], 'message' => 'Berhasil Login'], 200);
                }
            }
            return response()->json(['message' => 'email atau password invalid'], 401);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validasi gagal
            $errors = $e->validator->getMessageBag();
    
            return response()->json(['message' => 'Validasi gagal', 'errors' => $errors], 422); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            // Kesalahan umum lainnya
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500); // 500 Internal Server Error
        }
    }

    public function changePassword(Request $request){
        try {
            $data = $request->validate([
                'id_user' => 'required|integer',
                'old_password' => 'required|max:10',
                'new_password' => 'required|max:10'
            ]);

            $userCur = User::find($data['id_user']);
            if($userCur != null){
                if(md5($data['old_password']) == $userCur['password_user'] && $data['old_password'] != $data['new_password']){
                    $user = User::find($data['id_user'])->update(['password_user' => md5($data['new_password'])]);
                    return response()->json(['message' => 'Berhasil Mengubah Password'], 200);
                }
                return response()->json(['message' => 'password tidak boleh sama dengan sebelumnya'], 401);
            }
            return response()->json(['message' => 'email tidak terdaftar'], 401);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->getMessageBag();
            return response()->json(['message' => 'Validasi gagal', 'errors' => $errors], 422); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500); // 500 Internal Server Error
        }
    }
}
