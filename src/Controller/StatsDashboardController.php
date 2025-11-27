<?php

namespace App\Controller;

use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsDashboardController extends AbstractController
{
    #[Route('/admin/stats', name: 'admin_stats')]
    public function index(UserRepository $userRepo, SubscriptionRepository $subRepo): Response
    {
        $totalUsers = $userRepo->count([]);
        $totalSubscriptions = $subRepo->count([]);

        $now = new \DateTimeImmutable();

        // Active / expired counts
        $activeSubscriptions = (int) $subRepo->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.endDate > :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult();

        $expiredSubscriptions = (int) $subRepo->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.endDate <= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult();

        // Monthly data for charts
        $monthlyTotal = $subRepo->getMonthlySubscriptions(6);
        $monthlyActive = $subRepo->getMonthlyActiveSubscriptions(6);
        $monthlyExpired = $subRepo->getMonthlyExpiredSubscriptions(6);

        $labels = array_map(fn($r) => $r['month'], $monthlyTotal);
        $totalData = array_map(fn($r) => (int)$r['count'], $monthlyTotal);
        $activeData = array_map(fn($r) => (int)$r['count'], $monthlyActive);
        $expiredData = array_map(fn($r) => (int)$r['count'], $monthlyExpired);

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalSubscriptions' => $totalSubscriptions,
            'activeSubscriptions' => $activeSubscriptions,
            'expiredSubscriptions' => $expiredSubscriptions,
            'chartLabels' => $labels,
            'chartData' => $totalData,
            'activeSubsChartData' => $activeData,
            'expiredSubsChartData' => $expiredData,
        ]);
    }
}
