.. include:: /Includes.rst.txt
.. _field_type_country:

=======
Country
=======

The :yaml:`Country` type provides a country selection.

Settings
========

..  confval-menu::
    :name: confval-country-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: country-default
   :required: false
   :type: string
   :default: ''

   Default value set if a new record is created.

.. confval:: required
   :name: country-required
   :required: false
   :type: boolean
   :default: false

   If true, an empty selection cannot be made.

.. confval:: filter
   :name: country-filter
   :required: false
   :type: array
   :default: []

   With the filter option it is possible to constrain the possible countries.

   .. code-block:: yaml

    name: example/country
    fields:
      - identifier: country
        type: Country
        filter:
          onlyCountries:
            - DE
            - AT
            - CH

   .. code-block:: yaml

    name: example/country
    fields:
      - identifier: country
        type: Country
        filter:
          excludeCountries:
            - DE
            - AT
            - CH

.. confval:: labelField
   :name: country-labelField
   :required: false
   :type: string
   :default: name

   The value to use for the label. Possible values are:

   * :yaml:`localizedName`
   * :yaml:`name`
   * :yaml:`iso2`
   * :yaml:`iso3`
   * :yaml:`officialName`
   * :yaml:`localizedOfficialName`

.. confval:: prioritizedCountries
   :name: country-prioritizedCountries
   :required: false
   :type: array
   :default: []

   Countries (ISO2 or ISO3 codes) which are listed before all others countries.

   .. code-block:: yaml

    name: example/country
    fields:
      - identifier: country
        type: Country
        prioritizedCountries:
          - DE
          - AT
          - CH

.. confval:: sortItems
   :name: country-sortItems
   :required: false
   :type: array
   :default: []

   Sort order of the select items in the country picker.

   .. code-block:: yaml

    name: example/country
    fields:
      - identifier: country
        type: Country
        sortItems:
          label: 'desc'

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/country
    fields:
      - identifier: country
        type: Country

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/country
    fields:
      - identifier: country
        type: Country
        required: true
        default: 'DE'
        labelField: 'localizedName'
        prioritizedCountries:
          - DE
          - AT
          - CH
