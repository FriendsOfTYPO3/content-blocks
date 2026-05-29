.. include:: /Includes.rst.txt

.. _container-integration:

==============================
Integration with EXT:container
==============================

This guide describes how to combine Content Blocks with the
`EXT:container <https://extensions.typo3.org/extension/container>`_ extension
to create container content elements with grid-based backend rendering.

Content Blocks handles the Content Element definition, icon registration, and
frontend template, while EXT:container takes care of the grid configuration and
backend preview rendering.

Prerequisites
=============

* EXT:container is installed and configured in your TYPO3 project.

Step 1: Define the Content Block
=================================

Create a Content Block for your container element. The fields you define here
are the container's own fields (e.g. a header). The child columns are managed
by EXT:container separately.

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/two-column-container/config.yaml

   name: vendor/two-column-container
   typeName: vendor_two_columns_container
   group: container
   saveAndClose: true
   fields:
     - identifier: header
       useExistingField: true

Step 2: Register the Container Configuration
============================================

Register the container grid configuration manually via TCA overrides. This is
where EXT:container's :php:`ContainerConfiguration` API is used to define the
column layout.

.. note::

   In the future, EXT:container may provide a helper method to simplify this
   registration further.

.. code-block:: php
   :caption: EXT:your_extension/Configuration/TCA/Overrides/tt_content_container.php

   <?php

   use B13\Container\Backend\Preview\ContainerPreviewRenderer;
   use B13\Container\Tca\ContainerConfiguration;

   $containerConfiguration = new ContainerConfiguration(
       cType: 'vendor_two_columns_container',
       label: '',
       description: '',
       grid: [
           [
               ['name' => 'Left', 'colPos' => 200],
               ['name' => 'Right', 'colPos' => 201],
           ],
       ]
   );
   $GLOBALS['TCA']['tt_content']['containerConfiguration'][$containerConfiguration->getCType()] = $containerConfiguration->toArray();
   $GLOBALS['TCA']['tt_content']['types'][$containerConfiguration->getCType()]['previewRenderer'] = ContainerPreviewRenderer::class;

Step 3: Add the TypoScript Rendering Definition
================================================

Add a TypoScript rendering definition that uses EXT:container's
:typoscript:`ContainerProcessor` to load the child elements.

.. code-block:: typoscript

   tt_content.vendor_two_columns_container {
     dataProcessing {
       100 = B13\Container\DataProcessing\ContainerProcessor
     }
   }

Step 4: Remove the Backend Preview Template
============================================

Delete the :file:`backend-preview.html` file from your Content Block if it
exists. This allows EXT:container's :php:`ContainerPreviewRenderer` (registered
in Step 2) to take over and render the grid in the backend.

Result
======

After completing these steps, Content Blocks and EXT:container are working
together:

* **Content Blocks** manages the Content Element definition, field configuration
  (including FlexForm via YAML instead of XML), icon registration, and the
  frontend template.
* **EXT:container** manages the grid configuration and backend preview rendering.

This gives you full control over the container's own fields while EXT:container
handles child element placement.

.. _container-integration-future:

Future Improvements
====================

Ideally, all configuration would live in one place — the Content Block YAML
file. This would require:

* Content Blocks introducing a dedicated `Container` content type that holds
  the EXT:container grid configuration.
* EXT:container providing an API to register container configuration without
  simultaneously registering a new Content Element in TCA.

Work towards this closer integration between the two extensions is planned.
