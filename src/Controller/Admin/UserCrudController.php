<?php

namespace App\Controller\Admin;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/user', name: 'admin_user_crud')]
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username'),
            TextField::new('email'),
            DateField::new('createdAt')->onlyOnIndex(),
            CollectionField::new('subscriptions')->onlyOnDetail(), // show related subscriptions
        ];
    }
    public function getStatus(): string
    {
        return $this->getEndDate() > new \DateTime() ? 'Active' : 'Expired';
    }
}
