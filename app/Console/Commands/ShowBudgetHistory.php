<?php

namespace App\Console\Commands;

use App\Models\Budget;
use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class ShowBudgetHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'budget:history {code? : Budget code} {--limit=10 : Number of activities to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show activity history for budgets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $code = $this->argument('code');
        $limit = $this->option('limit');

        if ($code) {
            $budget = Budget::where('code', $code)->first();

            if (! $budget) {
                $this->error("Budget with code '{$code}' not found!");

                return 1;
            }

            $this->showBudgetHistory($budget, $limit);
        } else {
            $this->showAllBudgetActivities($limit);
        }

        return 0;
    }

    protected function showBudgetHistory(Budget $budget, int $limit)
    {
        $this->info("History for Budget: {$budget->code}");
        $this->newLine();

        $activities = $budget->activities()
            ->with('causer')
            ->latest()
            ->limit($limit)
            ->get();

        if ($activities->isEmpty()) {
            $this->warn('No activities found for this budget.');

            return;
        }

        $headers = ['Date', 'Action', 'User', 'Changes'];
        $rows = [];

        foreach ($activities as $activity) {
            $changes = $activity->properties?->get('old', []);
            $changeCount = count($changes);
            $changesSummary = $changeCount > 0 ? "{$changeCount} fields" : 'Initial creation';

            $rows[] = [
                $activity->created_at->format('Y-m-d H:i:s'),
                ucfirst($activity->description),
                $activity->causer?->name ?? 'System',
                $changesSummary,
            ];
        }

        $this->table($headers, $rows);

        if ($this->confirm('Show detailed changes?', false)) {
            $this->showDetailedChanges($activities->first());
        }
    }

    protected function showAllBudgetActivities(int $limit)
    {
        $this->info("Recent Budget Activities (Last {$limit})");
        $this->newLine();

        $activities = Activity::query()
            ->where('subject_type', Budget::class)
            ->with(['causer', 'subject'])
            ->latest()
            ->limit($limit)
            ->get();

        if ($activities->isEmpty()) {
            $this->warn('No activities found.');

            return;
        }

        $headers = ['Date', 'Budget', 'Action', 'User'];
        $rows = [];

        foreach ($activities as $activity) {
            $rows[] = [
                $activity->created_at->format('Y-m-d H:i:s'),
                $activity->subject?->code ?? 'Deleted',
                ucfirst($activity->description),
                $activity->causer?->name ?? 'System',
            ];
        }

        $this->table($headers, $rows);
    }

    protected function showDetailedChanges(Activity $activity)
    {
        $this->newLine();
        $this->info('Detailed Changes:');
        $this->newLine();

        $properties = $activity->properties?->toArray() ?? [];
        $old = $properties['old'] ?? [];
        $new = $properties['attributes'] ?? [];

        if (empty($old)) {
            $this->line('Budget was created with these values:');
            foreach ($new as $key => $value) {
                if (! in_array($key, ['updated_at', 'created_at'])) {
                    $this->line("  • {$key}: ".$this->formatValue($value));
                }
            }

            return;
        }

        foreach ($old as $key => $oldValue) {
            $newValue = $new[$key] ?? null;

            if ($key === 'updated_at') {
                continue;
            }

            $this->line("  • <fg=yellow>{$key}:</>");
            $this->line('    Before: '.$this->formatValue($oldValue));
            $this->line('    After:  '.$this->formatValue($newValue));
            $this->newLine();
        }
    }

    protected function formatValue($value): string
    {
        if (is_null($value)) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        return (string) $value;
    }
}
