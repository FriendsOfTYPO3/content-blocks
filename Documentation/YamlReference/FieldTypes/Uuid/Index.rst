.. include:: /Includes.rst.txt
.. _field_type_uuid:

====
Uuid
====

The :yaml:`Uuid` type is for a text input which contains an uuid value.

Settings
========

..  confval-menu::
    :name: confval-uuid-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: size
   :name: uuid-size
   :required: false
   :type: integer
   :default: 30

   Size for the input field. Min value is 10 and max value is 50.

.. confval:: version
   :name: uuid-version
   :required: false
   :type: integer
   :default: 4

   Version for the uuid. Please have a look at the `Symphony Documentation <https://symfony.com/doc/current/components/uid.html#uuids>`__ for more information.

.. confval:: enableCopyToClipboard
   :name: uuid-enableCopyToClipboard
   :required: false
   :type: boolean
   :default: true

   If set to false, the button for copying the uuid into the clipboard will not be rendered.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/uuid
    fields:
      - identifier: uuid
        type: Uuid

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/uuid
    fields:
      - identifier: uuid
        type: Uuid
        size: 50
        version: 7
        enableCopyToClipboard: false
