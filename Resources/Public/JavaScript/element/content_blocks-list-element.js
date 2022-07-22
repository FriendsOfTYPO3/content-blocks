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
var __decorate=function(t,e,n,o){var i,l=arguments.length,a=l<3?e:null===o?o=Object.getOwnPropertyDescriptor(e,n):o;if("object"==typeof Reflect&&"function"==typeof Reflect.decorate)a=Reflect.decorate(t,e,n,o);else for(var c=t.length-1;c>=0;c--)(i=t[c])&&(a=(l<3?i(a):l>3?i(e,n,a):i(e,n))||a);return l>3&&a&&Object.defineProperty(e,n,a),a};import{html,LitElement}from"lit";import{customElement}from"lit/decorators.js";import{MainController}from"@typo3/content-blocks/controller/main-controller.js";import{until}from"lit/directives/until.js";import"@typo3/backend/element/spinner-element.js";import"@typo3/backend/element/icon-element.js";let ContentBlocksListElement=class extends LitElement{constructor(){super(...arguments),this._controller=MainController.instance(this)}createRenderRoot(){return this}render(){return html`
      <div class="table-fit">
        <table id="content_blocks-list" class="table table-striped table-hover">
          <thead>
          <tr>
            <th></th>
            <th>
              ${TYPO3.lang["contentblocks.contentblock.key"]}
            </th>
            <th>
              ${TYPO3.lang["contentblocks.contentblock.location"]}
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
            <a href="${MainController.urls.contentBlocks.edit}&amp;cType=${e.CType}" title="${TYPO3.lang["contentblocks.action.edit"]}">
              ${e.key}
            </a>
          </td>
          <td>
            <code>${e.path}</code>
          </td>
          <td class="col-control">
            <a href="" data-identifier="showReferences">
              <span class="badge badge-info">
                xlf:references
              </span>
            </a>
          </td>
          <td>
            <div class="btn-group" role="group">
              <a href=""
                title="${TYPO3.lang["contentblocks.action.edit"]}"
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
                    data-bs-original-title="${TYPO3.lang["contentblocks.action.duplicate"]}"
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
                    data-bs-original-title="${TYPO3.lang["cm.delete"]}"
                    data-identifier="delete"
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
      `))}};ContentBlocksListElement=__decorate([customElement("typo3-content_blocks-list")],ContentBlocksListElement);export{ContentBlocksListElement};