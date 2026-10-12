<?php

namespace App\Models;

use App\Enums\WorkspaceColor;
use App\Models\Concerns\OwnedByUser;
use Illuminate\Database\Eloquent\Model;

/**
 * Nota rápida do Meu Workspace.
 */
class WorkspaceNote extends Model
{
    use OwnedByUser;

    protected $fillable = [
        'title',
        'content',
        'color',
    ];

    protected $attributes = [
        'color' => 'gray',
    ];

    protected function casts(): array
    {
        return [
            'color' => WorkspaceColor::class,
        ];
    }

    /** @return array<string, mixed> estado do formulário de edição */
    public function toFormData(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'color' => $this->color?->value,
        ];
    }
}
