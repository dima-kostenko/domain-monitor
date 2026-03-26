<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Services\DomainCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckDomains extends Command
{
    protected $signature = 'check:domains
                            {--domain=   : Check a single domain by ID}
                            {--force     : Ignore check_interval, check all active domains now}';

    protected $description = 'Check domain availability and save results to domain_checks';

    public function __construct(private readonly DomainCheckService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $query = Domain::query()->where('is_active', true)->with('latestCheck');

        if ($id = $this->option('domain')) {
            $query->where('id', $id);
        } elseif (! $this->option('force')) {
            // Only pick domains whose next scheduled check is due now
            $query->where(function ($q) {
                $q->whereDoesntHave('checks')
                  ->orWhereHas('latestCheck', function ($q) {
                      $q->whereRaw('DATE_ADD(created_at, INTERVAL domains.check_interval MINUTE) <= NOW()');
                  });
            });
        }

        $domains = $query->get();

        if ($domains->isEmpty()) {
            $this->line('<fg=gray>No domains due for checking.</>');
            return self::SUCCESS;
        }

        $this->info("Checking {$domains->count()} domain(s)...");
        $bar = $this->output->createProgressBar($domains->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% – %message%');
        $bar->start();

        $stats = ['online' => 0, 'offline' => 0, 'errors' => 0];

        foreach ($domains as $domain) {
            $bar->setMessage($domain->name);

            try {
                $check = $this->service->check($domain);

                $stats[$check->status]++;

                $bar->setMessage(
                    $check->status === 'online'
                        ? "<fg=green>{$domain->name}</> {$check->response_time}ms"
                        : "<fg=red>{$domain->name}</> {$check->error_message}"
                );
            } catch (\Throwable $e) {
                $stats['errors']++;
                Log::error("CheckDomains: failed for domain #{$domain->id} ({$domain->name})", [
                    'exception' => $e->getMessage(),
                ]);
                $bar->setMessage("<fg=yellow>Error: {$domain->name}</>");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Online', 'Offline', 'Errors'],
            [[$stats['online'], $stats['offline'], $stats['errors']]]
        );

        return self::SUCCESS;
    }
}
