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
            'metrics' => [
                new Metric(
                    [
                        'name' => 'activeUsers',
                    ]
                )
            ]
        ]);

        foreach ($response->getRows() as $row) {
            foreach ($row->getDimensionValues() as $dimensionValue) {
                // Log::debug("Dimensions rows " . print_r($dimensionValue->getValue(), true));
                print 'Dimension Value: ' . $dimensionValue->getValue() . PHP_EOL;
                $data['dimension value'] = $dimensionValue->getValue();
                // print 'Dimension Name: ' . $dimensionValue->getName() . PHP_EOL;

                Log::debug("Dimension value " . print_r($row->getDimensionValues(), true));
            }

            foreach ($row->getMetricValues() as $metricValue) {
                // Log::debug("Dimensions rows " . print_r($dimensionValue->getValue(), true));
                print 'Metric value Value: ' . $metricValue->getValue() . PHP_EOL;
                $data['Metric value'] = $metricValue->getValue();
                // print 'Dimension Name: ' . $dimensionValue->getName() . PHP_EOL;

                Log::debug("Metric value " . print_r($row->getMetricValues(), true));

            }
        }
    }

}
