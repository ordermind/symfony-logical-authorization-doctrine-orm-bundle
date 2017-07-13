<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Ordermind\LogicalAuthorizationDoctrineORMBundle\DependencyInjection\LogAuthDoctrineORMExtension;

class OrdermindLogicalAuthorizationDoctrineORMBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->registerExtension(new LogAuthDoctrineORMExtension());
    }
}
