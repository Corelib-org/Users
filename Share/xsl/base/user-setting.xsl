<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

	<!-- Lists -->
	<xsl:template match="user-setting-list" mode="xhtml-list">
		<table class="list">
			<thead>
				<th class="text ident">
					Ident
				</th>
				<th class="text value">
					Value
				</th>
				<th class="actions">
					Actions
				</th>
			</thead>
			<tfoot>
				<tr>
					<td class="pager" colspan="2">
						<xsl:apply-templates select="pager" />
					</td>
					<td class="count" colspan="1">
						<xsl:value-of select="concat(count(user-setting), ' settings')" />
					</td>
				</tr>
			</tfoot>
			<tbody>
				<xsl:apply-templates select="user-setting" mode="xhtml-list" />
			</tbody>
		</table>
	</xsl:template>
	<xsl:template match="user-setting-list/user-setting" mode="xhtml-list">
		<tr>
			<td class="text ident">
				<xsl:value-of select="@ident" />
			</td>
			<td class="text value">
				<xsl:value-of select="@value" />
			</td>
			<td class="actions">
				<a href="corelib/extensions/Users/{../../user/@id}/Settings/{@ident}/">
					<img src="corelib/resource/manager/images/icons/generic/edit.png" alt="edit" title="Edit setting"/>
				</a>
				<a href="corelib/extensions/Users/{../../user/@id}/Settings/{@ident}/delete/">
					<img src="corelib/resource/manager/images/icons/generic/delete.png" alt="delete" title="Delete setting"/>
				</a>
			</td>
		</tr>
	</xsl:template>
	
	<!-- Lists end -->

	<!-- Edit -->
	<xsl:template name="user-setting-edit-user">
		<xsl:param name="user" />
		<div class="edit-container user-setting-edit user-setting-edit-user">
			<label for="user-setting-edit-user">
				user
				<xsl:if test="/page/settings/get/user-error = true()">
					<span class="error">
						invalid user
					</span>
				</xsl:if>
			</label>
			<select name="user" id="user-setting-edit-user" />
		</div>
	</xsl:template>
	
	<xsl:template name="user-setting-edit-ident">
		<xsl:param name="ident" />
		
		<label for="user-setting-edit-ident">
			Ident
			<xsl:if test="/page/settings/get/ident-error = true()">
				<span class="error">
					invalid ident
				</span>
			</xsl:if>
		</label>
		<input type="text" name="ident" class="text" id="user-setting-edit-edit" value="{$ident}" />
		<div class="fielddesc">
			<p>Set setting ident</p>
		</div>		
	</xsl:template>
	
	<xsl:template name="user-setting-edit-value">
		<xsl:param name="value" />
		<xsl:param name="ident" />
		
		<label for="user-setting-edit-value">
			<xsl:choose>
				<xsl:when test="$ident = true()">
					<xsl:value-of select="$ident"/>
				</xsl:when>
				<xsl:otherwise>
					Value
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="/page/settings/get/value-error = true()">
				<span class="error">
					invalid value
				</span>
			</xsl:if>
		</label>
		<input type="text" name="value" class="text" id="user-setting-edit-value" value="{$value}" />
		<div class="fielddesc">
			<p>Change user setting value</p>
		</div>		
	</xsl:template>
	
	<!-- Edit end -->


	<!-- View -->
	<xsl:template name="user-setting-view-field">
		<xsl:param name="name" />
		<xsl:param name="value" />
		<div class="user-setting-view-field">
			<span>
				<xsl:value-of select="$name" />
			</span>
			<xsl:value-of select="$value" />
		</div>
	</xsl:template>
	<!-- View end -->

</xsl:stylesheet>