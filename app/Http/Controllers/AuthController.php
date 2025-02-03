<?php
namespace App\Http\Controllers;

use App\Models\Usuario;  // Asegúrate de usar el modelo correcto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Método para el login
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Verificar las credenciales
        $user = Usuario::where('email', $request->email)->first();  // Cambia User a Usuario

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Credenciales incorrectas.'], 401);
        }

        // Generar el token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['token' => $token, 'message' => 'Inicio de sesión exitoso'], 200);  // Añadir mensaje
    }
    public function getUsuarioAutenticado(Request $request)
{
    $user = Auth::user();  // Obtiene el usuario autenticado
    return response()->json($user, 200);
}
}
