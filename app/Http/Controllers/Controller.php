<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function translations($locale)
    {
        App::setLocale($locale);
        $path = resource_path(sprintf('lang/%s', $locale));
        $translationFiles = array_filter(scandir($path), function ($file) {
            return $file !== '.' && $file !== '..';
        });
        $data = [];

        foreach ($translationFiles as $translationFile) {
            $file = explode('.', $translationFile)[0];
            $data[$file] = __($file);
        }

        return response()->json($data);
    }
}
