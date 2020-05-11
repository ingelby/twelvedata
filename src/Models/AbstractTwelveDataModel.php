<?php


namespace Ingelby\Twelvedata\Models;


use yii\base\Model;

abstract class AbstractTwelveDataModel extends Model
{
    /**
     * @param float $value
     * @param int   $decimalPlaces
     * @return string
     */
    public function getInFormat($value, int $decimalPlaces = 2): string
    {
        return number_format((float)$value, $decimalPlaces);
    }
}
