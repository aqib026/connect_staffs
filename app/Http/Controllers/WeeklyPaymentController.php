<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WeeklyPayment;
use App\Payment;

class WeeklyPaymentController extends Controller
{
    //

    public function save(Request $request){

        $week = date('W',strtotime($request->endDate));
        $year = date('Y',strtotime($request->endDate));
        foreach($request->ue as $payment){
            $wp = WeeklyPayment::where('user_id',$payment['user_id'])
                                ->where('week',$week)->where('year',$year)->first();
            if($wp == null){
                $wp = new WeeklyPayment;
                $wp->user_id   = $payment['user_id'];
                $wp->worker_id = $payment['user_id'];    
                $wp->week = $week;    
                $wp->year = $year;    
            }
            $wp->amount_paid  = $request->advPayment[$payment['user_id']]['amount_paid'];
            $wp->hours        = $payment['total_hours'];
            $wp->rate         = $payment['user']['rate'];
            $wp->cost         = $request->advPayment[$payment['user_id']]['cost'];
            $wp->ni_payment   = $request->advPayment[$payment['user_id']]['ni_payment'];
            $wp->tax          = $request->advPayment[$payment['user_id']]['tax'];
            $wp->week_outstanding = $request->advPayment[$payment['user_id']]['week_outstanding'];
            $wp->save();

            if($request->advPayment[$payment['user_id']]['week_outstanding'] < 0){
                $next_week = date('W',strtotime($request->endDate." +7 days"));
                $next_year = date('Y',strtotime($request->endDate." +7 days"));        
                $advPayment = Payment::where('week',$next_week)->where('year',$next_year)->where('user_id',$payment['user_id'])->first();
                if($advPayment == null){
                    $advPayment = new Payment;
                    $advPayment->week      = $next_week;
                    $advPayment->year = $next_year;     
                    $advPayment->user_id = $payment['user_id'];                    
                }
                $advPayment->notes     = 'Last week remaining payment';
                $advPayment->amount    = abs($request->advPayment[$payment['user_id']]['week_outstanding']);
                $advPayment->save();
            }
        }

    }
}
