<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Group;
use Illuminate\Validation\Validator;

class TransactionController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //only the people in the transaction's group can view the transaction
        $this->middleware(['authorization.transaction:group'], ['only' => 'show']);
        //only the group users can add transactions in the group
        $this->middleware(['authorization.group'], ['only' => ['store']]);
        //only the creator of transaction can edit / delete the transaction
        $this->middleware(['authorization.transaction'], ['only' => ['update', 'destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * @POST
     * @route : /transactions/{group_id}
     * @params description, amount, location
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Group $group)
    {
        $user = Auth::user();
        $request->request->add([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'house_id' => $group->house->id
        ]);

        $validate = Validator::make($request->all(), Transaction::$storeRules);
        if (!$validate->fails()) {
            $transaction = new Transaction($request->all());
            if ($transaction->save()) {
                $response["status"] = "CREATED";
                $response["user"] = $user;
                $response["group"] = $group;
                $response["house"] = $group->house;
                $response["transaction"] = $transaction;

                $response["links"]["self"] = $request->url();
                $response["links"]["group"] = route('groups.show', $group->id);
                $response["links"]["house"] = route('houses.show', $group->house->id);
                $response["links"]["transaction"] = route('transactions.show', $transaction->id);

                return response()->json($response, 201);//created
            }

            return response()->json(["status" => "FAILED"], 400);
        } else {
            return response()->json($validate->errors(), 422);// 422 - unprocessable request
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        $hidden = ['group', 'house', 'user'];
        $response["transaction"] = $transaction->makeHidden($hidden);
        $response["group"] = $transaction->group;
        $response["house"] = $transaction->house;
        $response["user"] = $transaction->user;
        $response["links"]["self"] = route('transactions.show', ['id' => $transaction->id]);
        $response["links"]["group"] = route('groups.show', ['id' => $transaction->group_id]);
        $response["links"]["house"] = route('houses.show', ['id' => $transaction->house_id]);
        $response["links"]["user"] = route('users.show', ['id' => $transaction->user_id]);
        return response()->json($response, 200);
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
