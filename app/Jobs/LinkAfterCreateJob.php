<?php

namespace App\Jobs;

use App\Classes\Sitemaps\FrapSiteMap;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LinkAfterCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $link;
    /**
     * Create a new job instance.
     *
     * @param FrapSiteMap $frapSiteMap
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FrapSiteMap $frapSiteMap)
    {
        $frapSiteMap->createSiteMap($this->link);
        logs()->warning('Типа успешно');
    }
}
