<?php declare(strict_types=1);

namespace DataTypeRdf;

use Omeka\Stdlib\Message;

/**
 * @var Module $this
 * @var \Laminas\ServiceManager\ServiceLocatorInterface $services
 * @var string $newVersion
 * @var string $oldVersion
 *
 * @var \Omeka\Api\Manager $api
 * @var \Omeka\Settings\Settings $settings
 * @var \Doctrine\DBAL\Connection $connection
 * @var \Doctrine\ORM\EntityManager $entityManager
 * @var \Omeka\Mvc\Controller\Plugin\Messenger $messenger
 */
$plugins = $services->get('ControllerPluginManager');
$api = $plugins->get('api');
$settings = $services->get('Omeka\Settings');
$connection = $services->get('Omeka\Connection');
$messenger = $plugins->get('messenger');
$entityManager = $services->get('Omeka\EntityManager');

if (version_compare($oldVersion, '3.3.4.3', '<')) {
    $settings->set('datatyperdf_html_mode_resource', $settings->get('datatyperdf_html_mode_resource', $settings->get('blockplus_html_mode')) ?: 'inline');
    $settings->set('datatyperdf_html_config_resource', $settings->get('datatyperdf_html_config_resource', $settings->get('blockplus_html_config')) ?: 'default');

    $message = new Message(
        'It’s now possible to choose mode of display to edit html values of resources in main params.' // @translate
    );
    $messenger->addSuccess($message);
}

if (version_compare($oldVersion, '3.4.6', '<')) {
    $message = new Message(
        'Data type xml is now displayed with colors in resource form.' // @translate
    );
    $messenger->addSuccess($message);
}
