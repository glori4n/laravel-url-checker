<?php

namespace App\Jobs;

use App\Models\Url;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GetUrls implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $urls = Url::get();

        foreach ($urls as $url) {
            try {
                $client = new \GuzzleHttp\Client();

                $response = $client->request('GET', $url->uri);

                $url->status = $response->getStatusCode();
                $host = parse_url($url->uri, PHP_URL_HOST);
                Storage::put($host.'.html', $response->getBody());
                $url->response = $host.'.html';

                $url->update();

            } catch (Exception $e) {
                $url->status = 404;
                $url->response = $e->getMessage();
                $url->update();
            }
        }
    }
}
