<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $label
 * @property string $name
 * @property string $filename_woff2
 * @property string|null $filename_ttf
 * @property string|null $filename_otf
 * @property string $preview
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $system_name
 * @method static \Database\Factories\FontFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereFilenameOtf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereFilenameTtf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereFilenameWoff2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font wherePreview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereSystemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Font extends Model
{
    /** @use HasFactory<\Database\Factories\FontFactory> */
    use HasFactory;

    protected $fillable = ['label', 'name', 'filename_woff2', 'filename_ttf', 'filename_otf', 'preview'];
}
