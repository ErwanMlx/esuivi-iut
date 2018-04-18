<?php

namespace App\Twig;

use App\Twig\AppRuntime;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            // the logic of this filter is now implemented in a different class
            new \Twig_SimpleFilter('addChar', array(AppRuntime::class, 'addChar')),
        );
    }
}