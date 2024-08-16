.. include:: /Includes.rst.txt
.. _field_type_pass:

====
Pass
====

:php:`type => 'passthrough' // TCA`

The :yaml:`Pass` type provides a virtual field, which is not visible in the
backend. It is useful for extension logic handling this value independently.

Settings
========

.. confval:: default
   :name: pass-default

   :Required: false
   :Type: mixed
   :Default: ''

   Default value set if a new record is created.

   .. note::

      This does not work right now in some circumstances. See issue: https://forge.typo3.org/issues/104646

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/pass
    fields:
      - identifier: pass
        type: Pass
