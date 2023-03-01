.. include:: /Includes.rst.txt

.. _cb_reuse_existing_fields:

=====================
Reuse existing fields
=====================

It is possible to reuse already existing fields by using the `useExistingField`
flag. Currently you can reuse only the standard core fields. Even when reusing
an existing field at least the type of that field is still required. However, it
is possible to extend existing fields with your own properties. While it's
possible to reuse standard core fields with the `useExistingField` flag, it's
not allowed on :ref:`collections <field_type_collection>`. We highly recommend
to use the header field this way, because it is used for the title on different
places in the backend.

It's recommended to use existing fields whenever possible instead of creating
new ones. This also avoids the risk of the :ref:`"Row size to large" <row-size-too-large>`
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

    group: common
    fields:
        -
            identifier: header
            type: Text
            useExistingField: true
        -
            identifier: bodytext
            type: Textarea
            useExistingField: true
            properties:
                enableRichtext: true
        -
            identifier: image
            type: File
            useExistingField: true
