<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scheduler;

class JobSchedulerController extends Controller
{
    //

    public function index(Request $request){
        
        $days_arr = $request->all();
        $schedule = Scheduler::whereIn('jb_date', $days_arr)->where('type','schedule')->get();
        $data = [];
        foreach($schedule as $s){
            $data[$s->user_id][$s->jb_date] = [
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'total_hours' => $s->total_hours,
                'title' => $s->title,
                'job_id' => $s->job_id,
            ];
        }
        return response()->json($data);

    } 

    public function templates(Request $request)
    {        
        $templates = Scheduler::where('type','template')->get();
        return response()->json($templates);

    } 


    public function save(Request $request){
        $update_date = date('Y-m-d' , strtotime($request->selectedDate));
        $schedule = Scheduler::where('jb_date',$update_date)
                            ->where('type',$request->selectedType)
                            ->where('user_id',$request->selectedUserId)->first();
        if($schedule == null){
            $schedule = new Scheduler;
        }           
        if($request->selectedType != 'template'){
            $schedule->jb_date     = date('Y-m-d' , strtotime($request->selectedDate));                
            $schedule->user_id     = $request->selectedUserId;                    
        }
        $schedule->job_id      = $request->selectedJob;
        $schedule->start_time  = $request->selectedStartTime;                
        $schedule->end_time    = $request->selectedEndTime;                
        $schedule->total_hours = $request->selectedDuration;
        $schedule->title       = $request->selectedShiftTitle;    
        $schedule->type       = $request->selectedType;        
        $schedule->save();        

    }

}
