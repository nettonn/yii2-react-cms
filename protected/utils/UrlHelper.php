<?php namespace app\utils;

use Yii;

class UrlHelper
{

    public static function generateAlias($str)
    {
        if(Yii::$app->params['transliterateUrl']) {
            return self::transliterate($str);
        }
        return self::filterUrl($str);
    }

    public static function filterUrl($str)
    {
        $str = preg_replace('~[^\w]+~ui', '-', $str);
        $str = preg_replace('~-$~ui', '', $str);
        $str = mb_strtolower($str, 'UTF-8');
        return $str;
    }

    public static $rustable =array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '',  'ы' => 'y',   'ъ' => '',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );


    /* Функция перевода русского текста в транслит
     *
     * @var string $str - исходная строка
     * @var string $spacechar - строка-разделитель пробелов
     *
     */
    public static function transliterate($str = null, $spacechar = '-')
    {
        if ($str)
        {
            $str = strtr($str, self::$rustable);
            $str = preg_replace('~[^-a-z0-9_]+~ui', $spacechar, $str);
            $str = preg_replace('~'.$spacechar.$spacechar.'+~ui', $spacechar, $str);
            $str = trim($str, $spacechar);
            $str = mb_strtolower($str, 'utf-8');
            return $str;
        } else {
            return;
        }
    }
}
