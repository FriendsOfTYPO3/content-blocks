.. include:: /Includes.rst.txt
.. _cb_extendTca:

============================
Extend TCA of Content Blocks
============================

.. warning::

    This way of overriding Content Blocks TCA will with high probability be
    replaced with another, better, approach.

The Content Blocks TCA generation happens after all TCA (overrides) from files
are processed. This means, TCA overrides will not work.

For this reason, we there is a :php:`TYPO3\CMS\ContentBlocks\Event\AfterContentBlocksTcaCompilationEvent`
event, on which you can hook in and extend the TCA of the Content Blocks.

Thus you directly get the generated TCA and are able to add your configuration
in a smart way.

First of all you need a class, which does the TCA customisation:
(E.g. in your extension: Classes/Generator/TcaCustomisation.php)

.. code-block:: php

    <?php
    declare(strict_types=1);

    namespace Vendor\MyExtension\Generator;

    use TYPO3\CMS\ContentBlocks\Event\AfterContentBlocksTcaCompilationEvent;

    class TcaCustomisation
    {
        public function extendTcaOfContentBlocks(AfterContentBlocksTcaCompilationEvent $event): void
        {
            $tca = $event->getTca();
            $tca['tt_content']['columns']['vendor_package_fieldidentifier']['config']['enableRichtext'] = true;
            $event->setTca($tca);
        }
    }


Then you need to register the event listener for your class in your Configuration/Services.yaml:

.. code-block:: yaml

    Vendor\MyExtension\Generator\TcaCustomisation:
      tags:
        - name: event.listener
          identifier: 'vendor-myextension-tcacustomisation'
          event: 'TYPO3\CMS\ContentBlocks\Event\AfterContentBlocksTcaCompilationEvent'
          method: 'extendTcaOfContentBlocks'


See also:
:ref:`EventDispatcher (PSR-14 Events) <t3coreapi:EventDispatcher>`
