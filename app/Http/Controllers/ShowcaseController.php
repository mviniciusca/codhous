<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use Illuminate\Http\Request;

class ShowcaseController extends Controller
{
    public function show($id)
    {
        $showcase = Showcase::findOrFail($id);
        
        return view('showcase-detail', [
            'showcase' => $showcase
        ]);
    }
}
