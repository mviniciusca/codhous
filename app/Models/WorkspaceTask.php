<?php

namespace App\Models;

use App\Enums\WorkspacePriority;
use App\Models\Concerns\OwnedByUser;
use App\Support\WorkspaceDates;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Tarefa pessoal do Meu Workspace.
 */
class WorkspaceTask extends Model
{
    use OwnedByUser;

    protected $fillable = [
        'title',
        'due_date',
        'due_time',
        'priority',
        'completed_at',
    ];

    protected $attributes = [
        'priority' => 'medium',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'priority' => WorkspacePriority::class,
        ];
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isOverdue(?CarbonInterface $today = null): bool
    {
        return ! $this->isCompleted()
            && $this->due_date
            && WorkspaceDates::day($this->due_date)->lt($today ?? WorkspaceDates::today());
    }

    /** "Hoje, 10:00", "Amanhã", "20 out" */
    public function dueLabel(?CarbonInterface $today = null): ?string
    {
        if (! $this->due_date) {
            return null;
        }

        return WorkspaceDates::relativeDay($this->due_date, $today).($this->due_time ? ', '.$this->due_time : '');
    }

    /** @return array<string, mixed> estado do formulário de edição */
    public function toFormData(): array
    {
        return [
            'title' => $this->title,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'due_time' => $this->due_time,
            'priority' => $this->priority?->value,
            'is_completed' => $this->isCompleted(),
        ];
    }
}
