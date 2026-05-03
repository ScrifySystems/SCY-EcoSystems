<?php 
namespace Scy\Core\FileManager\Controllers;

class PanelController
{
    public function index()
    {
        return view('btv-filemanager::dashboard');
    }

    public function loginPage()
    {
        return view('btv-filemanager::login');
    }

    public function login(Request $request)
    {
        $users = config('filemanager.users');

        if (!isset($users[$request->user])) {
            return back();
        }

        if ($users[$request->user] !== $request->pass) {
            return back();
        }

        session(['btv_user' => $request->user]);

        return redirect('/btv/scy/file-manager');
    }
}