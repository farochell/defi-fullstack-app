<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\UI\Http\Rest\Input;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

class LoginInput {
    #[ApiProperty(
        description: 'Email de l\'utilisateur',
        required: true
    )]
    #[Assert\Email]
    #[Assert\NotBlank(
        message: "Veuillez fournir un email valide."
    )]
    public string $email;

    #[ApiProperty(
        description: 'Mot de passe de l\'utilisateur',
        required: true
    )]
    #[Assert\NotBlank(
        message: "Veuillez fournir un mot de passe."
    )]
    public string $password;
}
