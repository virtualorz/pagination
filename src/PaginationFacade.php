<?php

namespace Virtualorz\Pagination;

use Illuminate\Support\Facades\Facade;

/**
 * @see Virtualorz\Pagination\Pagination
 */
class PaginationFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'pagination';
    }

}
