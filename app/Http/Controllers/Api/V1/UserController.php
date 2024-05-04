<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function ban() {
        return Common::response(200, "Tính năng chưa cần thiết! ^^");
    }
    public function destroy(){
        return Common::response(200, "Tính năng chưa cần thiết! ^^");
    }
}
