.. include:: /Includes.rst.txt

.. _known-problems:

==============
Known Problems
==============

.. _row-size-too-large:

Row size too large
==================

Read `this TYPO3 changelog document by @lolli <https://docs.typo3.org/permalink/changelog:important-104153-1718790066>`__ for in depth explanation and more tips.

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

Missing asset symlink from extension
====================================

If your extension does not contain a ``Resources/Public/`` directory, the asset
symlink under ``public/_assets/`` will not be created. This issue most
commonly occurs in deployment pipelines.

The reason is that Content Blocks generates its assets **after** Composer's
autoloading step. When the Composer installer creates the asset symlinks,
the extension's ``Resources/Public/`` directory does not yet exist, so no symlink is
created.

Solution
--------

Ensure that your extension always contains a ``Resourecs/Public/`` directory. You can
do this, for example, by:

* Adding an extension icon (``Resources/Public/Icons/Extension.svg`` or
  ``.png``).
* Placing a ``.gitkeep`` file inside the ``Resources/Public/`` directory.

This ensures that the asset symlink is created correctly during installation.
