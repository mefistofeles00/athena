<?php
namespace App\Controllers;
use App\Helpers\View;

class UserController extends Controller {
    public function index()
    {
        View::make('users.index', [
            'title' => 'sadness gardess'
        ]);
    }
}