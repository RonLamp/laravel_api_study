<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) { // retornar JWT - Json Web Token
        // autenticação e-mail, password
        $credenciais = $request->all(['email','password']);
        $token = auth('api')->attempt($credenciais);
        if($token){ // usuario autenthicado com sucesso
            return response()->json(['token'=>$token], 200);
        } else { // erro de usuario e/ou senha
            return response()->json(['erro'=>'Usuário e/ou senha inválido!'], 403);
            //401 = Unauthorized
            //403 = forbiden / Proibido
        }
    }

    public function logout() {
        return 'logout';
    }

    public function refresh() {
        return 'refresh';
    }

    public function me() {
        return 'me';
    }
}
