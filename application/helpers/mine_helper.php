<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('dump')) {
    function dump($data, $continue = false)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if (!$continue) {
            die();
        }
        return true;
    }
}

if (!function_exists('dd')) {
    function dd($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die();
    }
}

if (!function_exists('verify_language')) {
    function verify_language($lang): string
    {
        if(empty($lang)) return 'ru';
        $lang = strtolower($lang);
        if(!in_array($lang, ['ru', 'ro'])) return 'ru';

        return $lang;
    }
}

if (!function_exists('transliteration')) {
    function transliteration($str)
    {
        $tr = array(
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
            "Д" => "D", "Е" => "E", "Ё" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
            "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N", 'â' => 'a', 'Â'=> 'A', 'ă' => 'a', 'Ă' => 'A', 'ţ' => 't', 'Ţ' => 'T', 'ş' => 's', 'Ş' => 'S',
            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
            "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH", "î" => "i", "Î" => "I", "," => "", "№" => "", "ț" => "t", "Ț" => "T", "ș" => "s", "Ș" => "S",
            "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "e", "ж" => "j",
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
            "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya", " " => "_", " (" => "", ")" => "",
            "." => "", "\\" => "", "/" => "-", "'" => "", "»" => "", "«" => "", "&quot;" => "", "\"" => "", "&" => "i", "%" => "",
            "$" => "usd", "€" => "eur", "!" => "", "?" => "", "+" => "plus",
        );

        $str = strtolower(strtr($str, $tr));
        $str = preg_replace('/\s+/u', '-', $str);
        $str = preg_replace('/[^a-zA-Z0-9_-]+/', '', $str);

        return $str;
    }
}
