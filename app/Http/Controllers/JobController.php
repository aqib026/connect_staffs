<?php

namespace App\Http\Controllers;

use App\User;
use App\Job;
use App\Scheduler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class JobController extends Controller
{
    //

    public function jobs_list(Request $request){
        $jobs = Job::where('status','Published')->pluck('job_name', 'id');;
        return response()->json($jobs);
    }

    public function shifts_list(Request $request){
        $schedules = Scheduler::where('title','!=','')->where('type','schedule')->groupBy('job_id')->pluck('title', 'title');;
        return response()->json($schedules);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $jobs = new Job();
        if($status != ''){
            $jobs = $jobs->where('status',$status);
        }
        if($search != ''){
            $jobs = $jobs->where(function ($q) use ($search) {
                $q->where('job_name', 'like', "%$search%");
                $q->orWhere('status', "$search");
            });
        }
        $jobs = $jobs->paginate(25);

        return response()->json($jobs);
    }

    public function unpublish_multijobs(Request $request){
        $data = $request->all();
        foreach($data as $j){
            $job = Job::find($j);
            $job->status = 'Unpublished';
            $job->save();    
        }
        return response()->json([
            "success" => true,
            "message" => "Jobs Unpublished Successfully.",
            "data" => []
        ], 201);

    }

    public function publish_multijobs(Request $request){
        $data = $request->all();
        foreach($data as $j){
            $job = Job::find($j);
            $job->status = 'Published';
            $job->save();    
        }
        return response()->json([
            "success" => true,
            "message" => "Jobs Published successfully.",
            "data" => []
        ], 201);

    }


    public function store(Request $request)
    {
        $request->validate([
            'job_name' => 'required',
            'start_date' => 'required',
        ]);
        $data = [
            'job_name' => $request->job_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'customer_id' => $request->customer_id,
            'status' => 'Unpublished'
        ];
        $job = Job::create($data);
        return response()->json([
            "success" => true,
            "message" => "Job created successfully.",
            "data" => $job
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = [
            'job_name' => $request->job_name,
            'customer_id' => $request->customer_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ];
        $user = Job::where('id',$id)->update($data);

        return response()->json([
            "success" => true,
            "message" => "Job updated successfully.",
            "data" => []
        ]);


    }

    public function show($id)
    {
        $job = Job::find($id);
        return response()->json([
            "success" => true,
            "message" => "Job Data",
            "data" => $job
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $customer = User::find($id);
        $customer->delete();

        return response()->json([
            "success" => true,
            "message" => "User deleted successfully."
        ]);
    }
}
