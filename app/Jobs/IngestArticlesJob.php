<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Models\Source;

class IngestArticlesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $providerKey;
    public array $params;

    /**
     * Constructor.
     */
    public function __construct(string $providerKey, array $params = [])
    {
        $this->providerKey = $providerKey;
        $this->params = $params;
    }

    /**
     * Handle the job.
     */
    public function handle()
    {
        // Resolve repository and provider from the container inside the handle method
        $repo = app(ArticleRepositoryInterface::class);
        $provider = app("news.provider.{$this->providerKey}");

        if (! $provider) {
            logger()->warning("Provider not found: {$this->providerKey}");
            return;
        }

        $normalized = $provider->fetch($this->params);

        $source = Source::firstOrCreate(
            ['key' => $this->providerKey],
            [
                'name' => ucfirst($this->providerKey),
                'api_url' => '', // Optional, can be updated later
            ]
        );

        $repo->upsertMany($normalized, $source->id);
    }
}
