<?php

namespace App\Http\Controllers\web;

use Response;
use App\Category;
use App\Subcategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\Subcategory as SubcategoryResource;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //All categories from the database
        $subcategories = Subcategory::with('categories')->get();
        $page = 'Subcategory';
        return view('subcategories/index')
            ->with('subcategories', $subcategories)
            ->with('page', $page);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Load the creation page
        $page = 'Subcategory';
        $categories = Category::get();
        return view('subcategories/create')
            ->with('page', $page)
            ->with('categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Insert a new subcategory from creation page
        $category = $request->category;
        $category = Category::find($category);
        $user = Auth::user();
        $subcategory = new Subcategory;
        $subcategory->uuid = (string) Str::uuid();
        $subcategory->name = $request['name'];
        $subcategory->description = $request['description'];
        $subcategory->created_by = $user->id;
        $subcategory->save();
        $subcategory->categories()->attach($category->id, array('subcategory_id' => $subcategory->id, 'category_id' => $category->id, 'uuid' => (string) Str::uuid()));
        $page = 'Subcategory';
        $subcategories = Subcategory::get();
        $categories = Category::get();
        // return $subcategory->created_by;
        return view('subcategories/index')
            ->with('page', $page)
            ->with('subcategories', $subcategories);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
