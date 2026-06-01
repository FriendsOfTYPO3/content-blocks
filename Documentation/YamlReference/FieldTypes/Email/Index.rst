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

   Placeholder text for the field. Can also be used as automatic language key
   in labels.xlf. See :ref:`here <api_automatic_language_keys>` for more
   information.

.. confval:: required
   :name: email-required
   :required: false
   :type: boolean
   :default: false

   If set, the field becomes mandatory.

.. confval:: autocomplete
   :name: email-autocomplete
   :required: false
   :type: boolean

   Enables or disables browser autocomplete for the field.

.. confval:: behaviour.allowLanguageSynchronization
   :name: email-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: eval
   :name: email-eval
   :required: false
   :type: string

   Configuration of field evaluation. For example :yaml:`trim` to strip
   whitespace from the value before saving.

.. confval:: fieldControl
   :name: email-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: email-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: email-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: mode
   :name: email-mode
   :required: false
   :type: string

   When set to :yaml:`useOrOverridePlaceholder`, a checkbox appears above the
   field allowing the user to override the placeholder value.

.. confval:: nullable
   :name: email-nullable
   :required: false
   :type: boolean
   :default: false

   Allows the database field to store a :sql:`NULL` value.

.. confval:: readOnly
   :name: email-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: size
   :name: email-size
   :required: false
   :type: integer
   :default: 30

   Abstract width of the input field. Minimum :yaml:`10`, maximum :yaml:`50`.

.. confval:: valuePicker
   :name: email-valuePicker
   :required: false
   :type: object

   Renders a select box next to the field from which predefined values can be
   inserted. Requires an :yaml:`items` array of objects with :yaml:`label` and
   :yaml:`value` keys.

   Example:

   .. code-block:: yaml

      valuePicker:
        items:
          - label: 'Support'
            value: 'support@example.com'
          - label: 'Sales'
            value: 'sales@example.com'

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
