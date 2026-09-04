<?php

namespace App\Services;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use Throwable;

class MongoOrderStatistics
{
    private const COLLECTION =
    'menu_order_statistics';

    /* -------------------------------------------------- */
    /* synchronisation */
    /* -------------------------------------------------- */

    /* Synchronise le nombre de commandes par menu dans MongoDB. */
    public static function synchronize(
        array $statistics
    ): bool {
        try {
            $manager =
                MongoDatabase::getManager();

            $namespace =
                MongoDatabase::getDatabaseName()
                . '.'
                . self::COLLECTION;

            $bulkWrite =
                new BulkWrite();

            foreach ($statistics as $statistic) {
                $menuId =
                    (int) $statistic['menu_id'];

                $bulkWrite->update(
                    [
                        'menu_id' => $menuId,
                    ],
                    [
                        '$set' => [
                            'menu_id' => $menuId,
                            'menu_title' =>
                            (string)
                            $statistic['menu_title'],
                            'order_count' =>
                            (int)
                            $statistic['order_count'],
                            'updated_at' =>
                            date(DATE_ATOM),
                        ],
                    ],
                    [
                        'upsert' => true,
                    ]
                );
            }

            $manager->executeBulkWrite(
                $namespace,
                $bulkWrite
            );

            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }

    /* -------------------------------------------------- */
    /* lecture MongoDB */
    /* -------------------------------------------------- */

    /* Récupère les statistiques directement depuis MongoDB. */
    public static function getAll(): ?array
    {
        try {
            $manager =
                MongoDatabase::getManager();

            $namespace =
                MongoDatabase::getDatabaseName()
                . '.'
                . self::COLLECTION;

            $query =
                new Query(
                    [],
                    [
                        'sort' => [
                            'order_count' => -1,
                            'menu_title' => 1,
                        ],
                    ]
                );

            $cursor =
                $manager->executeQuery(
                    $namespace,
                    $query
                );

            $statistics = [];

            foreach ($cursor as $document) {
                $statistics[] = [
                    'menu_id' =>
                    (int) $document->menu_id,
                    'menu_title' =>
                    (string) $document->menu_title,
                    'order_count' =>
                    (int) $document->order_count,
                ];
            }

            return $statistics;
        } catch (Throwable $exception) {
            return null;
        }
    }
}
