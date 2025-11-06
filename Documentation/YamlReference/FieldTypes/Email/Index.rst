.. include:: /Includes.rst.txt
.. _field_type_email:

=====
Email
=====

The :yaml:`Email` type creates an input field, which is validated against an
email pattern. If the input does not contain a valid email address, a flash
message warning will be displayed.

Settings
========

..  confval-menu::
    :name: confval-email-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: email-default
   :required: false
   :type: string
   :default: ''

   Default value set if a new record is created.

.. confval:: placeholder
   :name: email-placeholder
   :required: false
   :type: string
   :default: ''

   Placeholder text for the field.

.. confval:: required
   :name: email-required
   :required: false
   :type: boolean
   :default: false

   If set, the field becomes mandatory.

.. confval:: searchable
   :name: email-searchable
   :required: false
   :type: boolean
   :default: true

   If set to false, the field will not be considered in backend search.

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
