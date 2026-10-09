.. include:: /Includes.rst.txt
.. _field_type_language:

========
Language
========

The :yaml:`Language` type is for rendering a select box with all available languages for the current installation.

Settings
========

..  confval-menu::
    :name: confval-language-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: language-default
   :required: false
   :type: integer
   :default: 0

   Language id which is pre-selected if a new record is created. :yaml:`0` is the
   default language, :yaml:`-1` means "all languages".

.. confval:: readOnly
   :name: language-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: showIfEmpty
   :name: language-showIfEmpty
   :required: false
   :type: boolean
   :default: false

   By default, this field is hidden in the backend form if only one language
   is available (the "All languages" item is not counted). Set this option to
   :yaml:`true` to always render the field.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/language
    fields:
      - identifier: language
        type: Language
