<?php

namespace App\Http\Controllers\iCalendar;

use App\Http\Controllers\Controller;

class ManageController extends Controller
{
    /**
     * Просмотр календаря
     */
    public function browse(int $userID): string
    {
        $dir = storage_path("app/public/calendar/user_{$userID}");
        $filename = $dir .  "/calendar.ics";
        $file = file_get_contents($filename);

        header('Content-type: text/calendar; charset=utf-8');

        return $file;
    }
}
