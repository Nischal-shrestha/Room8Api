<?php

namespace App\Http\Controllers\API;

use App\Payable;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;

class PayableController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['authorization.payable'], ['except' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payable  $payable
     * @return \Illuminate\Http\Response
     */
    public function show(Payable $payable)
    {
        $response["payable"] = $payable->makeHidden(['payer', 'receiver', 'group']);
        $response["payer"] = $payable->payer;
        $response["receiver"] = $payable->receiver;
        $response["group"] = $payable->group->makeHidden('house');
        $response["house"] = $payable->group->house;

        $response["links"]["self"] = route('payables.show', $payable->id);
        $response["links"]["payer"] = route('users.toPay', $payable->payer->id);
        $response["links"]["receiver"] = route('users.toReceive', $payable->receiver->id);
        $response["links"]["group"] = route('groups.show', $payable->group->id);
        $response["links"]["house"] = route('houses.show', $payable->group->house->id);

        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payable  $payable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payable $payable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payable  $payable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payable $payable)
    {
        //
    }
}
