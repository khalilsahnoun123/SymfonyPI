<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminCovController extends AbstractController
{
    #[Route('/admin/cov', name: 'app_admin_cov_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin_cov/dashboard.html.twig');
    }
}
