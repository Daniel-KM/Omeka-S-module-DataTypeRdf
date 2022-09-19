<?php declare(strict_types=1);

namespace DataTypeRdf;

use Omeka\Mvc\Controller\Plugin\Messenger;
use Omeka\Stdlib\Message;

/**
 * @var Module $this
 * @var \Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator
 * @var string $newVersion
 * @var string $oldVersion
 *
 * @var \Doctrine\DBAL\Connection $connection
 * @var \Doctrine\ORM\EntityManager $entityManager
 * @var \Omeka\Mvc\Controller\Plugin\Api $api
 */
$services = $serviceLocator;
$settings = $services->get('Omeka\Settings');
// $config = require dirname(dirname(__DIR__)) . '/config/module.config.php';
$connection = $services->get('Omeka\Connection');
// $entityManager = $services->get('Omeka\EntityManager');
// $plugins = $services->get('ControllerPluginManager');
// $api = $plugins->get('api');

if (version_compare($oldVersion, '3.3.4.3', '<')) {
    $settings->set('datatyperdf_html_mode_resource', $settings->get('datatyperdf_html_mode_resource', $settings->get('blockplus_html_mode')) ?: 'inline');
    $settings->set('datatyperdf_html_config_resource', $settings->get('datatyperdf_html_config_resource', $settings->get('blockplus_html_config')) ?: 'default');

    $messenger = new Messenger();
    $message = new Message(
        'It’s now possible to choose mode of display to edit html values of resources in main params.' // @translate
    );
    $messenger->addSuccess($message);
}
