.. include:: /Includes.rst.txt
.. _field_type_slug:

====
Slug
====

The :yaml:`Slug` type generates a slug field, which generates a unique string
for the record.

Settings
========

..  confval-menu::
    :name: confval-slug-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: eval
   :name: slug-eval
   :required: false
   :type: string

   :yaml:`unique`, :yaml:`uniqueInSite` or :yaml:`uniqueInPid`.

.. confval:: generatorOptions.fields
   :name: slug-generatorOptions.fields
   :required: false
   :type: array

   An array of fields to use for the slug generation. Adding multiple fields
   to the simple array results in a concatenation. In order to have fallback
   fields, a nested array must be used.

   Example:

   .. code-block:: yaml

      generatorOptions:
        fields:
          - header

.. confval:: generatorOptions.fieldSeparator
   :name: slug-generatorOptions.fieldSeparator
   :required: false
   :type: string

   Separator placed between slug parts built from multiple fields. If a field
   value contains this character, it is replaced by :yaml:`fallbackCharacter`.

.. confval:: generatorOptions.prefixParentPageSlug
   :name: slug-generatorOptions.prefixParentPageSlug
   :required: false
   :type: boolean

   Prepends the parent page slug to the generated slug. Only applies to page
   records.

.. confval:: generatorOptions.postModifiers
   :name: slug-generatorOptions.postModifiers
   :required: false
   :type: array

   List of PHP class names that are called after slug generation to apply
   custom modifications.

.. confval:: generatorOptions.regexReplacements
   :name: slug-generatorOptions.regexReplacements
   :required: false
   :type: object

   Key-value pairs for regex-based string replacement applied during slug
   generation. Keys are patterns, values are replacements.

.. confval:: generatorOptions.replacements
   :name: slug-generatorOptions.replacements
   :required: false
   :type: object

   Key-value pairs for plain string replacement applied to slug parts.

.. confval:: appearance.prefix
   :name: slug-appearance.prefix
   :required: false
   :type: string

   A user function that provides a string displayed in front of the input
   field.

.. confval:: behaviour.allowLanguageSynchronization
   :name: slug-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: default
   :name: slug-default
   :required: false
   :type: string

   Default value set if a new record is created.

.. confval:: fallbackCharacter
   :name: slug-fallbackCharacter
   :required: false
   :type: string

   Character used as a replacement when a slug section contains the
   :yaml:`generatorOptions.fieldSeparator`.

.. confval:: fieldControl
   :name: slug-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: slug-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: slug-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: prependSlash
   :name: slug-prependSlash
   :required: false
   :type: boolean

   Whether the slug field should contain a leading slash. Useful for nested
   records with speaking URL segments.

.. confval:: readOnly
   :name: slug-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: size
   :name: slug-size
   :required: false
   :type: integer
   :default: 30

   Abstract width of the input field. Minimum :yaml:`10`, maximum :yaml:`50`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/slug
    fields:
      - identifier: slug
        type: Slug
        eval: unique
        generatorOptions:
          fields:
            - header

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/slug
    fields:
      - identifier: slug
        type: Slug
        eval: unique
        generatorOptions:
          fields:
            -
              - header
              - fallbackField
            - date
