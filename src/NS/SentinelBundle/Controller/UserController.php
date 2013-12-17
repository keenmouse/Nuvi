<?php
namespace NS\SentinelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use NS\SentinelBundle\Form\MeningitisType;

/**
 * Description of UserController
 *
 * @author gnat
 * @Route("/{_locale}")
 */
class UserController extends Controller
{
    /**
     * @Template()
     */
    public function regionDashboardAction()
    {
        return array();
    }

    /**
     * @Template()
     */
    public function countryDashboardAction()
    {
        return array();
    }

    /**
     * @Template()
     */
    public function siteDashboardAction()
    {
        return array();
    }

    /**
     * @Template()
     */
    public function labDashboardAction()
    {
        return array();
    }
}
