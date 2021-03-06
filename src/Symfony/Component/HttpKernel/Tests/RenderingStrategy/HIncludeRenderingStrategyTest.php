<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\RenderingStrategy;

use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\RenderingStrategy\HIncludeRenderingStrategy;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\HttpFoundation\Request;

class HIncludeRenderingStrategyTest extends AbstractRenderingStrategyTest
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            $this->markTestSkipped('The "HttpFoundation" component is not available');
        }

        if (!interface_exists('Symfony\Component\Routing\Generator\UrlGeneratorInterface')) {
            $this->markTestSkipped('The "Routing" component is not available');
        }
    }

    /**
     * @expectedException \LogicException
     */
    public function testRenderExceptionWhenControllerAndNoSigner()
    {
        $strategy = new HIncludeRenderingStrategy();
        $strategy->render(new ControllerReference('main_controller', array(), array()));
    }

    public function testRenderWithControllerAndSigner()
    {
        $strategy = new HIncludeRenderingStrategy(null, new UriSigner('foo'));
        $strategy->setUrlGenerator($this->getUrlGenerator());
        $this->assertEquals('<hx:include src="/main_controller.html?_hash=6MuxpWUHcqIddMMmoN36uPsEjws%3D"></hx:include>', $strategy->render(new ControllerReference('main_controller', array(), array())));
    }

    public function testRenderWithUri()
    {
        $strategy = new HIncludeRenderingStrategy();
        $this->assertEquals('<hx:include src="/foo"></hx:include>', $strategy->render('/foo'));

        $strategy = new HIncludeRenderingStrategy(null, new UriSigner('foo'));
        $this->assertEquals('<hx:include src="/foo"></hx:include>', $strategy->render('/foo'));
    }

    public function testRenderWhithDefault()
    {
        // only default
        $strategy = new HIncludeRenderingStrategy();
        $this->assertEquals('<hx:include src="/foo">default</hx:include>', $strategy->render('/foo', null, array('default' => 'default')));

        // only global default
        $strategy = new HIncludeRenderingStrategy(null, null, 'global_default');
        $this->assertEquals('<hx:include src="/foo">global_default</hx:include>', $strategy->render('/foo', null, array()));

        // global default and default
        $strategy = new HIncludeRenderingStrategy(null, null, 'global_default');
        $this->assertEquals('<hx:include src="/foo">default</hx:include>', $strategy->render('/foo', null, array('default' => 'default')));
    }
}
