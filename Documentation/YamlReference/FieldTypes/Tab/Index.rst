.. include:: /Includes.rst.txt
.. _field_type_tab:

===
Tab
===

The :yaml:`Tab` field can be used to create a new tab in the editor interface.
It needs an unique `identifier` and can be placed between any two fields. Note:
Prohibited inside `Palettes`.

Labels
======

XLF translation keys for Tabs have the following convention:

.. code-block:: xml

    <body>
        <trans-unit id="tabs.TAB_IDENTIFIER">
            <source>Label for Tab</source>
        </trans-unit>
        <trans-unit id="COLLECTION_IDENTIFIER.tabs.TAB_IDENTIFIER">
            <source>Label for Tab in Collection</source>
        </trans-unit>
        <trans-unit id="COLLECTION_IDENTIFIER1.COLLECTION_IDENTIFIER2.tabs.TAB_IDENTIFIER">
            <source>Label for Tab in nested Collection</source>
        </trans-unit>
    </body>


Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/tab
    fields:
      - identifier: text
        type: Text
      - identifier: tab_1
        type: Tab
      - identifier: text2
        type: Textarea


For in-depth information about tabs refer to the :ref:`TCA documentation <t3tca:types-properties-showitem>`.
