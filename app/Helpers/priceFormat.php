<?php

if (!function_exists('priceFormat')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function priceFormat($price): string
    {
        return number_format($price, 2, '.', ',');
    }
}
