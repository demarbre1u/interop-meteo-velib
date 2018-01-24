<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:template match="/">
    <html> 
      <body>
        
          <xsl:for-each select="previsions/echeance">
            <xsl:choose>
              <xsl:when test="position() &lt;= 8" >
                <div class="ui floating message centered row" style="height: 190px">

                  <xsl:choose>
                    <xsl:when test="pluie &gt;= 0.5">
                      <img class="ui tiny image" src="img/rain.png" alt="rain"/>
                    </xsl:when>
                    <xsl:otherwise>
                      <img class="ui tiny image" src="img/sun.png" alt="sun"/>                    
                    </xsl:otherwise>
                  </xsl:choose>

                  <h3><xsl:value-of select="substring(@timestamp,12,5)"/></h3>
                  <p><xsl:value-of select="concat(substring(temperature/level[@val='sol'] - 273, 0, 5), ' °C')"/></p>
                </div>
              </xsl:when>
            </xsl:choose>
          </xsl:for-each>
        
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>