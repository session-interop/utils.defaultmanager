<?php
declare(strict_types = 1);

namespace Interop\Session\Manager\Utils\DefaultManager;

use Interop\Container\ContainerInterface;
use Interop\Session\Manager\{
    SessionManagerInterface, Utils\DefaultManager as DefaultManager
};
use Interop\Session\Configuration\SessionConfigurationInterface;
use Interop\Container\ServiceProvider;

class DefaultSessionManagerServiceProvider implements ServiceProvider
{

    public function getServices()
    {
        return [
            SessionConfigurationInterface::class => function (ContainerInterface $container) {
                return new DefaultManager\SessionConfiguration();
            },
            SessionManagerInterface::class => function (ContainerInterface $container) {
                return new DefaultManager\DefaultSessionManager($container->get(SessionConfigurationInterface::class));
            }
        ];
    }
}
