/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
var __decorate=function(e,t,a,r){var o,i=arguments.length,n=i<3?t:null===r?r=Object.getOwnPropertyDescriptor(t,a):r;if("object"==typeof Reflect&&"function"==typeof Reflect.decorate)n=Reflect.decorate(e,t,a,r);else for(var f=e.length-1;f>=0;f--)(o=e[f])&&(n=(i<3?o(n):i>3?o(t,a,n):o(t,a))||n);return i>3&&n&&Object.defineProperty(t,a,n),n};import{html,LitElement}from"lit";import{customElement}from"lit/decorators.js";import{MainController}from"@typo3/content_blocks/controller/main-controller.js";import{until}from"lit/directives/until.js";import"@typo3/backend/element/spinner-element.js";import"@typo3/backend/element/icon-element.js";let ContentBlocksList=class extends LitElement{constructor(){super(...arguments),this.controller=MainController.instance(this)}render(){return html`
      <div class="table-fit">
        <table id="forms" class="table table-striped table-hover">
          <thead>
          <tr>
            <th></th>
            <th>
              XLF:name
            </th>
            <th>
              XLF:location
            </th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr>
            ${until(this.controller.contentBlocks().then((e=>e)),html`loading...`)}
            <td class="col-icon">
              <span title="id=TODO:" data-bs-toggle="tooltip" data-bs-placement="right">
                TOOD: icon
              </span>
            </td>
            <td class="col-title">
                <a href="" title="TODO_XLF:edit">
                  <typo3-backend-icon identifier="actions-edit"
                    alternativeMarkupIdentifier="inline" size="small"
                    class="icon icon-size-small"></typo3-backend-icon>
                </a>
            </td>
            <td><code>{form.persistenceIdentifier}</code></td>
            <td class="col-control">
              <f:if condition="{form.referenceCount}">
                <f:then>
                  <a href="#" data-identifier="showReferences" data-form-persistence-identifier="{form.persistenceIdentifier}" data-form-name="{form.name}">
                                                <span class="badge badge-info">
                                                    {form.referenceCount} <f:translate key="LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.references"/>
                                                </span>
                  </a>
                </f:then>
                <f:else>
                                            <span class="badge badge-default">
                                                {form.referenceCount} <f:translate key="LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.references"/>
                                            </span>
                </f:else>
              </f:if>
            </td>
            <td>
              <div class="btn-group" role="group">
                <f:if condition="{form.invalid} || {form.readOnly}">
                  <f:then>
                    <button class="btn btn-default form-record-readonly" disabled="disabled" title="{f:translate(key: 'LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.edit_form_not_allowed')}"><core:icon identifier="actions-open" /></button>
                  </f:then>
                  <f:else>
                    <f:link.action controller="FormEditor" action="index" arguments="{formPersistenceIdentifier: form.persistenceIdentifier}" title="{f:translate(key: 'LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.edit_form')}" class="btn btn-default form-record-open"><core:icon identifier="actions-open" /></f:link.action>
                  </f:else>
                </f:if>
                <f:if condition="{form.invalid}">
                  <button class="btn btn-default form-record-readonly" disabled="disabled" title="{f:translate(key: 'LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.duplicate_form_not_allowed')}"><core:icon identifier="actions-duplicate" /></button>
                </f:if>
              </div>
              <div class="btn-group dropdown position-static">
                <a href="#actions-{form.identifier}" class="btn btn-default dropdown-toggle dropdown-toggle-no-chevron" data-bs-toggle="dropdown" data-bs-boundary="window" aria-expanded="false"><core:icon identifier="actions-menu-alternative" /></a>
                <ul id="actions-{form.identifier}" class="dropdown-menu dropdown-list">
                  <f:if condition="{form.invalid}">
                    <f:else>
                      <li>
                        <a href="#" class="dropdown-item" data-bs-original-title="{f:translate(key: 'LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.duplicate_this_form')}" data-identifier="duplicateForm" data-form-persistence-identifier="{form.persistenceIdentifier}" data-form-name="{form.name}">
                          <core:icon identifier="actions-duplicate" />
                          <f:translate key="LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.duplicate_this_form" />
                        </a>
                      </li>
                    </f:else>
                  </f:if>
                  <f:if condition="{form.referenceCount}">
                    <li>
                      <a href="#" class="dropdown-item" data-bs-original-title="{f:translate(key: 'LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.show_references')}" data-identifier="showReferences" data-form-persistence-identifier="{form.persistenceIdentifier}" data-form-name="{form.name}">
                        <core:icon identifier="actions-eye-link" />
                        <f:translate key="LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.show_references" />
                      </a>
                    </li>
                  </f:if>
                  <f:if condition="{form.removable} && ({form.invalid} == '0') && ({form.referenceCount} == '0')">
                    <li>
                      <a href="#" class="dropdown-item" data-bs-original-title="{f:translate(key: 'LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.delete_form')}" data-identifier="removeForm" data-form-persistence-identifier="{form.persistenceIdentifier}">
                        <core:icon identifier="actions-edit-delete" />
                        <f:translate key="LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.delete_form" />
                      </a>
                    </li>
                  </f:if>
                </ul>
              </div>
            </td>
            <td></td>
          </tr>
          </tbody>
        </table>
      </div>
    `}};ContentBlocksList=__decorate([customElement("typo3-content_blocks-list")],ContentBlocksList);