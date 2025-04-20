<?php

namespace App\Enums;

enum FeatureType : string {
    case SMS = 'SMS';
    case CALL = 'CALL';
    case INTERNET = 'INTERNET';
}