<?php

namespace App\Services;

use App\Models\Base;

class BaseService
{
    public function getContent(Base $base): string
    {
        $file = storage_path('app/public/' . $base->filename);
        return file_get_contents($file);
    }
}
