<?php

namespace App\Console\Commands;

use App\Http\Controllers\CurrenciesController;
use App\Services\AccountsServices;
use App\Services\MensuelleCommissionsServices;
use Illuminate\Console\Command;

class ExecuteMensuelleCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:mensuelle_commissions';

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
     * @return mixed
     */
    public function handle()
    {

        $service_commission_mensuelle = new MensuelleCommissionsServices();
        $service_commission_mensuelle->executeMensuelleCommissions();

    }
}
