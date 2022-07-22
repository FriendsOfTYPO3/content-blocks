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
var __decorate=function(t,e,o,n){var i,a=arguments.length,l=a<3?e:null===n?n=Object.getOwnPropertyDescriptor(e,o):n;if("object"==typeof Reflect&&"function"==typeof Reflect.decorate)l=Reflect.decorate(t,e,o,n);else for(var c=t.length-1;c>=0;c--)(i=t[c])&&(l=(a<3?i(l):a>3?i(e,o,l):i(e,o))||l);return a>3&&l&&Object.defineProperty(e,o,l),l};import{html,LitElement}from"lit";import{customElement}from"lit/decorators.js";import{MainController}from"@typo3/content-blocks/controller/main-controller.js";import{until}from"lit/directives/until.js";import"@typo3/backend/element/spinner-element.js";import"@typo3/backend/element/icon-element.js";let ContentBlocksList=class extends LitElement{constructor(){super(...arguments),this._controller=MainController.instance(this)}createRenderRoot(){return this}render(){return html`
      <div class="table-fit">
        <table id="content_blocks-list" class="table table-striped table-hover">
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
          ${until(this._contentBlocksTableRows(),html`
            <tr>
              <td colspan=99>
                <typo3-backend-spinner></typo3-backend-spinner>
              </td>
            </tr>`)}
          </tbody>
        </table>
      </div>
    `}async _contentBlocksTableRows(){const t=await this._controller.contentBlocks();return Object.entries(t).map((([t,e])=>html`
        <tr>
          <td class="col-icon">
              <span title="id=TODO:" data-bs-toggle="tooltip" data-bs-placement="right">
                <typo3-backend-icon identifier="actions-question" size="small"></typo3-backend-icon>
              </span>
          </td>
          <td class="col-title">
            <a href="" title="TODO_XLF:edit">
              ${e.key}
            </a>
          </td>
          <td>
            <code>${e.path}</code>
          </td>
          <td class="col-control">
            <a href="" data-identifier="showReferences">
              <span class="badge badge-info">
                xxx
                xlf:references
              </span>
            </a>
          </td>
          <td>
            <div class="btn-group" role="group">
              <a href=""
                title="XLF:edit"
                class="btn btn-default form-record-open">
                <typo3-backend-icon identifier="actions-open" size="small"></typo3-backend-icon>
              </a>
            </div>
            <div class="btn-group dropdown position-static">
              <a href=""
                class="btn btn-default dropdown-toggle dropdown-toggle-no-chevron"
                data-bs-toggle="dropdown" data-bs-boundary="window" aria-expanded="false">
                <typo3-backend-icon identifier="actions-menu-alternative" size="small"></typo3-backend-icon>
                <typo3-backend-icon identifier="actions-caret-down"></typo3-backend-icon>
              </a>
              <ul class="dropdown-menu dropdown-list">
                <li>
                  <a href="#" class="dropdown-item"
                    data-bs-original-title="xlf:duplicate"
                    data-identifier="duplicateForm"
                  >
                    <typo3-backend-icon identifier="actions-duplicate" size="small"></typo3-backend-icon>
                    XLF:duplicate
                  </a>
                </li>
                <li>
                  <a href="#" class="dropdown-item">
                    <typo3-backend-icon identifier="actions-eye-link" size="small"></typo3-backend-icon>
                    XLF:show_references
                  </a>
                </li>
                <li>
                  <a href="#" class="dropdown-item"
                    data-bs-original-title="xlf:delete_form"
                    data-identifier="removeForm"
                  >
                    <typo3-backend-icon identifier="actions-edit-delete" size="small"></typo3-backend-icon>
                    XLF:delete
                  </a>
                </li>
              </ul>
            </div>
          </td>
          <td></td>
        </tr>
      `))}};ContentBlocksList=__decorate([customElement("typo3-content_blocks-list")],ContentBlocksList);export{ContentBlocksList};