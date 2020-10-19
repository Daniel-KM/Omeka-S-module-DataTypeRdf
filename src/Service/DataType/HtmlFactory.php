<?php

namespace DataTypeRdf\Service\DataType;

use Interop\Container\ContainerInterface;
use DataTypeRdf\DataType\Html;
use Laminas\ServiceManager\Factory\FactoryInterface;

class HtmlFactory implements FactoryInterface
{
    /**
     * Create the service for RdfHtml datatype.
     *
     * @return Html
     */
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new Html($services->get('Omeka\HtmlPurifier'));
    }
}
