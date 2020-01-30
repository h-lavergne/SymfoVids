<?php

namespace App\Tests\Twig;

use PHPUnit\Framework\TestCase;
use App\Twig\AppExtension;

class SluggerTest extends TestCase
{
    /**
     * @param $string
     * @param $slug
     * @dataProvider getSlugs
     */
    public function testSlugify($string, $slug)
    {
        $slugger = new AppExtension();
        $this->assertSame($slug, $slugger->slugify($string));
    }

    public function getSlugs()
    {

        yield ["Lorem Ipsum", "lorem-ipsum"];
        yield ["  Lorem Ipsum", "lorem-ipsum"];

    }
}
