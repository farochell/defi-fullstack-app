<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Route\Infrastructure\Repository;

use App\Route\Domain\Entity\Route;
use App\Route\Domain\Entity\Station;
use App\Route\Domain\Repository\RouteRepositoryInterface;
use App\Route\Domain\ValueObject\AnalyticDistance;
use App\Route\Domain\ValueObject\AnalyticDistances;
use App\Route\Domain\ValueObject\GroupBy;
use App\Route\Infrastructure\Doctrine\Mapping\DoctrineRoute;
use App\Shared\Domain\Exception\EntityPersistenceException;
use App\Shared\Infrastructure\Repository\BaseRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use function Lambdish\Phunctional\map;

/**
 * @extends BaseRepository<DoctrineRoute>
 */
class MysqlRouteRepository extends BaseRepository implements RouteRepositoryInterface
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        LoggerInterface $logger,
    ) {
        parent::__construct(
            $managerRegistry,
            DoctrineRoute::class,
            'Route',
            $logger
        );
    }

    public function save(Route $route): void
    {
        try {
            $entity = $this->toDoctrineEntity($route);
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            $exception = EntityPersistenceException::fromPrevious($this->entityName, $e);
            $this->logAndThrowException($exception, $route, [
                'data' => $this->serializeEntity($route)
            ]);
        }
    }

    public function getAnalyticDistances(
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
        ?GroupBy $groupBy = null
    ): AnalyticDistances {
        $conn = $this->getEntityManager()->getConnection();
        if (null === $from || null === $to) {
            $minMaxSql = 'SELECT MIN(created_at) AS min_dt, MAX(created_at) AS max_dt FROM route';
            $minMax = $conn->fetchAssociative($minMaxSql);

            if (!$minMax || $minMax['min_dt'] === null || $minMax['max_dt'] === null) {
                return new AnalyticDistances([]);
            }

            $from = $from ?? new DateTimeImmutable($minMax['min_dt']);
            $to = $to ?? new DateTimeImmutable($minMax['max_dt']);
        }

        $fromStr = $from->format('Y-m-d 00:00:00');
        $toStr = $to->format('Y-m-d 23:59:59');
        $groupExpr = null;

        if ($groupBy) {
            $groupExpr = match ($groupBy) {
                GroupBy::DAY => "DATE_FORMAT(created_at, '%Y-%m-%d')",
                GroupBy::MONTH => "DATE_FORMAT(created_at, '%Y-%m')",
                GroupBy::YEAR => "DATE_FORMAT(created_at, '%Y')",
                GroupBy::NONE => null,
            };
        }

        $selectFields = [
            'analytic_code AS analyticCode',
            'SUM(distance_km) AS totalDistanceKm',
            'DATE(MIN(created_at)) AS periodStart',
            'DATE(MAX(created_at)) AS periodEnd'
        ];

        $groupByFields = ['analytic_code'];
        $orderByFields = ['analytic_code'];

        if ($groupExpr !== null) {
            array_splice($selectFields, 1, 0, ["{$groupExpr} AS `group`"]);
            $groupByFields[] = '`group`';
            $orderByFields[] = '`group`';
        }

        $sql = sprintf(
            'SELECT %s FROM route WHERE created_at BETWEEN :from AND :to GROUP BY %s ORDER BY %s',
            implode(', ', $selectFields),
            implode(', ', $groupByFields),
            implode(', ', $orderByFields)
        );

        $rows = $conn->fetchAllAssociative($sql, ['from' => $fromStr, 'to' => $toStr]);

        $analyticDistances = array_map(static function (array $r) use ($groupExpr): AnalyticDistance {
            return new AnalyticDistance(
                (string) $r['analyticCode'],
                (float) $r['totalDistanceKm'],
                (string) $r['periodStart'],
                (string) $r['periodEnd'],
                $groupExpr !== null ? (string) $r['group'] : null
            );
        }, $rows);

        return new AnalyticDistances($analyticDistances);
    }

    private function toDoctrineEntity(Route $route): DoctrineRoute {
        $paths = map(
            static fn(Station $station) => [
                'id' => $station->id,
                'shortName' => $station->shortName,
                'longName' => $station->longName
            ],
            $route->path->getIterator()->getArrayCopy()
        );

        $doctrineRoute = new DoctrineRoute();
        $doctrineRoute->id = $route->id;
        $doctrineRoute->fromStation = $route->fromStation->shortName;
        $doctrineRoute->toStation = $route->toStation->shortName;
        $doctrineRoute->distanceKm = $route->distanceKm;
        $doctrineRoute->analyticCode = $route->analyticCode;
        $doctrineRoute->path = $paths;
        $doctrineRoute->createdAt = $route->createdAt;

        return $doctrineRoute;
    }
}
