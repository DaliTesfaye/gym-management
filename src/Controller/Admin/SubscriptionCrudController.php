<?php

namespace App\Controller\Admin;

use App\Entity\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/subscription', name: 'admin_subscription_crud')]
class SubscriptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Subscription::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('planName', 'Plan Name'),
            AssociationField::new('user', 'Member'), // shows the linked user
            DateField::new('startDate'),
            DateField::new('endDate'),
            TextField::new('status'), // Active / Expired
            DateField::new('createdAt')->onlyOnDetail(), // optional
        ];
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status')->setChoices([
                'Active' => 'Active',
                'Expired' => 'Expired',
            ]))
            ->add('user'); // optional: filter by user
    }

    public function __toString(): string
    {
        return $this->username ?? $this->email ?? 'User';
    }



}
