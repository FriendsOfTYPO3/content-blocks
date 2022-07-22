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
var __decorate=function(e,t,o,n){var l,c=arguments.length,i=c<3?t:null===n?n=Object.getOwnPropertyDescriptor(t,o):n;if("object"==typeof Reflect&&"function"==typeof Reflect.decorate)i=Reflect.decorate(e,t,o,n);else for(var r=e.length-1;r>=0;r--)(l=e[r])&&(i=(c<3?l(i):c>3?l(t,o,i):l(t,o))||i);return c>3&&i&&Object.defineProperty(t,o,i),i};import{html,LitElement}from"lit";import{customElement,property,state}from"lit/decorators.js";import{MainController}from"@typo3/content-blocks/controller/main-controller.js";import{until}from"lit/directives/until.js";import"@typo3/backend/element/spinner-element.js";import"@typo3/backend/element/icon-element.js";import"@typo3/content-blocks/element/content_blocks-edit-inspector-element.js";let ContentBlocksEditElement=class extends LitElement{constructor(){super(...arguments),this._controller=MainController.instance(this)}createRenderRoot(){return this}connectedCallback(){super.connectedCallback(),this._controller.loadContentBlock(this.cType)}render(){return html`
      <main>
${until(this._contentBlockFields(),html`
        <tr>
          <td colspan=99>
            <typo3-backend-spinner></typo3-backend-spinner>
          </td>
        </tr>
      `)}
      </main>
      <aside role="region" aria-labelledby="t3-contentblocks-inspector-label">
        <h2 id="t3-contentblocks-inspector-label">xlf:Inspector</h2>
        <typo3-content_blocks-edit-inspector
          cType="${this.cType}"
          field-identifier="${this._activeField?.identifier}"
        ></typo3-content_blocks-edit-inspector>
      </aside>
    `}async _contentBlockFields(){const e=await this._controller.currentContentBlock;return html`
      <h2>${e.key}</h2>
      <p>
        Path: <code>${e.path}</code>
      </p>
      <p>
        cType: <code>${e.CType}</code>
      </p>
      <fieldset>
        <legend>Fields</legend>
  ${Object.entries(e.fields).map((([e,t])=>html`
      <a href="" @click="${e=>{this._activeField=t,e.preventDefault()}}">
        <div class="card ${this._activeField===t?"border-primary":""} mb-3">
          <h5 class="card-header">todo: field title (from xlf)</h5>
          <div class="card-body">
            <h5 class="card-title">${t.type}</h5>
            <p class="card-text">
              <code>${t.identifier}</code>
            </p>
          </div>
        </div>
      </a>
    `))}
      </fieldset>
    `}};__decorate([property()],ContentBlocksEditElement.prototype,"cType",void 0),__decorate([state()],ContentBlocksEditElement.prototype,"_activeField",void 0),ContentBlocksEditElement=__decorate([customElement("typo3-content_blocks-edit")],ContentBlocksEditElement);export{ContentBlocksEditElement};