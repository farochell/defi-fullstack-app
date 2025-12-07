<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Phpstan\Rules\Application;

use PhpParser\Node;
use PhpParser\Node\UseItem;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<UseItem>
 */
class NoInfrastructureInApplicationRule implements Rule
{
    public function getNodeType(): string
    {
        return UseItem::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (
            false === str_contains($scope->getNamespace(), "\Application\\")
            && false === str_ends_with($scope->getNamespace(), "\Application\\")
        ) {
            return [];
        }

        if (!in_array('Infrastructure', $node->name->getParts(), true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Application layer should not depend on Infrastructure layer.'
            )->identifier('cleanArchitecture.noInfrastructureInDomain')->build(),
        ];
    }
}
