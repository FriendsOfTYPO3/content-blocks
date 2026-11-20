.. include:: /Includes.rst.txt
.. _api_tabs:

====
Tabs
====

With a growing number of fields, a single :file:`config.yaml` becomes hard to
read. Tabs created with the field type :ref:`Tab <field_type_tab>` are
placed between fields, which makes it difficult to see where one tab ends and
the next one begins. Moving fields into :ref:`Basics <basics>` is not a good
fit either, as Basics are global and meant to be shared between multiple
Content Blocks.

Instead, each tab can be defined in its own YAML file inside the folder
:path:`tabs` of the Content Block. The tabs are then referenced by their file
name (without the extension) in the root option :confval:`tabs <root-tabs>`.
This keeps the Content Block self-contained, so it can be copied to another
project without any dependencies.

..  card::
    :class: mb-4

    ..  directory-tree::
        :level: 3

            *   :path:`my-content-block`

                *   :path:`tabs`

                    *   :file:`media.yaml`
                    *   :file:`settings.yaml`

                *   :file:`config.yaml`

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/my-content-block/config.yaml

    name: example/my-content-block
    fields:
        - identifier: header
          useExistingField: true
    tabs:
        - media
        - settings
    basics:
        - TYPO3/Appearance

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/my-content-block/tabs/settings.yaml

    fields:
        - identifier: layout
          type: Select
          renderType: selectSingle
          items:
              - label: Default
                value: default

Each tab file must define a :yaml:`fields` list. It can contain any field
type, which is also allowed in the root :yaml:`fields` of the
:file:`config.yaml`, including :ref:`Palettes <field_type_palette>` and
:ref:`Basics <field_type_basic>`. Files in the folder :path:`tabs`, which are
not referenced in :yaml:`tabs`, are ignored.

Order
=====

Tabs are converted into a field of type :yaml:`Tab` followed by the fields of
the tab file. The resulting order is:

#.  The root :yaml:`fields`. These are displayed in the "General" tab.
#.  The :yaml:`tabs` in the order in which they are listed.
#.  The global :confval:`basics <root-basics>`.

If the root :yaml:`fields` are omitted, the first tab replaces the "General"
tab, in the same way as a :yaml:`Tab` field at the very beginning does.

Identifier and labels
=====================

The identifier of a tab defaults to its file name. It can be overridden with
the option :yaml:`identifier`. The label is resolved with the same convention
as for the field type :ref:`Tab <field_type_tab>`, namely with the
translation key :xml:`tabs.TAB_IDENTIFIER`. The option :yaml:`label` can be
used as a fallback, if no translation exists.

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/my-content-block/tabs/settings.yaml

    identifier: options
    label: Options
    fields:
        - identifier: layout
          type: Text

.. code-block:: xml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/my-content-block/language/labels.xlf

    <trans-unit id="tabs.options">
        <source>Options</source>
    </trans-unit>

.. note::

   Tab identifiers must be unique within a Content Block. This includes tabs
   added by :yaml:`Tab` fields and Basics.

.. note::

   Tabs are only available on root level. They can't be used inside
   :ref:`Collections <field_type_collection>`.
