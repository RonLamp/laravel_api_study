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
        auth('api')->logout();
        return response()->json(['msg'=>'Logout foi realizado com sucesso!!!']);
    }

    public function refresh() {
        $token = auth('api')->refresh();
        return response()->json(['token'=>$token]);
    }

    public function me() {  // retorna as informações do usuario que gerou o token
        return response()->json(auth()->user());
    }
}
