<?php 
namespace SCY\Core\FileManager\Controllers;

class AuthController
{
    public function show()
    {
        return view('filemanager.login');
    }

    public function login(Request $request)
    {
        $user = $request->input('user');
        $pass = $request->input('pass');

        // DEV MODE / config users
        $validUsers = config('filemanager.users');

        if (!isset($validUsers[$user]) || $validUsers[$user] !== $pass) {
            return back()->with('error', 'Invalid login');
        }

        session(['btv_user' => $user]);

        // webhook trigger
        app(AuthService::class)->sendWebhook($user, $request->ip());

        return redirect('/btv/scy/filemanager');
    }
}