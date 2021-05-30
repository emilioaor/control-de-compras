<?php

namespace App\Contract;

Trait NumberTrait {

    /**
     * initializeNumberTrait
     */
    public function initializeNumberTrait()
    {
        static::registerModelEvent('creating', function () {
            $this->nextNumber();
        });
    }

    /**
     * Next number
     */
    public function nextNumber()
    {
        $prefix = $this->number_prefix ?? '';
        $count = self::query()->select(['id'])->withTrashed()->count() + 1;
        $number = substr('00000' . $count, -6, 6);
        $number = $prefix . $number;

        $this->number = $number;
    }
}
