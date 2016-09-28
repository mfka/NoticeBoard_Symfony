<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;



class GeneralController extends Controller
{
    /**
     * @Route("/")
     */
    public function mainRedirect() {
        return $this->redirectToRoute("");
    }
}
