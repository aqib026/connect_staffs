<?php

namespace App\Http\Controllers;

use App\ShiftComparison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\UserEntry;
use Illuminate\Support\Facades\Response;

class ShiftComparisonController extends Controller
{
    public function update(Request $request){
        $data = $request->all();
        foreach($data as $i => $u){
            if(is_array($u)){
                $comp = new ShiftComparison;
                $comp->user_id = $request->user_id;
                $comp->date = $u['Date'];
                $comp->reported_start_time = $u['Start Time'];
                $comp->reported_end_time = $u['End Time'];
                $comp->reported_hours = $u['Hours Worked'];
                $comp->logged_start_time = array_key_exists('Logged Start Time', $u ) ? $u['Logged Start Time']: null;
                $comp->logged_end_time = array_key_exists('Logged End Time', $u ) ? $u['Logged End Time']: null;
                $comp->logged_hours = array_key_exists('Logged Hours', $u ) ? $u['Logged Hours']: null;
                $comp->difference = $u['Difference'];
                $comp->save();    
        }
    }
        return response()->json([
            "success" => true,
            "message" => "Comparison Added successfully.",
            "data" => []
        ], 201);
    }

    public function getUserSchedule(Request $request){
        $data = $request->all();
        $response = [];        
        foreach($data as $key => $value){
           
            $current_date   = date('Y-m-d',strtotime( str_replace('/', '-', $value['Date']) )); 
          
            $row = UserEntry::where(['date'=>$current_date,'customer_name'=>$value['Customer'],'job_name'=>$value['Jobtype']])->first();
            if($row !=null){
                UserEntry::where(['id'=>$row->id])
                ->update([  'customer_stime'  => $value['Start time'],
                            'customer_etime'  => $value['Finish time'],
                            'customer_thours' => $this->clockalize($value['Hours worked']) ]);

                $temp = array(
                    'date' => $current_date,
                    'job_name' => $value['Jobtype'],
                    'worker_name' => $value['Lastname'],
                    'customer_name' => $value['Customer'],
                    'start_time' => $row->start_date,
                    'end_date'   => $row->end_date,
                    'total_hours' => $row->total_hours,
                    'customer_stime' => $value['Start time'],
                    'customer_etime'   => $value['Finish time'],
                    'customer_thours' => $this->clockalize($value['Hours worked']),
                    'difference'  => $this->getTimeDifference($row->total_hours,$value['Hours worked']),
                );         
                $response[] = $temp;                                               
            }   
        }
        return response()->json($response);
    }

    public function downloadCsv(Request $request){
        $items = $request->all();
        $responseHeaders = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=employees.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $callback = function () use ( $items ) {
            // Header row
            $header = ['Date' , 'Worker Name' , 'Customer' , 'Job Title' , 
                        'Customer Start Time' , 'Customer End Time', 'Customer Hours Worked',
                        'Worker Start Time' , 'Worker End Time', 'Worker Hours Worked','Difference'
                     ];
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);        

            foreach ($items as $item) {
                $rows = [];
                $rows[] = $item['date'];
                $rows[] = $item['worker_name'];
                $rows[] = $item['customer_name'];
                $rows[] = $item['job_name'];

                $rows[] = $item['customer_stime'];
                $rows[] = $item['customer_etime'];
                $rows[] = $item['customer_thours'];

                $rows[] = $item['start_time'];
                $rows[] = $item['end_date'];
                $rows[] = $item['total_hours'];
                $rows[] = $item['difference'];

                fputcsv($file, $rows);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $responseHeaders);


    }

    public function clockalize($in){

        $h = intval($in);
        $m = round((((($in - $h) / 100.0) * 60.0) * 100), 0);
        if ($m == 60)
        {
            $h++;
            $m = 0;
        }
        $retval = sprintf("%02d:%02d", $h, $m);
        return $retval;
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
}
