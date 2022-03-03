<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController
{
    /**
     * @Route("/search")
     * @return Response
     */
    public function search(): Response
    {
        return new Response('hello world');
    }
}