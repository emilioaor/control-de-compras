<?php

namespace App\Contract;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

Trait WeekTrait {

    /**
     * Get the range date to this week
     *
     * @return array
     */
    public static function weekRange()
    {
        $start = Carbon::now()->startOfDay();

        while ($start->format('l') !== 'Sunday') {
            $start->subDay();
        }

        $end = clone $start;
        $end->addDays(6)->endOfDay();

        return [$start, $end];
    }

    /**
     * Is open week?
     *
     * @return bool
     */
    public function isOpenWeek()
    {
        $dates = self::weekRange();

        return $this->created_at >= $dates[0] && $this->created_at <= $dates[1];
    }

    /**
     * This week, From sunday to saturday
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', self::weekRange());
    }
}
