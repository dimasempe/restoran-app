<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    //
    public function index(){
        $items = Item::select('id','name','price','image')->get();
        return response(['data' => $items]);
    }

    public function store(Request $request){
        // return 'nice';
        $validateData = $request->validate([
            'name' => 'required|max:100',
            'price' => 'required|integer',
            'image' => 'nullable|mimes:jpg,png|file|max:2048'
        ]);

        if($request->file('image')){
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp.'_'.$fileName;
            // return $newName;
            Storage::disk('public')->putFileAs('items', $file, $newName);
            $validateData['image'] = $newName;
            // $request->merge(['image' => $newName]);
        }
        $item = Item::create($validateData);

        return response(['data' => $item]);  
    }

    public function update(Request $request, Item $item){
        // return $item;
        $validateData = $request->validate([
            'name' => 'required|max:100',
            'price' => 'required|integer',
            'image' => 'nullable|mimes:jpg,png|file|max:2048'
        ]);

        if($request->file('image')){
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp.'_'.$fileName;
            // return $newName;
            if ($item->image) {
                Storage::disk('public')->delete('items/' . $item->image);
            }
            Storage::disk('public')->putFileAs('items', $file, $newName);
            $validateData['image'] = $newName;
            // $request->merge(['image' => $newName]);
        }
        
        $item->update($validateData);

        return response(['data'=>$item]);
    }
}
