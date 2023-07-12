.. include:: /Includes.rst.txt
.. _cb_skeleton:

===============================
Create a Content Block skeleton
===============================

Create a Content Block skeleton in your terminal
================================================

@todo Adjust documentation for new ContentBlocks/ContentTypes folder.

This command creates a Content Block skeleton for you.
It creates the basic structure of a Content Block, by vendor and name in
the `ContentBlocks` folder of the selected or entered extension.

Example creating a Content Block skeleton in one line:

.. code-block:: bash

   vendor/bin/typo3 make:content-block --vendor=foo --name=bar --extension=foo_bar

If you do not want to use the options, you can also use the interactive mode:

.. code-block:: bash

   vendor/bin/typo3 make:content-block

On non-composer installations use:

.. code-block:: bash

   typo3/sysext/core/bin/typo3 make:content-block


This will ask you for the following information:

    Enter your vendor name:
    Enter your content-block name:
    Choose extension in which the content block should be stored:
