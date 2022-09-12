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
import AjaxRequest from"@typo3/core/ajax/ajax-request.js";export class MainController{constructor(){this._hosts=[]}static instance(t){return null==this._instance&&(this._instance=new MainController),t&&(this._instance._hosts.push(t),t.addController(this._instance)),this._instance}async contentBlocks(){if(this._contentBlocks)return this._contentBlocks;const t=MainController.urls.ajax.contentBlocks.list;try{const n=new AjaxRequest(t).get();return this._contentBlocks=n.then((t=>t.resolve())),this._contentBlocks}catch{throw new Error(`todo:: fetch errör ${t}`)}}loadContentBlock(t){const n=MainController.urls.ajax.contentBlock.get+"&cType="+encodeURIComponent(t);try{new AjaxRequest(n).get().then((t=>{t.resolve("json").then((t=>{this.currentContentBlock=t,this.requestUpdate()}))}))}catch{throw new Error(`todo:: fetch errör ${n}`)}}hostConnected(){}requestUpdate(){this._hosts.forEach((t=>t.requestUpdate()))}}MainController.urls={ajax:{contentBlock:{get:TYPO3.settings.ajaxUrls["tools_contentblocks/ajax/contentBlock/get/json"]},contentBlocks:{list:TYPO3.settings.ajaxUrls["tools_contentblocks/ajax/contentBlocks/list/json"]}},contentBlocks:{edit:TYPO3.settings.urls["contentblocks/contentBlock/edit"]}};