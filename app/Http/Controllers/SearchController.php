<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Facility;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $doctors = collect();
        $facilities = collect();
        if ($q !== '') {
            $doctors = User::where('role','doctor')
                ->where(function($w) use ($q){
                    $w->where('name','like','%'.$q.'%')
                      ->orWhere('specialty','like','%'.$q.'%')
                      ->orWhere('case_categories','like','%'.$q.'%');
                })
                ->orderBy('name')
                ->limit(20)
                ->get();
            $facilities = Facility::where(function($w) use ($q){
                    $w->where('name','like','%'.$q.'%')
                      ->orWhere('city','like','%'.$q.'%')
                      ->orWhere('type','like','%'.$q.'%');
                })
                ->orderBy('name')
                ->limit(20)
                ->get();
        }
        return view('search.results', compact('q','doctors','facilities'));
    }
}
