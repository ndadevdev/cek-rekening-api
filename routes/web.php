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
            $http = new \Illuminate\Http\Request();
            $http->merge(['email' => 'admin@example.com', 'password' => 'password']);
            $controller = new \App\Http\Controllers\Api\AuthController;
            $res = $controller->login($http);
            $data = $res->getData();
            if ($data->success ?? false) {
                session(['api_token' => $data->data->token]);
            }
        } catch (\Throwable $e) {
            // abai
        }

        return redirect('/demo');
    }

    return back()->withErrors(['password' => 'Kode salah']);
});
