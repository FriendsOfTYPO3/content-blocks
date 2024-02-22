.. include:: /Includes.rst.txt

.. confval:: allowedCustomProperties

   :Required: false
   :Type: array
   :Default: ["itemsProcConfig"]

   Sometimes it is needed to provide custom configuration for the :ref:`itemsProcFunc <t3tca:tca_property_itemsProcFunc>`
   functionality. These extra properties need to be explicitly allowed via this
   option. This option receives an array of those strings. By default the
   custom option :yaml:`itemsProcConfig` is allowed.
