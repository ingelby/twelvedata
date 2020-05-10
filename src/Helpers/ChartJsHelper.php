<?php


namespace Ingelby\Twelvedata\Helpers;


use Ingelby\Twelvedata\Models\TimeSeries;

class ChartJsHelper
{

    /**
     * @param TimeSeries[]  $timeSeries
     * @param float  $openingValue
     * @param string $dateFormat
     * @param bool   $reverseValues
     * @return [][]
     */
    public static function mapTimeSeriesPercent(
        array $timeSeries,
        float $openingValue,
        string $dateFormat = 'G:i:s',
        bool $reverseValues = true
    ) {
        if (true === $reverseValues) {
            $timeSeries = array_reverse($timeSeries);
        }

        $mappedValues = [
            'labels' => [],
            'data'   => [],
        ];
        foreach ($timeSeries as $series) {
            $value = round($series->open - $openingValue, 3);
            $mappedValues['data'][] = $value;
            $mappedValues['labels'][] = $series->getDateInFormat($dateFormat) . ' - ' . $value;
        }
        return $mappedValues;
    }

    /**
     * @param TimeSeries[]  $timeSeries
     * @param string $dateFormat
     * @param bool   $reverseValues
     * @return [][]
     */
    public static function mapTimeSeries(array $timeSeries, string $dateFormat = 'jS F Y', bool $reverseValues = true)
    {
        if (true === $reverseValues) {
            $timeSeries = array_reverse($timeSeries);
        }

        $mappedValues = [
            'labels' => [],
            'data'   => [],
        ];
        foreach ($timeSeries as $series) {
            $mappedValues['data'][] = $series->open;
            $mappedValues['labels'][] = $series->getDateInFormat($dateFormat);
        }
        return $mappedValues;
    }

}