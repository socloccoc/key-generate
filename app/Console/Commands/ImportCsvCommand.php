<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Excel;

class ImportCsvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
//        $file = fopen(public_path('1000keys-10days-1.xlsx'),"r");
//        print_r(fgetcsv($file));
//        fclose($file);

        $results = Excel::load(public_path('1000keys-10days-1.xlsx'));
        $data = $results->toArray();
        dd($data);
    }
}
