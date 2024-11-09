<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Inertia\Inertia;
use App\Models\Column;
use Illuminate\Http\Request;

class CardController extends Controller
{
    
    public function create (Request $request)
{
    $validatedData = $request->validate([
        'title'=> 'required|string|max:255',
        'description' => 'required|string|max:255',
        'column_id' => 'required',
        'status_id' => 'required'
    ]);

    

    $card = Card::create($validatedData);

    return redirect()->back();
}
}
