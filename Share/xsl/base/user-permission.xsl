<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

	<!-- Lists -->
	<xsl:template match="user-permission-list" mode="xhtml-list">
		<table class="list">
			<thead>
				<th class="text ident">
					ident
				</th>
				<th class="text title">
					title
				</th>
				<th class="text description">
					description
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
					<td class="count" colspan="2">
						<xsl:value-of select="concat(@count, ' user-permission')" />
					</td>
				</tr>
			</tfoot>
			<tbody>
				<xsl:choose>
					<xsl:when test="count(user-permission) &gt; 0">
						<xsl:apply-templates select="user-permission" mode="xhtml-list" />
					</xsl:when>
					<xsl:otherwise>
						<tr><td colspan="5" style="text-align: center"><br/>No permissions found.<br/><br/></td></tr>					
					</xsl:otherwise>
				</xsl:choose>			
			</tbody>
		</table>
	</xsl:template>
	<xsl:template match="user-permission-list/user-permission" mode="xhtml-list">
		<tr>
			<td class="text ident">
				<xsl:value-of select="@ident" />
			</td>
			<td class="text title">
				<xsl:value-of select="@title" />
			</td>
			<td class="text description">
				<xsl:value-of select="@description" />
			</td>
			<td class="actions">
				<a href="corelib/extensions/Users/Permissions/{@id}/edit/">
					<img src="corelib/resource/manager/images/icons/generic/edit.png" alt="edit" title="Edit permission"/>
				</a>
				<a href="corelib/extensions/Users/Permissions/{@id}/delete/">
					<img src="corelib/resource/manager/images/icons/generic/delete.png" alt="delete" title="Delete permission"/>
				</a>
			</td>
		</tr>
	</xsl:template>
	
	<xsl:template match="user-permission-list" mode="form-select-options">
		<xsl:param name="select" />
		<xsl:apply-templates select="user-permission" mode="form-select-options">
			<xsl:with-param name="select" select="$select" />
		</xsl:apply-templates>
	</xsl:template>
	<xsl:template match="user-permission-list/user-permission" mode="form-select-options">
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
	<xsl:template name="user-permission-edit-ident">
		<xsl:param name="ident" />
		<label for="user-permission-edit-ident">
			ident
			<xsl:if test="/page/settings/get/ident-error = true()">
				<span class="error">
					invalid ident
				</span>
			</xsl:if>
		</label>
		<input type="text" name="ident" class="text" id="user-permission-edit-ident" value="{$ident}" />
		<div class="fielddesc">
		  <p>Retype the password to confirm it.</p>
		</div>		
	</xsl:template>
	
	<xsl:template name="user-permission-edit-title">
		<xsl:param name="title" />
		<label for="user-permission-edit-title">
			title
			<xsl:if test="/page/settings/get/title-error = true()">
				<span class="error">
					invalid title
				</span>
			</xsl:if>
		</label>
		<input type="text" name="title" class="text" id="user-permission-edit-title" value="{$title}" />
		<div class="fielddesc">
		  <p>Retype the password to confirm it.</p>
		</div>		
	</xsl:template>
	
	<xsl:template name="user-permission-edit-description">
		<xsl:param name="description" />
		<label for="user-permission-edit-description">
			description
			<xsl:if test="/page/settings/get/description-error = true()">
				<span class="error">
					invalid description
				</span>
			</xsl:if>
		</label>
		<input type="text" name="description" class="text" id="user-permission-edit-description" value="{$description}" />
		<div class="fielddesc">
		  <p>Retype the password to confirm it.</p>
		</div>		
	</xsl:template>
	
	<!-- Edit end -->


	<!-- View -->
	<xsl:template name="user-permission-view-field">
		<xsl:param name="name" />
		<xsl:param name="value" />
		<div class="user-permission-view-field">
			<span>
				<xsl:value-of select="$name" />
			</span>
			<xsl:value-of select="$value" />
		</div>
	</xsl:template>
	<!-- View end -->

</xsl:stylesheet>