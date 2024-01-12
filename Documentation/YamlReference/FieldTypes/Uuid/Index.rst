.. include:: /Includes.rst.txt
.. _field_type_uuid:

====
Uuid
====

:php:`type => 'uuid' // TCA`

The :yaml:`Uuid` type is for a text input which contains an uuid value.

Settings
========

.. confval:: size

   :Required: false
   :Type: integer
   :Default: 30

   Size for the input field. Min value is 10 and max value is 50.

.. confval:: version

   :Required: false
   :Type: integer
   :Default: 4

   Version for the uuid. Please have a look at the `Symphony Documentation <https://symfony.com/doc/current/components/uid.html#uuids>`__ for more information.


.. confval:: enableCopyToClipboard

   :Required: false
   :Type: boolean
   :Default: true

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
