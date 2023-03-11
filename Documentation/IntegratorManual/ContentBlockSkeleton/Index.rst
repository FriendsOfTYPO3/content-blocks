.. include:: /Includes.rst.txt
.. _cb_skeleton:

===============================
Create a Content Block skeleton
===============================


Create a Content Block skeleton	on your terminal
================================================

This command creates a Content Block skeleton for you.
It creates the basic structure of a Content Block, by vendor and package in
the `ContentBlocks` folder of the selected extension.

Example creating a Content Block skeleton in one line:

.. code-block:: bash
    typo3/sysext/core/bin/typo3 make:content-block --vendor=foo --package=bar --extension=foo_bar

If you do not want to use the options, you can also use the interactive mode:

.. code-block:: bash

    vendor/bin/typo3 make:content-block


This will ask you for the following information:

    Enter your vendor name:
    Enter your package name:
    Choose extension in which the content block should be stored:
