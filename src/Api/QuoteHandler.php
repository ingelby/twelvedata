<?php

namespace Ingelby\Twelvedata\Api;

use Ingelby\Twelvedata\Exceptions\TwelvedataRateLimitException;
use Ingelby\Twelvedata\Exceptions\TwelvedataResponseException;
use Ingelby\Twelvedata\Models\Quote;
use ingelby\toolbox\constants\HttpStatus;
use ingelby\toolbox\services\InguzzleHandler;

class QuoteHandler extends AbstractHandler
{
    protected const QUOTE = 'quote';

    /**
     * @param string $symbol
     * @return Quote
     * @throws TwelvedataResponseException
     * @throws TwelvedataRateLimitException
     */
    public function getQuote(string $symbol): Quote
    {
        $response = $this->query(
            static::QUOTE,
            [
                'symbol' => $symbol,
            ]
        );
        return $this->map($response);
    }

    /**
     * Please note caching is done on a less granular level
     * @param string[] $symbols
     * @return Quote[]
     * @throws TwelvedataRateLimitException
     * @throws TwelvedataResponseException
     */
    public function getQuotes(array $symbols): array
    {
        if (1 === count($symbols)) {
            return [$this->getQuote(current($symbols))];
        }

        $bulkResponse = $this->query(
            static::QUOTE,
            [
                'symbol' => implode(',', $symbols),
            ]
        );

        return $this->mapBulk($bulkResponse, $symbols);
    }

    /**
     * @param array $response
     * @return Quote
     */
    protected function map(array $response): Quote
    {
        return new Quote(
            [
                'symbol'        => $response['symbol'] ?? null,
                'name'          => $response['name'] ?? null,
                'datetime'      => $response['datetime'] ?? null,
                'open'          => $response['open'] ?? null,
                'high'          => $response['high'] ?? null,
                'low'           => $response['low'] ?? null,
                'close'         => $response['close'] ?? null,
                'volume'        => $response['volume'] ?? null,
                'previousClose' => $response['previous_close'] ?? null,
                'change'        => $response['change'] ?? null,
                'percentChange' => $response['percent_change'] ?? null,
                'averageVolume' => $response['average_volume'] ?? null,
            ]
        );
    }
}
