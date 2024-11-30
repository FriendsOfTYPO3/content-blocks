.. include:: /Includes.rst.txt
.. _cb_definition_language:

========
language
========

This is the folder for your translations. In fact, if you only have one
language, there is no actual need to maintain translations here. However, it is
best practice to separate labels and configuration.

labels.xlf
==========

This XLF file is the **english** basis for your translations. All translations
for backend labels as well as for frontend labels are defined here. Translations
to other languages are defined in separate files prefixed with the language code
e.g. **de.labels.xlf**.

*  Learn about the :ref:`language key convention <api_automatic_language_keys>`
*  Learn about the :ref:`XLIFF Format in TYPO3 <t3coreapi:xliff>`
