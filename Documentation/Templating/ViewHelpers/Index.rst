.. include:: /Includes.rst.txt
.. _content_blocks_view_helpers:

===========
ViewHelpers
===========

Content Blocks ships a small set of Fluid ViewHelpers to solve problems, which
are specific to Content Blocks, like resolving the current Content Block's
assets or language file, and providing backend edit links that play nicely
with the Page Layout module.

All ViewHelpers are available under the :html:`cb` namespace. It is registered
globally, so you don't have to import it manually.

.. note::

    Even though importing the namespace is not required, IDEs and Fluid
    linters may still expect the :html:`xmlns:cb` declaration on the root
    tag of your template to provide proper autocompletion and validation:

    .. code-block:: html

        <html
            xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
            xmlns:cb="http://typo3.org/ns/TYPO3/CMS/ContentBlocks/ViewHelpers"
            data-namespace-typo3-fluid="true"
        >

Available ViewHelpers:

..  rst-class:: horizbuttons-attention-m

*  :ref:`cb:assetPath <content_blocks_view_helper_asset_path>`
*  :ref:`cb:languagePath <content_blocks_view_helper_language_path>`
*  :ref:`cb:link.editRecord <content_blocks_view_helper_link_edit_record>`

**Table of Contents**

.. toctree::
   :titlesonly:
   :maxdepth: 1

   AssetPath/Index
   LanguagePath/Index
   Link/EditRecord/Index
