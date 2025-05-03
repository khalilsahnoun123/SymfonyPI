<?php
// src/Controller/ReservationController.php

namespace App\Controller\transportpublic;

use App\Entity\transportpublic\User;
use App\Entity\transportpublic\Ligne;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\transportpublic\Reservation;
use App\Form\transportpublic\ReservationType;
use App\Form\transportpublic\ReservationStepOneType;
use App\Form\transportpublic\ReservationUserEditType;
use App\Form\transportpublic\ReservationStepTwoType;
use App\Form\transportpublic\ReservationEditTP;
use App\Repository\transportpublic\ReservationRepository;
use App\Repository\transportpublic\VehiculesRepository;
use App\Repository\transportpublic\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

use App\Service\FlouciClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/reservationTransportPublic')]
class ReservationController extends AbstractController
{
   

#[Route('/', name: 'app_reservation_index', methods: ['GET'])]
public function index(Request $request, ReservationRepository $repo): Response
{
    $category = $request->query->get('category');
    $status   = $request->query->get('status');
    $fromStr  = $request->query->get('from');
    $toStr    = $request->query->get('to');

    $from = $fromStr ? new \DateTime($fromStr) : null;
    $to   = $toStr   ? new \DateTime($toStr)   : null;

    if ($category || $status || $from || $to) {
        $reservations = $repo->findByFilters($category, $status, $from, $to);
    } else {
        $reservations = $repo->findAll();
    }

    return $this->render('Admin/admin_transportPublic/reservation/index.html.twig', [
        'reservations' => $reservations,
    ]);
}
#[Route('/mes-reservations/{id}/ticket', name: 'app_my_reservation_ticket', methods: ['GET'])]
public function ticket(Reservation $reservation): Response
{
    // only confirmed reservations can get a ticket
    if ($reservation->getStatus() !== 'CONFIRMED') {
        $this->addFlash('error', 'Seules les réservations confirmées peuvent produire un billet.');
        return $this->redirectToRoute('app_my_reservations');
    }
    
  

    $options = new Options();
    $options->set('defaultFont', 'Helvetica');
    $options->setIsRemoteEnabled(true);

    $dompdf = new Dompdf($options);
    // render the HTML of your voucher
    $html = $this->renderView('Front/Transportpublic/ticket.html.twig', [
        'reservation' => $reservation,
       
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return new Response(
        $dompdf->output(),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="ticket.pdf"',
        ]
    );
}
    #[Route('/mes-reservations/{id}', name: 'app_my_reservation_show', methods: ['GET'])]
    public function showMyReservation(Reservation $reservation): Response
    {
        // (Optionally verify ownership: if ($reservation->getUser()->getId() !== 1) { … })

        return $this->render('Front/Transportpublic/reservation_detail.html.twig', [
            'reservation' => $reservation,
           
        ]);
    }
    #[Route('/mes-reservations', name: 'app_my_reservations', methods: ['GET'])]
    public function myreservations(ReservationRepository $reservationRepository): Response
    {
        // always load reservations with ID = 1
        $reservations = $reservationRepository->findBy(['user' => 1], ['travel_date' => 'DESC']);


      

        return $this->render('Front/Transportpublic/my_reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET','POST'])]
    public function newReservationByAdmin(Request $request, EntityManagerInterface $em): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             // Calculate total price based on Ligne pricing
             $departStation = $reservation->getDepart_station();
             if ($departStation) {
                 $ligne = $departStation->getLigne();
                 if ($ligne) {
                     $category = strtoupper($reservation->getTicketCategory());
                     switch ($category) {
                         case 'VIP':
                             $unitPrice = $ligne->getPrixVip();
                             break;
                         case 'PREMIUM':
                             $unitPrice = $ligne->getPrixPremium();
                             break;
                         default:
                             $unitPrice = $ligne->getPrixEconomique();
                     }
                     $totalPrice = $unitPrice * $reservation->getNumberOfSeats();
                     $reservation->setTotalPrice($totalPrice)
                     ->setUser($this->getUser() ?? $em->getReference(User::class, 1));
                 }
             }
            $em->persist($reservation);
            $em->flush();

        
           
            $this->addFlash('success', 'Réservation créée.');
            return $this->redirectToRoute('app_reservation_index');
        }

        return $this->render('Admin/admin_transportPublic/reservation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
  
   
    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('Admin/admin_transportPublic/reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReservationEditTP::class,$reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $departStation = $reservation->getDepart_station();
            if ($departStation) {
                $ligne = $departStation->getLigne();
                if ($ligne) {
                    $category = strtoupper($reservation->getTicketCategory());
                    switch ($category) {
                        case 'VIP':
                            $unitPrice = $ligne->getPrixVip();
                            break;
                        case 'PREMIUM':
                            $unitPrice = $ligne->getPrixPremium();
                            break;
                        default:
                            $unitPrice = $ligne->getPrixEconomique();
                    }
                    $totalPrice = $unitPrice * $reservation->getNumberOfSeats();
                    $reservation->setTotalPrice($totalPrice);
                }
            }
            $em->flush();
            $this->addFlash('success', 'Réservation mise à jour.');
            return $this->redirectToRoute('app_reservation_index');
        }

        return $this->render('Admin/admin_transportPublic/reservation/edit.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getReservationId(), $request->request->get('_token'))) {
            $em->remove($reservation);
            $em->flush();
            $this->addFlash('success', 'Réservation supprimée.');
        }
        return $this->redirectToRoute('app_reservation_index');
    }
   



    #[Route('/res/new/step1', name: 'app_reservation_new_step1')]
    public function newStepOne(Request $request, StationRepository $stationRepo): Response
    {
        $form = $this->createForm(ReservationStepOneType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // fetch Ligne and vehicle availability
            $ligne        = $form->get('ligne')->getData();
            $vehicleType  = $form->get('vehicle_type')->getData();
            $available = $ligne->getVehicules()
                                ->filter(fn($v)=> $v->getType() === $vehicleType);

            if (count($available) === 0) {
                $this->addFlash('error', 'Aucun véhicule de type "'.$vehicleType.'" disponible pour cette ligne.');
                return $this->redirectToRoute('app_reservation_new_step1');
            }
        
            // store step1 in session
            $request->getSession()->set('res_step1', [
                'date'     => $data['travel_date'],
                'ligne_id' => $ligne->getId(),
                'category' => $data['ticket_category'],
                'veh_type' => $vehicleType,
            ]);

            return $this->redirectToRoute('app_reservation_new_step2');
        }

        return $this->render('Front/Transportpublic/new_step1.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/res/new/step2', name: 'app_reservation_new_step2')]
    public function newStepTwo(
        Request $request, MailerInterface $mailer,
        EntityManagerInterface $em,
        VehiculesRepository $vehRepo,
        StationRepository $stationRepo , FlouciClient $flouci
    ): Response {
        // retrieve stored step1
        $step1 = $request->getSession()->get('res_step1');
        if (!$step1) {
            return $this->redirectToRoute('app_reservation_new_step1');
        }

        // load stations for that ligne
        $stations = $stationRepo->findBy(['ligne' => $step1['ligne_id']]);

        $form = $this->createForm(ReservationStepTwoType::class, null, [
            'stations' => $stations
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // assemble final Reservation
            $res = new Reservation();
            $res->setTravelDate($step1['date'])
                ->setTicketCategory($step1['category'])
                ->setNumberOfSeats($form->get('number_of_seats')->getData())
                ->setDepartStation($form->get('depart_station')->getData())
                ->setFinStation($form->get('fin_station')->getData())
                ->setStatus('PENDING')
                ->setUser($this->getUser() ?? $em->getReference(User::class, 1)); // fallback to user #1

            // pick first matching vehicle
            $veh = $vehRepo->findOneBy([
                'ligne' => $step1['ligne_id'],
                'type'  => $step1['veh_type']
            ]);
            $res->setVehicule($veh);

            // calculate price
            $priceField = 'getPrix'.ucfirst(strtolower($step1['category']));
            $ligne = $em->getReference(Ligne::class, $step1['ligne_id']);

            $category = strtoupper($step1['category']);
            switch ($category) {
                case 'VIP':
                    $unitPrice = $ligne->getPrixVip();
                    break;
                case 'PREMIUM':
                    $unitPrice = $ligne->getPrixPremium();
                    break;
                case 'ECONOMIC':
                default:
                    $unitPrice = $ligne->getPrixEconomique();
            }
            $res->setTotalPrice($unitPrice * $res->getNumberOfSeats());
            
            $em->persist($res);
            $em->flush();
            try {
                $result = $flouci->generatePayment(
                    (int) ($res->getTotalPrice() * 1000),
                    $this->generateUrl('reservation_success', ['id'=>$res->getReservation_id()], UrlGeneratorInterface::ABSOLUTE_URL),
                    $this->generateUrl('reservation_fail',    ['id'=>$res->getReservation_id()], UrlGeneratorInterface::ABSOLUTE_URL),
                );
            } catch (Throwable $e) {
                $this->addFlash('error', 'Erreur de paiement : '.$e->getMessage());
                
            }
            
            $res
                ->setFlouciPaymentId($result['payment_id'])
                ->setPaymentLink($result['link']);
                $em->persist($res);
                $em->flush();


            $email = (new TemplatedEmail())
            ->from(new Address('sahnoun.khalil78@gmail.com'))            
            ->to('sahnoun.khalil78@gmail.com')                  
            ->subject('Nouvelle réservation créée')  
            ->htmlTemplate('Admin/admin_transportPublic/reservation/email.html.twig') 
            ->context([                               
                'reservation' => $res,
            ])
        ;
        $mailer->send($email);


            $this->addFlash('success', 'Réservation créée et en attente de confirmation.');
            return $this->redirect($result['link']);
          
         
        }

        return $this->render('Front/Transportpublic/new_step2.html.twig', [
            'form' => $form->createView(),
        ]);
    }

#[Route('/mes-reservations/{id}/edit', name: 'app_my_reservation_edit', methods: ['GET','POST'])]
public function editUserReservation(
    Reservation $reservation,
    Request $request,
    EntityManagerInterface $em
): Response {
    if ($reservation->getStatus() !== 'PENDING') {
        $this->addFlash('error', 'Only pending reservations can be modified.');
        return $this->redirectToRoute('app_my_reservations');
    }

    $ligne = $reservation->getDepartStation()->getLigne();
    $form  = $this->createForm(ReservationUserEditType::class, $reservation, [
        'ligne' => $ligne,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // recalc total price
        $cat = strtoupper($reservation->getTicketCategory());
        switch ($cat) {
            case 'VIP':
                $unit = $ligne->getPrixVip(); break;
            case 'PREMIUM':
                $unit = $ligne->getPrixPremium(); break;
            default:
                $unit = $ligne->getPrixEconomique();
        }
        $reservation->setTotalPrice($unit * $reservation->getNumberOfSeats());

        // keep it pending
        $reservation->setStatus('PENDING');

        $em->flush();
        $this->addFlash('success', 'Réservation mise à jour.');
        return $this->redirectToRoute('app_my_reservations');
    }

    return $this->render('Front/Transportpublic/edit_reservation.html.twig', [
        'form'        => $form->createView(),
        'reservation' => $reservation,
    ]);
}


    /**
     * “Cancel” a reservation by setting its status to CANCELLED
     */
    #[Route('/mes-reservations/{id}/cancel', name: 'app_my_reservation_cancel', methods: ['POST'])]
    public function cancelUserReservation(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('cancel' . $reservation->getReservationId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($reservation->getStatus() !== 'CANCELLED') {
            $reservation->setStatus('CANCELLED');
            $em->flush();
            $this->addFlash('success', 'Réservation annulée.');
        } else {
            $this->addFlash('error', 'Seules les réservations en attente et confirme peuvent être annulées.');
        }

        return $this->redirectToRoute('app_my_reservations');
    }





    #[Route('/reservations/export', name: 'reservation_export')]
    public function export(Request $request, ReservationRepository $repo): StreamedResponse
    {
        // 1) Récupérer filtres
        $category = $request->query->get('category');
        $status   = $request->query->get('status');
        $from     = $request->query->get('from') ? new \DateTime($request->query->get('from')) : null;
        $to       = $request->query->get('to')   ? new \DateTime($request->query->get('to'))   : null;
    
        // 2) Récupérer les réservations filtrées
        $reservations = $repo->findByFilters($category, $status, $from, $to);
    
        // 3) Calcul des statistiques
        $stats = [
            'status'   => ['PENDING'=>0,'CONFIRMED'=>0,'CANCELLED'=>0],
            'category' => ['ECONOMIC'=>0,'PREMIUM'=>0,'VIP'=>0],
            'lines'    => [], // [ 'Ligne A'=>count, ... ]
            'total_price' => 0.0,
            'total'    => count($reservations),
        ];
        foreach ($reservations as $r) {
            // status
            $s = $r->getStatus();
            if (isset($stats['status'][$s])) {
                $stats['status'][$s]++;
            }
            // category
            $c = strtoupper($r->getTicketCategory());
            if (isset($stats['category'][$c])) {
                $stats['category'][$c]++;
            }
            // ligne
            $l = $r->getDepartStation()->getLigne()->getName();
            $stats['lines'][$l] = ($stats['lines'][$l] ?? 0) + 1;
            // total price
            $stats['total_price'] += (float) $r->getTotalPrice();
        }
    
        // 4) Créer le document
        $spreadsheet = new Spreadsheet();
    
        // ** feuille principale “Reservations” **
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reservations');
    
        // 4.1) entêtes
        $headers = [
            'A1'=>'ID','B1'=>'Date','C1'=>'Places',
            'D1'=>'Catégorie','E1'=>'Statut',
            'F1'=>'Départ','G1'=>'Arrivée','H1'=>'Prix'
        ];
        foreach ($headers as $cell=>$text) {
            $sheet->setCellValue($cell, $text);
        }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')
              ->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFD3D3D3');
    
        // 4.2) largeur de colonnes
        foreach (range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        // 4.3) données
        $row = 2;
        foreach ($reservations as $r) {
            $sheet->setCellValue("A{$row}", $r->getReservationId());
            $sheet->setCellValue("B{$row}", $r->getTravelDate()->format('Y-m-d'));
            $sheet->setCellValue("C{$row}", $r->getNumberOfSeats());
            $sheet->setCellValue("D{$row}", $r->getTicketCategory());
            $sheet->setCellValue("E{$row}", $r->getStatus());
            $sheet->setCellValue("F{$row}", $r->getDepartStation()->getNom());
            $sheet->setCellValue("G{$row}", $r->getFinStation()->getNom());
            $sheet->setCellValue("H{$row}", $r->getTotalPrice());
            $row++;
        }
    
        // ** feuille “Stats” **
        $statsSheet = $spreadsheet->createSheet();
        $statsSheet->setTitle('Statistiques');
        $statsSheet->setCellValue('A1', 'Statistique')->getStyle('A1')->getFont()->setBold(true);
    
        $line = 3;
        // Totaux généraux
        $statsSheet->setCellValue('A2', 'Total Réservations :');
        $statsSheet->setCellValue('B2', $stats['total']);
    
        // Par statut
        $statsSheet->setCellValue("A{$line}", 'Statuts');
        foreach ($stats['status'] as $st => $cnt) {
            $statsSheet->setCellValue("A{$line}", ucfirst(strtolower($st)).' :');
            $statsSheet->setCellValue("B{$line}", $cnt);
            $line++;
        }
        $line++;
    
        // Par catégorie
        $statsSheet->setCellValue("A{$line}", 'Catégories');
        foreach ($stats['category'] as $cat => $cnt) {
            $statsSheet->setCellValue("A{$line}", ucfirst(strtolower($cat)).' :');
            $statsSheet->setCellValue("B{$line}", $cnt);
            $line++;
        }
        $line++;
    
        // Par ligne
        $statsSheet->setCellValue("A{$line}", 'Par Ligne');
        foreach ($stats['lines'] as $ln => $cnt) {
            $statsSheet->setCellValue("A{$line}", $ln.' :');
            $statsSheet->setCellValue("B{$line}", $cnt);
            $line++;
        }
        $line++;
    
        // Chiffre d’affaires
        $statsSheet->setCellValue("A{$line}", 'Revenu total :');
        $statsSheet->setCellValue("B{$line}", $stats['total_price'].' TND');
    
        // Ajuster largeur colonnes de la feuille Stats
        $statsSheet->getColumnDimension('A')->setWidth(25);
        $statsSheet->getColumnDimension('B')->setAutoSize(true);
    
        // 5) Streamer la réponse
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function() use($writer) {
            $writer->save('php://output');
        });
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'reservations.xlsx'
        );
        $response->headers->set('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);
    
        return $response;
    }







    #[Route('/payment/success/{id}', name: 'reservation_success')]
    public function success(Reservation $reservation, FlouciClient $flouci, EntityManagerInterface $em)
    {
        $result = $flouci->verifyPayment($reservation->getFlouciPaymentId());
        if ($result['status'] === 'SUCCESS') {
            $reservation
                ->setStatus('CONFIRMED')
                ->setPaid(true);
            $em->persist($reservation);
            $em->flush();
            $this->addFlash('success', 'Votre paiement a été confirmé, réservation validée.');
        } else {
            $this->addFlash('error', 'Le paiement a échoué ('.$result['status'].').');
        }

        return $this->redirectToRoute('app_my_reservations');
    }

    #[Route('/payment/fail/{id}', name: 'reservation_fail')]
    public function fail(Reservation $reservation)
    {
        $this->addFlash('error', 'Paiement annulé ou échoué, votre réservation est en attente.');
        return $this->redirectToRoute('app_my_reservations');
    }
}

