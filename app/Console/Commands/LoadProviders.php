<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\StoreRepository;

class LoadProviders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:providers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads delivery and payment providers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(StoreRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->repository->loadMethodFromConfigFile();
        $this->repository->loadPaymentProvidersFromConfig();

        return Command::SUCCESS;
    }
}
