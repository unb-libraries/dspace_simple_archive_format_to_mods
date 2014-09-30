<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:sru_dc="info:srw/schema/1/dc-schema" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:mods="http://www.loc.gov/mods/v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" exclude-result-prefixes="sru_dc oai_dc dc" version="1.0">
    <xsl:output method="xml" indent="yes" encoding="UTF-8"/>
    <xsl:include href="inc/dcmiType.xsl"/>
    <xsl:include href="inc/mimeType.xsl"/>
    <xsl:include href="inc/csdgm.xsl"/>
    <xsl:include href="inc/forms.xsl"/>
    <xsl:include href="inc/iso3166-1.xsl"/>
    <xsl:include href="inc/iso639-2.xsl"/>

    <xsl:template match="oai_dc:dc">
        <mods:mods xmlns:mods="http://www.loc.gov/mods/v3" xmlns="http://www.loc.gov/mods/v3" xmlns:etd="http://www.ndltd.org/standards/metadata/etdms/1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xlink="http://www.w3.org/1999/xlink">
            <xsl:call-template name="dcModsThesisMain"/>
            <mods:name type="personal">
                <mods:role>
                    <mods:roleTerm authority="marcrelator" type="text">Committee member</mods:roleTerm>
                </mods:role>
                <mods:displayForm>Committee Members</mods:displayForm>
            </mods:name>
            <xsl:call-template name="dcModsGranter"/>
            <mods:typeOfResource>text</mods:typeOfResource>
            <mods:genre>Senior Report</mods:genre>
            <xsl:call-template name="dcModsOriginInfo"/>
            <xsl:call-template name="dcModsLanguage"/>
            <mods:physicalDescription>
                <mods:form authority="marcform">electronic</mods:form>
                <mods:extent/>
            </mods:physicalDescription>
            <mods:abstract/>
            <mods:location>
                <mods:url>Location</mods:url>
            </mods:location>
            <xsl:call-template name="dcModsStatementResponsibility"/>
            <mods:subject authority="lcsh">
                <mods:topic/>
                <mods:geographic/>
                <mods:temporal/>
            </mods:subject>
            <mods:identifier type="isbn"/>
            <mods:classification authority="lcc"/>
            <mods:classification edition="21" authority="ddc"/>
            <mods:extension>
                <etd:degree>
                    <etd:name> Bachelor of Science in Engineering</etd:name>
                    <etd:level>Bachelor</etd:level>
                    <etd:discipline>Engineering</etd:discipline>
                </etd:degree>
            </mods:extension>
            <xsl:call-template name="dcModsNote"/>
            <mods:accessCondition type="use and reproduction">author</mods:accessCondition>
            <mods:physicalDescription authority="local">PRE-PUBLICATION</mods:physicalDescription>
        </mods:mods>
    </xsl:template>

    <xsl:template name="dcModsThesisMain">
        <xsl:for-each select="dc:title">
            <xsl:apply-templates select="."/>
        </xsl:for-each>

        <xsl:for-each select="dc:creator">
            <xsl:apply-templates select="."/>
        </xsl:for-each>

        <xsl:for-each select="dc:contributor">
            <xsl:apply-templates select="."/>
        </xsl:for-each>

        <xsl:for-each select="dc:date">
            <xsl:apply-templates select="."/>
        </xsl:for-each>

    </xsl:template>

    <xsl:template name="dcModsLanguage">
        <xsl:for-each select="dc:language">
            <xsl:apply-templates select="."/>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="dc:title">
        <mods:titleInfo>
            <mods:title>
                <xsl:apply-templates/>
            </mods:title>
        </mods:titleInfo>
    </xsl:template>

    <xsl:template match="dc:creator">
        <mods:name type="personal">
            <mods:role>
                <mods:roleTerm authority="marcrelator" type="text">author</mods:roleTerm>
            </mods:role>
            <mods:namePart type="given">
                <xsl:apply-templates/>
            </mods:namePart>
        </mods:name>
    </xsl:template>

    <xsl:template match="dc:contributor">
        <mods:name type="personal">
            <mods:role>
                <mods:roleTerm authority="marcrelator" type="text">Thesis advisor</mods:roleTerm>
            </mods:role>
            <mods:displayForm>
                <xsl:apply-templates/>
            </mods:displayForm>
        </mods:name>
    </xsl:template>

    <xsl:template name="dcModsGranter">
        <mods:name type="corporate" authority="lcnaf">
            <mods:role>
                <mods:roleTerm authority="marcrelator" type="text">Degree grantor</mods:roleTerm>
            </mods:role>
            <mods:namePart>University of New Brunswick</mods:namePart>
            <xsl:for-each select="dc:identifier">
                <xsl:apply-templates select="."/>
            </xsl:for-each>
        </mods:name>
    </xsl:template>

    <xsl:template name="dcModsOriginInfo">
        <mods:originInfo>
            <xsl:for-each select="dc:date">
                <xsl:apply-templates select="."/>
            </xsl:for-each>
            <mods:issuance>monographic</mods:issuance>
        </mods:originInfo>
    </xsl:template>

    <xsl:template name="dcModsStatementResponsibility">
        <xsl:for-each select="dc:description">
            <xsl:apply-templates select="."/>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="dc:identifier">
        <xsl:if test="contains(text(), 'Dept. of')">
            <mods:namePart></mods:namePart>
        </xsl:if>
    </xsl:template>

    <xsl:template match="dc:date">
        <xsl:if test="string-length(text()) = 4">
            <mods:dateIssued keyDate="yes">
                <xsl:apply-templates/>
            </mods:dateIssued>
        </xsl:if>
    </xsl:template>

    <xsl:template match="dc:language">
        <mods:language>
            <mods:languageTerm authority="iso639-2b" type="code">
                <xsl:apply-templates/>
            </mods:languageTerm>
        </mods:language>
    </xsl:template>

    <xsl:template match="dc:description">
        <mods:note type="statement of responsibility">
            <xsl:apply-templates/>
        </mods:note>
    </xsl:template>

    <xsl:template name="dcModsNote">
        <mods:note>
            <xsl:for-each select="dc:identifier">
                <xsl:if test="string-length(text()) != 4">
                    <xsl:value-of select="."/>
                    <xsl:text>
                    </xsl:text>
                </xsl:if>
            </xsl:for-each>
        </mods:note>
    </xsl:template>

</xsl:stylesheet>
