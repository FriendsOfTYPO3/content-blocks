.. include:: /Includes.rst.txt
.. _field_type_tab:

===
Tab
===

The `Tab` field can be used to create a new tab in the editor interface. It
needs an unique `identifier` and can be placed between any two fields. Note: Not
allowed inside a `Palette`.

Labels.xlf
==========

The naming convention is `tabs.<identifier>`

Inside Collection each identifier must be prepended:

`<collection1>.<collection2>.tabs.<identifier>`

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
