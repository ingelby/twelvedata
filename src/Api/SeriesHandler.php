<?php

namespace Ingelby\Twelvedata\Api;

use Carbon\Carbon;
use Ingelby\Twelvedata\Exceptions\TwelvedataRateLimitException;
use Ingelby\Twelvedata\Exceptions\TwelvedataResponseException;
use Ingelby\Twelvedata\Models\Quote;
use Ingelby\Twelvedata\Models\TimeSeries;
use Ingelby\Twelvedata\Models\TimeSeriesDay;
use Ingelby\Twelvedata\Models\TimeSeriesIntraDay;
use ingelby\toolbox\constants\HttpStatus;
use ingelby\toolbox\services\InguzzleHandler;

class SeriesHandler extends AbstractHandler
{
    protected const TIME_SERIES_INTRADAY = 'TIME_SERIES_INTRADAY';
    protected const TIME_SERIES = 'time_series';

    /**
     * @param string $symbol
     * @param string $interval
     * @return TimeSeries[]
     * @throws TwelvedataResponseException
     * @throws TwelvedataRateLimitException
     */
    public function getIntraDaySeriesFromStartOfDay(string $symbol, string $interval = '5min', int $outputsize = 288)
    {
        $response = $this->query(
            static::TIME_SERIES,
            [
                'symbol'     => $symbol,
                'interval'   => $interval,
                'outputsize' => $outputsize,
            ]
        );

        if (!array_key_exists('values', $response) || !is_array($response['values'])) {
            throw new TwelvedataResponseException(HttpStatus::BAD_REQUEST, 'Invalid response');
        }

        $responseValues = $response['values'];
        $timezone = $response['meta']['exchange_timezone'];
        $firstValue = current($responseValues);
        $startOfLastDay = Carbon::parse($firstValue['datetime'])->startOfDay();

        $timeseries = [];

        foreach ($responseValues as $value) {
            $seriesDateTime = Carbon::parse($value['datetime']);

            if ($seriesDateTime->lessThan($startOfLastDay)) {
                break;
            }

            $mappedResponse = $this->map($value);
            $mappedResponse->timezome = $timezone;
            
            $timeseries[$value['datetime']] = $mappedResponse;

        }

        return $timeseries;
    }

    /**
     * @param string $symbol
     * @param int    $maxPoints
     * @return TimeSeries[]
     * @throws TwelvedataResponseException
     * @throws TwelvedataRateLimitException
     */
    public function getDaySeries(string $symbol, string $interval = '1day', int $outputsize = 365)
    {
        $response = $this->query(
            static::TIME_SERIES,
            [
                'symbol'     => $symbol,
                'interval'   => $interval,
                'outputsize' => $outputsize,
            ]
        );

        if (!array_key_exists('values', $response) || !is_array($response['values'])) {
            throw new TwelvedataResponseException(HttpStatus::BAD_REQUEST, 'Invalid response');
        }

        $responseValues = $response['values'];
        $timezone = $response['meta']['exchange_timezone'];

        $timeseries = [];
        foreach ($responseValues as $value) {
            $mappedResponse = $this->map($value);
            $mappedResponse->timezome = $timezone;
            $timeseries[$value['datetime']] = $mappedResponse;
        }
        
        return $timeseries;
    }

    /**
     * @param array $response
     * @return TimeSeries
     */
    protected function map(array $response): TimeSeries
    {
        return new TimeSeries(
            [
                'dateTime' => Carbon::parse($response['datetime']),
                'open'     => $response['open'] ?? 0,
                'high'     => $response['high'] ?? 0,
                'low'      => $response['low'] ?? 0,
                'close'    => $response['close'] ?? 0,
                'volume'   => $response['volume'] ?? 0,
            ]
        );;
    }
}
