<?php


namespace Ingelby\Twelvedata\Models;


use yii\base\Model;

class Price extends AbstractTwelveDataModel
{

    /**
     * @var
     */
    public $price;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'price',
                ],
                'safe',
            ],
        ];
    }
}
