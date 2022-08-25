<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Provinsi;
use App\Models\City;

class GetDataRajaOngkir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:rajaongkir';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get data provinsi and kabupaten with raja ongkir';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $get_prov = Http::get('https://api.rajaongkir.com/starter/province', [
            'key' => env('RAJA_ONGKIR_KEY')
        ]);

        $get_kab = Http::get('https://api.rajaongkir.com/starter/city', [
            'key' => env('RAJA_ONGKIR_KEY')
        ]);

        if ($get_prov->ok() && $get_kab->ok()) {
            // $this->info('The command was successful!');
            $prov = $get_prov->collect('rajaongkir.results');
            $city = $get_kab->collect('rajaongkir.results');
            // $this->info($prov);

            $prov->each(function ($item, $key) {
                Provinsi::create([
                    'province' => $item['province']
                ]);
            });

            $city->each(function ($item, $key) {
                City::create([
                    'province_id' => $item['province_id'],
                    'city_name' => $item['city_name'],
                    'postal_code' => $item['postal_code']
                ]);
            });

            $this->info('The command was successful!');
        } else {
            $this->error('Something went wrong ! ');
            $this->error('Province => '.$get_prov->collect('rajaongkir.status.description'));
            $this->error('City => '.$get_kab->collect('rajaongkir.status.description'));
        }
    }
}
