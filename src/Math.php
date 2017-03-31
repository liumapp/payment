<?php
/**
 * Created by PhpStorm.
 * User: liumapp.com
 * contact: liumapp.com@gmail.com
 * Date: 3/31/17
 * Time: 10:43 AM
 */

namespace liumapp\payment;

class Math {
    public $number ;

    public function __construct($number = 1)
    {
        $this->number = $number;
    }

    public function multiplication ()
    {
        return new Math($this->number * 1.5);
    }

    public function getNumber ()
    {
        return $this->number;
    }
}