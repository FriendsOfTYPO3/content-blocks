.. include:: /Includes.rst.txt
.. _content_blocks_view_helper_language_path:

===============
cb:languagePath
===============

.. rst-class:: horizbuttons-attention-m

*  Class: :php:`TYPO3\CMS\ContentBlocks\ViewHelpers\LanguagePathViewHelper`

Resolves the :html:`LLL:` path to the :file:`labels.xlf` language file of a
Content Block. Use it together with :html:`f:translate` to build a full
translation key without having to hardcode the extension path.

.. warning::

    This ViewHelper only works inside the template of a Content Block. It
    resolves the current Content Block via the :html:`data._name` or
    :html:`settings._content_block_name` template variable. If neither
    variable is available and :html:`name` is not given explicitly, an
    exception is thrown. An exception is also thrown if the resolved
    Content Block is not registered.

Arguments
=========

..  confval-menu::
    :name: confval-language-path-arguments
    :display: table
    :type:
    :default:
    :required:

.. confval:: name
   :name: language-path-name
   :required: false
   :type: string

   The vendor/package name of the Content Block (:yaml:`vendor/name`). If not
   set, the current Content Block is resolved automatically.

Examples
========

Default usage
--------------

Inside the current Content Block's own template, :html:`name` can be omitted.

.. code-block:: html

    <f:translate key="{cb:languagePath()}:header"/>

Explicit Content Block name
----------------------------

.. code-block:: html

    <f:translate key="{cb:languagePath(name: 'vendor/name')}:header"/>

See also
========

*  :ref:`LanguagePath ViewHelper <language_path_view_helper>`
*  :ref:`Language definition <cb_definition_language>`
