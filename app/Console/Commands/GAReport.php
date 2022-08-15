<?php

namespace App\Console\Commands;

use Google\Analytics\Data\V1alpha\Filter;
use Illuminate\Console\Command;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\DateRange;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class GAReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ga-report';

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
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = $this->initializeGA();
        $this->report($client);
        return Command::SUCCESS;
    }

    public function initializeGA()
    {
        $KEY_FILE_LOCATION = public_path("duxstore-358301-1fbf37dc0361.json");

        $client = new BetaAnalyticsDataClient(
            array(
                'credentials' => $KEY_FILE_LOCATION
            )
        );

        return $client;
    }

    public function report($client)
    {
        $carbon = new Carbon();

        $response = $client->runReport([
            'property' => 'properties/325248268',
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
                    ],
                    [
                        'name' => 'event'
                    ],
                    [
                        'name' => 'sessions'
                    ]
                ),
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'active7DayUsers',
                    ]
                )
            ]
        ]);

        Log::debug("Report rows " . print_r($response->getRows(), true));
        foreach ($response->getRows() as $row) {
            foreach ($row->getDimensionValues() as $dimensionValue) {
                // Log::debug("Dimensions rows " . print_r($dimensionValue->getValue(), true));
                print 'Dimension Value: ' . $dimensionValue->getValue() . PHP_EOL;
                // print 'Dimension Name: ' . $dimensionValue->getName() . PHP_EOL;
            }
        }
    }

}
