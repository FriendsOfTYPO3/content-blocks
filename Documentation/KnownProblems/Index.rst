.. include:: /Includes.rst.txt

.. _known-problems:

==============
Known Problems
==============

.. _row-size-too-large:

Row size too large
==================

Read `this TYPO3 changelog document by @lolli <https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/13.2/Important-104153-AboutDatabaseErrorRowSizeTooLarge.html#important-104153-about-database-error-row-size-too-large/>`__ for in depth explanation and more tips.

Field labels displayed as identifier
====================================

In some areas in the TYPO3 backend fields created by Content Blocks are
displayed with their identifier instead of the localized label. E.g. in the
backend user permissions view or the list view / export view. The reason is that
you can't define fields centrally in Content Blocks. Multiple Content Blocks can
reuse the same field and define alternative labels. If you really need proper
labels in these areas, we recommend to use TCA overrides in order to define a
default label.

.. code-block:: php

    $GLOBALS['TCA']['tt_content']['columns']['my_prefix_my_identifier']['label'] = 'LLL:EXT:my_extension/path/to/locallang.xlf';
