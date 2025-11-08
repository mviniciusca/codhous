<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BudgetDocument extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('budget')
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'document_attached',
                'deleted' => 'document_removed',
                default   => $eventName,
            });
    }

    /**
     * Tap into activity log to associate with budget
     */
    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity, string $eventName)
    {
        // Log on the budget instead of the document
        if ($this->budget) {
            $activity->subject()->associate($this->budget);

            $userName = \Illuminate\Support\Facades\Auth::user()?->name ?? 'System';
            $description = match ($eventName) {
                'created' => "Documento anexado",
                'deleted' => "Documento removido",
                default   => $eventName,
            };

            $activity->description = $description;
        }
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the full path to the file
     */
    public function getFullPath(): string
    {
        return Storage::disk('public')->path($this->file_path);
    }

    /**
     * Check if file exists
     */
    public function fileExists(): bool
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Get download URL
     */
    public function getDownloadUrl(): string
    {
        return asset('storage/'.$this->file_path);
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::disk('public')->delete($this->file_path);
        }

        return false;
    }

    /**
     * Boot method to handle file deletion
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }
}
