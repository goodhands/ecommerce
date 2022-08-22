<?php

namespace App\Repositories\Traits;

use App\Models\Store;
use App\Models\Store\Analytic;
use App\Models\Store\Product;
use Carbon\Carbon;
use Exception;
use Google\Analytics\Admin\V1alpha\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1alpha\Property;
use Google\Analytics\Admin\V1alpha\DataStream;
use Google\Analytics\Admin\V1alpha\CreateDataStreamRequest;
use Google\Analytics\Admin\V1alpha\DataStream\WebStreamData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Google\Analytics\Data\V1alpha\Filter;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;

trait Analytics
{
    public static ?string $ACCOUNT_ID = null;

    public function initializeGAClient()
    {
        $KEY_FILE_LOCATION = public_path("duxstore-358301-1fbf37dc0361.json");

        self::$ACCOUNT_ID = env('GA_ACCOUNT_ID', "236370720"); // 236032008

        $client = new AnalyticsAdminServiceClient(
            array(
                'credentials' => $KEY_FILE_LOCATION
            )
        );

        return $client;
    }

    public function createGAProperty($store)
    {
        $client = $this->initializeGAClient();

        $property = new Property();
        $property->setDisplayName($store->name . " Analytics Property");
        $property->setAccount("accounts/" . self::$ACCOUNT_ID);
        // $property->setPropertyType("ORDINARY_PROPERTY");
        $property->setParent("accounts/" . self::$ACCOUNT_ID);
        // TODO: #11 Populate our database with industries from the ones listed here
        // https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties#industrycategory
        // then allow users pick their industry while signing up
        $property->setIndustryCategory(10);
        // TODO: #10 Sign up information in future should ask for these details
        $property->setTimeZone("Africa/Lagos");
        $property->setCurrencyCode("NGN");
        $property->setServiceLevel(1);

        try {
            // Create property
            $property = $client->createProperty($property);
            // Create Stream
            $store_url = Store::find($store->id)->url;
            $stream = $this->createGAWebStream($client, $store_url, $property->getName());
            // Insert db
            $analytic = Analytic::create([
                'store_id' => $store->id,
                'property_id' => $property->getName(),
                'measurement_id' => $stream->getWebStreamData()->getMeasurementId(),
                'type' => 'ga4',
            ]);


            if ($analytic) {
                return $stream->getWebStreamData()->getMeasurementId();
            }

            Log::debug("Analytic instance not created " . print_r($analytic, true));
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createGAWebStream($client, $url, $propertyName)
    {
        $webStream = new WebStreamData();
        $webStream->setDefaultUri($url);

        $streamData = new DataStream();
        $streamData->setDisplayName("Default Stream");
        $streamData->setType(1); // Web Stream
        $streamData->setWebStreamData($webStream);

        try {
            $response = $client->createDataStream($propertyName, $streamData);

            $stream = new CreateDataStreamRequest();
            $stream->setDataStream($response);

            return $response;
        } catch (Exception $e) {
            Log::debug("Error occurred while creating a stream");
            throw $e;
        }
    }

    public function createConversionEvent()
    {
        //
    }

    public function initializeGADataAPIClient()
    {
        $KEY_FILE_LOCATION = public_path("duxstore-358301-1fbf37dc0361.json");

        $client = new BetaAnalyticsDataClient(
            array(
                'credentials' => $KEY_FILE_LOCATION
            )
        );

        return $client;
    }

    public function getStoreVisits($storeId)
    {
        $carbon = new Carbon();
        $ttl = $carbon::now()->addHours(10);

        return Cache::remember('views_store_' . $storeId, $ttl, function () use ($storeId, $carbon) {

            $property_id = Store::find($storeId)->analytic->property_id;

            $client = $this->initializeGADataAPIClient();

            $response = $client->runReport([
                'property' => $property_id,
                'dateRanges' => [
                    new DateRange([
                        'start_date' => (string) $carbon->startOfWeek(0)->format('Y-m-d'),
                        'end_date' => (string) $carbon::now()->format('Y-m-d'),
                    ]),
                ],
                'dimensions' => [
                    new Dimension(
                        [
                            'name' => 'city',
                        ],
                        [
                            'name' => 'country',
                        ]
                    ),
                ],
                'metrics' => [
                    new Metric(
                        [
                            'name' => 'activeUsers',
                        ]
                    )
                ]
            ]);

            $data = array();

            foreach ($response->getRows() as $row) {
                foreach ($row->getMetricValues() as $metric) {
                    $data['views'] = $metric->getValue();
                }
            }

            return collect($data);
        });
    }

    /**
     * Returns a collection of most viewed products
     * Collection includes their id and view count
     */
    public function getProductViews($storeId)
    {
        $carbon = new Carbon();
        $ttl = $carbon::now()->addHours(10);

        return Cache::remember('views_product_' . $storeId, $ttl, function () use ($storeId, $carbon) {

            $property_id = Store::find($storeId)->analytic->property_id;

            $client = $this->initializeGADataAPIClient();

            $response = $client->runReport([
                'property' => $property_id,
                'dateRanges' => [
                    new DateRange([
                        'start_date' => (string) $carbon->startOfWeek(0)->format('Y-m-d'),
                        'end_date' => (string) $carbon::now()->format('Y-m-d'),
                    ]),
                ],
                'dimensions' => [
                    new Dimension(
                        [
                            'name' => 'itemName',
                        ],
                        [
                            'name' => 'itemId',
                        ],
                        [
                            'name' => 'screenResolution',
                        ],
                        [
                            'name' => 'eventName'
                        ]
                    ),
                ],
                "dimensionFilter" => [
                    new Filter(
                        [
                            "field_name" => "eventName",
                            "string_filter" => [
                                "value" => "view_item"
                            ],
                        ]
                    )
                ],
                'metrics' => [
                    new Metric(
                        [
                            'name' => 'itemViews', //clicked to view details
                        ],
                        [
                            'name' => 'itemListClicks', //clicked when it appeared in a list
                        ]
                    )
                ]
            ]);

            $data = array();

            foreach ($response->getRows() as $row) {
                foreach ($row->getDimensionValues() as $dimension) {
                    $data[] = $dimension->getValue();
                }
            }

            Log::debug('Product views data ' . print_r($data, true));

            return collect($data);
        });
    }
}
