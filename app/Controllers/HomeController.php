<?php

namespace App\Controllers;

use App\Helpers\View;

class HomeController extends Controller
{
    public function index()
    {
        return View::make('home.index', [
            'title' => 'Ana Sayfa'
        ]);
    }

    public function about()
    {
       return View::make('home.about',
            [
                'title' => 'selam dayi'
            ]);
    }
}