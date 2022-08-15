<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use Google\Analytics\Admin\V1alpha\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1alpha\Property;
use Google\Analytics\Admin\V1alpha\DataStream;
use Google\Analytics\Admin\V1alpha\CreateDataStreamRequest;
use Google\Analytics\Admin\V1alpha\DataStream\WebStreamData;
use Illuminate\Support\Facades\Log;

class TestGA4 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ga4';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private static ?string $ACCOUNT_ID = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        self::$ACCOUNT_ID = env('GA_ACCOUNT_ID', "236370720"); // 236032008
        $client = $this->initializeGAClient();

        $property = $this->createProperty($client);

        return Command::SUCCESS;
    }

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

    public function createProperty($client)
    {
        $property = new Property();
        $property->setDisplayName("300 Duxstore");
        $property->setAccount("accounts/" . self::$ACCOUNT_ID);
        // $property->setPropertyType("ORDINARY_PROPERTY");
        $property->setParent("accounts/" . self::$ACCOUNT_ID);
        $property->setIndustryCategory(10);
        $property->setTimeZone("Africa/Lagos");
        $property->setCurrencyCode("NGN");
        $property->setServiceLevel(1);

        try {
            // Create property
            $property = $client->createProperty($property);
            $stream = $this->createStreams($client, $property->getName());

            Log::debug("Create property {$property->getName()} for stream {$stream->getName()}, measurement id {$stream->getWebStreamData()->getMeasurementId()}");
            return $property;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createStreams($client, $propertyName)
    {
        $webStream = new WebStreamData();
        $webStream->setDefaultUri('https://300.myduxstore.co');

        echo "name: " . $propertyName;

        $streamData = new DataStream();
        $streamData->setDisplayName("Default web stream");
        $streamData->setType(1);
        $streamData->setWebStreamData($webStream);

        try {
            $response = $client->createDataStream($propertyName, $streamData);

            $stream = new CreateDataStreamRequest();
            $stream->setDataStream($response);

            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createConversionEvent()
    {
        //
    }
}
