<?php

namespace Ingelby\Twelvedata\Api;

use Ingelby\Twelvedata\Exceptions\TwelvedataRateLimitException;
use Ingelby\Twelvedata\Exceptions\TwelvedataResponseException;
use Ingelby\Twelvedata\Models\Price;
use ingelby\toolbox\constants\HttpStatus;
use ingelby\toolbox\services\InguzzleHandler;

class PriceHandler extends AbstractHandler
{
    protected const PRICE = 'price';

    /**
     * @param string $symbol
     * @return Price
     * @throws TwelvedataResponseException
     * @throws TwelvedataRateLimitException
     */
    public function getPrice(string $symbol): Price
    {
        $response = $this->query(
            static::PRICE,
            [
                'symbol' => $symbol,
            ]
        );

        return $this->map($response);
    }

    /**
     * Please note caching is done on a less granular level
     * @param string[] $symbols
     * @return Price[]
     * @throws TwelvedataRateLimitException
     * @throws TwelvedataResponseException
     */
    public function getPrices(array $symbols): array
    {
        if (1 === count($symbols)) {
            return [$this->getPrice(current($symbols))];
        }

        $bulkResponse = $this->query(
            static::PRICE,
            [
                'symbol' => implode(',', $symbols),
            ]
        );

        return $this->mapBulk($bulkResponse, $symbols);
    }

    /**
     * @param array $response
     * @return Price
     */
    protected function map(array $response): Price
    {
        return new Price(
            [
                'price'        => $response['price'] ?? null,
            ]
        );
    }
}
