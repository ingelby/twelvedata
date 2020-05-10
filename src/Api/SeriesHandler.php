<?php

namespace Ingelby\Twelvedata\Api;

use Carbon\Carbon;
use Ingelby\Twelvedata\Exceptions\TwelvedataRateLimitException;
use Ingelby\Twelvedata\Exceptions\TwelvedataResponseException;
use Ingelby\Twelvedata\Models\Quote;
use Ingelby\Twelvedata\Models\TimeSeriesDay;
use Ingelby\Twelvedata\Models\TimeSeriesIntraDay;
use ingelby\toolbox\constants\HttpStatus;
use ingelby\toolbox\services\InguzzleHandler;

class SeriesHandler extends AbstractHandler
{
    protected const TIME_SERIES_INTRADAY = 'TIME_SERIES_INTRADAY';
    protected const TIME_SERIES_DAILY = 'TIME_SERIES_DAILY';

    /**
     * @param string $symbol
     * @param string $interval
     * @param int    $maxPoints
     * @return TimeSeriesIntraDay[]
     * @throws TwelvedataResponseException
     * @throws TwelvedataRateLimitException
     */
    public function getIntraDaySeries(string $symbol, string $interval = '5min', int $maxPoints = null)
    {
        $response = $this->query(
            static::TIME_SERIES_INTRADAY,
            [
                'symbol'   => $symbol,
                'interval' => $interval,
            ]
        );

        $timeseriesKey = 'Time Series (' . $interval . ')';
        if (!array_key_exists('Meta Data', $response) || !array_key_exists($timeseriesKey, $response)) {
            throw new TwelvedataResponseException(HttpStatus::BAD_REQUEST, 'Invalid response');
        }

        $timezone = $response['Meta Data']['6. Time Zone'];

        $response = $response[$timeseriesKey];

        $timeseries = [];

        $points = 0;

        foreach ($response as $dateTime => $value) {
            if (null !== $maxPoints && $maxPoints >= $points++) {
                break;
            }
            $seriesDateTime = Carbon::parse($dateTime);

            $timeseries[$dateTime] = new TimeSeriesIntraDay(
                [
                    'dateTime' => $seriesDateTime,
                    'timezome' => $timezone,
                    'open'     => $response['1. open'] ?? 0,
                    'high'     => $response['2. high'] ?? 0,
                    'low'      => $response['3. low'] ?? 0,
                    'close'    => $response['4. close'] ?? 0,
                    'volume'   => $response['5. volume'] ?? 0,
                ]
            );
        }

        return $timeseries;
    }

    /**
     * @param string $symbol
     * @param string $interval
     * @return TimeSeriesIntraDay[]
     * @throws TwelvedataResponseException
     * @throws TwelvedataRateLimitException
     */
    public function getIntraDaySeriesFromStartOfDay(string $symbol, string $interval = '5min')
    {
        $response = $this->query(
            static::TIME_SERIES_INTRADAY,
            [
                'symbol'   => $symbol,
                'interval' => $interval,
            ]
        );

        $timeseriesKey = 'Time Series (' . $interval . ')';

        if (!array_key_exists('Meta Data', $response) || !array_key_exists($timeseriesKey, $response)) {
            throw new TwelvedataResponseException(HttpStatus::BAD_REQUEST, 'Invalid response');
        }


        $timezone = $response['Meta Data']['6. Time Zone'];

        $response = $response[$timeseriesKey];

        $startOfLastDay = Carbon::parse(array_key_first($response))->startOfDay();

        $timeseries = [];

        foreach ($response as $dateTime => $value) {
            $seriesDateTime = Carbon::parse($dateTime);

            if ($seriesDateTime->lessThan($startOfLastDay)) {
                break;
            }

            $timeseries[$dateTime] = new TimeSeriesIntraDay(
                [
                    'dateTime' => $seriesDateTime,
                    'timezome' => $timezone,
                    'open'     => $value['1. open'] ?? 0,
                    'high'     => $value['2. high'] ?? 0,
                    'low'      => $value['3. low'] ?? 0,
                    'close'    => $value['4. close'] ?? 0,
                    'volume'   => $value['5. volume'] ?? 0,
                ]
            );
        }

        return $timeseries;
    }

    /**
     * @param string $symbol
     * @param int    $maxPoints
     * @return TimeSeriesDay[]
     * @throws TwelvedataResponseException
     * @throws TwelvedataRateLimitException
     */
    public function getDaySeries(string $symbol, int $maxPoints = null)
    {
        $response = $this->query(
            static::TIME_SERIES_DAILY,
            [
                'symbol' => $symbol,
            ]
        );

        $timeseriesKey = 'Time Series (Daily)';
        if (!array_key_exists('Meta Data', $response) || !array_key_exists($timeseriesKey, $response)) {
            throw new TwelvedataResponseException(HttpStatus::BAD_REQUEST, 'Invalid response');
        }

        $timezone = $response['Meta Data']['5. Time Zone'];

        $response = $response[$timeseriesKey];

        $timeseries = [];

        $points = 0;

        foreach ($response as $date => $value) {
            if (null !== $maxPoints && $points++ >= $maxPoints) {
                break;
            }
            $seriesDateTime = Carbon::parse($date);

            $timeseries[$date] = new TimeSeriesDay(
                [
                    'date'     => $seriesDateTime,
                    'timezome' => $timezone,
                    'open'     => $value['1. open'] ?? 0,
                    'high'     => $value['2. high'] ?? 0,
                    'low'      => $value['3. low'] ?? 0,
                    'close'    => $value['4. close'] ?? 0,
                    'volume'   => $value['5. volume'] ?? 0,
                ]
            );
        }


        return $timeseries;
    }
}
