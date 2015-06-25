<?php

namespace Uneak\RoutesManagerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RoutesCompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {
        if ($container->hasDefinition('uneak.routesmanager') === false) {
            return;
        }
        $definition = $container->getDefinition('uneak.routesmanager');
        $taggedServices = $container->findTaggedServiceIds('uneak.routesmanager.route');
		
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
					'addRoute', array(new Reference($id), $attributes['id'], $attributes['priority'], $attributes['group'])
                );
            }
        }

    }

}
