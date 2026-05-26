.. include:: /Includes.rst.txt
.. _changelog-2.4:

===
2.4
===

Content Blocks version 2.4 adds a collection of small new features.

..  contents::

Feature
=======

Identifier `alias` for fields
-----------------------------

You can now define an :yaml:`alias` for fields, which will then be used instead
of the :yaml:`identifier` in your Fluid templates. This has two main advantages:

1. You are not forced to use snake_case in Fluid, just because it is better
   suited for database column names.
2. You can use semantic names when re-using shared, generic fields in the
   context of your Content Block.

.. code-block:: yaml

    name: example/cta
    fields:
      - identifier: header
        alias: title

Field Type SelectText
---------------------

A new field type :ref:`SelectText <field_type_select-text>` is added. This
new type allows to have a select field with exclusively text values. The
database column will also have type :sql:`varchar(255)`, instead of
:sql:`longtext`.

..  code-block:: yaml

    name: example/select-text
    fields:
      - identifier: select_text
        type: SelectText
        items:
          - label: 'The first'
            value: 'first'
          - label: 'The second'
            value: 'second'

New option `hideInUi` for Record Types
--------------------------------------

It is now possible to explicitly hide Record Types in the record overview
by defining :yaml:`hideInUi: true`. This is already done automatically when
the Record Type is used as a child item in Collections.

New automatic language keys
---------------------------

New automatic language keys are added, which can now be used in the labels.xlf
file:

* :yaml:`placeholder` (for types with input field: Text, Textarea, Email, ...)
* :yaml:`labelChecked` (for :ref:`Checkbox <confval-checkbox-items>` with :yaml:`renderType: checkboxLabeledToggle`)
* :yaml:`labelUnchecked` (for :ref:`Checkbox <confval-checkbox-items>` with :yaml:`renderType: checkboxLabeledToggle`)

See :ref:`here <api_automatic_language_keys>` for more information.

Site and Site Settings available in backend previews
----------------------------------------------------

You have now access to the :html:`site` and :html:`siteSettings` variables in
your backend-preview.html templates. Having these, you can now display your
previews depending on the current site and settings. This is especially useful
if you define special colors in your theme and want to load CSS variables into
your templates.

Generate frontend and backend preview templates via CLI
-------------------------------------------------------

Two new commands allow you to generate starter Fluid templates for a Content
Block directly from the command line:

* :bash:`content-blocks:generate:frontend` generates
  :file:`templates/frontend.fluid.html`
* :bash:`content-blocks:generate:backend-preview` generates
  :file:`templates/backend-preview.fluid.html`

Both commands require the Content Block name as an argument and will not
overwrite an existing file unless the :bash:`--force` option is given.

.. code-block:: bash

   vendor/bin/typo3 content-blocks:generate:frontend example/my-block
   vendor/bin/typo3 content-blocks:generate:backend-preview example/my-block

See :ref:`command_generate_frontend_template` and
:ref:`command_generate_backend_preview_template` for details.
