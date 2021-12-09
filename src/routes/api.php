<?php


use App\Http\Controllers\Api\{
    AuthenticateController,
    PaymentOrderController,
    RegisterController,
    UserController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [RegisterController::class, 'register'])->name('api.users.store');
Route::post('users/login', [AuthenticateController::class, 'login'])->name('api.users.login');

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::apiResource('users', UserController::class)->only(['show', 'update', 'destroy']);

    Route::get('payments', [PaymentOrderController::class, 'index'])->name('payment_order.index');
    Route::post('payments', [PaymentOrderController::class, 'store'])->name('payment_order.store');
    Route::get('payments/{id}', [PaymentOrderController::class, 'show'])->name('payment_order.show');
});
