<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    // Total subscriptions per month
    public function getMonthlySubscriptions(int $months = 6): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(id) AS count
        FROM subscription
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
        GROUP BY month
        ORDER BY month ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('months', $months);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    // Active subscriptions per month
    public function getMonthlyActiveSubscriptions(int $months = 6): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(id) AS count
        FROM subscription
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
          AND end_date > NOW()
        GROUP BY month
        ORDER BY month ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('months', $months);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    // Expired subscriptions per month
    public function getMonthlyExpiredSubscriptions(int $months = 6): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(id) AS count
        FROM subscription
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
          AND end_date <= NOW()
        GROUP BY month
        ORDER BY month ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('months', $months);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
}
