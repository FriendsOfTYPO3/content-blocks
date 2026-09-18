.. include:: /Includes.rst.txt
.. _field_type_password:

========
Password
========

The :yaml:`Password` type generates a password field.

Settings
========

..  confval-menu::
    :name: confval-password-options
    :display: table
    :type:
    :default:
    :required:

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

   Placeholder text for the field. Can also be used as automatic language key
   in labels.xlf. See :ref:`here <api_automatic_language_keys>` for more
   information.

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

.. confval:: autocomplete
   :name: password-autocomplete
   :required: false
   :type: boolean
   :default: false

   By default the rendered input field carries :html:`autocomplete="new-password"`.
   Set this to :yaml:`true` to emit :html:`autocomplete="current-password"` instead.

.. confval:: default
   :name: password-default
   :required: false
   :type: string
   :default: ''

   Default value set if a new record is created.

.. confval:: mode
   :name: password-mode
   :required: false
   :type: string
   :default: ''

   Related to the :yaml:`placeholder` property. The only allowed value is
   :yaml:`useOrOverridePlaceholder`. When defined, a checkbox is rendered above
   the field. While unchecked, the field is read-only and :sql:`NULL` is stored.
   Requires :yaml:`nullable` to be set to :yaml:`true`.

.. confval:: nullable
   :name: password-nullable
   :required: false
   :type: boolean
   :default: false

   Allows :sql:`NULL` values to be stored for this field.

.. confval:: readOnly
   :name: password-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

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
