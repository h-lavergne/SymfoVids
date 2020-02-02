<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        foreach ($this->commentData() as [$content, $user, $video, $created_at]) {

            $comment = new Comment();
            $user = $manager->getRepository(User::class)->find($user);
            $video = $manager->getRepository(Video::class)->find($video);

            $comment->setContent($content);
            $comment->setUser($user);
            $comment->setVideo($video);
            $comment->setCreatedAtForFixtures(new \DateTime($created_at));

            $manager->persist($comment);
        }

        $manager->flush();
    }


    private function commentData()
    {
        return [
            ["Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
              labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris", 1, 10, "2020-02-02 15:31:25"],

            ["Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
              labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
              nisi ut aliquip ex ea commodo consequat. Duis aute irure dolo",
                2, 10, "2020-02-02 23:03:25"],

            ["Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
              labore et dolore magna aliqua.tur. Excepteur sint occaecat cupidatat non proident, sunt ",
                3, 10, "2020-02-02 12:51:45"],

            ["Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
              labore et dolore magna aliqua.ariatur. Excepteur sint occaecat cupidatat non proident, sunt ",
                1, 11, "2020-02-02 05:41:15"],

            ["Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
              labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
              nisi ut aliquip ex ea codatat non proident, sunt ",
                2, 11, "2020-02-02 09:12:22"],

            ["Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
              labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
              nisi ut aliquip ex ea commodo consequat. Duis at occaecat cupidatat non proident, sunt ",
                3, 11, "2020-02-02 15:31:25"],

            ["Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
              labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
              nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit
              esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt ",
                3, 11, "2020-02-02 15:39:25"],

            ["Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
              labore et dolore magna aliqua. Ut eni aute irure dolor in reprehenderit in voluptate velit
              esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt ",
                3, 11, "2020-02-02 16:24:45"],
        ];
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array class-string[]
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
