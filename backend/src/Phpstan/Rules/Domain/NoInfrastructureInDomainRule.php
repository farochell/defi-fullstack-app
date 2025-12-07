<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Phpstan\Rules\Domain;

use PhpParser\Node;
use PhpParser\Node\UseItem;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<UseItem>
 */
class NoInfrastructureInDomainRule implements Rule
{
    public function getNodeType(): string
    {
        return UseItem::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (
            false === str_contains($scope->getNamespace(), "\Domain\\")
            && false === str_ends_with($scope->getNamespace(), '\\Domain')
        ) {
            return [];
        }

        if (!in_array('Infrastructure', $node->name->getParts(), true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Domain layer should not depend on Infrastructure layer.'
            )->identifier('cleanArchitecture.noInfrastructureInDomain')->build(),
        ];
    }
}
