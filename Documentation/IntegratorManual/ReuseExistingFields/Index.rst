.. include:: /Includes.rst.txt

.. _cb_reuse_existing_fields:

=====================
Reuse existing fields
=====================

It's possible to reuse already existing fields by using the :yaml:`useExistingField`
flag. By doing so, you can extend existing fields with your own properties on a
per element level. It is highly recommend to use the `header` field this way,
because it is used for the title on different places in the backend.

Reusing fields between different Content Blocks is only possible, if the option
:yaml:`prefixFields: false` is turned off. As soon as the :yaml:`identifier`
is the same, the field will only be generated once. Be careful to define the
same :yaml:`type` for the field. Settings can be overridden on a per
element basis the same way as with core fields. Here it is not needed to define
:yaml:`useExistingField`.

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

*   header
*   header_layout
*   header_position
*   date
*   header_link
*   subheader
*   bodytext
*   assets
*   image
*   media
*   imagewidth
*   imageheight
*   imageborder
*   imageorient
*   imagecols
*   image_zoom
*   bullets_type
*   table_delimiter
*   table_enclosure
*   table_caption
*   file_collections
*   filelink_sorting
*   filelink_sorting_direction
*   target
*   filelink_size
*   uploads_description
*   uploads_type
*   pages
*   selected_categories
*   category_field

For example, if you want to use the existing column :sql:`bodytext`, or
:sql:`header` or :sql:`image` you can do one of the following:

.. code-block:: yaml

    name: vendor/content-block-name
    group: common
    fields:
        -
            identifier: header
            useExistingField: true
        -
            identifier: bodytext
            useExistingField: true
            enableRichtext: true
        -
            identifier: image
            useExistingField: true
