<?php

namespace App\Http\Controllers;

use Auth;
use App\Job;
use App\User;
use App\Scheduler;
use App\UserEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function update_user_enteries(Request $request)
    {
        //dd($request->all());
        $editable = 0;
        if ($request->type == 'submit') {
            $editable = 1;
        }
        foreach ($request->all() as $key => $value) {
            if (is_array($value)) {
                $ue = UserEntry::where('date', $key)->where('user_id', $request->user_id)->first();
                if ($ue == null) {
                    $ue = new UserEntry;
                }
                $job    = Job::with('customer')->where('id', $value['job_id'])->first();
                $worker = User::select(['name', 'last_name'])->where('id', $request->user_id)->first();
                $ue->date           = $key;
                $ue->user_id        = $request->user_id;
                $ue->customer_name  = isset($job->customer) ? $job->customer->business_name : '';
                $ue->job_name       = $job->job_name;
                $ue->worker_name    = $worker->name . ' ' . $worker->last_name;
                $ue->job_id         = $value['job_id'];
                $ue->schedule_id    = $value['schedule_id'];
                $ue->start_date     = $value['user_start_time'];
                $ue->end_date       = $value['user_end_time'];
                $ue->total_hours    = $value['user_total_hours'];
                $ue->editable       = $editable;
                $ue->save();
            }
        }

        return response()->json([
            "success" => true,
            "message" => "Time Sheet Updated successfully.",
            "data" => []
        ], 201);
    }
    public function update_user_enteries_new(Request $request)
    {
        $customer = User::where('id', $request->selectedCustomer)->first();
        $userEntries = new UserEntry();
        $userEntries->customer_name = $customer->name . ' ' . $customer->last_name;
        $userEntries->worker_name = $request->selectedUsername;
        $userEntries->customer_id = $request->selectedCustomer;
        $userEntries->start_date = $request->selectedStartTime;
        $userEntries->end_date = $request->selectedEndTime;
        $userEntries->user_id = $request->selectedUserId;
        $userEntries->total_hours = $request->selectedDuration;
        $userEntries->date = date('Y-m-d', strtotime($request->selectedDate));
        $userEntries->save();


        return response()->json([
            "success" => true,
            "message" => "Time Sheet Updated successfully.",
            "data" => []
        ], 201);
    }

    public function user_schedule(Request $request)
    {

        $user = User::find($request->user_id);

        $jobs = Scheduler::with('job')
            ->where('jb_date', '<=', $request->end_date)
            ->where('jb_date', '>=', $request->start_date)
            ->where('user_id', $user->id)
            ->groupBy('job_id')
            ->get();

        $scheduler = Scheduler::where('jb_date', '<=', $request->end_date)
            ->where('jb_date', '>=', $request->start_date)
            ->where('user_id', $user->id)->get();
        $data = [];
        foreach ($scheduler as $s) {
            $user_start_time = '';
            $user_end_time = '';
            $user_total_hours = '';
            $user_editable = '';
            $ue = UserEntry::where('date', $s->jb_date)->where('user_id', $request->user_id)->first();
            if ($ue != null) {
                $user_start_time = $ue->start_date;
                $user_end_time = $ue->end_date;
                $user_total_hours = $ue->total_hours;
                $user_editable = $ue->editable;
            }
            $data[$s->jb_date] = [
                'schedule_id' => $s->id,
                'job_id' => $s->job_id,
                'title' => $s->title,
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'user_start_time' => $user_start_time,
                'user_end_time' => $user_end_time,
                'user_total_hours' => $user_total_hours,
                'editable' => $user_editable
            ];
        }
        $response = ['jobs' => $jobs, 'scheduler' => $data];
        return response()->json($response);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $users = User::where('type', $request->get('type'));
        if ($search != '') {
            $users = $users->where(function ($q) use ($search) {
                $q->where('email', 'like', "%$search%");
                $q->orWhere('name', 'like', "%$search%");
                $q->orWhere('phone_number', 'like', "%$search%");
                $q->orWhere('last_name', 'like', "%$search%");
            });
        }
        $users = $users->paginate(25);

        return response()->json($users);
    }

    public function delete_multiusers(Request $request)
    {
        $data = $request->all();
        foreach ($data as $u) {
            $user = User::find($u);
            $user->delete();
        }
        return response()->json([
            "success" => true,
            "message" => "Users Deleted successfully.",
            "data" => []
        ], 201);
    }

    public function save_multiusers(Request $request)
    {

        $data = $request->all();
        foreach ($data as $u) {
            if (is_array($u)) {
                $user = new User;
                $user->name = $u['n_first_name'];
                $user->last_name = $u['n_last_name'];
                $user->phone_number = $u['n_phone_number'];
                $user->type = $data['type'];
                $user->save();
            }
        }
        return response()->json([
            "success" => true,
            "message" => "Users Added successfully.",
            "data" => []
        ], 201);
    }

    public function customers_list(Request $request)
    {
        $customers = User::where('type', 'customers')->pluck('business_name', 'id');;
        return response()->json($customers);
    }

    public function workers_list(Request $request)
    {
        $workers = User::where('type', 'worker')->pluck('name', 'id');;
        return response()->json($workers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'email' => ['required', 'string', 'email'],
        ]);
        $address  = isset($request->address) ? $request->address : '';
        $password = '';
        if (isset($request->password)) {
            $password = $request->password;
        } elseif (isset($request->user_password)) {
            $password = $request->user_password;
        }
        $last_name = isset($request->last_name) ? $request->last_name : '';
        $business_name = isset($request->business_name) ? $request->business_name : '';

        $data = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'last_name' => $last_name,
            'address'  => $address,
            'business_name' => $business_name,
            'password' => Hash::make($password),
            'ni_number' => isset($request->ni_number) ? $request->ni_number : null,
            'sort_code' => isset($request->sort_code) ? $request->sort_code : null,
            'account_number' => isset($request->account_number) ? $request->account_number : null,
            'rate' => isset($request->rate) ? $request->rate : null,
            'post_address' => isset($request->post_address) ? $request->post_address : null,
            'type' => $request->type
        ];
        $customer = User::create($data);
        return response()->json([
            "success" => true,
            "message" => "User created successfully.",
            "data" => $customer
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $address  = isset($request->address) ? $request->address : '';
        $business_name  = isset($request->business_name) ? $request->business_name : '';

        $data = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $address,
            'business_name' => $business_name,
            'email' => $request->email,
            'last_name' => $request->last_name,
            'ni_number' => isset($request->ni_number) ? $request->ni_number : null,
            'sort_code' => isset($request->sort_code) ? $request->sort_code : null,
            'account_number' => isset($request->account_number) ? $request->account_number : null,
            'rate' => isset($request->rate) ? $request->rate : null,
            'post_address' => isset($request->post_address) ? $request->post_address : null
        ];

        $user = User::where('id', $id)->update($data);

        return response()->json([
            "success" => true,
            "message" => "User updated successfully.",
            "data" => []
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json([
            "success" => true,
            "message" => "User Data",
            "data" => $user
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
    public function timeSheet(Request $request)
    {
        if ($request->customerId) {
            $days_arr = $request->date_range;
            $timesheet = UserEntry::whereIn('date', $days_arr)->where('customer_id', $request->customerId)->get();
        } else {
            $days_arr = $request->all();
            $timesheet = UserEntry::whereIn('date', $days_arr)->get();
        }

        $data = [];
        foreach ($timesheet as $s) {
            $data[$s->user_id][$s->date] = [
                'start_time' => $s->start_date,
                'end_time' => $s->end_date,
                'total_hours' => $s->total_hours,
            ];
        }
        return response()->json($data);
    }
    public function searchWorkers(Request $request)
    {
        $name = $request->keyword;
        $data = User::where(function ($query) use ($name) {
            $query->where('name', 'like', '%' . $name . '%')
                ->orWhere('last_name', 'like', '%' . $name . '%');
        })->Where('type', '=', 'worker')->get();
        // $data = User::where('name', 'LIKE', '%' . $request->keyword . '%')->get();
        return response()->json($data);
    }
    public function importFromLastWeek(Request $request)
    {
        $previous_week = $request->date_range;
        $timesheet = UserEntry::whereIn('date', $previous_week)->where('customer_id', $request->customerId)->get();
        foreach ($timesheet as  $value) {
            $userEntries = new UserEntry();
            $date = Carbon::parse($value->date);
            $date->addDays(7);
            $userEntries->date = $date->format("Y-m-d");
            $userEntries->customer_name = $value->customer_name;
            $userEntries->worker_name = $value->worker_name;
            $userEntries->customer_id = $value->customer_id;
            $userEntries->start_date = $value->start_date;
            $userEntries->end_date = $value->end_date;
            $userEntries->user_id = $value->user_id;
            $userEntries->total_hours = $value->total_hours;
            $userEntries->save();
        }
        if($userEntries){
            return response()->json([
                "success" => true,
                "message" => "Imported Successfully",
                "data" => []
            ]);
        }
        // $currentWeek = [];
        // foreach ($previous_week as $key => $day) {
        //     $date = Carbon::parse($day);
        //     $date->addDays(7);
        //     $currentWeek[$key]  = $date->format("Y-m-d");
        // }
        // $userEntries = new UserEntry();

        // dd($timesheet);
        // foreach ($currentWeek as $key => $day) {
        //     foreach ($timesheet as  $value) {

        //         $userEntries->date = date('Y-m-d', strtotime($day));
        //         $userEntries->customer_name = $value->customer_name;
        //         $userEntries->worker_name = $value->worker_name;
        //         $userEntries->customer_id = $value->customer_id;
        //         $userEntries->start_date = $value->start_date;
        //         $userEntries->end_date = $value->end_date;
        //         $userEntries->user_id = $value->user_id;
        //         $userEntries->total_hours = $value->total_hours;

        //         $userEntries->save();
        //     }
        // }

    }
    public function weeklyTimeSchdule(Request $request){
        $days_arr = $request->date_range;
        $timesheet = UserEntry::whereIn('date', $days_arr)->where('customer_id', $request->customerId)->get();
        $responseHeaders = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=employees.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $callback = function () use ( $timesheet,$days_arr) {
            // Header row
            $header = ['Worker Name' ,'Date','Start Time','End Time','Duration','Customer Name'];
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);        

            foreach ($timesheet as $item) {
                $rows       = [];
                $rows[]     =   $item->worker_name; 
                $rows[]     =   $item->date; 
                $rows[]     =   $item->start_date;
                $rows[]     =   $item->end_date;
                $rows[]     =   $item->total_hours;
                $rows[]     =   $item->customer_name;
              
                fputcsv($file, $rows);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $responseHeaders);
    }
    public function copyRecord(Request $request){
        $timesheet = UserEntry::where('date', $request->FromDate)->where('customer_id', $request->customer_id)->get();
       if($timesheet){
           foreach ($timesheet as $key => $value) {
            $userEntries = new UserEntry();
                    $userEntries->customer_name = $value->customer_name;
                    $userEntries->worker_name = $value->worker_name;
                    $userEntries->customer_id = $value->customer_id;
                    $userEntries->start_date = $value->start_date;
                    $userEntries->end_date = $value->end_date;
                    $userEntries->user_id = $value->user_id;
                    $userEntries->total_hours = $value->total_hours;
                    $userEntries->date = $request->ToDate;
                    $userEntries->save();
           }
           if($userEntries){
            return response()->json([
                "success" => true,
                "message" => "Copied Successfully",
                "data" => []
            ]);
        }
       }
    }
}
