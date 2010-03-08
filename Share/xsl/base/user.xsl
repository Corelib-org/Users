<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

	<!-- Lists -->
	<xsl:template match="user-list" mode="xhtml-list">
		<table class="list">
			<thead>
				<th class="text username">
					username
				</th>
				<th class="text email">
					email
				</th>
				<th class="boolean activated">
					activated
				</th>
				<th class="boolean deleted">
					deleted
				</th>
				<th class="date create-timestamp">
					create-timestamp
				</th>
				<th class="date last-timestamp">
					last-timestamp
				</th>
				<th class="actions">
					actions
				</th>
			</thead>
			<tfoot>
				<tr>
					<td class="pager" colspan="2">
						<xsl:apply-templates select="pager" />
					</td>
					<td class="count" colspan="5">
						<xsl:value-of select="concat(@count, ' user')" />
					</td>
				</tr>
			</tfoot>
			<tbody>
				<xsl:apply-templates select="user" mode="xhtml-list" />
			</tbody>
		</table>
	</xsl:template>
	<xsl:template match="user-list/user" mode="xhtml-list">
		<tr>
			<td class="text username">
				<xsl:value-of select="@username" />
			</td>
			<td class="text email">
				<xsl:value-of select="@email" />
			</td>
			<td class="boolean activated">
				<xsl:value-of select="@activated" />
			</td>
			<td class="boolean deleted">
				<xsl:value-of select="@deleted" />
			</td>
			<td class="date create-timestamp">
				<xsl:value-of select="@create-timestamp" />
			</td>
			<td class="date last-timestamp">
				<xsl:value-of select="@last-timestamp" />
			</td>
			<td class="actions">
				<a href="corelib/extensions/Users/{@id}/edit/">
					edit
				</a>
				<a href="corelib/extensions/Users/{@id}/delete/">
					delete
				</a>
			</td>
		</tr>
	</xsl:template>
	
	<xsl:template match="user-list" mode="form-select-options">
		<xsl:param name="select" />
		<xsl:apply-templates select="user" mode="form-select-options">
			<xsl:with-param name="select" select="$select" />
		</xsl:apply-templates>
	</xsl:template>
	<xsl:template match="user-list/user" mode="form-select-options">
		<xsl:param name="select" />
		<xsl:element name="option">
			<xsl:attribute name="value">
				<xsl:value-of select="@id" />
			</xsl:attribute>
			<xsl:if test="@id = $select">
				<xsl:attribute name="selected">
					true
				</xsl:attribute>
			</xsl:if>
			<xsl:value-of select="@id" />
		</xsl:element>
	</xsl:template>
	
	<!-- Lists end -->

	<!-- Edit -->
	<xsl:template name="user-edit-username">
		<xsl:param name="username" />
		<label for="user-edit-username">
			Username
			<xsl:if test="/page/settings/get/username-error = true()">
				<span class="error">
					invalid username
				</span>
			</xsl:if>
		</label>
		<input type="text" name="username" class="text" id="user-edit-username" value="{$username}" />
		<div class="fielddesc">
		  <p>Valid characters are a-z and 0-9</p>
		</div>
	</xsl:template>
	
	<xsl:template name="user-edit-password">
		<xsl:param name="password" />
		<label for="user-edit-password">
			Password
			<xsl:if test="/page/settings/get/password-error = true()">
				<span class="error">
					invalid password
				</span>
			</xsl:if>
		</label>
		<input type="password" name="password" id="user-edit-password" class="text"/>
		<div class="fielddesc">
		  <p></p>
		</div>		
		<label for="user-edit-password">
			Confirm password
			<xsl:if test="/page/settings/get/password-error = true()">
				<span class="error">
					invalid password
				</span>
			</xsl:if>
		</label>
		<input type="password" name="confirm-password" id="user-edit-password" class="text"/>
		<div class="fielddesc">
		  <p>Retype the password to confirm it.</p>
		</div>		
	</xsl:template>
	
	<xsl:template name="user-edit-email">
		<xsl:param name="email" />
		<label for="user-edit-email">
			E-mail address
			<xsl:if test="/page/settings/get/email-error = true()">
				<span class="error">
					invalid email
				</span>
			</xsl:if>
		</label>
		<input type="text" name="email" class="text" id="user-edit-email" value="{$email}" />
		<div class="fielddesc">
		  <p>Input a valid e-mail address</p>
		</div>
	</xsl:template>
	
	<xsl:template name="user-edit-activated">
		<xsl:param name="activated" />
		<label for="user-edit-activated">
			activated
			<xsl:if test="/page/settings/get/activated-error = true()">
				<span class="error">
					invalid activated
				</span>
			</xsl:if>
		</label>
		<span class="radio-container">
			<input type="radio" name="activated" id="activated_active" value="true"/> <label for="activated_active"> Activated</label>
			<input type="radio" name="activated" id="activated_inactive" value="false"/><label for="activated_inactive"> Inactive</label>
		</span>
		<div class="fielddesc">
		  <p></p>
		</div>
	</xsl:template>
	
	<xsl:template name="user-edit-activation-string">
		<xsl:param name="activation-string" />
		<label for="user-edit-activation-string">
			activation-string
			<xsl:if test="/page/settings/get/activation-string-error = true()">
				<span class="error">
					invalid activation-string
				</span>
			</xsl:if>
		</label>
		<input type="text" name="activation-string" id="user-edit-activation-string" value="{$activation-string}" />
		<div class="fielddesc">
		  <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam cursus. Sed metus massa, luctus vel, nonummy ut, blandit quis, magna.</p>
		</div>
	</xsl:template>
	
	<xsl:template name="user-edit-deleted">
		<xsl:param name="deleted" />
		<label for="user-edit-deleted">
			deleted
			<xsl:if test="/page/settings/get/deleted-error = true()">
				<span class="error">
					invalid deleted
				</span>
			</xsl:if>
		</label>
		<input type="text" name="deleted" id="user-edit-deleted" value="{$deleted}" />
		<div class="fielddesc">
		  <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam cursus. Sed metus massa, luctus vel, nonummy ut, blandit quis, magna.</p>
		</div>
	</xsl:template>
	
	<xsl:template name="user-edit-last-timestamp">
		<xsl:param name="last-timestamp" />
		<label for="user-edit-last-timestamp">
			last-timestamp
			<xsl:if test="/page/settings/get/last-timestamp-error = true()">
				<span class="error">
					invalid last-timestamp
				</span>
			</xsl:if>
		</label>
		<input type="text" name="last-timestamp" id="user-edit-last-timestamp" value="{$last-timestamp}" />
		<div class="fielddesc">
		  <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam cursus. Sed metus massa, luctus vel, nonummy ut, blandit quis, magna.</p>
		</div>		
	</xsl:template>
	
	<!-- Edit end -->


	<!-- View -->
	<xsl:template name="user-view-field">
		<xsl:param name="name" />
		<xsl:param name="value" />
		<div class="user-view-field">
			<span>
				<xsl:value-of select="$name" />
			</span>
			<xsl:value-of select="$value" />
		</div>
	</xsl:template>
	<!-- View end -->

</xsl:stylesheet>
