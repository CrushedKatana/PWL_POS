<?php
 
 namespace App\Http\Controllers;
 
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Auth;
 use App\Models\UserModel;
 use Illuminate\Support\Facades\Hash;
 use Illuminate\Support\Facades\Validator;
 
 class AuthController extends Controller
 {
     public function login()
     {
         if (Auth::check()) { // jika sudah login, maka redirect ke halaman home
             return redirect('/');
         }
         return view('auth.login');
     }
 
     public function postlogin(Request $request)
     {
         if ($request->ajax() || $request->wantsJson()) {
             $credentials = $request->only('username', 'password');
 
             if (Auth::attempt($credentials)) {
                 return response()->json([
                     'status' => true,
                     'message' => 'Login Berhasil',
                     'redirect' => url('/')
                 ]);
             }
 
             return response()->json([
                 'status' => false,
                 'message' => 'Login Gagal'
             ]);
         }
 
         return redirect('login');
     }
 
     public function logout(Request $request)
     {
         Auth::logout();
 
         $request->session()->invalidate();
         $request->session()->regenerateToken();
         return redirect('login');
     }

     public function register() {
        return view('auth.register');
    }

    public function postregister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|min:3|max:100',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal!',
                'msgField' => $validator->errors()
            ]);
        }

        $user = new UserModel();
        $user->username = $request->username;
        $user->nama = $request->nama;
        $user->password = Hash::make($request->password);
        $user->level_id = 3; // Level default (staff)

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil! Silakan login.',
            'redirect' => url('login')
        ]);        
    }
 }