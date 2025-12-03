<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Shared\Application;

use JsonSerializable;

abstract class SerializableResponse implements  JsonSerializable {}
