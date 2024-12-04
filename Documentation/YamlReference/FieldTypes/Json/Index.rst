.. include:: /Includes.rst.txt
.. _field_type_json:

====
Json
====

The :yaml:`Json` type is for a textarea which will be rendered as a json editor.

Settings
========

..  confval-menu::
    :name: confval-json-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: cols
   :name: json-cols
   :required: false
   :type: integer
   :default: 30

   Size for the input field. Min value is 10 and max value is 50.

.. confval:: rows
   :name: json-rows
   :required: false
   :type: integer
   :default: 5

   Amount of rows for the textarea. Min value is 1 and max value is 20.

.. confval:: required
   :name: json-required
   :required: false
   :type: boolean
   :default: false

   If set, the Json textarea needs to be filled.

.. confval:: readOnly
   :name: json-readOnly
   :required: false
   :type: boolean
   :default: false

   If set, the Json textarea is read only.

.. confval:: enableCodeEditor
   :name: json-enableCodeEditor
   :required: false
   :type: boolean
   :default: true

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
