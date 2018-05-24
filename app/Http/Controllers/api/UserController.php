<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\User;
use App\Level;
use App\Location;
use App\Ward;
use App\Transaction;
use App\Sms;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // /**
    //  * A Construct setting up User and Response
    //  * 
    //  * @return NULL
    //  */
    public function __construct(Request $request, User $user) {
        $this->request = $request;
        $this->user = $user;
    }

    
    /**
     * List Users
     * 
     * JSON List of all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // List all the users in a collection
        UserResource::WithoutWrapping();
        return UserResource::collection(User::with('levels')->with('locations')->with('wards')->get());
    }

    /**
     * Add User
     * 
     * Store a newly created user resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get all the details for User creation
        $level = Level::where('name', $request['level'])->first();
        $location = Location::where('name', $request['location'])->first();
        $location = Ward::where('name', $request['ward'])->first();
        $user = new User;
        $user->uuid = (string) Str::uuid();
        $user->firstname = $request['firstname'];
        $user->middlename = $request['middlename'];
        $user->surname = $request['surname'];
        $user->email = $request['email'];
        $user->username = $request['username'];
        $user->password = Hash::make($request['password']);
        $user->created_by = Config::get('apiuser');
        $user->save();
        $user->levels()->attach($level->id, array('level_id' => $level->id, 'user_id' => $user->id, 'uuid' => (string) Str::uuid()));
        $user->locations()->attach($location->id, array('location_id' => $location->id, 'user_id' => $user->id, 'uuid' => (string) Str::uuid()));
        $user->wards()->attach($ward->id, array('ward_id' => $ward->id, 'user_id' => $user->id, 'uuid' => (string) Str::uuid()));
        return response()->json([
            'action' => 'create',
            'status' => 'OK',
            'entity' => $user->uuid,
            'type' => 'user',
            'user' => Config::get('apiuser')
        ], 201);
    }

    /**
     * Update User
     * 
     * Edit an existing User details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        // Check if user is not in the DB
        if ($user === null) {
            return response()->json([
                'action' => 'show',
                'status' => 'FAIL',
                'entity' => NULL,
                'type' => 'user',
                'user' => Config::get('apiuser')
            ], 404);
        }
        else {
        // List the details of a specific user
        UserResource::WithoutWrapping();
        return new UserResource(User::with('levels')->with('locations')->with('wards')->find($id));
        }
    }

    /**
     * Update User
     * 
     * Update the specified user resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Update the resource with the addressed ID
        $level = Level::where('name', $request['level'])->first();
        $level_uuid = DB::table('level_user')->where('user_id', $id)->value('uuid');
        $level_id = DB::table('level_user')->where('user_id', $id)->value('id');
        $location = Location::where('name', $request['location'])->first();
        $location_uuid = DB::table('location_user')->where('user_id', $id)->value('uuid');
        $location_id = DB::table('location_user')->where('user_id', $id)->value('id');
        $ward = Ward::where('name', $request['ward'])->first();
        $ward_uuid = DB::table('user_ward')->where('user_id', $id)->value('uuid');
        $ward_id = DB::table('user_ward')->where('user_id', $id)->value('id');

        $user = User::find($id);
        $user->firstname = $request['firstname'];
        $user->middlename = $request['middlename'];
        $user->surname = $request['surname'];
        $user->email = $request['email'];
        $user->username = $request['username'];
        $user->password = Hash::make($request['password']);
        $user->updated_by = Config::get('apiuser');
        $user->levels()->sync($level->id);
        $user->levels()->updateExistingPivot($level->id, array('uuid' => $level_uuid, 'id' => $level_id));
        $user->locations()->sync($location->id);
        $user->locations()->updateExistingPivot($location->id, array('uuid' => $location_uuid, 'id' => $location_id));
        $user->wards()->sync($ward->id);
        $user->wards()->updateExistingPivot($ward->id, array('uuid' => $ward_uuid, 'id' => $ward_id));
        $user->save();
        return response()->json([
            'action' => 'update',
            'status' => 'OK',
            'entity' => $user->uuid,
            'type' => 'user',
            'user' => Config::get('apiuser')
        ], 200);
    }

    /**
     * Delete User
     * 
     * Remove the specified user resource from storage.
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
        return response()->json([
            'action' => 'delete',
            'status' => 'OK',
            'entity' => $user->uuid,
            'type' => 'user',
            'user' => Config::get('apiuser')
        ], 200);
    }

    /**Show Auth Details
     * 
     * Get the auth page then send back user details
     * 
     * @return \Ilumminate\Http\Response
     */
    public function auth()
    {
        $user = User::find(Config::get('apiuser'))->with('levels')->with('locations')->with('wards')->first();
        return response()->json($user);
    }

    /**
     * Show Product Balances
     * 
     * Specific user product details such as Totals, Cash, Balances and Profit.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userBalance($user_id, $product_id)
    {
        $user = User::find($user_id);
        $totalBought = Transaction::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->where('transactiontype_id', 1)
            ->sum('amount');
        $totalSold = Transaction::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->where('transactiontype_id', 2)
            ->sum('amount');
        $expenditure = DB::table('transactions')
            ->selectRaw('SUM(price * amount) AS price')
            ->where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->where('transactiontype_id', 1)
            ->value('price');
        $revenues = DB::table('transactions')
            ->selectRaw('SUM(price * amount) AS price')
            ->where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->where('transactiontype_id', 2)
            ->value('price');
        $profit = $revenues - $expenditure;
        $balance = $totalBought - $totalSold;
            return response()->json([
                'action' => 'balance',
                'status' => 'OK',
                'entity' => $user->uuid,
                'type' => 'user',
                'product' => (int)$product_id,
                'bought' => (int)$totalBought,
                'sold' => (int)$totalSold,
                'balance' => $balance,
                'expenditure' => (int)$expenditure,
                'revenues' => (int)$revenues,
                'profit' => $profit,
                'user' => Config::get('apiuser')
            ], 200);
    }

    /**
     * Show Product Balances
     * 
     * Specific user product details such as Totals, Cash, Balances and Profit.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userBalances($user)
    {
        $user = User::find($user);
        $products = Transaction::where('user_id', $user->id)
            ->addSelect(
                DB::raw('
                    product_id,
                    CAST(SUM(CASE transactiontype_id WHEN 1 THEN amount END) as signed) AS product_bought,
                    CAST(SUM(CASE transactiontype_id WHEN 2 THEN amount END) as signed) AS product_sold,
                    CAST(SUM(CASE transactiontype_id WHEN 1 THEN amount WHEN 2 THEN -amount END) AS SIGNED) AS product_balance,
                    COUNT(CASE transactiontype_id WHEN 1 THEN id END) AS product_purchases,
                    COUNT(CASE transactiontype_id WHEN 2 THEN id END) AS product_sales,
                    CAST(SUM(CASE transactiontype_id WHEN 1 THEN amount * price END) as signed) AS product_expenditure,
                    CAST(SUM(CASE transactiontype_id WHEN 2 THEN amount * price END) as signed) AS product_revenue,
                    CAST(SUM(CASE transactiontype_id WHEN 1 THEN amount * -price WHEN 2 THEN amount * price END) as signed) AS product_profit
                    '))
            ->groupBy('product_id')
            ->get();

            return response()->json([
                'action' => 'user_products',
                'status' => 'OK',
                'entity' => $user->uuid,
                'type' => 'user',
                'products' => $products,
                'user' => Config::get('apiuser')
            ], 200);
    }

    /**
     * Add User
     * 
     * Store a newly created user resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sms(Request $request)
    {

        $data = $request->input('input.urn');
        $sms = new Sms;
        $sms->uuid = (string) Str::uuid();
        $sms->urn = $request->input('input.urn');
        $sms->text = strtolower($request->input('input.text'));
        $sms->save();
        $text_array = explode(' ', $sms->text);
        if (str_word_count($sms->text) == 3) {
            $district = $text_array[2];
            $product = $text_array[1];
            $agrodealers = DB::select(DB::raw ("
                select distinct users.firstname as name, MAX(transactions.price) as price from transactions 
                inner join users on users.id = transactions.user_id 
                inner join products on products.id = transactions.product_id 
                inner join transactiontypes on transactiontypes.id = 1 
                inner join product_subcategory on product_subcategory.product_id = products.id
                inner join subcategories on product_subcategory.subcategory_id = subcategories.id
                inner join user_ward on user_ward.user_id = users.id 
                inner join wards on wards.id = user_ward.ward_id
                inner join districts on wards.district_id = districts.id
                where districts.name like 'Kasulu Mji'
                group by users.firstname;
            "));
            // return response()->json([
            //     'district' => $district,
            //     'product' => $product,
            //     'action' => 'sms',
            //     'status' => 'OK',
            //     'entity' => $sms->uuid,
            //     'type' => 'sms',
            //     'text' => $agrodealers,
            //     'user' => Config::get('apiuser')
            // ], 200);
            $agrodealers = json_decode(json_encode($agrodealers), true);
            $result = array();
            foreach($agrodealers as $key=> $val)
            {
            $result[] = $val['name'].':'.$val['price'];
            }

            return response()->json([
                    'urn' => $sms->urn,
                    'text'=> implode(', ', $result)
                ], 200)
                ->header('Authorization', 'Token ff3c9c1b920aa755f9dbcb6f83bab52c1fa27689');
        }
        else {
            return response()->json([
                'error' => 'SMS haiko sawa, tafadhali tuma sms kama hii, BEI BIDHAA WILAYA mfano, BEI MAHINDI KASULU'
            ]);
        }
        
    }
}
