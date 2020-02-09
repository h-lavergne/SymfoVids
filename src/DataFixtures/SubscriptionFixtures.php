<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\VarDumper\Cloner\Data;

class SubscriptionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getSubscriptionData() as [$user_id, $plan, $valid_to, $payment_status, $free_plan_used]) {
            $sub = new Subscription();
            $sub->setPlan($plan);
            $sub->setValidTo($valid_to);
            $sub->setPaymentStatus($payment_status);
            $sub->setFreePlanUsed($free_plan_used);

            $user = $manager->getRepository(User::class)->find($user_id);
            $user->setSubscription($sub);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getSubscriptionData()
    {
        return [
            [1, Subscription::getPlanDataNameByIndex(2), (new \DateTime())->modify("+100 year"), "paid", false ],
            [2, Subscription::getPlanDataNameByIndex(0), (new \DateTime())->modify("+1 month"), "paid", true ],
            [3, Subscription::getPlanDataNameByIndex(1), (new \DateTime())->modify("+1 minute"), "paid", false ],
        ];
    }

}
