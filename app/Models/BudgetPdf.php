<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BudgetPdf extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'budget_id',
        'filename',
        'path',
        'download_token',
        'token_expires_at',
        'download_count',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'token_expires_at' => 'datetime',
        'is_active'        => 'boolean',
        'download_count'   => 'integer',
    ];

    /**
     * Get the budget that owns the PDF.
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Generate a unique download token for this PDF
     *
     * @param int $expirationMinutes Minutes until token expires (default: 1440 - 24 hours)
     * @return string The generated token
     */
    public function generateDownloadToken(int $expirationMinutes = 1440): string
    {
        $token = Str::random(64);
        $expiresAt = now()->addMinutes($expirationMinutes);

        $this->update([
            'download_token'   => $token,
            'token_expires_at' => $expiresAt,
        ]);

        return $token;
    }

    /**
     * Generate a download URL for this PDF
     *
     * @param int $expirationMinutes Minutes until URL expires (default: 1440 - 24 hours)
     * @return string The download URL
     */
    public function getDownloadUrl(int $expirationMinutes = 1440): string
    {
        // Generate a fresh token if needed
        if (empty($this->download_token) || $this->token_expires_at === null || $this->token_expires_at->isPast()) {
            $this->generateDownloadToken($expirationMinutes);
        }

        return URL::route('budget.pdf.download', ['token' => $this->download_token]);
    }

    /**
     * Check if the file exists in storage
     *
     * @return bool
     */
    public function fileExists(): bool
    {
        return Storage::disk('public')->exists($this->path);
    }

    /**
     * Increment the download count
     *
     * @return void
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Get the full path to the PDF file
     *
     * @return string
     */
    public function getFullPath(): string
    {
        return Storage::disk('public')->path($this->path);
    }
}
