<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../../../../Base/share/xsl/base/layouts/default.xsl"/>
	<xsl:include href="../../base/user.xsl"/>

	<xsl:template match="content" mode="xhtml-content">
		<xsl:call-template name="h1">
			<xsl:with-param name="headline">User list</xsl:with-param>
			<xsl:with-param name="nav">
				<label for="view"><a href="corelib/extensions/Users/create/">Create user</a>&#160;&#160;&#160;</label>
				<label for="view">View</label>
				<select id="view" class="select" onchange="Toolbox.setLocation('corelib/extensions/Users/?view='+this.options[this.selectedIndex].value);">
					<xsl:element name="option">
						<xsl:attribute name="value">active</xsl:attribute>
						<xsl:if test="/page/settings/get/view = 'active'">
							<xsl:attribute name="selected">true</xsl:attribute>	
						</xsl:if>
						Active users
					</xsl:element>
					<xsl:element name="option">
						<xsl:attribute name="value">inactive</xsl:attribute>
						<xsl:if test="/page/settings/get/view = 'inactive'">
							<xsl:attribute name="selected">true</xsl:attribute>	
						</xsl:if>
						Inactive users
					</xsl:element>
					<xsl:element name="option">
						<xsl:attribute name="value">deleted</xsl:attribute>
						<xsl:if test="/page/settings/get/view = 'deleted'">
							<xsl:attribute name="selected">true</xsl:attribute>	
						</xsl:if>
						Deleted users
					</xsl:element>
					<xsl:element name="option">
						<xsl:attribute name="value">all</xsl:attribute>
						<xsl:if test="/page/settings/get/view = 'all'">
							<xsl:attribute name="selected">true</xsl:attribute>	
						</xsl:if>
						All users
					</xsl:element>
				</select>
			</xsl:with-param>			
		</xsl:call-template>
		
		<!-- List --> 
		<xsl:apply-templates select="user-list" mode="xhtml-list"/>
		<!-- List end -->
	</xsl:template>
</xsl:stylesheet>