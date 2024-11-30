.. include:: /Includes.rst.txt
.. _field_type_password:

========
Password
========

:php:`type => 'password' // TCA`

The :yaml:`Password` type generates a password field.

Settings
========

.. confval:: hashed
   :name: password-hashed
   :required: false
   :type: bool
   :default: true

   Whether the password should be hashed with the configured hashing algorithm.
   Set this value to :yaml:`false` to disable hashing.

.. confval:: passwordPolicy
   :name: password-passwordPolicy
   :required: false
   :type: string

   The :ref:`password policy <t3coreapi:password-policies>` will ensure, that
   the new password complies with the configured password policy.

   Password policy requirements are shown below the password field, when the
   focus is changed to the password field.

.. confval:: placeholder
   :name: password-placeholder
   :required: false
   :type: string

   Placeholder text for the field.

.. confval:: required
   :name: password-required
   :required: false
   :type: boolean
   :default: false

   If set, the field becomes mandatory.

.. confval:: size
   :name: password-size
   :required: false
   :type: integer

   Abstract value for the width of the `<input>` field.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-password>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/password
    fields:
      - identifier: password
        type: Password

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/password
    fields:
      - identifier: password
        type: Password
        required: true
        hashed: false
        passwordPolicy: 'default'
        fieldControl:
          passwordGenerator:
            renderType: passwordGenerator
