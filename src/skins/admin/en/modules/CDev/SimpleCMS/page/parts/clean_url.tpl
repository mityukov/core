{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Clean URL
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 *}

<input{getAttributesCode():h}/>
<p />
<input type="checkbox" name="{getNamePostedData(#autogenerateCleanURL#)}" value="1" checked="{!getValue()}" id="autogenerateFlag" />
<label for="autogenerateFlag" class="note">{t(#Autogenerate Clean URL#)}</label>