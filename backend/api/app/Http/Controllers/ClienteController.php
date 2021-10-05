<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use JWTAuth;

class ClienteController extends Controller
{
    public function signup(Request $request){
        $data = $request->all();
        $data['clave'] = Hash::make($data['clave']);

        $cliente = Cliente::create($data);

        $token = JWTAuth::fromUser($cliente);

        return array('token' => $token);
    }

    public function login(Request $request){
        $credentials = $request->all();
        $cliente = Cliente::where('correo', $credentials['correo'])->first();

        if(Hash::check($credentials['clave'], $cliente['clave'])){
            $token = JWTAuth::fromUser($cliente);
        }else{
            return response()->json(['error' => 'credenciales invalidas'], 400);
        }

        return array('token' => $token);
    }    

    public function showAll(){
        return Cliente::all();
    }

    public function update(Request $request){
        $data = $request->all();
        $data['clave'] = Hash::make($data['clave']);

        $token = $request->bearerToken();
        $doc = JWTAuth::getPayload($token)->toArray()['sub'];

        $updated = Cliente::where('doc', $doc)->update($data) != 0;

        return array('updated' => $updated);
    }
}
