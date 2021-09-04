<?php

namespace App\Contract;

Trait NumberTrait {

    /**
     * Get next number
     *
     * @return string
     */
    protected function _nextNumber()
    {
        $prefix = $this->number_prefix ?? '';
        $count = self::query()->select(['id'])->withTrashed()->count() + 1;
        $number = substr('00000' . $count, -6, 6);
        $number = $prefix . $number;

        return $number;
    }
}
