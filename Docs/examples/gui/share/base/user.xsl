<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

	<!-- Lists -->
	<xsl:template match="user-list" mode="xhtml-list">
		<table class="list">
			<thead>
				<th class="text username">
					username
				</th>
				<th class="actions">
					actions
				</th>
			</thead>
			<tfoot>
				<tr>
					<td class="pager" colspan="0">
						<xsl:apply-templates select="pager" />
					</td>
					<td class="count" colspan="2">
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
			<td class="actions">
				<a href="users/{@id}/edit/">
					edit
				</a>
				<a href="users/{@id}/delete/">
					delete
				</a>
			</td>
		</tr>
	</xsl:template>
	
	<!-- Lists end -->

	<!-- Edit -->
	<xsl:template name="user-edit-username">
		<xsl:param name="username" />
		<div class="edit-container user-edit user-edit-username">
			<label for="user-edit-username">
				username
				<xsl:if test="/page/settings/get/username-error = true()">
					<span class="error">
						invalid username
					</span>
				</xsl:if>
			</label>
			<input type="text" name="username" id="user-edit-username" value="{$username}" />
		</div>
	</xsl:template>
	
	<xsl:template name="user-edit-password">
		<xsl:param name="password" />
		<div class="edit-container user-edit user-edit-password">
			<label for="user-edit-password">
				password
				<xsl:if test="/page/settings/get/password-error = true()">
					<span class="error">
						invalid password
					</span>
				</xsl:if>
			</label>
			<input type="password" name="password" id="user-edit-password" value="{$password}" />
		</div>
		<div class="edit-container user-edit user-edit-password-confirm">
			<label for="user-edit-password">
				Confirm password
				<xsl:if test="/page/settings/get/password-confirm-error = true()">
					<span class="error">
						invalid password
					</span>
				</xsl:if>
			</label>
			<input type="password" name="password-confirm" id="user-edit-password-confirm" class="text"/>			
		</div>
	</xsl:template>
	
	<xsl:template name="user-edit-email">
		<xsl:param name="email" />
		<div class="edit-container user-edit user-edit-email">
			<label for="user-edit-email">
				email
				<xsl:if test="/page/settings/get/email-error = true()">
					<span class="error">
						invalid email
					</span>
				</xsl:if>
			</label>
			<input type="text" name="email" id="user-edit-email" value="{$email}" />
		</div>
	</xsl:template>
	
	<xsl:template name="user-edit-activated">
		<xsl:param name="activated" />
		<div class="edit-container user-edit user-edit-activated">
			<label for="user-edit-activated">
				activated
				<xsl:if test="/page/settings/get/activated-error = true()">
					<span class="error">
						invalid activated
					</span>
				</xsl:if>
			</label>
			<input type="text" name="activated" id="user-edit-activated" value="{$activated}" />
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