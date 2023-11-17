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

Settings
========

.. confval:: default

   :Required: false
   :Type: string
   :Default: ''

   Default value set if a new record is created.

.. confval:: placeholder

   :Required: false
   :Type: string
   :Default: ''

   Placeholder text for the field.

.. confval:: required

   :Required: false
   :Type: boolean
   :Default: false

   If set, the field becomes mandatory.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-email>`.

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/email
    fields:
      - identifier: email
        type: Email

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/email
    fields:
      - identifier: email
        type: Email
        autocomplete: true
        default: 'developer@localhost.de'
        placeholder: 'Enter your email address'
        required: true
