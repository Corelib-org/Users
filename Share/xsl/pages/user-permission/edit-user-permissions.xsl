<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../../../../Base/share/xsl/base/layouts/default.xsl"/>
	<xsl:include href="../../base/user-permission.xsl"/>


	<xsl:template match="content" mode="xhtml-content">
		<xsl:call-template name="h1">
			<xsl:with-param name="headline">Edit user permissions</xsl:with-param>
			<xsl:with-param name="nav">
				<xsl:apply-templates select="user-editmodes" mode="view-selector">
					<xsl:with-param name="id" select="user/@id"/>
					<xsl:with-param name="select">permissions</xsl:with-param>
				</xsl:apply-templates>
			</xsl:with-param>
		</xsl:call-template>	
		<form method="post">
				<script type="text/javascript">
					<xsl:comment>
						function checkCheckbox(row, checkbox){
							if(checkbox.checked == false){
								row.addClassName('selected');
								checkbox.checked = true;
							} else {
								row.removeClassName('selected');
								checkbox.checked = false;
							}
						}
					// </xsl:comment>
				</script>
				
				<table>
					<thead>
						<th class="text"></th>
						<th class="text ident">Ident</th>
						<th class="text title">Title</th>
						<th class="date description">Description</th>
					</thead>
					<xsl:for-each select="user-permission-list/user-permission">
						<xsl:variable name="id" select="@id"/>
						<xsl:element name="tr">
							<xsl:attribute name="class">
								<xsl:if test="position() mod 2 = 0">
									<xsl:text>highlight</xsl:text>
								</xsl:if>
								<xsl:if test="../../user/user-permission-manager/user-permission[@id = $id] = true()">
									<xsl:text> selected</xsl:text>
								</xsl:if>
							</xsl:attribute>

					
							<td class="checkbox" onclick="checkCheckbox(this.parentNode, $('permission_{@id}'));">
								<xsl:element name="input">
									<xsl:attribute name="type">checkbox</xsl:attribute>
									<xsl:attribute name="name">permission[<xsl:value-of select="@id"/>]</xsl:attribute>
									<xsl:attribute name="id">permission_<xsl:value-of select="@id"/></xsl:attribute>
									<xsl:attribute name="onclick">checkCheckbox(this.parentNode, $('permission_<xsl:value-of select="@id"/>'));</xsl:attribute>
									<xsl:if test="../../user/user-permission-manager/user-permission[@id = $id] = true()">
										<xsl:attribute name="checked">true</xsl:attribute>
									</xsl:if>
								</xsl:element>
							</td>
							<td onclick="checkCheckbox(this.parentNode, $('permission_{@id}'));"><xsl:value-of select="@ident"/></td>
							<td onclick="checkCheckbox(this.parentNode, $('permission_{@id}'));"><xsl:value-of select="@title"/></td>
							<td onclick="checkCheckbox(this.parentNode, $('permission_{@id}'));"><xsl:value-of select="@description"/></td>
						</xsl:element>
					</xsl:for-each>
				</table>				
				<!-- Edit end -->
			<input type="submit" value="Change permissions" class="button submit right"/>
		</form>
	</xsl:template>
</xsl:stylesheet>