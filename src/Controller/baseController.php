<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class  baseController extends AbstractController
{
    #[Route('/admin', name:'admin_index')]

    public function openAdminIndex(): Response
    {
        
         
        return $this->render('admin/index.html.twig');
    }
}