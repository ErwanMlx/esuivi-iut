<?php

namespace App\Twig;

class AppRuntime
{
    public function __construct()
    {
        // this simple example doesn't define any dependency, but in your own
        // extensions, you'll need to inject services using this constructor
    }

    /*
     * Permet d'ajouter un certain nombre de $char a la suite du $string (utile pour la génération du bordereau)
     */
    public function addChar($string, $nbChar = 0, $char = '')
    {
        $string_size = strlen($string);
//        if($string_size == 0) {
            if ($string_size < $nbChar) {
                for ($i = 1; $i <= $nbChar - $string_size; $i++) {
                    $string = $string . $char;
                }
            }
//        }

        return $string;
    }
}
