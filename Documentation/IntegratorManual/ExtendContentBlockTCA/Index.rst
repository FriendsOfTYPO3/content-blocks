.. include:: /Includes.rst.txt
.. _cb_extendTca:

==========================
Extend TCA of Content Blocks
==========================

Since TYPO3 v12 the AfterTcaCompilationEvent is established, the Content Blocks TCA
generation happens after the TCA is completed. This means, TCAOverrides will not work.

For this reason, we created the AfterContentBlocksTcaCompilationEvent event, on which you
can hook in and extend the TCA of the Content Blocks.

Thus you directly get the generated TCA and are able to add your configuration in a smart way.

First of all you need a class, which do the TCA customisation:
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
            $tca['tt_content']['columns']['cb_vendor_package_fieldidentifier']['config']['enableRichtext'] = true;
            $tca = array_replace_recursive($event->getTca(), $tca);
            $event->setTca($tca);
        }
    }


Then you need to register the eventlistener for your class:

.. code-block:: yaml

    Vendor\MyExtension\Generator\TcaCustomisation:
      tags:
        - name: event.listener
          identifier: 'vendor-myextension-tcacustomisation'
          event: 'TYPO3\CMS\ContentBlocks\Event\AfterContentBlocksTcaCompilationEvent'
          method: 'extendTcaOfContentBlocks'


See also:
:ref:`EventDispatcher (PSR-14 Events) <t3coreapi:EventDispatcher>`
