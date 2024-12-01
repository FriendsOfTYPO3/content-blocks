.. include:: /Includes.rst.txt
.. _api_prefixing:

===============
Field Prefixing
===============

Content Blocks comes with a built-in prefixing mechanism for your custom fields.
Whenever you create a new field, the identifier will be prefixed internally
with a combination of the :yaml:`name`. This is important in order to avoid
collisions with other Content Blocks that use the same identifier for their
field. Every new field you create will add a new database column to the table.
If you want to avoid creating new database columns, see how you can
:ref:`reuse and share <cb_reuse_existing_cb_fields>` fields between different
Content Blocks.

Configure prefixing behavior
============================

By default, prefixing is enabled. In case you don't want prefixing at all, you
can either disable it globally with :ref:`prefixFields <confval-root-prefixfields>`
or on a per field level with :ref:`prefixField <confval-field-types-prefixfield>`.

The default prefix type is :yaml:`full`. That means the complete :yaml:`name` is
used as a prefix. All dashes are removed and the slash will be converted to an
underscore:

..  code-block:: none

    my-vendor/my-element️ => myvendor_myelement

An alternative to the full prefix is the :yaml:`vendor` prefix. This option can
be set in :ref:`prefixType <confval-root-prefixtype>`. This does also work on a
per field level. By doing so, only the `vendor` part of :yaml:`name` is used as
a prefix. This is especially useful if you want all your fields to have the same
prefix. In case you just want to have a static prefix, which differs from your
vendor, you can set a fixed vendor prefix with :ref:`vendorPrefix <confval-root-vendorprefix>`.

Examples
========

..  code-block:: yaml

    # This will prefix all your fields with "myvendor_myelement"
    name: my-vendor/my-element
    prefixFields: true
    prefixType: full

..  code-block:: yaml

    # This will disable prefixing altogether
    name: my-vendor/my-element
    prefixFields: false

..  code-block:: yaml

    # This will prefix all your fields with "myvendor"
    name: my-vendor/my-element
    prefixFields: true
    prefixType: vendor

..  code-block:: yaml

    # This will prefix all your fields with "tx_foo"
    name: my-vendor/my-element
    prefixFields: true
    prefixType: vendor
    vendorPrefix: tx_foo

..  code-block:: yaml

    # This will disable prefixing only for the field "my_field"
    name: my-vendor/my-element
    prefixFields: true
    prefixType: full
    fields:
        - identifier: my_field
          type: Text
          prefixField: false
