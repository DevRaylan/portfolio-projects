<?php
namespace Geral\Model;

abstract class Filter
{
    public function __construct( )
    {
    }

    static public function getOnlyDigits( $val )
    {
        return preg_replace("/[^0-9]/", '', $val);
    }
}
