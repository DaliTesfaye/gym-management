<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User; // Make sure this points to your actual User entity

final class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/forgot-password', name: 'app_reset_password')]
    public function index(Request $request): Response
    {
        // 1️⃣ Redirect if user is already logged in
        if ($this->getUser()) {
            // Redirect to admin dashboard if user/admin is logged in
            return $this->redirectToRoute('admin_dashboard');
        }

        // 2️⃣ Handle POST submission
        if ($request->isMethod('POST') && $request->get('email')) {
            $email = $request->get('email');

            // For testing: dump the email
            // dd($email); // Uncomment this line to debug email

            // 3️⃣ Check if email exists in the database
            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'This email does not exist.');
                return $this->redirectToRoute('app_reset_password');
            }

            // 4️⃣ TODO: Generate reset token and send email
            // Example: $this->sendResetEmail($user);

            $this->addFlash('success', 'If this email exists, a reset link has been sent.');
            return $this->redirectToRoute('app_login');
        }

        // 5️⃣ Render forgot password form
        return $this->render('reset_password/index.html.twig', [
            'controller_name' => 'ResetPasswordController',
        ]);
    }

    // Optional: You can add a method to send email later
    // private function sendResetEmail(User $user)
    // {
    //     // Mailjet or Symfony Mailer integration
    // }
}
