<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Notice;
use AppBundle\Form\NoticeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \DateTime;

/**
 * Notice controller.
 *
 * @Route("/notice/")
 */
class GeneralController extends Controller
{
    /**
     * @Route("/register/confirmed/")
     */
    public function redirectAction()
    {
        return new Response ($this->redirect($this->generateUrl("")));
    }
}