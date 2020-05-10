<?php


namespace Ingelby\Twelvedata\Models;


use yii\base\Model;

class Quote extends AbstractTwelveDataModel
{

    /**
     * @var
     */
    public $symbol;
    /**
     * @var
     */
    public $name;
    /**
     * @var
     */
    public $datetime;
    /**
     * @var
     */
    public $open;
    /**
     * @var
     */
    public $high;
    /**
     * @var
     */
    public $low;
    /**
     * @var
     */
    public $close;
    /**
     * @var
     */
    public $volume;
    /**
     * @var
     */
    public $previousClose;
    /**
     * @var
     */
    public $change;
    /**
     * @var
     */
    public $percentChange;
    /**
     * @var
     */
    public $averageVolume;

    /**
     * @var Price|null 
     */
    protected ?Price $price = null;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'symbol',
                    'name',
                    'datetime',
                    'open',
                    'high',
                    'low',
                    'close',
                    'volume',
                    'previousClose',
                    'change',
                    'percentChange',
                    'averageVolume',
                ],
                'safe',
            ],
        ];
    }

    /**
     * @return bool
     */
    public function isNoChange(): bool
    {
        return 0 === (int)$this->change;
    }

    /**
     * @return bool
     */
    public function isPositive(): bool
    {
        if (strpos($this->change, '-') === false) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isNegative(): bool
    {
        return !$this->isPositive();
    }

    /**
     * @return Price|null
     */
    public function getPrice(): ?Price
    {
        return $this->price;
    }

    /**
     * @param Price $price
     */
    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }
}
