<?php

namespace App\Service;

class Slugify
{
    public function generate(string $input): string
    {
        $slug = trim(strtolower($input));
        $slug = preg_replace('/\s+/', ' ', $slug);
        $slug = preg_replace('/[\&\~\"\#\'\{\(\[\|\`\_\^\\\@\)\]\°\=\}\+\$\£\%\!\§\:\/\;\.\,\?\<\>\)]+/', '', $slug);
        $slug = str_replace(' ', '-', $slug);
        $slug = preg_replace('/\-+/', '-', $slug);
        $slug = strtr(utf8_decode($slug),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');

        return $slug;
    }
}
