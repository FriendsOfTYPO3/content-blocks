.. include:: /Includes.rst.txt
.. _field_type_link:

====
Link
====

The "Link" type generates a simple `<input>` field, which handles different kinds
of links.

It corresponds with the TCA `type='input'` and `renderType='inputLink'`.


Properties
==========

.. rst-class:: dl-parameters

autocomplete
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   Controls the autocomplete attribute of a given input field. If set to true
   (default false), adds attribute autocomplete="on" to the input field allowing
   browser auto filling the field.

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

linkPopup
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   The link browser control is typically used with `type='input'` with
   `renderType='inputLink'` adding a button which opens a popup to select an
   internal link to a page, an external link or a mail address.

   allowedExtensions (string, list)
      Comma separated list of allowed file extensions. By default, all extensions
      are allowed.

   blindLinkFields (string, list)
      Comma separated list of link fields that should not be displayed. Possible
      values are `class`, `params`, `target` and `title`. By default, all link
      fields are displayed.

   blindLinkOptions (string, list)
      Comma separated list of link options that should not be displayed. Possible
      values are `file`, `folder`, `mail`, `page`, `spec`, `telephone` and `url`.
      By default, all link options are displayed.

   Example:

   .. code-block:: yaml

      linkPopup:
        allowedExtensions: 'pdf'
        blindLinkFields: 'target,title'
        blindLinkOptions: 'folder,spec,telefone,mail'
        windowOpenParameters: 'height=800,width=600'

max
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '700'
   :sep:`|`

   Value for the “maxlength” attribute of the `<input>` field. Javascript
   prevents adding more than the given number of characters.

placeholder
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Placeholder text for the field.

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

size
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '20'
   :sep:`|`

   Abstract value for the width of the `<input>` field.

valuePicker
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Renders a select box with static values next to the input field. When
   a value is selected in the box, the value is transferred to the field. Keys:

   items (array)
      An array with selectable items. Each item is an array with the first being
      the value transferred to the input field, and the second being the label
      in the select drop-down (LLL reference possible).

   Example:

   .. code-block:: yaml

      valuePicker:
        items:
          'https://www.typo3.org': TYPO3 CMS
          'https://www.typo3.com': TYPO3 GmbH

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: url
        type: Url
        properties:
          autocomplete: true
          default: 'https://typo3.org'
          linkPopup:
            allowedExtensions: 'pdf'
            blindLinkFields: 'target,title'
            blindLinkOptions: 'folder,spec,telefone,mail'
          max: 150
          placeholder: 'Placeholder text'
          required: false
          size: 20
          valuePicker:
            items:
              'https://www.typo3.org': TYPO3 CMS
              'https://www.typo3.com': TYPO3 GmbH
