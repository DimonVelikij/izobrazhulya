<?php

class Price
{
    private $price_large;
    private $price_middle;
    private $price_little;

    public function __construct($price_large, $price_middle, $price_little)
    {
        $this->price_large = $price_large;
        $this->price_middle = $price_middle;
        $this->price_little = $price_little;
    }

    public function getPriceLarge()
    {
        return $this->price_large;
    }

    public function getPriceMiddle()
    {
        return $this->price_middle;
    }

    public function getPriceLittle()
    {
        return $this->price_little;
    }
}