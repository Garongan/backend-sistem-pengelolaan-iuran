<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function show(string $url)
    {
        return Storage::url($url);
    }
}
