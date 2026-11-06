<?php

namespace App\Enums;

enum ApiStatusCode:int {
	case SUCCESS = 100;
	case ERROR   = 200;
	case VALIDATION_ERROR = 422;
	case NOT_FOUND = 404;
}
