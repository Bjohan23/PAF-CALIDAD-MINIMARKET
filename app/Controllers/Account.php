<?php
namespace App\Controllers;

class Account extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('admin/login'));
        }

        return view('account');
    }
}