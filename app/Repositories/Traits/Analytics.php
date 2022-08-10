<?php

namespace App\Repositories\Traits;

use App\Models\Store;
use App\Models\Store\Product;
use Exception;
use Google\Analytics\Admin\V1alpha\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1alpha\Property;
use Google\Analytics\Admin\V1alpha\WebDataStream;
use Illuminate\Support\Facades\Log;

trait Analytics
{
    public ?string $ACCOUNT_ID = null;

    public function initializeGAClient()
    {
        $KEY_FILE_LOCATION = public_path("duxstore-358301-1fbf37dc0361.json");

        $client = new AnalyticsAdminServiceClient(
            array(
                'credentials' => $KEY_FILE_LOCATION
            )
        );

        return $client;
    }

    public function createGAProperty($data)
    {
        $client = $this->initializeGAClient();

        $property = new Property();
        $property->setDisplayName($data['name'] . " Analytics Property");
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
            $store_url = Store::find($data['id'])->url;
            // $stream = $this->createGAWebStream($client, $store_url, "properties/" . $property->getName());
            // Insert db
            Log::debug("Create property {$property->getName()} for stream {$stream->getName()}");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createGAWebStream($client, $url, $propertyName)
    {
        $stream = new WebDataStream();

        $stream->setDisplayName("Default web stream");
        $stream->setDefaultUri($url);

        try {
            $property = $client->propertyName($propertyName);
            return $client->createDataStream($property, $stream);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createConversionEvent()
    {
        //
    }

    public function getGAReport()
    {

    }
}
