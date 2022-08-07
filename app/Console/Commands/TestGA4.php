<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use Google\Analytics\Admin\V1alpha\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1alpha\Property;
use Google\Analytics\Admin\V1alpha\WebDataStream;
use GPBMetadata\Google\Analytics\Admin\V1Alpha\Resources;
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
        self::$ACCOUNT_ID = env('GA_ACCOUNT_ID', "236370720");
        $client = $this->initializeGAClient();
        // $accounts = $client->listAccounts();

        // foreach ($accounts as $account) {
        //     print 'Found account: ' . $account->getName() . PHP_EOL;
        // }

        // $property = $this->createProperty($client);
        // Log::debug("Created property response " . print_r($property, true));

        $stream = $this->createStreams($client, "properties/326299414");

        Log::debug("Create property for stream {$stream->getName()}");

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

        Resources::initOnce();

        return $client;
    }

    public function createProperty($client)
    {
        $property = new Property();
        $property->setDisplayName("Wig seller Duxstore");
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
            // Create Stream
            $stream = $this->createStreams($client, "properties/" . $property->getName());

            Log::debug("Create property {$property->getName()} for stream {$stream->getName()}");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createStreams($client, $propertyName)
    {
        $stream = new WebDataStream();

        $stream->setDisplayName("Default web stream");
        $stream->setDefaultUri("https://duxstore.myduxstore.com");

        try {
            $property = $client->propertyName($propertyName);
            // $client->createDataStream();
            return $client->createDataStream($property, $stream);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createConversionEvent()
    {
        //
    }
}
