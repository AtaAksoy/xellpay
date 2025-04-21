<?php

namespace App\Traits;

trait ArrayConvertable {

    public function toArray() {
        return get_object_vars($this);
    }

}