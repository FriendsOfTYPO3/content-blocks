.. include:: /Includes.rst.txt

.. _cb_reuse_existing_fields:

=====================
Reuse existing fields
=====================

It's possible to reuse already existing fields by using the :yaml:`useExistingField`
flag. By doing so, you can extend existing fields with your own properties on a
per element level.

Reusing base fields
===================

Base fields are fields, which are defined by extensions in
`Configuration/TCA/table_name.php`. They serve as a basis and can be reused in
different Content Types. For Content Elements it's highly recommended to reuse
the :yaml:`header` field this way, because it is used for the title on different
places in the backend.

.. code-block:: yaml

    name: example/block
    fields:
      - identifier: header
        useExistingField: true

Reusing custom fields
=====================

Custom fields are fields, which are defined by extensions in
`Configuration/TCA/Overrides/*.php`. They extend the basic set of fields. These
can also be reused in Content Blocks, but you have to define the :yaml:`type`
explicitly in contrast to base fields. For this, you have to know the
:ref:`type mapping <type_mapping>` from TCA type to Content Blocks type.

.. code-block:: yaml

    name: example/block
    fields:
      - identifier: my_custom_field
        type: Text
        useExistingField: true

Reusing Content Block fields
============================

Reusing fields between different Content Blocks is only possible, if the option
:yaml:`prefixField(s)` is turned off. Inside the same project with same vendor
names you can also set :yaml:`prefixType` to :yaml:`vendor`. As soon as
the :yaml:`identifier` is the same, the field will only be generated once. Be
careful to define the same :yaml:`type` for the field. Settings can be
overridden on a per element basis. Here it is not needed to define
:yaml:`useExistingField`.

.. code-block:: yaml

    name: example/block1
    prefixFields: false # prefixing disabled
    fields:
      - identifier: my_custom_field # same identifier
        type: Text # same type
        required: true # different settings

.. code-block:: yaml

    name: example/block2
    prefixFields: false # prefixing disabled
    fields:
      - identifier: my_custom_field # same identifier
        type: Text # same type
        max: 10 # different settings


Best practice
=============

It's recommended to use existing fields whenever possible instead of creating
new ones. This also avoids the risk of the :ref:`"Row size too large" <row-size-too-large>`
problem that can arise when a database table becomes too large and difficult to
manage.

It's also important to consider which existing fields are appropriate to reuse
(the extension makes no restrictions here or carries out checks). It is
generally not recommended to reuse fields such as uid, pid or sorting as they
have a specific use and should not be misused due to the potential for negative
side effects. Below we have listed the fields from the table :sql:`tt_content`
that are eligible for reuse:

*  `header`
*  `header_layout`
*  `header_position`
*  `header_link`
*  `subheader`
*  `bodytext`
*  `date`
*  `assets`
*  `image`
*  `media`
*  `categories`
*  `pages`

For example, if you want to use the existing column :sql:`bodytext`,
:sql:`header` or :sql:`image` you can do one of the following:

.. code-block:: yaml

    name: vendor/content-block-name
    fields:
        - identifier: header
          useExistingField: true
        - identifier: bodytext
          useExistingField: true
          enableRichtext: true
        - identifier: image
          useExistingField: true

For page types, you may reuse the `media` field, which is commonly known from
the "Resources" tab. It is not included by default, but can be used as needed.

The full list:

*  `media`
*  `categories`
*  `layout`
*  `author`
*  `author_email`
*  `newUntil`
*  `lastUpdated`

.. warning::

   It is not possible to override the properties below. The reason is they are
   used in the SqlSchema generation to provide a proper db type and in
   RelationHandler to resolve the records. The :yaml:`type` of a field must not
   be changed in any case.

   * type
   * relationship
   * dbType
   * nullable
   * MM
   * MM_opposite_field
   * MM_hasUidField
   * MM_oppositeUsage
   * allowed (type: Relation)
   * foreign_table
   * foreign_field
   * foreign_table_field
   * foreign_match_fields
   * ds
   * ds_pointerField
   * exclude
