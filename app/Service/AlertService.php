<?php

namespace App\Service;

use Illuminate\Support\Facades\Session;

class AlertService {

    /**
     * Alert success
     */
    public static function alertSuccess(string $alertMessage): void
    {
        self::alert('success', $alertMessage);
    }

    /**
     * Alert danger
     */
    public static function alertFail(string $alertMessage): void
    {
        self::alert('danger', $alertMessage);
    }

    /**
     * Alert
     */
    public static function alert(string $alertType, string $alertMessage): void
    {
        Session::flash('alert-type', $alertType);
        Session::flash('alert-message', $alertMessage);
    }

}