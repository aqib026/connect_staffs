<?php

namespace App\Http\Controllers;

use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\UserEntry;
use App\WeeklyPayment;

class PaymentController extends Controller
{
    public function index($id)
    {
        $payment = Payment::where('user_id', $id)->get();
        return response()->json([
            "success" => true,
            "message" => "Payment Data",
            "data" => $payment
        ]);
    }

    public function create(Request $request, $id)
    {
        $payment = Payment::updateOrCreate(
            ['user_id' => $id, 'week' => $request->week, 'year' => $request->year],
            ['notes' => isset($request->notes) ? $request->notes : '',
                'amount' => $request->amount]
        );
        return response()->json([
            "success" => true,
            "message" => "Payment Added Successfully",
            "data" => []
        ]);
    }

    public function getPayments(Request $request){

        $ue = UserEntry::with(['schedule','job','user'])
                        ->selectRaw('*, SEC_TO_TIME( SUM( TIME_TO_SEC( `total_hours` ) ) ) as total_week_hours')
                        ->groupBy('user_id')
                        ->where('date','>=',$request->startDate)
                        ->where('date','<=',$request->endDate)->get();
        $week   = date('W',strtotime($request->startDate)); 
        $year   = date('Y',strtotime($request->startDate)); 

        $aps = [];        
        foreach($ue as $e){
            $amount = 0;
            $advPayment = Payment::where('week', $week)->where('year', $year)->where('user_id' , $e->user_id)->first();
            $wkPayment  = WeeklyPayment::where('week', $week)->where('year', $year)->where('user_id' , $e->user_id)->first();
            $cost = 0;
            $ni_payment = 0;
            $tax = 0;
            $amount_paid = 0;
            $week_outstanding = 0;
            if($wkPayment != null){
                $cost = $wkPayment->cost;
                $ni_payment =  $wkPayment->ni_payment;
                $tax =  $wkPayment->tax;
                $amount_paid =  $wkPayment->amount_paid;
                $week_outstanding =  $wkPayment->week_outstanding;    
            }
            if($advPayment != null ) $amount = $advPayment->amount;
            $aps[$e->user_id] = ['amount'=> $amount , 
                                'cost' => $cost, 
                                'ni_payment' => $ni_payment , 
                                'tax' => $tax, 
                                'amount_paid' => $amount_paid, 
                                'outstanding' => 0,
                                'week_outstanding' => $week_outstanding,
                                'total_due' => 0];            
        }

        $data = [ 'ue' => $ue , 'advPayment' => $aps ];

        return response()->json([
            "success" => true,
            "message" => "Payment Added Successfully",
            "data" => $data
        ]);
                
    }
}
