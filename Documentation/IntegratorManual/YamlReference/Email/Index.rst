.. include:: /Includes.rst.txt
.. _field_type_email:

=====
Email
=====

The `Email` type creates an input field, which is validated against an email
pattern. If the input does not contain a valid email address, a flash message
warning will be displayed.

It corresponds with the TCA :php:`type => 'email'`.

SQL overrides via `alternativeSql` allowed: yes.

Properties
==========

.. rst-class:: dl-parameter

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

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-email>`.

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/email
    group: common
    fields:
      - identifier: email
        type: Email

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/email
    group: common
    fields:
      - identifier: email
        type: Email
        properties:
          autocomplete: true
          default: 'developer@localhost.de'
          placeholder: 'Enter your email address'
          required: true
