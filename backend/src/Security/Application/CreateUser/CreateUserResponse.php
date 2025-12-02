<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Application\CreateUser;

use App\Shared\Application\SerializableResponse;

class CreateUserResponse extends SerializableResponse {
    public function __construct(public string $id, public string $email) {}
    public function jsonSerialize(): mixed {
       return [
           'id' => $this->id,
           'email' => $this->email
       ];
    }
}
