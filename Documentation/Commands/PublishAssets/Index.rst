.. include:: /Includes.rst.txt
.. _command_publish_assets:

======================
Publish Assets Command
======================

The command :bash:`content-blocks:assets:publish` publishes your public Content
Block `assets` into the Resources/Public folder of the host extension. Normally,
this is performed automatically every time Content Blocks is compiled. In some
deployment scenarios this command could be performed in the CI pipeline to
publish assets without the requirement for a database connection.
