<?php

use Illuminate\Database\Seeder;
use App\Payable;
use App\Transaction;
use App\House;

class PayablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //to make sure we only calculate if the group has more than 1 user
        $done = false;
        while (!$done) {
            $house = House::all()->random();
            $group = $house->groups->random();
            if (count($group->users) > 1) {
                //initialize all the variables
                $payables = array();
                $finalAssesment = array();
                //total of all the transactions in a group
                $grand_total = 0;
                //amount to be paid by every person (grand_total/number_of_person)
                $per_person = 0;
                $receiver = 0;

            //STEP 1 : Calculate grand_total and total of every person's transactions
                foreach ($group->transactions as $transaction) {
                    if (!$transaction->is_calculated) {
                        $grand_total += $transaction->amount;
                        if (array_key_exists($transaction->user_id, $payables)) {
                            $payables[$transaction->user_id] += $transaction->amount;
                        } else {
                            $payables[$transaction->user_id] = $transaction->amount;
                        }
                        $transaction->is_calculated = 1;
                        $transaction->save();
                    }
                }
            //STEP 2 : calculate per_person amount
                $per_person = round($grand_total / count($payables), 2);

            //STEP 3 : Calculate and asses if the person is a receiveable or a payable
                foreach ($payables as $user => $amount) {
                    $payables[$user] = ($amount - $per_person);
                    if ($payables[$user] > 0) {
                        $receiver = $user;
                    }
                }

            //STEP 4 : Insert every payable into the payables table
                foreach ($payables as $user => $amount) {
                    if ($amount < 0 && $amount != 0) {
                        $toPay = new Payable;
                        $toPay->payer_id = $user;
                        $toPay->receiver_id = $receiver;
                        $toPay->group_id = $group->id;
                        $toPay->amount_due = abs($amount);
                        $toPay->is_paid = 0;
                        $toPay->save();
                    }
                }
                $done = true;
            }

        }

    }
}
