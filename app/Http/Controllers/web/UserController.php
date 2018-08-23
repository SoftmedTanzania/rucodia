<?php

namespace App\Http\Controllers\web;

use App\User;
use App\Region;
use App\District;
use App\Ward;
use App\Level;
use App\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //All users from the database
        $users = User::paginate(10);
        $page = 'User';
        return view('users/index')
            ->with('users', $users)
            ->with('page', $page);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Load the user creation page/form
            $regions = Region::all();
            $districts = District::all();
            $wards = Ward::all();
            $levels = Level::all();
            $locations = Location::all();
            $page = 'User';

        return view('users/create')
            ->with('regions', $regions)
            ->with('districts', $districts)
            ->with('wards', $wards)
            ->with('levels', $levels)
            ->with('locations', $locations)
            ->with('page', $page);
    }

    /**
     * A method to pull all districts for a given region
     * @return [array] [districts]
     */
    public function ajaxdistricts(Request $request, $id)
    {
        $districts = District::where('region_id', $id)->get();
        return $districts;
    }

    /**
     * A method to pull all wards for a given district
     * @return [array] [wards]
     */
    public function ajaxwards(Request $request, $id)
    {
        $wards = Ward::where('district_id', $id)->get();
        return $wards;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // The validation of the form
        $this->validate($request, [
            'number' => 'integer|nullable',
            'firstname' => 'required|max:50',
            'middlename' => 'required|max:50',
            'surname' => 'required|max:50',
            'phone' => 'required|unique:users|numeric|min:10',
            'username' => 'required|unique:users|min:4',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'region' => 'required',
            'district' => 'required',
            'ward' => 'required',
            ]);

        $level = Level::where('id', $request->level)->first();
        $ward = Ward::where('id', $request->ward)->first();
        $user = new User;
        $user->uuid = (string) Str::uuid();
        $user->firstname = $request->firstname;
        $user->middlename = $request->middlename;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();
        $user->levels()->attach($level->id, array('level_id' => $level->id, 'user_id' => $user->id, 'uuid' => (string) Str::uuid()));
        $user->wards()->attach($ward->id, array('ward_id' => $ward->id, 'user_id' => $user->id, 'uuid' => (string) Str::uuid()));

        $users = User::paginate(10);
        $page = 'User';
        return view('users/index')
            ->with('users', $users)
            ->with('page', $page);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Select a specific user and show details and form
        $user = User::find($id);
        $page = 'User';
        $revenue = DB::table('transactions')
                        ->selectRaw('SUM(price * amount) AS price')
                        ->where('user_id', $id)
                        ->where('transactiontype_id', 2)
                        ->value('price');
        return view('users/show')
            ->with('user', $user)
            ->with('revenue', $revenue)
            ->with('page', $page);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $regions = Region::all();
        $districts = District::all();
        $wards = Ward::all();
        $levels = Level::all();
        $locations = Location::all();
        $page = 'User';

        // Get the details for a specific user
        $user = User::find($id);
        return view('users/edit')
            ->with('user', $user)
            ->with('regions', $regions)
            ->with('districts', $districts)
            ->with('wards', $wards)
            ->with('levels', $levels)
            ->with('locations', $locations)
            ->with('page', $page);
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
        
        // Update the resource with the addressed ID
        $level = Level::where('id', $request['level'])->first();
        $level_uuid = DB::table('level_user')->where('user_id', $id)->value('uuid');
        $level_id = DB::table('level_user')->where('user_id', $id)->value('id');
        $location = Location::where('id', $request['location'])->first();
        $location_uuid = DB::table('location_user')->where('user_id', $id)->value('uuid');
        $location_id = DB::table('location_user')->where('user_id', $id)->value('id');
        $ward = Ward::where('id', $request['ward'])->first();
        $ward_uuid = DB::table('user_ward')->where('user_id', $id)->value('uuid');
        $ward_id = DB::table('user_ward')->where('user_id', $id)->value('id');

        $user = User::find($id);
        $user->firstname = $request['firstname'];
        $user->middlename = $request['middlename'];
        $user->surname = $request['surname'];
        $user->phone = $request['phone'];
        $user->username = $request['username'];
        $user->password = Hash::make($request['password']);
        $user->updated_by = Config::get('apiuser');
        $user->levels()->sync($level->id);
        $user->levels()->updateExistingPivot($level->id, array('uuid' => $level_uuid, 'id' => $level_id));
        // $user->locations()->sync($location->id);
        // $user->locations()->updateExistingPivot($location->id, array('uuid' => $location_uuid, 'id' => $location_id));
        $user->wards()->sync($ward->id);
        $user->wards()->updateExistingPivot($ward->id, array('uuid' => $ward_uuid, 'id' => $ward_id));
        $user->save();

        $users = User::paginate(10);
        $page = 'User';
        return view('users/index')
            ->with('users', $users)
            ->with('page', $page);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Delete a specific User by ID (Soft-Deletes)
        $user = User::find($id);
        $user->update(['deleted_by' => Config::get('apiuser')]);
        $user->delete();
        $users = User::paginate(10);
        $page = 'User';
        return view('users/index')
            ->with('users', $users)
            ->with('page', $page);
    }
}
