<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Url;
use AppBundle\Entity\UrlHit;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        // Display the homepage, to allow them to create a new shortened URL
        return $this->render(
            'shortener/index.html.twig',
            [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]
        );
    }

    /**
     * @Route("/{slug}", name="url_redirect")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param string $slug
     * @return null
     */
    public function redirectAction(Request $request, $slug)
    {
        $expirationTime = new DateTime();
        $expirationTime = $expirationTime->modify('-1 year');
        $expirationTime = $expirationTime->getTimestamp();

        $doctrine = $this->getDoctrine();

        // Find the record based on the url slug
        $url = $doctrine->getRepository(Url::class)
            ->findOneBy(['slug' => $slug]);
        
        // If no match is found for the slug, send them to the homepage (or maybe a 404 page...)
        if (empty($url) || $url->getCreated() < $expirationTime) {
            return $this->redirectToRoute('homepage');
        }

        // Create a record to document the hit on this URL
        $urlHit = new UrlHit();
        $urlHit->setUrl($url);
        $urlHit->setIp($request->getClientIp());
        $urlHit->setAccessed(new DateTime());
        $urlHit->setReferrer($request->server->get('HTTP_REFERER'));

        $doctrine->getManager()->persist($urlHit);
        $doctrine->getManager()->flush();

        return $this->redirect($url->getOriginalUrl());
    }
    
    /**
     * @Route("/create", name="create")
     * @Method({"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        /** @var LoggerInterface $logger */
        $logger = $this->get('logger');
        $url = '';
        $doctrine = $this->getDoctrine();
        
        $data = json_decode($request->getContent(), true);
        $logger->debug('Incoming create request for URL: '.$data['source']);
        
        if (false === filter_var($data['source'], FILTER_VALIDATE_URL)) {
            return new Response('Invalid URL format.<br />Please be sure to include the full URL (ex. http://google.com)');
        }

        // Create or update the new entity and persist to DB
        $url = $this->createUrl($data['source']);
        
        $doctrine->getManager()->persist($url);
        $doctrine->getManager()->flush();
        
        $shortUrl = $url->getShortURL();
        return new Response('Your Shortened URL is: <a href="'.$shortUrl.'" target="_blank" class="alert-link">'.$shortUrl.'</a>');
    }

    /**
     * @Route("/stats/{slug}", name="url_stats")
     * @Method({"GET"})
     *
     * @param string $slug
     * @return null
     */
    public function statisticsAction($slug)
    {
        $doctrine = $this->getDoctrine();

        // Find the record based on the url slug
        /** @var Url $url */
        $url = $doctrine->getRepository(Url::class)
            ->findOneBy(['slug' => $slug]);

        // If no match is found for the slug, send them to the homepage (or maybe a 404 page...)
        if (empty($url)) {
            return $this->redirectToRoute('homepage');
        }

        $hits = $doctrine->getRepository(UrlHit::class)
            ->createQueryBuilder('h')
            ->where('h.url = :url_id')
            ->setParameter('url_id', $url->getId())
            ->orderBy('h.accessed', 'DESC')
            ->getQuery()
            ->getResult();

        $months = [];

        /** @var UrlHit $hit */
        foreach($hits as $hit) {
            $month = $hit->getAccessed()->format('M');

            if (!isset($months[$month])) {
                $months[$month] = 0;
            }

            $months[$month]++;
        }

        // Display the statistics page for a given slug
        return $this->render(
            'shortener/stats.html.twig',
            [
                'url' => $url,
                'stats' => $hits,
                'months' => $months,
                'max_hits' => max($months),
            ]
        );
    }


        /**
     * Private helper method to generate a random alphanumeric string for the slug
     *
     * @param int $length
     * @return string
     */
    private function randomKey($length)
    {
        $key = '';
        $pool = array_merge(range(0, 9), range('a', 'z'));

        for($i = 0; $i < $length; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }
        
        return $key;
    }
    
    /**
     * Create a new URL entity or re-purpose an old expired entity
     * @param string $sourceUrl
     * @return Url
     */
    private function createUrl($sourceUrl)
    {
        $valid = false;
        $url = '';
        $expirationTime = new DateTime();
        $expirationTime = $expirationTime->modify('-1 year');
        $expirationTime = $expirationTime->getTimestamp();
        
        $doctrine = $this->getDoctrine();
        
        while(false === $valid) {
            $slug = $this->randomKey(8);
            $url = $doctrine->getRepository(Url::class)
                ->findOneBy(['slug' => $slug]);
            
            if (empty($url)) {
                $url = new Url();
                $valid = true;
            } elseif ($url->getCreated() < $expirationTime) {
                $valid = true;
            }
        }
        
        $shortUrl = $this->getParameter('redirect_base_url').'/'.$slug;
        $url->setOriginalURL($sourceUrl);
        $url->setShortURL($shortUrl);
        $url->setSlug($slug);
        $url->setCreated(time());

        return $url;
    }
}
