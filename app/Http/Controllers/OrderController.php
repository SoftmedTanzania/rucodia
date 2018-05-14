<?php

namespace App\Http\Controllers;

use App\Order;
use App\Subcategory;
use App\Unit;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\Order as OrderResource;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // List all the Orders in a collection
        OrderResource::WithoutWrapping();
        return OrderResource::collection(Order::with('dealer')->with('supplier')->with('product')->get());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get all the details for Order creation
        $order = new Order;
        $order->uuid = (string) Str::uuid();
        $order->ordered = $request['ordered'];
        $order->batch = $request['batch'];
        $order->status_id = 1;
        $order->product_id = $request['product_id'];
        $order->dealer_id = $request['dealer_id'];
        $order->supplier_id = $request['supplier_id'];
        $order->created_by = Config::get('apiuser');
        $order->save();
        return response()->json([
            'action' => 'create',
            'status' => 'OK',
            'entity' => $order->uuid,
            'type' => 'order',
            'user' => Config::get('apiuser')
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        // Check if order is not in the DB
        if ($order === NULL) {
            return response()->json([
                'action' => 'show',
                'status' => 'FAIL',
                'entity' => NULL,
                'type' => 'order',
            'user' => Config::get('apiuser')
            ], 404);
        }
        else {
        // List the details of a specific order
        OrderResource::WithoutWrapping();
        return new OrderResource(Order::with('dealer')->with('supplier')->with('product')->find($id));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Update the resource with the addressed ID
        $order = Order::find($id);
        $order->ordered = $request['ordered'];
        $order->delivered = $request['delivered'];
        $order->status = $request['status'];
        $order->updated_by = Config::get('apiuser');
        $order->save();
        return response()->json([
            'action' => 'update',
            'status' => 'OK',
            'entity' => $order->uuid,
            'type' => 'order',
            'user' => Config::get('apiuser')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Delete a specific Order by ID (Soft-Deletes)
        $order = Order::find($id);
        $order->update(['deleted_by' => Config::get('apiuser')]);
        $order->delete();
        return response()->json([
            'action' => 'delete',
            'status' => 'OK',
            'entity' => $order->uuid,
            'type' => 'order',
            'user' => Config::get('apiuser')
        ], 200);
    }
}
