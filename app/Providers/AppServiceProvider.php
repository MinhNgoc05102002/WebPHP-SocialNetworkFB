<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success',function ($data,$msg,$status_code){
            return response()->json([
                'success' => true,
                'msg' => $msg,
                'status' => $status_code,
                'returnObj' => $data
            ]);
        });

        Response::macro('error',function ($msg,$status_code){
            return response()->json([
                'success' => false,
                'status' => $status_code,
                'msg' => $msg
            ]);
        });
    }
}
