<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SerialNumberGenerator extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'serial_number_generators';
    public $timestamps = true;

    protected $fillable = [
        'type',
        'current_number',
        'prefix',
        'padding',
        'separator',
        'format',
    ];

    /**
     * Generate next serial number
     */
    public static function generate($type)
    {
        $generator = static::where('type', $type)->firstOrFail();
        $generator->increment('current_number');

        return $generator->formatNumber($generator->current_number);
    }

    /**
     * Format the number according to pattern
     */
    public function formatNumber($number)
    {
        $paddedNumber = str_pad($number, $this->padding, '0', STR_PAD_LEFT);

        if ($this->format) {
            return str_replace('{number}', $paddedNumber, $this->format);
        }

        $parts = [];
        if ($this->prefix) {
            $parts[] = $this->prefix;
        }
        $parts[] = $paddedNumber;

        return implode($this->separator, $parts);
    }

    /**
     * Get the next number that will be generated
     */
    public function getNextNumber()
    {
        return $this->formatNumber($this->current_number + 1);
    }
}
