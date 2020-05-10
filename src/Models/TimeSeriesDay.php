<?php


namespace Ingelby\Twelvedata\Models;


use Carbon\Carbon;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class TimeSeriesDay extends TimeSeries
{
    /**
     * @var Carbon
     */
    public $date;

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [
                    [
                        'date',
                    ],
                    'safe',
                ],
            ]
        );
    }

    /**
     * @param string $format
     * @return string
     */
    public function getDateInFormat(string $format): string
    {
        return $this->date->format($format);
    }
}
