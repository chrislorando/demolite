<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ResponseStatus;

/**
 * App\Models\CurriculumVitae
 *
 * Represents an uploaded CV and its analysis results.
 */
class CurriculumVitae extends Model
{
    use HasFactory, HasUuids;

    /**
     * The primary key is a UUID string, not an incrementing integer.
     */
    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The table associated with the model.
     */
    protected $table = 'curriculum_vitaes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'model_id',
        'job_offer',
        'job_position',
        'file_name',
        'file_size',
        'file_url',
        'skill_match',
        'experience_match',
        'education_match',
        'overall_score',
        'job_offer',
        'summary',
        'suggestion',
        'cover_letter',
        'is_recommended',
        'response',
        'status',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'skill_match' => 'float',
        'experience_match' => 'float',
        'education_match' => 'float',
        'overall_score' => 'float',
        'is_recommended' => 'boolean',
        'response' => 'array',
        'status' => ResponseStatus::class,
    ];

    /**
     * The user that owns this CV (if any).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
