<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\UI\Http\Rest\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserInput
{
    #[Assert\NotBlank(message: "Champ email requis.")]
    #[Assert\Email(message: "Adresse email invalide.")]
    public string $email;

    #[Assert\NotBlank(message: "Veuillez fournir un mot de passe.")]
    public string $password;
}
