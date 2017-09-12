<?php

use Illuminate\Http\Request;

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

// get only hot tickets
Route::get('ticketsHot', 'HotTicketController@index');
Route::get('ticketsAging', 'AgingTicketController@index');
Route::get('ticketsStale', 'StaleTicketController@index');
Route::get('ticketsProblem', 'ProblemTicketController@index');
Route::get('ticketsMine', 'MyTicketController@index');
Route::get('ticketsClosed', 'ClosedTicketController@index');

Route::get('tickets', 'TicketController@index');
Route::get('tickets/{groupOrIndivual}/{id}', 'TicketController@index');

Route::get('assignees', 'RepController@index');

// Route::get('agent-build', 'ClientController@agentBuild');
// Route::get('definitions', 'DefinitionsController@index');
// Route::get('definitions-status', 'DefinitionsController@status');

// Route::get('exemptions', 'ExemptionController@index');
// Route::get('exemptions/{exemption}', 'ExemptionController@show');

// Route::get('matches', 'MatchedFileController@index');
// Route::get('matches/{match}', 'MatchedFileController@show');

// Route::get('patterns', 'PatternController@index');
// Route::post('patterns', 'PatternController@store');
// Route::get('patterns/{pattern}', 'PatternController@show');

// Route::get('logs', 'LogEntryController@index');
// Route::get('logs/{log}', 'LogEntryController@show');

// Route::get('clients/{client}/logs', 'ClientLogEntryController@index');
// Route::post('clients/{client}/logs', 'ClientLogEntryController@store');
// Route::post('clients/{client}/count', 'ClientController@count');
// Route::post('clients/{client}/count_current', 'ClientController@countCurrent');
// Route::post('clients/{client}/matches', 'ClientMatchedFilesController@store');

// Route::post('clients/{client}/admin-password-reset-complete', 'ClientPasswordResetController@complete');

// Route::get('clients', 'ClientController@index');
// Route::get('clients/{client}/heartbeat', 'ClientController@heartbeat');
// Route::get('clients/{client}/marco', 'ClientController@requestHeartbeat');
// Route::get('clients/{client}', 'ClientController@show');
// Route::patch('clients/{client}', 'ClientController@update');
// Route::post('clients/{name}', 'ClientController@store');

// Route::post('profile/reset', 'ProfileController@reset');

// Route::get('chat', 'ChatController@index');
// Route::post('chat', 'ChatController@store');
