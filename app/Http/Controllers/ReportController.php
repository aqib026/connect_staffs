<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Scheduler;
use App\WeeklyPayment;
use App\UserEntry;
use App\Payment;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{

    public function weeklyTimesheet(Request $request){
        $d = $request->week;
        $yw = explode('-',$d);
        $year = $yw[0];
        $week = str_replace('W','',$yw[1]);

        $items    = UserEntry::with(['user'])
                                ->select([  'user_entries.id',
                                            'user_entries.user_id',
                                            'user_entries.job_name',
                                            'schedulers.jb_date',
                                            'schedulers.total_hours   as scheduled_total_hours',
                                            'user_entries.total_hours   as ue_total_hours',
                                            ])
                                ->join('schedulers','schedulers.id','=','user_entries.schedule_id')
                                ->whereRaw("WEEK(date) = '$week'")
                                ->whereRaw("Year(date) = '$year'");
        if($request->job != "")            
            $items    =  $items->where('schedulers.job_id',$request->job);
        if($request->user != "")            
            $items    =  $items->where('schedulers.user_id',$request->user);
        
        $items    =  $items->get();
        $responseHeaders = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=employees.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $callback = function () use ( $items , $week , $year) {
            // Header row
            $header = ['First Name' , 'Last Name' , 'Date' , 'Scheduled Hours' , 'Timesheet Hours' , 'Difference', 'Job' ];
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);        

            foreach ($items as $item) {
                $rows = [];
                $rows[] = $item->user ? $item->user->name : ''; //user
                $rows[] = $item->user ? $item->user->last_name : ''; //phone number
                $rows[] = $item->jb_date; //phone number
                $rows[] = $item->scheduled_total_hours; //phone number
                $rows[] = $item->job_name; //phone number
                $rows[] = $this->getTimeDifference($item->scheduled_total_hours,$item->ue_total_hours); //phone number
                $rows[] = $item->job_name; //phone number
                fputcsv($file, $rows);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $responseHeaders);
    }
    public function missedDays(Request $request){
        $d = $request->week;
        $yw = explode('-',$d);
        $year = $yw[0];
        $week = str_replace('W','',$yw[1]);

        $items    = UserEntry::with(['user'])
                                ->select([  'user_entries.id',
                                            'user_entries.user_id',
                                            'user_entries.job_name',
                                            'schedulers.jb_date',
                                            'schedulers.total_hours   as scheduled_total_hours',
                                            'user_entries.total_hours   as worked_hours',
                                            ])
                                ->join('schedulers','schedulers.id','=','user_entries.schedule_id')
                                ->whereRaw("WEEK(date) = '$week'")
                                ->whereRaw("Year(date) = '$year'");
        if($request->job != "")            
            $items    =  $items->where('schedulers.job_id',$request->job);
        if($request->user != "")            
            $items    =  $items->where('schedulers.user_id',$request->user);
        
        $items    =  $items->get();
        $responseHeaders = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=misseddays.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $callback = function () use ( $items , $week , $year) {
            // Header row
            $header = ['First Name' , 'Last Name' , 'Date' , 'Worked Hours' ];
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);        

            foreach ($items as $item) {
                $rows = [];
                $rows[] = $item->user ? $item->user->name : ''; //user
                $rows[] = $item->user ? $item->user->last_name : ''; //phone number
                $rows[] = $item->jb_date; //phone number
                $rows[] = $item->worked_hours; //phone number
                fputcsv($file, $rows);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $responseHeaders);
    }
    public function missedHours(Request $request){
        $d = $request->week;
        $yw = explode('-',$d);
        $year = $yw[0];
        $week = str_replace('W','',$yw[1]);

        $items    = UserEntry::with(['user'])
                                ->select([  'user_entries.id',
                                            'user_entries.user_id',
                                            'user_entries.job_name',
                                            'schedulers.jb_date',
                                            'schedulers.total_hours   as scheduled_total_hours',
                                            'user_entries.total_hours   as worked_hours',
                                            ])
                                ->join('schedulers','schedulers.id','=','user_entries.schedule_id')
                                ->whereRaw("WEEK(date) = '$week'")
                                ->whereRaw("Year(date) = '$year'");
        if($request->job != "")            
            $items    =  $items->where('schedulers.job_id',$request->job);
        if($request->user != "")            
            $items    =  $items->where('schedulers.user_id',$request->user);
        
        $items    =  $items->get();
        $responseHeaders = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=missed_hours.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $callback = function () use ( $items , $week , $year) {
            // Header row
            $header = ['First Name' , 'Last Name' , 'Date' , 'Worked Hours','Difference in Hours' ];
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);        

            foreach ($items as $item) {
                $rows = [];
                $rows[] = $item->user ? $item->user->name : ''; //user
                $rows[] = $item->user ? $item->user->last_name : ''; //phone number
                $rows[] = $item->jb_date; //phone number
                $rows[] = $item->worked_hours; //phone number
                $rows[] = $this->getTimeDifference($item->scheduled_total_hours,$item->worked_hours); //phone number
             
                fputcsv($file, $rows);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $responseHeaders);
    }
    public function getTimeDifference( $t1 , $t2 ){
        if($t1 != null && $t2 != null){
            $t1 = explode( ':' , $t1);
            $h1 = isset($t1[0]) ? $t1[0] : 0;
            $m1 = isset($t1[1]) ? $t1[1] : 0;
            $tm1 = ($h1*60)+$m1;

            $t2 = explode( ':' , $t2);
            $h2 = isset($t2[0]) ? $t2[0] : 0;
            $m2 = isset($t2[1]) ? $t2[1] : 0;
            $tm2 = ($h2*60)+$m2;

            $f  = $tm1 - $tm2;
            $fh = 0;
            while($f > 60){
                $fh++;
                $f = $f - 60;
            }
            $mh = $f;
            return abs($fh).':'.abs($mh);
        }else{
            return 0;
        }

    }
 
    public function weeklyPayment(Request $request){
        $d = $request->all();
       
        $d = array_keys($d);
        
        $yw = explode('-',$d[0]);
        $year = $yw[0];
        $week = str_replace('W','',$yw[1]);
        dd($week);
        $items    = UserEntry::with(['schedule','job','user'])
                        ->whereRaw("WEEK(date) = '$week'")
                        ->whereRaw("Year(date) = '$year'")->get();

        $responseHeaders = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=employees.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $callback = function () use ( $items , $week , $year) {
            // Header row
            $header = ['User' , 'Phone Number' , 'Shift Title' , 'Job' , 'Customer' , 'date' ,
                        'Shift Time', 'Tasks' , 'Total Planned' , 'Hours Worekd' , 'Rate' , 
                        'Previous Due OR Over Payment' , 'N.I Contribution' , ' Total Due' , 'Paid' , 'Final Balance' ];
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);        

            foreach ($items as $item) {
                $advPayment = Payment::where('week', $week)->where('year', $year)->where('user_id' , $item->user_id)->first();
                $weekPayment = WeeklyPayment::where('week', $week)->where('year', $year)->where('user_id' , $item->user_id)->first();
                $rows = [];
                $rows[] = $item->user ? $item->user->name : ''; //user
                $rows[] = $item->user ? $item->user->phone_number : ''; //phone number
                $rows[] = $item->schedule ? $item->schedule->title : ''; //shoft title
                $rows[] = ($item->schedule && $item->schedule->job) ? $item->schedule->job->job_name : ''; //job
                $rows[] = ($item->schedule && $item->schedule->job && $item->schedule->job->customer) ? $item->schedule->job->customer->name.' '.$item->schedule->job->customer->last_name : ''; //customer
                $rows[] = $item->date; //date
                $rows[] = $item->schedule ? $item->schedule->start_time .' ' . $item->schedule->end_time : ''; //shift time
                $rows[] = $item->schedule ? $item->schedule->total_hours : ''; // shift hours should be
                $rows[] = $item->total_hours; //shoft hours entered
                $rows[] = $item->user ? $item->user->rate : ''; 
                $rows[] = ($advPayment != null) ? $advPayment->amount : 0; 
                $rows[] = ($weekPayment != null) ? $weekPayment->ni_payment : 0; 
                $rows[] = ''; 
                $rows[] = ($weekPayment != null) ? $weekPayment->amount_paid : 0; 
                $rows[] = ($weekPayment != null) ? $weekPayment->week_outstanding : 0; 
                fputcsv($file, $rows);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $responseHeaders);
    }

    public function dailyReport(Request $request){
        $fields = $request->all();
        if($request->start_date != '' && $request->end_date != ''){

            $items = Scheduler::with(['job','user'])
                                ->join('jobs', 'jobs.id', '=', 'schedulers.job_id')
                                ->join('users', 'jobs.customer_id', '=', 'users.id')
                                ->where('jb_date','>=', $request->start_date )
                                ->where('jb_date','<=', $request->end_date );

            if(isset($request->shift) && $request->shift != '')                                
                $items =   $items->where('title',$request->shift);
                // $items =   $items->where('shift',$request->shift);
            if(isset($request->job) && $request->job != '')                                
                $items =   $items->where('job_id',$request->job);

            if(isset($request->customer) && $request->customer != '')                                
                $items =   $items->where('jobs.customer_id',$request->customer);

            $items =  $items->where('schedulers.type','schedule')->get();
        }
        $responseHeaders = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=employees.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $callback = function () use ( $items) {
            // Header row
            $header = ['First Name' , 'Last Name' , 'Job' , 'Shift' , 'Customer' , 'Date'];
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);        
            foreach ($items as $item) {
                $rows = [];
                $rows[] = $item->user ? $item->user->name : '';
                $rows[] = $item->user ? $item->user->last_name : '';
                $rows[] = $item->job ? $item->job->job_name : '';
                $rows[] = $item->title;
                $rows[] = ($item->job && $item->job->customer ) ? $item->job->customer->name.' '.$item->job->customer->last_name : '';
                $rows[] = $item->jb_date;
                fputcsv($file, $rows);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $responseHeaders);

    }

    public function userCsv(Request $request)
    {
        $fields = $request->all();
        $items = User::select($fields)->where('type','worker')->get();

        $responseHeaders = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=employees.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        
        $callback = function () use ( $items , $fields) {
            // Header row
            $header = [];
            foreach($fields as $f){
                $header[] = ucwords(str_replace('_',' ',$f));
            }
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);        
            foreach ($items as $item) {
                $rows = [];
                foreach($fields as $f){
                    $rows[] = $item->$f;
                }
                fputcsv($file, $rows);
            }

            fclose($file);
        };
        
        return Response::stream($callback, 200, $responseHeaders);

    }

}
