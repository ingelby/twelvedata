<?php

namespace Ingelby\Twelvedata\Api;

use common\helpers\LoggingHelper;
use Ingelby\Twelvedata\Exceptions\TwelvedataRateLimitException;
use Ingelby\Twelvedata\Exceptions\TwelvedataResponseException;
use ingelby\toolbox\constants\HttpStatus;
use ingelby\toolbox\services\inguzzle\exceptions\InguzzleClientException;
use ingelby\toolbox\services\inguzzle\exceptions\InguzzleInternalServerException;
use ingelby\toolbox\services\inguzzle\exceptions\InguzzleServerException;
use ingelby\toolbox\services\inguzzle\InguzzleHandler;
use Ingelby\Twelvedata\Models\AbstractTwelveDataModel;
use yii\caching\TagDependency;
use yii\helpers\Json;

class AbstractHandler extends InguzzleHandler
{
    protected const DEFAULT_URL = 'https://api.twelvedata.com';
    protected const CACHE_KEY = 'TWELVEDATA_';
    protected const CACHE_TAG_DEPENDENCY = 'TWELVEDATA';

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var int
     */
    protected $cacheTimeout = 600;

    /**
     * AbstractHandler constructor.
     *
     * @param string      $apiKey
     * @param string|null $baseUrl
     */
    public function __construct(string $apiKey, $baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;

        if (null === $this->baseUrl) {
            $this->baseUrl = static::DEFAULT_URL;
        }

        parent::__construct($this->baseUrl);
    }

    /**
     * @param string $uri
     * @param array  $parameters
     * @throws TwelvedataResponseException
     * @throws TwelvedataRateLimitException
     */
    public function get(string $uri, array $parameters)
    {
        $standardParameters = [
            'apikey' => $this->apiKey,
        ];
        $finalParameters = array_merge($standardParameters, $parameters);

        $cacheKey = static::CACHE_KEY . $uri . md5(Json::encode($finalParameters));

        return \Yii::$app->cache->getOrSet(
            $cacheKey,
            function () use ($finalParameters, $uri) {
                try {
                    $response = $this->get($uri, $finalParameters);

                    return $response;
                } catch (InguzzleClientException | InguzzleInternalServerException | InguzzleServerException $e) {
                    if (429 === $e->statusCode) {
                        throw new TwelvedataRateLimitException($e->statusCode, 'Rate limit reached', 0, $e);
                    }
                    throw new TwelvedataResponseException($e->statusCode, 'Error contacting Twelevedata', 0, $e);
                }
            },
            $this->cacheTimeout,
            new TagDependency(['tags' => static::CACHE_TAG_DEPENDENCY])
        );
    }

    /**
     * @param array $bulkResponse
     * @param array $searchedSymbols
     * @return AbstractTwelveDataModel[]
     */
    protected function mapBulk(array $bulkResponse, array $searchedSymbols): array
    {
        $bulkMappedResponse = [];
        foreach ($searchedSymbols as $symbol) {
            if (!array_key_exists($symbol, $bulkResponse)) {
                \Yii::warning('Unknown stock symbol: ' . $symbol);
                continue;
            }

            $singularResponse = $bulkResponse[$symbol];

            if (array_key_exists('message', $singularResponse)) {
                \Yii::warning('Bad stock for symbol: ' . $symbol . ' message: ' . $singularResponse['message']);
                continue;
            }

            $singularResponse[$symbol] = $this->map($singularResponse);
        }

        return $bulkMappedResponse;
    }

    /**
     * @param int $cahceTimeout
     */
    public function setCacheTimeout(int $cahceTimeout)
    {
        $this->cacheTimeout = $cahceTimeout;
    }

    /**
     * @param array $response
     * @return AbstractTwelveDataModel
     */
    abstract protected function map(array $response): AbstractTwelveDataModel;
}
