<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vouchers\Voucher\GetTotalAmountsHandler;
use App\Http\Controllers\Vouchers\Voucher\DeleteVoucherHandler;
use App\Http\Controllers\Vouchers\Voucher\GetListFilterVoucher;

include_once 'v1/no-auth.php';

Route::group(['middleware' => ['jwt.verify']], function () {
    include_once 'v1/auth.php';

    Route::get('/vouchers/total-amounts', GetTotalAmountsHandler::class);
    Route::delete('/vouchers/{id}', DeleteVoucherHandler::class);
    Route::get('/vouchers', GetListFilterVoucher::class);
});
