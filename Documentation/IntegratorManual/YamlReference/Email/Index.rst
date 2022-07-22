.. include:: /Includes.rst.txt
.. _field_type_email:

=====
Email
=====

The "Email" type generates an `<input>` field, specified for entry of email
addresses only.
This type adds a server-side validation of an email address. If the input does
not contain a valid email address, a flash message warning will be displayed.

It corresponds with the TCA `type='input'` (default) and `eval='email'`.


Properties
==========

.. rst-class:: dl-parameters

autocomplete
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   Controls the autocomplete attribute of a given input field. If set to true
   (default false), adds attribute autocomplete="on" to the input field allowing
   browser auto filling the field.

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

placeholder
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Placeholder text for the field.

size
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '20'
   :sep:`|`

   Abstract value for the width of the `<input>` field.

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

valuePicker
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Renders a select box with static values next to the input field. When
   a value is selected in the box, the value is transferred to the field. Keys:

   items (array)
      An array with selectable items. Each item is an array with the first being
      the value transferred to the input field, and the second being the label
      in the select drop-down (LLL reference possible).

   Example:

   .. code-block:: yaml

      valuePicker:
        items:
          'contact_1@example.com': Contact 1
          'contact_2@example.com': Contact 2
          'contact_3@example.com': Contact 3

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: email
        type: Email
        properties:
          autocomplete: true
          default: 'developer@localhost'
          placeholder: 'Enter your email address'
          size: 20
          required: true
          trim: true
          valuePicker:
            items:
              'contact_1@example.com': Contact 1
              'contact_2@example.com': Contact 2
              'contact_3@example.com': Contact 3
