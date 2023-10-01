<?php

use App\ContohBootcamp\Controllers\LoginController;
use App\Http\Controller\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/register', [LoginController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::prefix('task')->group(['middleware' => 'jwt'], function() {
    
    Route::post('/show_tasks', [TaskController::class, 'showTasks'])->middleware('jwt');
    Route::post('/create_task', [TaskController::class, 'createTask']);
    Route::post('/update_task', [TaskController::class, 'updateTask']);

    // NOTE: lanjutkan tugas assignment di routing baru dibawah ini
    Route::post('/delete_task', [TaskController::class, 'deleteTask']);
    Route::post('/assign_task', [TaskController::class, 'assignTask']);
    Route::post('/unassign_task', [TaskController::class, 'unassignTask']);
    Route::post('/create_subtask', [TaskController::class, 'createSubtask']);
    Route::post('/delete_subtask', [TaskController::class, 'deleteSubtask']);
});