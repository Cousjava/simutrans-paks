//<?xml version="1.0" encoding="utf8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
 <html><head></head><body>
  <xsl:apply-templates />
 </body></html>
</xsl:template>

<xsl:template match="rss">
     <xsl:apply-templates />
</xsl:template>

<xsl:template match="channel">
     <xsl:apply-templates />
</xsl:template>

<xsl:template match="image">
     <xsl:apply-templates />
</xsl:template>

<xsl:template match="item">
     <xsl:apply-templates />
</xsl:template>

<xsl:template match="description">
     <xsl:apply-templates />
</xsl:template>

<xsl:template match="object">
   <xsl:apply-templates />
</xsl:template>

<xsl:template match="obj_type">
  <b><xsl:value-of select="." /></b>
</xsl:template>

<xsl:template match="obj_name">
  <xsl:value-of select="." /><br />
</xsl:template>

<xsl:template match="obj_trans">
  <p style="background-color:#88C4FF;"> 
   <xsl:apply-templates />
  </p>
</xsl:template>

<xsl:template match="trans_note">
    <b><xsl:value-of select="." /></b><br />
</xsl:template>

<xsl:template match="trans_text">
 <p><xsl:value-of select="." /></p><br />
</xsl:template>

<xsl:template match="obj_sug">
  <p style="background-color:#79FFFF;"> 
   <xsl:apply-templates />
  </p>
</xsl:template>

<xsl:template match="sug_note">
    <b><xsl:value-of select="." /></b><br />
</xsl:template>

<xsl:template match="sug_text">
 <p><xsl:value-of select="." /></p><br />
</xsl:template>

</xsl:stylesheet>
