<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;



class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);

            for ($j = 1; $j <= mt_rand(4,6); $j++) {
                $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

                $article = new Article();
                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($article);

                for($k = 1; $k <= mt_rand(4,10); $k++){
                    $date = new \DateTime();
                    $interval = $date->diff($article->getCreatedAt());
                    $days = $interval->days;
                    $minimumDays = '-' . $days . ' days';
                    
                    $content = '<p>' . join( '</p><p>' ,$faker->paragraphs(2)) . '</p>';     

                    $comment = new Comment();
                    $comment->setAuthor($faker->name())
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimumDays))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
