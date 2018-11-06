<?php

namespace App\Http\Controllers\API;

use App\Group;
use App\House;
use App\Payable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['authorization.group'], ['except' => ['index', 'store']]);
        $this->middleware(['authorization.house'], ['only' => 'store']);
    }


    /**
     * @GET
     * @route : /groups
     * Display a listing of the groups.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response["groups"] = Group::all();
        $response["links"]["self"] = route('groups.index');
        return response()->json($response, 200);
    }

    /**
     *  @POST
     * @route : /groups/{house_id}
     * @params : name, description, users[id,id,id], 
     * Store a newly created group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, House $house)
    {
        // dd($request->all());
        $request->request->add(['house_id' => $house->id]);
        $validate = Validator::make($request->all(), Group::$storeRules);
        if (!$validate->fails()) {
            $group = new Group($request->all());
            if ($group->save()) {
                $group->users()->attach(Auth::user()->id);
                if (count($request->input('users')) > 0) {
                    foreach ($request->input('users') as $user) {
                        $group->users()->attach($user);
                    }
                }

                $response["status"] = "CREATED";
                $response["group"] = $group->makeHidden('users');
                $response["links"]["self"] = $request->url();
                $response["links"]["group"] = route('groups.show', $group->id);
                return response()->json($response, 201); // 201 - created
            }
            return response()->json(["status" => "FAILED"], 400);
        } else {
            return response()->json($validate->errors(), 422);// 422 - unprocessable request
        }
    }

    /**
     * @GET
     * @route : /groups/{group_id}
     * Display the specified group.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        $response["group"] = $group;
        $response["links"]["self"] = route('groups.show', $group->id);
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


    /**
     * @GET
     * Display specified group's users
     *
     * @param  Group  $group
     * @return \Illuminate\Http\Response
     */

    public function allUsers(Group $group)
    {
        $response["group"] = $group->makeHidden(['users']);
        $response["users"] = $group->users;
        $response["links"] = array(
            "self" => route('groups.users', ['id' => $group->id]),
        );
        foreach ($group->users as $user) {
            $user_array[$user->id] = route('users.show', $user->id);
        }
        $response["links"]["users"] = $user_array;
        return response()->json($response, 200);
    }

    public function house(Group $group)
    {
        $response["group"] = $group->makeHidden(['house', 'house_id']);
        $response["house"] = $group->house;
        $response["links"] = array(
            "self" => route('groups.house', ['id' => $group->id]),
            "house" => route('houses.show', ['id' => $group->house_id])
        );
        return response()->json($response, 200);
    }

    public function allTransactions(Group $group)
    {
        $response["group"] = $group->makeHidden('transactions');
        $response["transactions"] = $group->transactions->makeHidden(['pivot', 'group_id']);
        $response["links"] = array(
            "self" => route('groups.transactions', ['id' => $group->id]),
        );

        foreach ($group->transactions as $transaction) {
            $transaction_array[$transaction->id] = route('transactions.show', $transaction->id);
        }
        $response["links"]["transactions"] = $transaction_array;

        return response()->json($response, 200);
    }

    public function allPayables(Group $group)
    {
        $payables = $group->payables;
        $repsonse = array();
        foreach ($payables as $payable) {
            $response[$payable->id] = $payable->makeHidden(['payer_id', 'receiver_id', 'group_id']);
            $response[$payable->id]["payer"] = $payable->payer;
            $response[$payable->id]["receiver"] = $payable->receiver;
        }
        $response["group"] = $group->makeHidden('payables');

        return response()->json($response, 200);
    }

    public function calculatePayables(Group $group)
    {
        $response = array();
        if (count($group->users) > 1) {
            $entry = array();
            $finalAssesment = array();
            $grand_total = 0;
            $per_person = 0;
            $payables = array();
            $receivables = array();
        //get grand total
            foreach ($group->transactions as $transaction) {
        //note:dont forget to check if transaction is already calculated (is_calculated)
                if (!$transaction->is_calculated) {
                    $grand_total += $transaction->amount;
                    if (array_key_exists($transaction->user_id, $entry)) {
                        $entry[$transaction->user_id] += $transaction->amount;
                    } else {
                        $entry[$transaction->user_id] = $transaction->amount;
                    }
                    $transaction->is_calculated = 1;
                    // $transaction->save();
                }
            }

        //perperson
            $per_person = round($grand_total / count($entry), 2);
        //calculate payables value - per_person (order is imp)
            foreach ($entry as $user => $amount) {
                $money = ($amount - $per_person);
                if ($money > 0) {
                    $receivables[$user] = round($money, 1);
                } else {
                    $payables[$user] = round(abs($money), 1);
                }
            }


            $ledgerEntries = array();
            foreach ($receivables as $receiver => $receiveAmount) {
                foreach ($payables as $payer => $payAmount) {
                    if (!$receivables[$receiver] == 0) {
                        if ($receivables[$receiver] > $payables[$payer] || $receivables[$receiver] == $payables[$payer]) {
                            $ledgerEntries[$receiver][$payer] = $payables[$payer];
                            $receivables[$receiver] -= $payables[$payer];
                            $payables[$payer] = 0;
                            unset($payables[$payer]);
                        } elseif ($receivables[$receiver] < $payables[$payer]) {
                            $ledgerEntries[$receiver][$payer] = $receivables[$receiver];
                            $payables[$payer] -= $receivables[$receiver];
                            $receivables[$receiver] = 0;
                        }
                    } else {
                        break;

                    }
                }
            }

            foreach ($ledgerEntries as $receiver => $payableEntry) {
                foreach ($payableEntry as $payer => $amount) {
                    if ($amount > 0) {
                        $toPay = new Payable;
                        $toPay->payer_id = $payer;
                        $toPay->receiver_id = $receiver;
                        $toPay->group_id = $group->id;
                        $toPay->amount_due = $amount;
                        $toPay->is_paid = 0;
                        $toPay->save();
                    }
                }

            }

            $response["status"] = "SUCCESS";
            $response["message"] = "Payables Calculated succesfully.";
            $response["payables"] = $group->payables;
            return response()->json($response, 200);
        }
        $response["status"] = "FAILED";
        $response["message"] = "Need more than 1 user in the group to calculate.";
        return response()->json($response, 400);
    }
}
