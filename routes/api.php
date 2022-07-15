<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/get/user_schedule', 'UserController@user_schedule');

Route::post('/save/update_user_enteries_new', 'UserController@update_user_enteries_new');
Route::post('/download/shiftcomparison', 'ShiftComparisonController@downloadCsv');


Route::get('/get/user_schedule', 'UserController@user_schedule');
Route::get('/users', 'UserController@index');

Route::get('/get/customers', 'UserController@customers_list');
Route::get('/get/workers', 'UserController@workers_list');
Route::get('/search-workers', 'UserController@searchWorkers');

Route::post('/get/get_user_schedule', 'ShiftComparisonController@getUserSchedule');
Route::post('/import-fromlastweek', 'UserController@importFromLastWeek');


Route::get('/get/jobs', 'JobController@jobs_list');
Route::get('/get/shifts', 'JobController@shifts_list');

Route::post('/save/user', 'UserController@store');
Route::post('/update/user/{id}', 'UserController@update');
Route::post('/delete/multiusers', 'UserController@delete_multiusers');
Route::post('/save/multiusers', 'UserController@save_multiusers');

Route::post('/publish/multijobs', 'JobController@publish_multijobs');
Route::post('/unpublish/multijobs', 'JobController@unpublish_multijobs');
Route::get('/job/{id}', 'JobController@show');
Route::post('/copy_date_record', 'UserController@copyRecord');



Route::post('/get/scheduler', 'JobSchedulerController@index');
Route::post('/get/timesheet', 'UserController@timeSheet');
Route::post('/get/templates', 'JobSchedulerController@templates');
Route::post('/save/scheduler', 'JobSchedulerController@save');
Route::post('/get/weeklyTimeSchdule', 'UserController@weeklyTimeSchdule');

// Route::post('/get/jobs', 'JobController@list');

Route::get('/jobs', 'JobController@index');
Route::post('/save/job', 'JobController@store');
Route::post('/update/job/{id}', 'JobController@update');
Route::post('/get/job/{id}', 'JobController@update');


Route::delete('/delete/user/{id}', 'UserController@destroy');

Route::get('/user/{id}', 'UserController@show');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/save/weekly_payment', 'WeeklyPaymentController@save');

Route::post('/get/user_csv', 'ReportController@userCsv');
Route::post('/get/daily_report', 'ReportController@dailyReport');
Route::post('/get/weekly_payment', 'ReportController@weeklyPayment');
Route::post('/get/weekly_timesheet', 'ReportController@weeklyTimesheet');
Route::post('/get/missed_days', 'ReportController@missedDays');
Route::post('/get/missed_hours', 'ReportController@missedHours');


Route::post('/products', 'ProductController@store');
Route::get('/products', 'ProductController@index');
Route::get('/prices', 'ProductController@prices');

Route::get('/customer', 'ProductController@index');
Route::get('/allusers', 'UserController@index');

Route::post('/customer', 'CustomerController@store');
Route::get('/customer/{id}', 'CustomerController@show');
Route::put('/customer/{id}', 'CustomerController@update');
Route::delete('/customer/{id}', 'CustomerController@destroy');

Route::get('/menus', 'MenusController@index');

Route::get('/get/payments', 'PaymentController@getPayments');
Route::get('/payments/{id}', 'PaymentController@index');
Route::post('/payments/{id}', 'PaymentController@create');

Route::post('/register', [App\Http\Controllers\AuthenticationController::class, 'store']);
Route::get('/user', [App\Http\Controllers\AuthenticationController::class, 'user']);


Route::post('/login', [App\Http\Controllers\AuthenticationController::class, 'login']);

Route::post('/shiftcomparison', 'ShiftComparisonController@update');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [App\Http\Controllers\AuthenticationController::class, 'logout']);
});

Route::group([
    'namespace' => 'Auth',
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('create', [App\Http\Controllers\ForgotPasswordController::class, 'create']);
    Route::get('find/{token}', [App\Http\Controllers\ForgotPasswordController::class, 'find']);
    Route::post('reset', [App\Http\Controllers\ForgotPasswordController::class, 'reset']);
});

Route::get('/redirect', function () {
    $query = http_build_query([
        'client_id' => 'client-id',
        'redirect_uri' => 'http://example.com/callback',
        'response_type' => 'code',
        'scope' => 'place-orders check-status',
    ]);
    return redirect('http://your-app.com/oauth/authorize?' . $query);
});
