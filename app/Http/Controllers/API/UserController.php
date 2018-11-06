<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['authorization.user'], ['except' => ['index', 'show', 'toPay', 'toReceive', 'paid', 'received']]);
    }

    /**
     * @GET
     * Display a listing of all the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(User::all(), 200);
    }


    /**
     * Store a newly created user in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @GET
     * Display a specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {

        UserResource::withoutWrapping();
        return new UserResource($user);
    }

    /**
     * Update the specified user in storage.
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
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return response()->json(["status" => "OK"], 200);
        }
        return response()->json(["status" => "ERROR"], 403);

    }

    /**
     * @GET
     * Display specified user's houses
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function allHouses(User $user)
    {
        $response["user"] = $user->makeHidden('houses');
        $response["houses"] = $user->houses;
        $response["links"] = array(
            "self" => route('users.houses', ['id' => $user->id]),
        );

        foreach ($user->houses as $house) {
            $house_array[$house->id] = route('houses.show', $house->id);
        }
        $response["links"]["houses"] = $house_array;

        return response()->json($response, 200);
    }

    public function allGroups(User $user)
    {
        $response["user"] = $user->makeHidden('groups');
        $response["groups"] = $user->groups->makeHidden('pivot');
        $response["links"] = array(
            "self" => route('users.groups', ['id' => $user->id]),
        );

        foreach ($user->groups as $group) {
            $group_array[$group->id] = route('groups.show', $group->id);
        }
        $response["links"]["groups"] = $group_array;

        return response()->json($response, 200);
    }

    public function allTransactions(User $user)
    {
        $response["user"] = $user->makeHidden('transactions');
        if (count($user->transactions) > 0) {
            $response["transactions"] = $user->transactions->makeHidden('user_id');
        } else {
            $response["transactions"] = "none";
        }
        $response["links"] = array(
            "self" => route('users.transactions', ['id' => $user->id]),
        );

        foreach ($user->transactions as $transaction) {
            $transaction_array[$transaction->id] = route('transactions.show', $transaction->id);
        }
        $response["links"]["transactions"] = $transaction_array;

        return response()->json($response, 200);
    }

    public function toPay(User $user)
    {
        $toPay = $user->toPay()->notPaid()->get();
        $response["user"] = $user->makeHidden('toPay');
        $response["toPay"] = $toPay->makeHidden('payer_id');
        $response["links"]["self"] = route('users.toPay', $user->id);

        foreach ($toPay as $tp) {
            $tp_array[$tp->id] = route('payables.show', $tp->id);
        }
        isset($tp_array) ? $response["links"]["toPay"] = $tp_array : null;

        return response()->json($response, 200);
    }

    public function toReceive(User $user)
    {
        $toReceive = $user->toReceive()->notPaid()->get();
        $response["user"] = $user->makeHidden('toRecieve');
        $response["receive"] = $toReceive->makeHidden('receiver_id');
        $response["links"]["self"] = route('users.toReceive', $user->id);

        foreach ($toReceive as $tr) {
            $tr_array[$tr->id] = route('payables.show', $tr->id);
        }
        isset($tr_array) ? $response["links"]["toReceive"] = $tr_array : null;

        return response()->json($response, 200);
    }

    public function paid(User $user)
    {
        $paidPayables = $user->toPay()->paid()->get();
        $response["user"] = $user->makeHidden('toPay');
        $response["paid"] = $paidPayables->makeHidden('payer_id');
        $response["links"]["self"] = route('users.paid', $user->id);

        foreach ($paidPayables as $paid) {
            $paid_array[$paid->id] = route('payables.show', $paid->id);
        }
        isset($paid_array) ? $response["links"]["paid"] = $paid_array : null;

        return response()->json($response, 200);
    }

    public function received(User $user)
    {
        $receivedPayables = $user->toReceive()->paid()->get();
        $response["user"] = $user->makeHidden('toRecieve');
        $response["received"] = count($receivedPayables) ? $receivedPayables->makeHidden('receiver_id') : "none";
        $response["links"]["self"] = route('users.received', $user->id);

        foreach ($receivedPayables as $received) {
            $received_array[$received->id] = route('payables.show', $received->id);
        }
        isset($received_array) ? $response["links"]["received"] = $received_array : null;

        return response()->json($response, 200);
    }
}
