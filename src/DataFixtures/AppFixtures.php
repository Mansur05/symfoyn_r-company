<?php

namespace App\DataFixtures;

use App\Entity\Holiday;
use App\Entity\Market;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        date_default_timezone_set('America/New_York');

        $this->loadHolidays($manager, [
            '2020-11-26',
            '2020-12-25',
            '2021-01-01',
            '2021-01-18',
            '2021-02-15',
            '2021-04-02',
            '2021-05-31',
            '2021-07-05',
            '2021-09-06',
            '2021-11-25',
            '2021-12-24',
            '2022-01-17',
            '2022-02-21',
            '2022-04-15',
            '2022-05-30',
            '2022-07-04',
            '2022-09-05',
            '2022-11-24',
            '2022-12-26',
        ]);

        $this->loadMarket($manager, [
            'name' => 'NASDAQ',
            'worktime' => [
                    'monday'     => [
                        'start' => date('U', mktime(9, 30)),
                        'end' => date('U', mktime(16, 00))
                    ],
                    'tuesday'    => [
                        'start' => date('U', mktime(9, 30)),
                        'end' => date('U', mktime(16, 00))
                    ],
                    'wednesday'  => [
                        'start' => date('U', mktime(9, 30)),
                        'end' => date('U', mktime(16, 00))
                    ],
                    'thursday'   => [
                        'start' => date('U', mktime(9, 30)),
                        'end' => date('U', mktime(16, 00))
                    ],
                    'friday'     => [
                        'start' => date('U', mktime(9, 30)),
                        'end' => date('U', mktime(16, 00))
                    ],
                    'saturday'   => [
                        'start' => null,
                        'end' => null
                    ],
                    'sunday'     => [
                        'start' => null,
                        'end' => null
                    ],
                ],
        ]);

        $this->loadMarket($manager, [
            'name' => 'NYSE',
            'worktime' => [
                'monday'     => [
                    'start' => date('U', mktime(9, 30)),
                    'end' => date('U', mktime(16))
                ],
                'tuesday'    => [
                    'start' => date('U', mktime(9, 30)),
                    'end' => date('U', mktime(16))
                ],
                'wednesday'  => [
                    'start' => date('U', mktime(9, 30)),
                    'end' => date('U', mktime(16))
                ],
                'thursday'   => [
                    'start' => date('U', mktime(9, 30)),
                    'end' => date('U', mktime(16))
                ],
                'friday'     => [
                    'start' => date('U', mktime(9, 30)),
                    'end' => date('U', mktime(16))
                ],
                'saturday'   => [
                    'start' => null,
                    'end' => null
                ],
                'sunday'     => [
                    'start' => null,
                    'end' => null
                ],
            ],
        ]);
    }

    public function loadHolidays(ObjectManager $manager, array $holidays)
    {
        foreach ($holidays as $holiday) {
            $hd = new Holiday();
            $hd->setTime(new DateTime($holiday));
            $manager->persist($hd);
        }

        $manager->flush();
    }

    public function loadMarket(ObjectManager $manager, array $market)
    {
        $m = new Market();
        $m->setName($market['name'])
            ->setWorkTime(json_encode($market['worktime'], JSON_UNESCAPED_UNICODE))
            ->setTimezone('America/New_York')
        ;

        $holidays = $manager->getRepository(Holiday::class)->findAll();
        foreach ($holidays as $holiday) {
            $m->addHoliday($holiday);
        }

        $manager->persist($m);

        $manager->flush();
    }
}
