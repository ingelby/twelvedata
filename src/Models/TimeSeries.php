<?php


namespace Ingelby\Twelvedata\Models;


use Carbon\Carbon;
use yii\base\Model;

class TimeSeries extends AbstractTwelveDataModel
{
    /**
     * @var Carbon
     */
    public $dateTime;

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
                    'dateTime',
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
    public function getDateInFormat(string $format): string
    {
        return $this->dateTime->format($format);
    }
}
