<?php

namespace App\Models;

use App\Enums\WorkspaceColor;
use App\Models\Concerns\OwnedByUser;
use Illuminate\Database\Eloquent\Model;

/**
 * Compromisso pessoal do Meu Workspace (calendário).
 */
class WorkspaceEvent extends Model
{
    use OwnedByUser;

    protected $fillable = [
        'title',
        'date',
        'all_day',
        'starts_at',
        'ends_at',
        'location',
        'color',
        'notes',
    ];

    protected $attributes = [
        'all_day' => false,
        'color' => 'primary',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'all_day' => 'boolean',
            'color' => WorkspaceColor::class,
        ];
    }

    /** "10:00 – 12:00", "10:00" ou "Dia inteiro" */
    public function timeLabel(): string
    {
        if ($this->all_day || ! $this->starts_at) {
            return 'Dia inteiro';
        }

        return $this->starts_at.($this->ends_at ? ' – '.$this->ends_at : '');
    }

    /** @return array<string, mixed> estado do formulário de edição */
    public function toFormData(): array
    {
        return [
            'title' => $this->title,
            'date' => $this->date?->format('Y-m-d'),
            'all_day' => $this->all_day,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'location' => $this->location,
            'color' => $this->color?->value,
            'notes' => $this->notes,
        ];
    }
}
