.. include:: /Includes.rst.txt
.. _field_type_textarea:

========
Textarea
========

The "Textarea" s for multi line text input. A Rich Text Editor can be enabled
by property.

It corresponds with the TCA `type='text'` (default).


Properties
==========

.. rst-class:: dl-parameters

cols
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '24'
   :sep:`|`

   Abstract value for the width of the `<textarea>` field.

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

enableRichtext
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set to true, the system renders a Rich Text Editor if that is enabled for
   the editor (default: yes), and if a suitable editor extension is loaded
   (default: rteckeditor).

   If either of these requirements is not met, the system falls back to
   a `<textarea>` field.

max
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '700'
   :sep:`|`

   Adds the HTML5 attribute “maxlength” to a textarea. Prevents the field from
   adding more than specified number of characters. This is a client side
   restriction, no server side length restriction is enforced.

   Does not apply for RTE fields.

placeholder
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Placeholder text for the field.


richtextConfiguration
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   The value is a key in :php:`$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']`
   array and specifies the YAML configuration source field used for that RTE
   field. It does not make sense without having property `enableRichtext` set to
   true.

   Extension `rte_ckeditor` registers three presets: `default`, `minimal` and
   `full` and points to YAML files with configuration details.

   Integrators may override for instance the `default` key to point to an own
   YAML file which will affect all core backend RTE instances to use that
   configuration.

   If this property is not specified for an RTE field, the system will fall back
   to the `default` configuration.

rows
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '3'
   :sep:`|`

   Abstract value for the height of the `<textarea>` field.

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

trim
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the PHP trim function is applied on the field's content.

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: textarea
        type: Textarea
        properties:
          cols: 40
          default: 'Default value'
          enableRichtext: true
          max: 150
          placeholder: 'Placeholder text'
          richtextConfiguration: default
          rows: 15
          required: false
          trim: true
