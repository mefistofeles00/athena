<?php

namespace App\Controllers;
use App\Helpers\Response;
use App\Helpers\View;
class Controller {
    protected function view($view, $data = []) {
        return View::make($view, $data);
    }

    protected function json($data, $status = 200) {
        return Response::json($data, $status);
    }
}