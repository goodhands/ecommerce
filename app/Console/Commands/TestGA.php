<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Client as Google_Client;
use Google\Service\Analytics as Google_Service_Analytics;
use Google\Service\Analytics\Webproperty as Google_Service_Analytics_Webproperty;
use Google\Service\Analytics\Profile as Google_Service_Analytics_Profile;
use Google\Service\Exception as apiServiceException;
use Exception;
use Illuminate\Support\Facades\Log;

class TestGA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ga';

    private static ?string $ACCOUNT_ID = null;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        self::$ACCOUNT_ID = env('GA_ACCOUNT_ID');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $analytics = $this->initializeAnalytics();
        $property = $this->createProperty($analytics);
        // $profile = $this->getFirstProfileId($analytics);
        // $results = $this->getResults($analytics, $profile);
        // $this->printResults($results);
        return Command::SUCCESS;
    }

    public function initializeAnalytics()
    {
        // Creates and returns the Analytics Reporting service object.

        // Use the developers console and download your service account
        // credentials in JSON format. Place them in this directory or
        // change the key file location if necessary.
        // TODO: Store this securely on AWS with Signed Signature
        $KEY_FILE_LOCATION = public_path('duxstore-358301-1fbf37dc0361.json');

        // Create and configure a new client object.
        $client = new Google_Client();
        $client->setApplicationName("Duxstore Analytics Reporting");
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes([
            'https://www.googleapis.com/auth/analytics.edit',
            'https://www.googleapis.com/auth/analytics.readonly'
        ]);
        $analytics = new Google_Service_Analytics($client);

        return $analytics;
    }

    /**
     * This request creates a new property.
     *
     * TODO: We have a limit of 100 properties per accounts,
     * we need to contact Google support for upgrade
     * @see https://support.google.com/analytics/answer/1102152?hl=en#zippy=%2Cin-this-article
     */
    public function createProperty($analytics)
    {
        try {
            $property = new Google_Service_Analytics_Webproperty();
            // Get the storename the user has used as the name here
            $property->setName('Tent Store');
            $property->setWebsiteUrl('https://duxstore.myduxstore.com');
            // We should set this based on what the user provides as industry
            // @see https://developers.google.com/analytics/devguides/config/mgmt/v3/
            // mgmtReference/management/webproperties#:~:text=XXXXX%2DYY.-,industryVertical,-string
            $property->setIndustryVertical('BEAUTY_AND_FITNESS');
            // We can't create accounts programmatically so we are using a constant here
            // New accounts can be created from the dashboard and replace this
            $webProperty = $analytics->management_webproperties->insert(self::$ACCOUNT_ID, $property);

            // Create view
            $propertyID = $webProperty->getId();
            $view = $this->createView($analytics, $propertyID);
            Log::debug("Propeerty created " . print_r(['view' => $view, $webProperty, $propertyID], true));
        } catch (apiServiceException $e) {
            print 'There was an Analytics API service error with creating the property'
                . $e->getCode() . ':' . $e->getMessage();
        } catch (apiException $e) {
            print 'There was a general API error with creating the property'
                . $e->getCode() . ':' . $e->getMessage();
        } catch (Exception $e) {
            print 'There was a base exception error with creating the property'
                . $e->getCode() . ':' . $e->getMessage();
        }
    }

    /**
     * This request creates a new view (profile).
     */
    public function createView($analytics, $propertyTrackingID)
    {
        // Construct the body of the request and set its properties.
        $profile = new Google_Service_Analytics_Profile();
        $profile->setName('Duxstore eCommerce View for 2');
        $profile->setECommerceTracking(true);
        $profile->setWebsiteUrl('https://duxstore.myduxstore.com');
        $profile->setTimezone('Africa/Lagos');
        $profile->setType('WEB');

        try {
            return $analytics->management_profiles->insert(self::$ACCOUNT_ID, $propertyTrackingID, $profile);
        } catch (apiServiceException $e) {
            print 'There was an Analytics API service error with creating the view '
            . $e->getCode() . ':' . $e->getMessage();
        } catch (apiException $e) {
            print 'There was a general API error with creating the view '
            . $e->getCode() . ':' . $e->getMessage();
        } catch (Exception $e) {
            print 'There was a base exception error with creating the view '
                . $e->getCode() . ':' . $e->getMessage();
        }
    }

    /**
     * Test functions by google
     */
    public function getFirstProfileId($analytics)
    {
        // Get the user's first view (profile) ID.

        // Get the list of accounts for the authorized user.
        $accounts = $analytics->management_accounts->listManagementAccounts();

        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            $firstAccountId = $items[1]->getId();

            // Get the list of properties for the authorized user.
            $properties = $analytics->management_webproperties
                ->listManagementWebproperties($firstAccountId);

            if (count($properties->getItems()) > 0) {
                $items = $properties->getItems();
                $firstPropertyId = $items[0]->getId();

                Log::debug("firstPropertyId " . print_r($firstPropertyId, true));

                // Get the list of views (profiles) for the authorized user.
                $profiles = $analytics->management_profiles
                    ->listManagementProfiles($firstAccountId, $firstPropertyId);

                Log::debug("profiles " . print_r($profiles, true));

                if (count($profiles->getItems()) > 0) {
                    $items = $profiles->getItems();

                    Log::debug("Return the first view (profile) ID. " . print_r($items, true));
                    // Return the first view (profile) ID.
                    return $items[0]->getId();

                } else {
                    throw new Exception('No views (profiles) found for this user.');
                }
            } else {
                throw new Exception('No properties found for this user.');
            }
        } else {
            throw new Exception('No accounts found for this user.');
        }
    }

    /**
     * Test functions by google
    */
    public function getResults($analytics, $profileId)
    {
        // Calls the Core Reporting API and queries for the number of sessions
        // for the last seven days.
        return $analytics->data_ga->get(
            'ga:' . $profileId,
            '7daysAgo',
            'today',
            'ga:sessions'
        );
    }

    /**
     * Test functions by google
    */
    public function printResults($results)
    {
        // Parses the response from the Core Reporting API and prints
        // the profile name and total sessions.
        Log::debug("Analytics result " . print_r($results, true));
        Log::debug("Rows " . print_r($results->getRows(), true));
        if (count($results->getRows()) > 0) {
            // Get the profile name.
            $profileName = $results->getProfileInfo()->getProfileName();

            // Get the entry for the first entry in the first row.
            $rows = $results->getRows();
            $sessions = $rows[0][0];

            // Print the results.
            print "First view (profile) found: $profileName\n";
            print "Total sessions: $sessions\n";
        } else {
            print "No results found.\n";
        }
    }
}
