<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Translatable
{
    /**
     * Override the getAttribute method to use the translated attribute.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $locale = app()->getLocale();

        if (array_key_exists($key . "_{$locale}", $this->attributes)) {
            return $this->attributes[$key . "_{$locale}"];
        }

        return parent::getAttribute($key);
    }

    public function getCreatedAtAttribute(): string
    {
        return date('Y-m-d h:i A', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute(): string
    {
        return date('Y-m-d h:i A', strtotime($this->attributes['updated_at']));
    }
}
