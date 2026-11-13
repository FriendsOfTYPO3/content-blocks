.. include:: /Includes.rst.txt
.. _changelog-2.5:

===
2.5
===

Content Blocks version 2.5 adds a collection of small new features.

..  contents::

Feature
=======

Title Field for Collection
--------------------------

Collections can now define a :ref:`title <confval-collection-title>` for the
table they generate. Previously the table title always equalled the Collection
label, so Collections with the same label in several Content Blocks could not
be told apart, e.g. in the table permissions of backend user groups or in the
List module. The title can be defined in YAML or in :file:`labels.xlf` with the
key :xml:`COLLECTION_IDENTIFIER.title`. It defaults to the Collection label.

..  code-block:: yaml

    fields:
      - identifier: slides
        type: Collection
        label: Slides
        title: Hero Slides
