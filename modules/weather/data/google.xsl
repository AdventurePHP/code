<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="/xml_api_reply/weather">
        <weather>
            <xsl:apply-templates/>
        </weather>
    </xsl:template>

    <xsl:template match="forecast_information/city">
        <city>
            <xsl:value-of select="@data"/>
        </city>
    </xsl:template>

    <xsl:template match="current_conditions/condition">
        <condition>
            <xsl:value-of select="@data"/>
        </condition>
    </xsl:template>

    <xsl:template match="current_conditions/temp_c">
        <temp>
            <xsl:value-of select="@data"/>
        </temp>
    </xsl:template>
</xsl:stylesheet>