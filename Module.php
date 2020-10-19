<?php declare(strict_types=1);
/**
 * DataTypeRdf
 *
 * Implement the main W3C RDF datatypes (html, xml, boolean, integer, decimal
 * and date/time) in order to simplify user input and to give more semanticity
 * to values of resources.
 *
 * @copyright Daniel Berthereau, 2018
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 *
 * This software is governed by the CeCILL license under French law and abiding
 * by the rules of distribution of free software.  You can use, modify and/ or
 * redistribute the software under the terms of the CeCILL license as circulated
 * by CEA, CNRS and INRIA at the following URL "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and rights to copy, modify
 * and redistribute granted by the license, users are provided only with a
 * limited warranty and the software's author, the holder of the economic
 * rights, and the successive licensors have only limited liability.
 *
 * In this respect, the user's attention is drawn to the risks associated with
 * loading, using, modifying and/or developing or reproducing the software by
 * the user in light of its specific status of free software, that may mean that
 * it is complicated to manipulate, and that also therefore means that it is
 * reserved for developers and experienced professionals having in-depth
 * computer knowledge. Users are therefore encouraged to load and test the
 * software's suitability as regards their requirements in conditions enabling
 * the security of their systems and/or data to be ensured and, more generally,
 * to use and operate it in the same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 */
namespace DataTypeRdf;

use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Omeka\Module\AbstractModule;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager): void
    {
        $controllers = [
            'Omeka\Controller\Admin\Item',
            'Omeka\Controller\Admin\ItemSet',
            'Omeka\Controller\Admin\Media',
            'Annotate\Controller\Admin\Annotation',
        ];
        foreach ($controllers as $controller) {
            $sharedEventManager->attach(
                $controller,
                'view.add.after',
                [$this, 'prepareResourceForm']
            );
            $sharedEventManager->attach(
                $controller,
                'view.edit.after',
                [$this, 'prepareResourceForm']
            );
        }
    }

    /**
     * Prepare resource forms for rdf types.
     *
     * @param Event $event
     */
    public function prepareResourceForm(Event $event): void
    {
        $view = $event->getTarget();
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $dataTypeRdfs = $settings->get('datatyperdf_datatypes', []);
        $view->headScript()->appendScript('var dataTypeRdfs = ' . json_encode($dataTypeRdfs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';');
    }
}
