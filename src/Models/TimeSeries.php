<?php


namespace Ingelby\Twelvedata\Models;


use Carbon\Carbon;
use yii\base\Model;

abstract class TimeSeries extends Model
{
    /**
     * @var Carbon
     */
    public $date;

    /**
     * @var string
     */
    public $open;

    /**
     * @var string
     */
    public $high;

    /**
     * @var string
     */
    public $low;

    /**
     * @var string
     */
    public $close;

    /**
     * @var string
     */
    public $volume;

    /**
     * @var string
     */
    public $timezome;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'open',
                    'high',
                    'low',
                    'close',
                    'volume',
                    'timezome',
                ],
                'safe',
            ],
        ];
    }

    /**
     * @param string $format
     * @return string
     */
    abstract public function getDateInFormat(string $format): string;
}
