<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <!-- Define the template for the root -->
    <xsl:template match="/projects">
        <html>
            <head>
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .badge-success { background-color: green; color: white; padding: 5px; border-radius: 3px; }
                    .badge-warning { background-color: yellow; color: black; padding: 5px; border-radius: 3px; }
                    .badge-info { background-color: blue; color: white; padding: 5px; border-radius: 3px; }
                </style>
            </head>
            <body>
                <!-- Display projects for all roles -->
                <h1>Projects</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Description</th>
                            <th>Budget Amount</th>
                            <th>Total Spending</th>
                            <th>Completion Time (days)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="project"/>
                    </tbody>
                </table>
            </body>
        </html>
    </xsl:template>

    <!-- Template for rendering each project -->
    <xsl:template match="project">
        <tr>
            <td><xsl:value-of select="name" /></td>
            <td><xsl:value-of select="description" /></td>
            <td><xsl:value-of select="budgetAmount" /></td>
            <td><xsl:value-of select="totalCost" /></td>
            <td><xsl:value-of select="Completion_project_time" /></td>
        </tr>
    </xsl:template>



</xsl:stylesheet>
