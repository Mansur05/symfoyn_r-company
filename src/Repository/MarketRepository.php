<?php

namespace App\Repository;

use App\Entity\Market;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Market|null find($id, $lockMode = null, $lockVersion = null)
 * @method Market|null findOneBy(array $criteria, array $orderBy = null)
 * @method Market[]    findAll()
 * @method Market[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Market::class);
    }

    public function getAllMarketNames()
    {
        $markets = $this->findAll();
        if (null === $markets)
            return null;

        $names = [];
        foreach ($markets as $market) {
            $names[] = $market->getName();
        }

        return (empty($names)) ? null : $names;
    }

    public function findByName(string $marketName)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.name = :marketName')
            ->setParameter('marketName', $marketName)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getHolidaysTime(Market $market)
    {
        $holidays = $market->getHolidays()->toArray();
        foreach ($holidays as $key => $holiday) {
            unset($holidays[$key]);

            $holidays[$holiday->getTime()->format('Y-m-d')] = [];
        }

        return $holidays;
    }

    public function prepareWorkTimeForOpeningHours(Market $market)
    {
        $workTime = json_decode($market->getWorkTime(), true);
        date_default_timezone_set($market->getTimezone());

        foreach ($workTime as $key => $item) {
            unset($workTime[$key]['start']);
            unset($workTime[$key]['end']);

            if (null === $item['start'] || null === $item['end']) {
                continue;
            }

            $workTime[$key] = [date('H:i', $item['start']) . '-' . date('H:i', $item['end'])];
        }

        return $workTime;
    }
}
