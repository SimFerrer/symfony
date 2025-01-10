<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BookTest extends KernelTestCase
{
    public function testValidBook(): void
    {
        self::bootKernel();
        $book = new Book();
        $book->setTitle('Valid Title')
            ->setIsbn('978-3-16-148410-0') 
            ->setCover('https://example.com/cover.jpg')
            ->setEditedAt(new \DateTimeImmutable())
            ->setPlot('This is a valid plot with more than 20 characters.')
            ->setPageNumber(100);

        $validator = self::getContainer()->get('validator');
        $errors = $validator->validate($book);

        $this->assertCount(0, $errors, (string) $errors);
    }

    public function testInvalidBookIsbn(): void
    {
        self::bootKernel();
        $book = new Book();
        $book->setTitle('Title')
            ->setIsbn('123456') // Invalid ISBN
            ->setCover('https://example.com/cover.jpg')
            ->setEditedAt(new \DateTimeImmutable())
            ->setPlot('Valid plot')
            ->setPageNumber(100);

        $validator = self::getContainer()->get('validator');
        $errors = $validator->validate($book);

        $this->assertGreaterThan(0, count($errors));
    }
}
