<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:template match="/">
    <html> 
      <body>
        <table border="1">
          <tr bgcolor="steelblue">
            <th style="text-align:left">Heure</th>
            <th style="text-align:left">Temperature</th>
            <th style="text-align:left">Pluie</th>
            <th style="text-align:left">Neige</th>
          </tr>
          <xsl:for-each select="previsions/echeance">
            <xsl:choose>
              <xsl:when test="position() &lt;= 8" >
                <tr>
                    <td><xsl:value-of select="substring(@timestamp,11,9)"/></td>
                    <td><xsl:value-of select="concat(substring(temperature/level[@val='sol'] - 273, 0, 5), ' °C')"/></td>
                    <td><xsl:value-of select="pluie"/></td>
                    <td><xsl:value-of select="risque_neige"/></td>
                </tr>
              </xsl:when>
            </xsl:choose>
          </xsl:for-each>
        </table>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>