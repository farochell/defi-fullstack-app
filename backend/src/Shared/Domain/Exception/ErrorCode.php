<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

enum ErrorCode: string
{
    case AUTH_MISSING_TOKEN = 'AUTH_MISSING_TOKEN';
    case AUTH_FORBIDDEN = 'AUTH_FORBIDDEN';
    case STATION_NOT_FOUND = 'STATION_NOT_FOUND';
    case ROUTE_NOT_FOUND = 'ROUTE_NOT_FOUND';
    case NETWORK_DISCONNECTED = 'NETWORK_DISCONNECTED';
    case ANALYTIC_INVALID_PERIOD = 'ANALYTIC_INVALID_PERIOD';
    case UNKNOWN_ERROR = 'UNKNOWN_ERROR';
    case MISSING_PARAMETERS = 'MISSING_PARAMETERS';
    case ENTITY_NOT_FOUND = 'ENTITY_NOT_FOUND';
    case STATIONS_FILE_NOT_FOUND = 'STATIONS_FILE_NOT_FOUND';
    case STATIONS_FILE_EMPTY = 'STATIONS_FILE_EMPTY';
    case STATIONS_FILE_INVALID_JSON = 'STATIONS_FILE_INVALID_JSON';
    case DISTANCES_FILE_NOT_FOUND = 'DISTANCES_FILE_NOT_FOUND';
    case DISTANCES_FILE_INVALID_JSON = 'DISTANCES_FILE_INVALID_JSON';
    case DISTANCES_FILE_EMPTY = 'DISTANCES_FILE_EMPTY';
    case NEGATIVE_DISTANCE = 'NEGATIVE_DISTANCE';
    case EMPTY_PATH = 'EMPTY_PATH';
    case EXPIRED_TOKEN = 'EXPIRED_TOKEN';
    case INVALID_TOKEN = 'INVALID_TOKEN';
    case EMAIL_ALREADY_EXISTS = 'EMAIL_ALREADY_EXISTS';
    case INVALID_CREDENTIALS = 'INVALID_CREDENTIALS';
    case INVALID_ANALYTIC_CODE = 'INVALID_ANALYTIC_CODE';
    case ACCESS_FORBIDDEN = 'ACCESS_FORBIDDEN';

    public static function toString(int $errorCode): string
    {
        return match ($errorCode) {
            404 => self::ROUTE_NOT_FOUND->value,
            default => self::UNKNOWN_ERROR->value
        };
    }
}
