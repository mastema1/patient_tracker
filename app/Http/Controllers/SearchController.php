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
        $jsonSuggest = $request->boolean('suggest') || $request->wantsJson();

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
                ->limit($jsonSuggest ? 5 : 20)
                ->get();
            $facilities = Facility::where(function($w) use ($q){
                    $w->where('name','like','%'.$q.'%')
                      ->orWhere('city','like','%'.$q.'%')
                      ->orWhere('type','like','%'.$q.'%');
                })
                ->orderBy('name')
                ->limit($jsonSuggest ? 5 : 20)
                ->get();
        }

        if ($jsonSuggest) {
            $suggestions = [];
            foreach ($doctors as $d) {
                $label = trim(($d->name ?? 'Doctor').' '.($d->specialty ? '• '.$d->specialty : ''));
                $suggestions[] = [
                    'type' => 'Doctor',
                    'label' => $label,
                    'url' => route('public.doctor.profile', $d->id),
                ];
            }
            foreach ($facilities as $f) {
                $label = trim(($f->name ?? 'Facility').' '.($f->city ? '• '.$f->city : ''));
                $suggestions[] = [
                    'type' => 'Facility',
                    'label' => $label,
                    'url' => null, // no dedicated facility profile route yet
                ];
            }
            return response()->json($suggestions);
        }

        return view('search.results', compact('q','doctors','facilities'));
    }
}
