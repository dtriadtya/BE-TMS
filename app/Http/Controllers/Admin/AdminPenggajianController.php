<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminPenggajianController extends Controller
{
    public function getPenggajian(Request $request){
        try {
            $this->validate($request, [
                'id_penggajian' => 'integer'
            ]);

            if($request['id_penggajian'] == null){
                $listPenggajian = Penggajian::all();
                return response()->json($listPenggajian, 200);
            }else{
                $penggajian = Penggajian::find($request['id_penggajian']);
                return response()->json($penggajian, 200);
            }

        } catch (Exception $e) {
            //throw $th;`
            return response("Gagal: " . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}
