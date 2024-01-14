.. include:: /Includes.rst.txt
.. _field_type_json:

====
Json
====

:php:`type => 'json' // TCA`

The :yaml:`Json` type is for a textarea which will be rendered as a json editor.

Settings
========

.. confval:: cols

   :Required: false
   :Type: integer
   :Default: 30

   Size for the input field. Min value is 10 and max value is 50.

.. confval:: rows

   :Required: false
   :Type: integer
   :Default: 5

   Amount of rows for the textarea. Min value is 1 and max value is 20.

.. confval:: required

   :Required: false
   :Type: boolean
   :Default: false

   If set, the Json textarea needs to be filled.

.. confval:: readonly

   :Required: false
   :Type: boolean
   :Default: false

   If set, the Json textarea is read only.

.. confval:: enableCodeEditor

   :Required: false
   :Type: boolean
   :Default: true

   In case :php:`enableCodeEditor` is set to :php:`true`, which is the default
   and the system extension `t3editor` is installed and active, the JSON value
   is rendered in the corresponding code editor. Otherwise it is rendered in a
   standard textarea HTML element.


Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/json
    fields:
      - identifier: json
        type: Json

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/json
    fields:
      - identifier: json
        type: Json
        required: true
        readOnly: true
        cols: 50
        rows: 10
        enableCodeEditor: false
        placeholder: '[{"foo": "bar"}]'
