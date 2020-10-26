<?php

namespace App\Controller;

use App\Entity\Market;
use DateTime;
use Spatie\OpeningHours\OpeningHours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }


    /**
     * @Route("/watch", methods={"POST"})
     */
    public function watch(Request $request): Response
    {
        if (null === $request->request->get('marketName'))
            return new Response(null, 400);


        $marketRepository = $this->getDoctrine()->getRepository(Market::class);
        $market = $marketRepository->findByName(mb_strtoupper($request->request->get('marketName')));
        if (null === $market)
            return new Response(null, 400);

        $openingHours = OpeningHours::create(array_merge(
            $marketRepository->prepareWorkTimeForOpeningHours($market),
            array('exceptions' => $marketRepository->getHolidaysTime($market))
        ));

        date_default_timezone_set($market->getTimezone());
        $currentTime = new DateTime();

        if ($openingHours->currentOpenRange($currentTime)) {
            $result['status'] = true;
            $result['time'] = $openingHours->currentOpenRange($currentTime)->end()->format('U');
            $result['timezone'] = $market->getTimezone();
        } else {
            $result['status'] = false;
            $result['time'] = $openingHours->nextOpen($currentTime)->format('U');
            $result['timezone'] = $market->getTimezone();
        }

        return $this->json($result);
    }


    /**
     * @Route("/close", methods={"POST"})
     */
    public function closeMarket(Request $request): Response
    {
        if (null === $request->request->get('marketName'))
            return new Response('Empty marketName!', 400);

        $market = $this->getDoctrine()->getRepository(Market::class)->findByName(mb_strtoupper($request->request->get('marketName')));
        if (null === $market)
            return new Response('Can not find market by name = ' . $request->request->get('marketName'), 400);

        date_default_timezone_set($market->getTimezone());
        $currentTime = new DateTime();

        $weekday = mb_strtolower($request->request->get('weekday', 'current'));
        if ('current' === $weekday)
            $weekday = mb_strtolower($currentTime->format('l'));

        $closeTime = mb_strtolower($request->request->get('closeTime', 'now'));
        if ('now' === $closeTime)
            $closeTime = $currentTime->format('U');

        $workTime = json_decode($market->getWorktime(), true);

        if ($closeTime > $workTime[$weekday]['start'])
            return new Response('Close Time (' . date('H:i', $closeTime) . ') must be more that Market Open Time (' . date('H:i', $workTime[$weekday]['start']) . ')', 400);

        $workTime[$weekday]['end'] = $closeTime;


        $market->setWorkTime(json_encode($workTime, JSON_UNESCAPED_UNICODE));
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($market);
        $manager->flush();

        return new Response('Close Market Time set to ' . date('H:i', $workTime[$weekday]['end']), 200);
    }


    /**
     * @Route("/open", methods={"POST"})
     */
    public function openMarket(Request $request): Response
    {
        if (null === $request->request->get('marketName'))
            return new Response('Empty marketName!', 400);

        $market = $this->getDoctrine()->getRepository(Market::class)->findByName(mb_strtoupper($request->request->get('marketName')));
        if (null === $market)
            return new Response('Can not find market by name = ' . $request->request->get('marketName'), 400);

        date_default_timezone_set($market->getTimezone());
        $currentTime = new DateTime();

        $weekday = mb_strtolower($request->request->get('weekday', 'current'));
        if ('current' === $weekday)
            $weekday = mb_strtolower($currentTime->format('l'));

        $openTime = mb_strtolower($request->request->get('openTime', 'now'));
        if ('now' === $openTime)
            $openTime = $currentTime->format('U');

        $workTime = json_decode($market->getWorktime(), true);

        if ($openTime < $workTime[$weekday]['end'])
            return new Response('Open Time (' . date('H:i', $openTime) . ') must be less that Market Close Time (' . date('H:i', $workTime[$weekday]['end']) . ')', 400);

        $workTime[$weekday]['start'] = $openTime;


        $market->setWorkTime(json_encode($workTime, JSON_UNESCAPED_UNICODE));
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($market);
        $manager->flush();

        return new Response('Open Market Time set to ' .  date('H:i', $workTime[$weekday]['start']), 200);
    }

}
