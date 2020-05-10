<?php


namespace Ingelby\Twelvedata\Helpers;


use Ingelby\Twelvedata\Models\SearchMatch;
use Ingelby\Twelvedata\Models\TimeSeries;

class Select2Helper
{

    /**
     * @param SearchMatch[] $searchResults
     * @return
     */
    public static function mapSimple(array $searchResults)
    {

        $mappedValues = [];
        foreach ($searchResults as $searchResult) {
            $mappedValues[] = [
                'id'   => $searchResult->symbol,
                'text' => $searchResult->getFriendlyName(),
            ];
        }
        return $mappedValues;
    }
}
