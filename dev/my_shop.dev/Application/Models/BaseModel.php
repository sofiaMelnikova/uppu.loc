<?php

namespace Application\Models;

use Application\TableDataGateway\Goods;
use Engine\DbQuery;

class BaseModel
{
    /**
     * @return Goods
     */
    public function newGoods () {
        return new Goods(new DbQuery());
    }
}