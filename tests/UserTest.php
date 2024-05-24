<?php
namespace App\Tests;

use App\Entity\Article;
use App\Entity\Plant;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase {
    public function testPlantCreation() {
        $user = (new User())
            ->addPlant(new Plant())
            ->addPlant(new Plant());
        $plants = $user->getPlants();
        $plant = $plants[0];
        $plant->setName('Test plant');

        $this->assertSame($user, $plant->getOwner());
        $this->assertSame('Test plant', $plant->getName());
        $this->assertSame(2, count($plants));
    }

    public function testArticleCreation() {
        $user = (new User())
            ->addArticle(new Article())
            ->addArticle(new Article());
        $articles = $user->getArticles();
        $article = $articles[0];
        $article->setTitle('A test article');

        $this->assertSame($user, $article->getAuthor());
        $this->assertSame('A test article', $article->getTitle());
        $this->assertSame(2, count($articles));
    }
}
