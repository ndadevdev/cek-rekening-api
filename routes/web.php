<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/demo');
});

Route::get('/demo', function () {
    if (session('demo_authenticated')) {
        return view('demo');
    }
    return view('demo-gate');
});

Route::post('/demo', function (\Illuminate\Http\Request $request) {
    $password = $request->input('password');
    $expected = config('app.demo_password', 'demo123');

    if ($password === $expected) {
        session(['demo_authenticated' => true]);

        // auto-login ke API biar dapet token
        try {
            $user = \App\Models\User::where('email', 'admin@example.com')->first();
            if (!$user) {
                $user = \App\Models\User::factory()->create([
                    'name' => 'Admin Developer',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('password'),
                ]);
            }
            $token = $user->createToken('api-token')->plainTextToken;
            session(['api_token' => $token]);
        } catch (\Throwable $e) {
            // abai
        }

        return redirect('/demo');
    }

    return back()->withErrors(['password' => 'Kode salah']);
});
