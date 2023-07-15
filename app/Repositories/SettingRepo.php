<?php

namespace App\Repositories;


use App\Models\Settings;

class SettingRepo
{
    public function update($type, $desc)
    {
        return Settings::where('type', $type)->update(['description' => $desc]);
    }

    public function getSetting($type)
    {
        return Settings::where('type', $type)->get();
    }

    public function all()
    {
        return Settings::all();
    }
}
