.. include:: /Includes.rst.txt
.. _data-processing-event:

===============
Data Processing
===============

Content Blocks is based on the new :ref:`Record API <ext_core:feature-103783-1715113274>`
in TYPO3 v13. As soon as a :php-short:`\TYPO3\CMS\Core\Domain\Record` is created
and enriched, it is immutable. This means the standard way of data processing
via custom :ref:`DataProcessors <t3coreapi:content-elements-custom-data-processor>`
will not work anymore, if it was based on manipulating the
:php:`$processedData['data']` array.

.. note::

    It is still possible to create a new variable alongside `data`.

The new recommended way adding custom logic via PHP is to use the new PSR-14
:ref:`RecordCreationEvent <ext_core:feature-104846-1725631434>`. This event has
the advantage that it is always triggered as soon as a Record is created,
independent of frontend or backend context or even in scenarios where the Record
is part of a nested structure.

Example
=======

The event listener class, using the PHP attribute :php:`#[AsEventListener]` for
registration, creates a :php:`Coordinates` object based on the field value of
the :php:`coordinates` field for the custom :php:`maps` content type.

..  code-block:: php

    final class RecordCreationEventListener
    {
        #[AsEventListener]
        public function __invoke(\TYPO3\CMS\Core\Domain\Event\RecordCreationEvent $event): void
        {
            $rawRecord = $event->getRawRecord();

            if ($rawRecord->getMainType() === 'tt_content' && $rawRecord->getRecordType() === 'maps' && $event->hasProperty('coordinates')) {
                $event->setProperty(
                    'coordinates',
                    new Coordinates($event->getProperty('coordinates'))
                );
            }
        }
    }
